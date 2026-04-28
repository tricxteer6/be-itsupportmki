<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()->latest()->paginate(10);

        return $this->successResponse([
            'items' => UserResource::collection($users->items()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::query()->create([
            ...$request->safe()->except('password'),
            'password' => Hash::make($request->validated('password')),
        ]);

        return $this->successResponse(new UserResource($user), 'User created.', 201);
    }

    public function show(User $user): JsonResponse
    {
        return $this->successResponse(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $payload = $request->safe()->except('password');
        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->validated('password'));
        }

        $user->update($payload);
        return $this->successResponse(new UserResource($user), 'User updated.');
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return $this->successResponse(null, 'User deleted.');
    }
}
