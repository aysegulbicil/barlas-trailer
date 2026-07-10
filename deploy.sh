#!/usr/bin/env bash
#
# deploy.sh — Barlas Trailer production deploy (Hostinger Business Hosting)
# ---------------------------------------------------------------------
# Server-side deployment script. Triggered over SSH by GitHub Actions
# (.github/workflows/deploy.yml) on every push to the production branch.
#
# It is safe to run manually as well:
#   cd ~/domains/barlastrailer.com/barlas-trailer && bash deploy.sh
#
# Guarantees:
#   * Fails fast — any error stops the whole deploy (set -euo pipefail + ERR trap).
#   * Never touches .env, writable/ user data or public_html/{uploads,downloads}.
#   * composer install runs ONLY when composer.json/lock changed (or vendor missing).
#   * public/ is mirrored into public_html/ and index.php's path is auto-repaired.
#   * Idempotent: running it twice on the same commit is a no-op.
# ---------------------------------------------------------------------

set -euo pipefail

# ── Configuration (override via environment if the layout ever changes) ──────
BRANCH="${DEPLOY_BRANCH:-fable-test}"
DOMAIN_ROOT="${DEPLOY_DOMAIN_ROOT:-$HOME/domains/barlastrailer.com}"
APP_DIR="${DEPLOY_APP_DIR:-$DOMAIN_ROOT/barlas-trailer}"
PUBLIC_HTML="${DEPLOY_PUBLIC_HTML:-$DOMAIN_ROOT/public_html}"

# PHP / Composer binaries. On Hostinger `php` maps to the version selected in
# hPanel; override with e.g. DEPLOY_PHP_BIN=/usr/bin/php8.3 if needed.
PHP_BIN="${DEPLOY_PHP_BIN:-php}"
COMPOSER_BIN="${DEPLOY_COMPOSER_BIN:-composer}"

# The exact require() line index.php must use once it lives in public_html/.
# (In the repo it is FCPATH . '../app/...'; there app/ is a sibling of public/,
#  but in public_html/ the app lives one level up in barlas-trailer/.)
INDEX_REQUIRE_LINE="require __DIR__ . '/../barlas-trailer/app/Config/Paths.php';"

# ── Pretty logging + fail-fast trap ──────────────────────────────────────────
log()  { printf '\033[1;34m[deploy]\033[0m %s\n' "$*"; }
warn() { printf '\033[1;33m[deploy:warn]\033[0m %s\n' "$*"; }
die()  { printf '\033[1;31m[deploy:error]\033[0m %s\n' "$*" >&2; exit 1; }
trap 'die "Deployment aborted (line $LINENO)."' ERR

START_TS=$(date +%s)

# ── 0. Sanity checks ─────────────────────────────────────────────────────────
[ -d "$APP_DIR/.git" ]   || die "Git repo not found at $APP_DIR (run initial clone first)."
[ -d "$PUBLIC_HTML" ]    || die "public_html not found at $PUBLIC_HTML."
command -v git >/dev/null 2>&1 || die "git is not available on PATH."

cd "$APP_DIR"

# ── 1. Sync repository (fetch + hard reset — deterministic, no merge surprises) ─
log "Fetching origin/$BRANCH …"
PREV_REF="$(git rev-parse HEAD)"
git fetch --prune origin "$BRANCH"
git reset --hard "origin/$BRANCH"
NEW_REF="$(git rev-parse HEAD)"

if [ "$PREV_REF" = "$NEW_REF" ]; then
  log "Already at $(git rev-parse --short HEAD) — no new commits."
else
  log "Updated $(git rev-parse --short "$PREV_REF") → $(git rev-parse --short "$NEW_REF")."
fi
# NOTE: intentionally NO `git clean` — it would wipe vendor/, .env and writable
# user data. `git reset --hard` already restores every tracked file and never
# touches untracked files (e.g. a runtime SQLite db under writable/db/).

# ── 2. composer install — only when dependencies actually changed ─────────────
needs_composer=false
if [ ! -f vendor/autoload.php ]; then
  needs_composer=true
  log "vendor/ missing → composer install required."
elif [ "$PREV_REF" != "$NEW_REF" ] \
     && git diff --name-only "$PREV_REF" "$NEW_REF" | grep -qE '^composer\.(json|lock)$'; then
  needs_composer=true
  log "composer.json/lock changed → composer install required."
fi

if [ "$needs_composer" = true ]; then
  if command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
    COMPOSER_CMD="$COMPOSER_BIN"
  elif [ -f composer.phar ]; then
    COMPOSER_CMD="$PHP_BIN composer.phar"
  else
    die "Composer not found (neither '$COMPOSER_BIN' on PATH nor composer.phar)."
  fi
  log "Running composer install (production) …"
  $COMPOSER_CMD install \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative \
    --no-interaction \
    --no-progress \
    --prefer-dist
