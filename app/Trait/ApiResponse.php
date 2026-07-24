<?php

namespace App\Trait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
   protected function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        $payload = ['status' => 'success', 'message' => $message];

        if ($data instanceof ResourceCollection
            && $data->resource instanceof LengthAwarePaginator) {
            $resolved = $data->response()->getData(true);
            $payload['data'] = $resolved['data'];
            $payload['pagination'] = [
                'per_page' => $resolved['meta']['per_page'],
                'total'    => $resolved['meta']['total'],
            ];
        } else {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    protected function successNoContent(string $message = 'Deleted successfully'): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 200);
        // note: using 200 with a message instead of 204,
        // since 204 forbids a response body by HTTP spec (see explanation below)
    }
}
