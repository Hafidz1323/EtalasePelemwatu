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
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        $category = Category::create($data);
        return response()->json(['status' => 'success', 'data' => $category], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|nullable|string|max:500',
        ]);
        $category->update($data);
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
