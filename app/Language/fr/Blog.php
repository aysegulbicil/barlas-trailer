<?php

/**
 * Blog language strings (fr)
 */
return [
    // SEO
    'meta_title'       => 'Blog',
    'meta_description' => 'Actualités du secteur, conseils d\'entretien, articles technologiques et guides d\'experts du monde des remorques et des citernes.',

    // En-tête de page
    'page_title' => 'Blog',
    'page_lead'  => 'Actualités du secteur, conseils d\'entretien et guides d\'ingénierie.',

    // Barre latérale
    'search_title'       => 'Recherche',
    'search_placeholder' => 'Rechercher des articles...',
    'search_submit'      => 'Rechercher',
    'categories_title'   => 'Catégories',
    'all_posts'          => 'Tous les articles',

    // Cartes et états de la liste
    'read_more'        => 'Lire la suite',
    'results_found'    => 'articles trouvés',
    'no_results_title' => 'Aucun résultat',
    'no_results_text'  => 'Aucun article ne correspond à vos critères de recherche. Essayez un autre mot-clé ou parcourez tous les articles.',
    'clear_filters'    => 'Afficher tous les articles',

    // Page de détail
    'related_title'   => 'Articles similaires',
    'related_eyebrow' => 'Poursuivre la lecture',
    'back_to_blog'    => 'Retour à tous les articles',

    // Catégories
    'cat_industry'    => 'Secteur',
    'cat_maintenance' => 'Entretien & Service',
    'cat_safety'      => 'Sécurité & ADR',
    'cat_technology'  => 'Technologie',

    // Format de date
    'months' => ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],

    // Articles (slug => textes)
    'posts' => [
        'adr-tanker-safety-standards' => [
            'title'   => 'Normes de sécurité des citernes certifiées ADR',
            'excerpt' => 'Comment la réglementation ADR façonne la conception des citernes pour le transport de matières dangereuses, et les pratiques de sécurité essentielles pour chaque exploitant.',
            'content' => '
                <p>Le transport de matières dangereuses par route est strictement encadré par l\'accord international ADR. Pour les citernes transportant du carburant, du GPL ou des produits chimiques, chaque étape — de la conception à la fabrication, des essais aux inspections périodiques — s\'effectue dans ce cadre réglementaire.</p>
                <h2>Comment l\'ADR façonne la conception des citernes</h2>
                <p>Dans une citerne certifiée ADR, le matériau de la cuve, l\'épaisseur des parois, le nombre de compartiments et la disposition des brise-flots sont calculés selon la classe du produit transporté. La résistance au retournement, la mise à la terre, les vannes d\'arrêt d\'urgence et les systèmes anti-débordement font partie intégrante de la conception. Après production, les essais de pression et d\'étanchéité sont validés par des organismes indépendants.</p>
                <h2>Les points de vigilance pour les exploitants</h2>
                <p>La certification seule ne suffit pas ; la sécurité repose sur une utilisation correcte et des contrôles réguliers. Les priorités des exploitants sont les suivantes :</p>
                <ul>
                    <li>Suivi des échéances d\'inspection intermédiaire et périodique</li>
                    <li>Contrôle régulier des vannes, joints et équipements de sécurité</li>
                    <li>Respect strict des procédures de chargement et de déchargement</li>
                    <li>Mise à jour des certificats de formation ADR des conducteurs</li>
                </ul>
                <p>Lorsqu\'une citerne bien conçue rencontre une équipe d\'exploitation bien formée, le transport de matières dangereuses s\'effectue à la fois en sécurité et avec efficacité.</p>
            ',
        ],

        'silo-trailer-maintenance-tips' => [
            'title'   => '7 conseils d\'entretien pour prolonger la vie d\'une citerne pulvé',
            'excerpt' => 'Les étapes d\'entretien essentielles pour que les semi-remorques citernes transportant des produits pulvérulents et granulaires restent performantes plus longtemps.',
            'content' => '
                <p>Les citernes pulvérulentes travaillent dans des conditions exigeantes, transportant ciment, farine, PVC et autres produits en poudre ou en granulés. Sans entretien régulier, le système de déchargement sous pression et les conduites d\'air deviennent la première source de perte d\'efficacité et de pannes.</p>
                <h2>L\'importance de l\'entretien planifié</h2>
                <p>L\'entretien planifié ne se limite pas à réduire le risque de panne : il raccourcit les temps de déchargement, diminue la consommation de carburant et préserve la valeur de revente. Les étapes suivantes préviennent la plupart des problèmes rencontrés sur le terrain :</p>
                <ul>
                    <li>Vérifiez l\'étanchéité des conduites d\'air et des manchettes avant chaque trajet</li>
                    <li>Contrôlez régulièrement le niveau d\'huile et les filtres du compresseur</li>
                    <li>Surveillez l\'usure des joints des vannes de déchargement</li>
                    <li>Testez les verrous et joints des trous d\'homme supérieurs</li>
                    <li>Nettoyez l\'intérieur de la cuve à chaque changement de produit</li>
                    <li>Faites tester la soupape de sécurité au moins une fois par an</li>
                    <li>Resserrez périodiquement les liaisons châssis et essieux</li>
                </ul>
                <h2>Un accompagnement professionnel</h2>
                <p>Si les contrôles quotidiens peuvent être effectués par l\'exploitant, les systèmes sous pression, les soupapes et les contrôles structurels doivent être confiés à des équipes de service spécialisées. L\'utilisation de pièces d\'origine approuvées par le constructeur reste le moyen le plus fiable de préserver les performances d\'origine du système.</p>
            ',
        ],

        'lowbed-heavy-transport-guide' => [
            'title'   => 'Bien choisir son porte-engins pour le transport lourd',
            'excerpt' => 'Les critères à prendre en compte pour définir la bonne configuration de semi-remorque surbaissée pour le transport d\'engins de chantier et d\'équipements lourds.',
            'content' => '
                <p>Le transport d\'engins de chantier, de groupes électrogènes, de transformateurs et d\'équipements industriels exige des solutions dépassant les limites des semi-remorques standards. Avec leur hauteur de chargement réduite et leur grande capacité de charge, les semi-remorques surbaissées sont l\'outil clé de ce segment.</p>
                <h2>Définir la bonne configuration</h2>
                <p>La première question lors du choix d\'un porte-engins est toujours la même : que va-t-on transporter ? Le poids, les dimensions et le centre de gravité de la charge déterminent toutes les décisions techniques — du nombre d\'essieux à la longueur du plateau, du type de rampes au besoin d\'élargissement. Outre les modèles fixes à deux, trois et quatre essieux, des solutions modulaires et extensibles sont également disponibles.</p>
                <h2>Les critères de choix essentiels</h2>
                <ul>
                    <li>Charge utile maximale et poids par essieu</li>
                    <li>Hauteur de chargement et longueur du col de cygne</li>
                    <li>Rampes hydrauliques ou à ressort</li>
                    <li>Besoin d\'essieux auto-directionnels</li>
                    <li>Restrictions de ponts et de tunnels sur l\'itinéraire</li>
                </ul>
                <p>Un porte-engins correctement configuré ne se contente pas de transporter la charge : il améliore la sécurité des opérations, simplifie les démarches d\'autorisation et réduit les coûts d\'exploitation sur le long terme. Travailler avec l\'équipe d\'ingénierie du constructeur garantit le meilleur résultat.</p>
            ',
        ],

        'lightweight-chassis-technology' => [
            'title'   => 'Châssis allégé : moins de poids, plus de charge utile',
            'excerpt' => 'Les avantages opérationnels de la réduction du poids à vide grâce aux aciers à haute résistance et à une conception optimisée du châssis.',
            'content' => '
                <p>Les limites légales de poids total étant fixes dans le transport routier, le gain provient du poids à vide de la semi-remorque. Chaque kilogramme économisé sur le châssis se transforme directement en charge utile — et en revenu supplémentaire à chaque trajet.</p>
                <h2>Le rôle des aciers à haute résistance</h2>
                <p>Dans la construction moderne de semi-remorques, les aciers à haute résistance remplacent progressivement les nuances structurelles standards. Ces matériaux offrent une résistance égale ou supérieure avec des sections plus fines. Associés à des procédures de soudage adaptées et à des techniques de production précises, ils permettent une réduction significative du poids du châssis tout en préservant la tenue en fatigue.</p>
                <h2>L\'optimisation de la conception</h2>
                <p>Le matériau seul ne suffit pas. L\'analyse par éléments finis (FEA) modélise la répartition des contraintes sur le châssis : le matériau n\'est utilisé que là où il est nécessaire. La géométrie des longerons, l\'implantation des traverses et les détails d\'assemblage sont optimisés sur la base de ces analyses.</p>
                <p>Le résultat : une consommation de carburant réduite, davantage de charge utile et moins d\'émissions de CO₂. L\'allègement est aujourd\'hui l\'un des sujets majeurs de l\'ingénierie des semi-remorques, tant sur le plan économique qu\'environnemental.</p>
            ',
        ],

        'reefer-cold-chain-logistics' => [
            'title'   => 'Les technologies frigorifiques dans la chaîne du froid',
            'excerpt' => 'Les solutions modernes de semi-remorques frigorifiques qui garantissent une chaîne du froid ininterrompue pour le transport alimentaire et pharmaceutique.',
            'content' => '
                <p>Lors du transport de produits thermosensibles tels que les denrées alimentaires et les médicaments, le moindre écart peut entraîner des pertes de produits et de graves risques sanitaires. Les semi-remorques frigorifiques — pierre angulaire de la chaîne du froid — assurent la continuité de cette opération délicate.</p>
                <h2>La qualité de l\'isolation avant tout</h2>
                <p>La performance d\'une caisse frigorifique se joue d\'abord sur la qualité de l\'isolation, avant même le groupe froid. Les panneaux à âme polyuréthane haute densité, associés à des assemblages minimisant les ponts thermiques, permettent au groupe de moins fonctionner et réduisent la consommation. Les joints de portes et la structure du plancher jouent également un rôle décisif dans les déperditions totales.</p>
                <h2>Suivi et enregistrement des données</h2>
                <p>Dans les opérations modernes, le suivi de température ne se limite plus à l\'afficheur de cabine. Les systèmes de télémétrie en temps réel permettent de surveiller à distance la température de la caisse, les ouvertures de portes et les performances du groupe froid. Ces données deviennent de plus en plus importantes, tant pour la conformité réglementaire que comme preuve de qualité auprès des clients.</p>
                <p>Lorsqu\'une isolation soignée, un groupe froid fiable et un suivi continu sont réunis, la chaîne du froid reste protégée même sur les plus longs itinéraires.</p>
            ',
        ],

        'trailer-winter-maintenance' => [
            'title'   => 'Préparer l\'hiver : check-list d\'entretien de la semi-remorque',
            'excerpt' => 'Une check-list complète pour préparer votre flotte de semi-remorques aux basses températures, au sel de déneigement et aux conditions hivernales difficiles.',
            'content' => '
                <p>Les mois d\'hiver — basses températures, sel de déneigement et chaussées difficiles — constituent la période la plus éprouvante de l\'année pour les semi-remorques. Une préparation planifiée avant la saison évite les pannes en bord de route au cœur de l\'hiver.</p>
                <h2>Freins et circuits pneumatiques</h2>
                <p>Par temps froid, le dessiccateur d\'air devient critique : si la cartouche n\'est pas remplacée à temps, l\'humidité accumulée dans les conduites peut geler et bloquer le système de freinage. Plaquettes, disques et leviers de frein automatiques doivent être contrôlés avant la saison.</p>
                <h2>Check-list d\'avant-saison</h2>
                <ul>
                    <li>Remplacez la cartouche du dessiccateur et purgez les réservoirs</li>
                    <li>Contrôlez l\'épaisseur des plaquettes et l\'état des disques</li>
                    <li>Mesurez la profondeur des sculptures et la pression des pneus</li>
                    <li>Vérifiez l\'éclairage et les connexions électriques</li>
                    <li>Lavez le châssis et appliquez une protection anticorrosion</li>
                    <li>Graissez les charnières et serrures de portes</li>
                </ul>
                <p>Une préparation hivernale régulière est le moyen le plus économique d\'augmenter la disponibilité de la flotte et de prolonger la durée de vie des véhicules. Notre réseau de service agréé est à votre disposition pour des contrôles saisonniers complets.</p>
            ',
        ],
    ],
];
