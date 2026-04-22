<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Service::orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100', 'unique:services,name'],
            'price'     => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        return response()->json(Service::create($data), 201);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['sometimes', 'string', 'max:100', 'unique:services,name,' . $service->id],
            'price'     => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $service->update($data);

        return response()->json($service->fresh());
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(null, 204);
    }
}
