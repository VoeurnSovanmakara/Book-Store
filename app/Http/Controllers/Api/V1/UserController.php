<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRoleRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;
    
    public function updateRole(UpdateUserRoleRequest $request, User $user): JsonResponse
    {
        $this->authorize('updateRole', $user); // only an Admin can promote/demote

        $user->update(['role' => $request->validated()['role']]);

        return $this->success(new UserResource($user), 'Role updated successfully');
    }
}
