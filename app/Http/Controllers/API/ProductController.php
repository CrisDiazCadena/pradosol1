<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductController extends ApiController
{

    public function getProducts(Request $request)
    {
        try {

            $query = Products::query();

            if ($request->has('order_by') && $request->input('order_by') && $request->has('order_by_column')) {
                $orderBy = $request->input('order_by');
                $orderByColumn = $request->input('order_by_column');
                $query->orderBy($orderByColumn, $orderBy);
            }

            if ($request->has('search') && !$request->has('search_column')) {
                $searchTerm = $request->input('search');
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                        ->orWhere('stock', 'like', "%$searchTerm%")
                        ->orWhere('description', 'like', "%$searchTerm%")
                        ->orWhere('discount_price', 'like', "%$searchTerm%");
                });
            }

            if ($request->has('search') && $request->has('search_column')) {
                $searchTerm = $request->input('search');
                $searchColumn = $request->input('search_column');
                $query->where($searchColumn, 'like', "%$searchTerm%");
            }

            $columns = collect($request->only(['column_filter_0', 'column_filter_1']))->values();
            $filters = collect($request->only(['filter_0', 'filter_1']))->values();

            // Aplicar los filtros a la consulta
            $query->where(function ($query) use ($columns, $filters) {
                $count = min($columns->count(), $filters->count());
                for ($i = 0; $i < $count; $i++) {
                    $column = $columns[$i];
                    $filter = $filters[$i];
                    $query->where("products." . $column, '=',  $filter);
                }
            });

            $products = $query->paginate($request->pages);

            return $this->successResponse($products, 200, 'productos extraidos correctamente');
        } catch (\Exception $e) {
            // Manejo de excepciones
            return $this->errorResponse(500, 'Error al obtener los prductos: ' . $e->getMessage());
        }
    }

    public function getMyProduct($id)
    {

        try {
            $product = Products::FindOrFail($id);
            $message = "Los datos del producto se han extraido correctamente";
            return $this->successResponse($product, 200, $message);
        } catch (\Exception $e) {
            $errorMessage = 'Error al obtener el producto' . $e->getMessage();
            return $this->errorResponse(400, $errorMessage);
        }
    }
}
