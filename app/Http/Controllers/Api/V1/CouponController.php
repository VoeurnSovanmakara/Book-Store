<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Services\CouponService;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    use ApiResponse;

    public function __construct(private CouponService $couponService) { }

    public function index(): JsonResponse
    {
        $coupons = $this->couponService->list();
        return $this->success(
            CouponResource::collection($coupons), 
            'Coupons retrieved successfully'
        );
    }

    public function store(StoreCouponRequest $request): JsonResponse
    {
        $coupon = $this->couponService->create($request->validated());
        return $this->success(
            new CouponResource($coupon), 
            'Coupon created successfully',
            201,
        );
    }

    public function show(Coupon $coupon): JsonResponse
    {
        return $this->success(
            new CouponResource($coupon), 
            'Coupon retrieved successfully'
        );
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): JsonResponse
    {
        $coupon = $this->couponService->update($coupon, $request->validated());
        return $this->success(
            new CouponResource($coupon),
            'Coupon updated successfully'
        );
        
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $this->couponService->delete($coupon);
        return $this->successNoContent('Coupon deleted successfully');
    }
}
