<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Faker\Provider\ar_EG\Color;
use Livewire\Component;

class ProductBrowser extends Component
{
    public $category;

    public $queryFilters = [];

    public $priceRange = [
        'max' => null
    ];

    public function mount()
    {
        $this->queryFilters = $this->category->products->pluck('variations')
            ->flatten()
            ->groupBy('type')
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => []])
            ->toArray();
    }

    public function render()
    {
        $search = Product::search('', function ($meilisearch, string $query, array $options) {
            $filters = collect($this->queryFilters)->filter(fn ($filter) => !empty($filter))
                ->recursive()
                ->map(function ($value, $key) {
                    return $value->map(fn ($value) => $key . ' = "' . $value . '"');
                })
                ->flatten()
                ->join(' OR ');

            $options['facetsDistribution'] = ['size', 'color']; // refactor

            $options['filter'] = null;

            if ($filters) {
                $options['filter'] = $filters;
            }

            if ($this->priceRange['max']) {
                $options['filter'] .= (isset($options['filter']) ? ' AND ' : '') . 'price <= ' . $this->priceRange['max'];
            }

            return $meilisearch->search($query, $options);
        })
            ->raw();

        $products = $this->category->products->find(collect($search['hits'])->pluck('id'));

        $maxPrice = $this->category->products->max('price');

        $this->priceRange['max'] = $this->priceRange['max'] ?: $maxPrice;

        return view('livewire.product-browser', [
            'products' => $products,
            'filters' => $search['facetsDistribution'],
            'maxPrice' => $maxPrice
        ]);
    }
}
