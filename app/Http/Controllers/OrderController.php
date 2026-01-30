<?php
namespace App\Http\Controllers;

use App\Services\Interfaces\OrderServiceInterface;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        return response()->json($this->orderService->getAllOrders(), 200);
    }

    public function show($id)
    {
        return response()->json($this->orderService->getOrderById($id), 200);
    }

   public function store(Request $request)
{
    $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.size_id' => 'nullable|exists:sizes,id',
    ]);

    // Récupérer l'utilisateur connecté
    $user = $request->user(); // ou auth()->user()

    // Ajouter l'id de l'utilisateur aux données
    $data = $request->all();
    $data['user_id'] = $user->id;

    // Créer la commande
    return response()->json($this->orderService->createOrder($data), 201);
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,delivered,cancelled',
        ]);

        return response()->json($this->orderService->updateOrder($id, $request->all()), 200);
    }

    public function destroy($id)
    {
        $this->orderService->deleteOrder($id);
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}
