<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $name = $request->input('name');
        // Menampilkan Relasi ke product
        $show_product = $request->input('show_product');

        // Spesifik Data
        if ($id) 
        {
            $category = ProductCategory::with(['products'])->find($id);

            if ($category)
            {
                return ResponseFormatter::success(
                    $category, 
                    'Data Kategori Berhasil Diambil'
                );
            } else {
                return ResponseFormatter::error($category, 
                    'Data Kategori Tidak Ada', 
                    404
                );
            }
        }

        // Filter
        $category = ProductCategory::with(['products']);
        if ($name) 
        {
            $category->where('name', 'like', '%' . $name . '%');
        }
        
        // Menampilkan data
        return ResponseFormatter::success(
            $category->paginate($limit), 
            'Data Berhasil Diambil'
        );
    }
}
