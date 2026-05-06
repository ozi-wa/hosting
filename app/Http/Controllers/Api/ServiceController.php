<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->services()->with('product')->latest()->get()]);
    }

    public function show(Request $request, Service $service): JsonResponse
    {
        abort_unless($service->user_id === $request->user()->id, 403);

        return response()->json(['data' => $service->load('product')]);
    }
}
