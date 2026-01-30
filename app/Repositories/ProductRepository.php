<?php

namespace App\Repositories;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function all()
    {
        return Product::with('category', 'sizes')->paginate(9);
    }

    public function find($id)
    {
        return Product::with('category', 'sizes')->findOrFail($id);
    }



    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $sizes = $data['sizes'] ?? null;
            unset($data['sizes']);

            $product = Product::create($data);

            if ($sizes) {
                $sizesData = [];

                foreach ($sizes as $size) {
                    $sizesData[$size['size_id']] = [
                        'stock' => $size['stock'],
                        'price' => $size['price']
                    ];
                }

                $product->sizes()->attach($sizesData);
            }

            return $product->load('sizes');
        });
    }


        public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {

            $product = $this->find($id);

            $sizes = $data['sizes'] ?? null;
            unset($data['sizes']);

            // mettre à jour fields produit (y compris price/stock si produit sans size)
            $product->update($data);

            // gérer pivot tailles si sizes envoyées
            if ($sizes && count($sizes)) {
                $sizesData = [];
                foreach ($sizes as $size) {
                    $sizesData[$size['size_id']] = [
                        'stock' => $size['stock'],
                        'price' => $size['price']
                    ];
                }
                // sync = ajoute ou met à jour
                $product->sizes()->sync($sizesData);
            }

            return $product->load('sizes');
        });
    }
    public function delete($id)
    {
        return $this->find($id)->delete();
    }
}
