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
        $categories = ServiceCategory::all();

        return Inertia::render('Admin/Services', compact(['services', 'categories']));
    }

    public function store(Request $request): \Illuminate\Http\Response
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

    public function update(Request $request, Service $service): \Illuminate\Http\Response
    {
        $data = $request->validate([
            'name' => 'required|string',
            'service_category_id' => 'nullable|integer|exists:service_categories,id',
            'price' => 'required|integer'
        ]);

        $service->update($data);

        return response([
            'service' => $service->load('category'),
        ]);
    }

    public function destroy(Service $service): void
    {
        $service->delete();
    }
}
