<?php

namespace App\Services\Implementation;

use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\Interfaces\OrderServiceInterface;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderServiceInterface
{
    protected $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAllOrders()
    {
        return $this->orderRepository->all();
    }

    public function getOrderById($id)
    {
        return $this->orderRepository->findById($id);
    }

    /**
     * Créer une commande avec gestion du stock pour produits avec/sans tailles
     */
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {

            $totalPrice = 0;

            // Calcul du total et vérification stock
            foreach ($data['items'] as $item) {
                $product = Product::with('sizes')->findOrFail($item['product_id']);

                // Produit avec tailles ?
                if (isset($item['size_id'])) {
                    $size = $product->sizes->firstWhere('id', $item['size_id']);
                    if (!$size) {
                        throw new \Exception("Size non trouvée pour le produit {$product->name}");
                    }

                    if ($size->pivot->stock < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour {$product->name} taille {$size->name}");
                    }

                    $totalPrice += $size->pivot->price * $item['quantity'];
                } else {
                    // Produit sans taille
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour {$product->name}");
                    }

                    $totalPrice += $product->price * $item['quantity'];
                }
            }

            // Créer la commande
            $order = $this->orderRepository->create([
                'user_id' => $data['user_id'],
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            // Créer les order items et décrémenter le stock
            foreach ($data['items'] as $item) {
                $product = Product::with('sizes')->findOrFail($item['product_id']);

                if (isset($item['size_id'])) {
                    $size = $product->sizes->firstWhere('id', $item['size_id']);

                    // Création de l'order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'size_id' => $size->id, 
                        'quantity' => $item['quantity'],
                        'price' => $size->pivot->price,
                    ]);

                    // Décrémenter le stock pivot
                    $product->sizes()->updateExistingPivot($size->id, [
                        'stock' => $size->pivot->stock - $item['quantity']
                    ]);
                } else {
                    // Produit sans taille
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                    ]);

                    $product->decrement('stock', $item['quantity']);
                }
            }

            return $order->load('items.product');
        });
    }

    public function updateOrder($id, array $data)
    {
        return $this->orderRepository->update($id, $data);
    }

    public function deleteOrder($id)
    {
        return $this->orderRepository->delete($id);
    }
}
