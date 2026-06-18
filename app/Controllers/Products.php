<?php

namespace App\Controllers;

use App\Libraries\ProductCatalog;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Products controller
 *
 * File-based product catalogue (see App\Libraries\ProductCatalog).
 * Three levels, all under the locale prefix:
 *
 *   products                    → all 11 categories
 *   products/{category}         → product grid of one category
 *   products/{category}/{slug}  → product detail with the complete
 *                                 technical sheets of every
 *                                 manufacturer/model variant
 *
 * Slugs are locale-independent so the language switcher and the mega
 * menu links keep working on every page.
 */
class Products extends BaseController
{
    /** Category index: all 11 main groups. */
    public function index(): string
    {
        $categories = ProductCatalog::categories();

        $totalProducts = 0;
        $totalVariants = 0;
        foreach ($categories as $category) {
            $totalProducts += count($category['products']);
            $totalVariants += ProductCatalog::variantCount($category);
        }

        return view('pages/products/index', [
            'metaTitle'       => lang('Products.meta_title'),
            'metaDescription' => lang('Products.meta_description'),
            'categories'      => $categories,
            'totalProducts'   => $totalProducts,
            'totalVariants'   => $totalVariants,
        ]);
    }

    /** Category page: filterable grid of the category's products. */
    public function category(string $slug): string
    {
        $category = ProductCatalog::category($slug);
        if ($category === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $name = $this->categoryName($category);

        return view('pages/products/category', [
            'metaTitle'       => $name . ' — ' . lang('Navigation.products'),
            'metaDescription' => lang('Products.category_meta', [$name]),
            'category'        => $category,
            'categoryName'    => $name,
            'variantCount'    => ProductCatalog::variantCount($category),
        ]);
    }

    /** Product detail: full technical sheets per model variant. */
    public function detail(string $categorySlug, string $productSlug): string
    {
        $category = ProductCatalog::category($categorySlug);
        $product  = ProductCatalog::product($categorySlug, $productSlug);
        if ($category === null || $product === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Related products: neighbours in the same category, wrapping
        // around so every page shows a varied rail.
        $siblings = $category['products'];
        $index    = 0;
        foreach ($siblings as $i => $candidate) {
            if ($candidate['slug'] === $productSlug) {
                $index = $i;
                break;
            }
        }
        $related = [];
        $count   = count($siblings);
        for ($step = 1; $step <= $count - 1 && count($related) < 3; $step++) {
            $related[] = $siblings[($index + $step) % $count];
        }

        // OG görseli: ürün fotoğrafı varsa WhatsApp/sosyal medya önizleme kartı
        // bunu kullanır (assets/images/products/{kategori}-{ürün}.jpg). Yoksa
        // meta.php varsayılan OG görseline düşer.
        $imageRel  = 'assets/images/products/' . $categorySlug . '-' . $productSlug . '.jpg';
        $metaImage = is_file(FCPATH . $imageRel) ? base_url($imageRel) : null;

        return view('pages/products/detail', [
            'metaTitle'       => $product['name'] . ' — ' . $this->categoryName($category),
            'metaDescription' => lang('Products.detail_meta', [$product['name']]),
            'metaImage'       => $metaImage,
            'category'        => $category,
            'categoryName'    => $this->categoryName($category),
            'product'         => $product,
            'related'         => $related,
        ]);
    }

    /**
     * Localized display name of a category. Falls back to the catalogue
     * name when no translation key exists.
     */
    private function categoryName(array $category): string
    {
        $key   = 'Navigation.cat_' . str_replace('-', '_', $category['slug']);
        $label = lang($key);

        return $label === $key ? $category['name'] : $label;
    }
}
