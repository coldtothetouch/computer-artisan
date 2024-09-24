<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string'
        ]);

        return response([
            'category' => ServiceCategory::query()->create($data)
        ]);
    }

    public function update(Request $request, ServiceCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);

        $category->update($data);

        return response([
            'category' => $category,
        ]);
    }

    public function destroy(ServiceCategory $category)
    {
        $category->delete();
    }
}
