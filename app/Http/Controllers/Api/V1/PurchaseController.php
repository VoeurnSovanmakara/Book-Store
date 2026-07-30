<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Http\Requests\Purchase\UpdatePurchaseStatusRequest;
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

    public function updateStatus(UpdatePurchaseStatusRequest $request, Purchase $purchase): JsonResponse
    {
        $purchase = $this->purchaseService->updateStatus($purchase, $request->validated()['status']);

        return $this->success(
            new PurchaseResource($purchase->load('details.book', 'address')),
            'Purchase status updated successfully'
        );
    }

    public function indexAll(Request $request): JsonResponse
    {
        $purchases = Purchase::query()
            ->with('details.book', 'address', 'customer')
            ->when($request->input('status'), fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return $this->success(
            PurchaseResource::collection($purchases),
            'Purchases retrieved successfully'
        );
    }
}
