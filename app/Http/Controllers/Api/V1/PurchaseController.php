<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use App\Services\PurchaseService;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    use ApiResponse;

    public function __construct(private PurchaseService $purchaseService) {}

    public function index(Request $request): JsonResponse
    {
        $purchases = $request->user()
            ->purchases()
            ->with('details.book', 'address')
            ->latest()
            ->paginate(15);

        return $this->success(
            PurchaseResource::collection($purchases),
            'Purchases retrieved successfully',
        );

    }

    public function store(StorePurchaseRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->create($request->user(),$request->validated());

        return $this->success(
            new PurchaseResource($purchase),
            'Purchase created successfully',
            201,
        );
    }

    public function show(Request $request, Purchase $purchase): JsonResponse
    {
        $this->authorize('view', $purchase);

        return $this->success(
            new PurchaseResource($purchase->load('details.book', 'address')),
            'Purchase retrieved successfully'
        );
    }
}
