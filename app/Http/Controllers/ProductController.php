<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Http\JsonResponse;
use App\Models\Product;

class ProductController extends Controller
{
    protected ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->productService->getAll()
        ]);
    }


    public function getByCategory($categoryId): JsonResponse
    {
        try {
            // On récupère les produits filtrés par l'ID de catégorie
            // 'with("category")' permet de récupérer aussi les infos de la catégorie
            $products = Product::where('category_id', $categoryId)
                                ->with('category') 
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->productService->getById($id)
        ]);
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $product = $this->productService->store($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    public function update(ProductRequest $request, $id): JsonResponse
    {
        $product = $this->productService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->productService->destroy($id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