else
  log "Dependencies unchanged — skipping composer install."
fi

# ── 3. Mirror public/ → public_html/ (preserve .env & user files) ────────────
log "Syncing public/ → public_html/ …"
if command -v rsync >/dev/null 2>&1; then
  rsync -a --delete \
    --exclude='.env' \
    --exclude='/uploads/' \
    --exclude='/uploads/***' \
    "$APP_DIR/public/" "$PUBLIC_HTML/"
else
  # Fallback for hosts without rsync: cp mirror (uploads/.env left untouched).
  warn "rsync unavailable — falling back to cp -a."
  cp -a "$APP_DIR/public/." "$PUBLIC_HTML/"
fi

# ── 4. Repair public_html/index.php path (self-healing) ──────────────────────
INDEX="$PUBLIC_HTML/index.php"
[ -f "$INDEX" ] || die "index.php missing in public_html after sync."
if ! grep -qF "$INDEX_REQUIRE_LINE" "$INDEX"; then
  log "Rewriting index.php Paths require line …"
  # Replace whatever require *Config/Paths.php line is present with the correct one.
  sed -i "s#^\s*require .*Config/Paths\.php';#${INDEX_REQUIRE_LINE}#" "$INDEX"
  grep -qF "$INDEX_REQUIRE_LINE" "$INDEX" || die "Failed to patch index.php require path."
else
  log "index.php path already correct."
fi

# ── 5. Fix writable/ permissions ─────────────────────────────────────────────
if [ -d "$APP_DIR/writable" ]; then
  log "Ensuring writable/ permissions …"
  chmod -R u+rwX,g+rwX "$APP_DIR/writable" 2>/dev/null || warn "Could not chmod writable/."
fi

# ── 5.5 Internal tools / panel login bootstrap (Shield + SQLite) ─────────────
# /patron and /panel authenticate against writable/db/barlas.sqlite. That DB is
# runtime data (deploy never copies it), so the connection config, the schema
# and the admin user must be provisioned on this server. Idempotent by design.
ENV_FILE="$APP_DIR/.env"
SQLITE_DB="$APP_DIR/writable/db/barlas.sqlite"

[ -f "$ENV_FILE" ] || { warn ".env missing — creating one with only the SQLite connection."; touch "$ENV_FILE"; }

if ! grep -q '^database\.default\.DBDriver' "$ENV_FILE"; then
  log "Adding SQLite connection to .env …"
  {
    printf '\n# Shield identity DB (/panel + /patron) — added by deploy.sh\n'
    printf 'database.default.DBDriver = SQLite3\n'
    printf 'database.default.database = %s\n' "$SQLITE_DB"
    printf 'database.default.foreignKeys = true\n'
  } >> "$ENV_FILE"
elif grep -q '^database\.default\.database *= */var/www/html/' "$ENV_FILE"; then
  # A copied local .env still points at the Docker container path — repair it.
  log "Rewriting Docker-era SQLite path in .env …"
  sed -i "s#^database\.default\.database *=.*#database.default.database = $SQLITE_DB#" "$ENV_FILE"
fi

mkdir -p "$APP_DIR/writable/db"

log "Running database migrations (App + Shield + Settings) …"
( cd "$APP_DIR" && "$PHP_BIN" spark migrate --all ) \
  || warn "migrate failed — /patron and /panel logins will not work until this is fixed."

# Admin user comes from GitHub secrets (PANEL_ADMIN_EMAIL / PANEL_ADMIN_PASSWORD,
# forwarded by deploy.yml). panel:admin updates the password if the user exists.
if [ -n "${PANEL_ADMIN_EMAIL:-}" ] && [ -n "${PANEL_ADMIN_PASSWORD:-}" ]; then
  log "Ensuring panel admin user exists (${PANEL_ADMIN_EMAIL}) …"
  ( cd "$APP_DIR" && "$PHP_BIN" spark panel:admin "$PANEL_ADMIN_EMAIL" "$PANEL_ADMIN_PASSWORD" ) \
    || warn "panel:admin failed — check the Actions log."
else
  log "PANEL_ADMIN_EMAIL / PANEL_ADMIN_PASSWORD not set — skipping admin bootstrap."
fi

# ── 6. Clear framework caches (best-effort) ──────────────────────────────────
if [ -f "$APP_DIR/spark" ]; then
  log "Clearing CodeIgniter caches …"
  ( cd "$APP_DIR" && "$PHP_BIN" spark cache:clear ) 2>/dev/null || warn "cache:clear skipped."
fi

log "Deploy finished in $(( $(date +%s) - START_TS ))s → now at $(git rev-parse --short HEAD)."
