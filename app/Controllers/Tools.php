<?php

namespace App\Controllers;

use App\Libraries\ProductCatalog;

/**
 * Tools controller
 *
 * Free logistics calculators + the "Which trailer suits me?" quiz — the
 * first two traffic engines of the ecosystem plan. Everything computes
 * client-side; the server only renders localized markup and injects the
 * shared constants (app/Data/tools-constants.json) and the decision tree
 * (app/Data/quiz-tree.json, key-based → localized here).
 */
class Tools extends BaseController
{
    public function index(): string
    {
        $constants = json_decode((string) @file_get_contents(APPPATH . 'Data/tools-constants.json'), true) ?? [];

        return view('pages/tools/index', [
            'metaTitle'       => lang('Tools.meta_title'),
            'metaDescription' => lang('Tools.meta_description'),
            'constants'       => $constants,
        ]);
    }

    public function quiz(): string
    {
        $tree = json_decode((string) @file_get_contents(APPPATH . 'Data/quiz-tree.json'), true) ?? [];

        return view('pages/tools/quiz', [
            'metaTitle'       => lang('Quiz.meta_title'),
            'metaDescription' => lang('Quiz.meta_description'),
            'payload'         => $this->localizedTree($tree),
        ]);
    }

    /** Key-based decision tree → fully localized client payload. */
    private function localizedTree(array $tree): array
    {
        $nodes = [];

        foreach ($tree['nodes'] ?? [] as $id => $node) {
            $options = [];

            foreach ($node['options'] as $opt) {
                $o = ['label' => lang('Quiz.' . $opt['o'])];

                if (isset($opt['next'])) {
                    $o['next'] = $opt['next'];
                }

                if (isset($opt['result'])) {
                    $r = $opt['result'];

                    $o['result'] = [
                        'reason' => lang('Quiz.' . $r['reason']),
                        'name'   => $this->categoryName($r['cat']),
                        'url'    => locale_url('products/' . $r['cat']),
                    ];

                    if (! empty($r['alt'])) {
                        $o['result']['altName'] = $this->categoryName($r['alt']);
                        $o['result']['altUrl']  = locale_url('products/' . $r['alt']);
                    }
                }

                $options[] = $o;
            }

            $nodes[$id] = [
                'question' => lang('Quiz.' . $node['q']),
                'options'  => $options,
            ];
        }

        return ['start' => $tree['start'] ?? 'q1', 'nodes' => $nodes];
    }

    /** Same convention as Products::categoryName (Navigation.cat_* fallback). */
    private function categoryName(string $slug): string
    {
        $key   = 'Navigation.cat_' . str_replace('-', '_', $slug);
        $label = lang($key);

        if ($label !== $key) {
            return $label;
        }

        $category = ProductCatalog::category($slug);

        return $category['name'] ?? $slug;
    }
}
