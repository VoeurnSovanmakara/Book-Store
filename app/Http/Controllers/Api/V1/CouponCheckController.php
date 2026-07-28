<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponCheckController extends Controller
{
    use ApiResponse;

    public function __construct(private CouponService $couponService) {}

    public function check(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $result = $this->couponService->validate($request->input('code'));
        
        return $this->success($result, $result['valid'] ? 'Coupon is valid' : 'Coupon is invalid');
    }
}
