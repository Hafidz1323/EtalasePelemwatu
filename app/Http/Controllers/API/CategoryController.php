<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Category::orderBy('id', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $category = Category::create($request->all());
        return response()->json(['status' => 'success', 'data' => $category], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        $category->update($request->all());
        return response()->json(['status' => 'success', 'data' => $category]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        $category->delete();
        return response()->json(['status' => 'success', 'message' => 'Kategori dihapus']);
    }
}

