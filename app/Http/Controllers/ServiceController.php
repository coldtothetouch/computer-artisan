<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Service::query()->with('category')->get();
        //dd($services);
        $categories = ServiceCategory::all();

        return Inertia::render('Admin/Services', compact(['services', 'categories']));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'service_category_id' => 'nullable|integer|exists:service_categories,id',
            'price' => 'required|integer'
        ]);

        return response([
            'service' => Service::query()->create($data)->load('category'),
        ]);
    }

    public function destroy(Service $service)
    {
        $service->delete();
    }
}
