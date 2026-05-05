<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GetUsersRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserCreation;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    public function getToken(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        }

        $token = $user->createToken('API')->plainTextToken;

        return response()->json([
            'token' => $token,
            'type' => 'Bearer'
        ], 201);
    }

    public function createUser(CreateUserRequest $request): JsonResponse
    {
        $auth = Auth::user();

        if ($auth->role === 'manager' && $request->filled('role') && in_array($request->role, ['administrator', 'manager'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to create user with selected role',
            ], 422);
        }

        $newUser = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'role' => $request->filled('role') ? $request->role : UserRoles::User->value,
        ]);

        $newUser->notify(new UserCreation($newUser));

        $administrators = User::where('role', UserRoles::Administrator->value)->get();
        Notification::send($administrators, new UserCreation($newUser));

        return (new UserResource($newUser))->response()->setStatusCode(201);
    }

    public function getUsers(GetUsersRequest $request): JsonResponse
    {
        $users = User::query()
            ->where('active', true);

        if ($request->filled('search')) {
            $keyword = $request->search;

            $users->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $users->orderBy($request->input('sortBy', 'created_at'));
        $users = $users->withCount('orders')->paginate(10);

        return UserResource::collection($users);
    }
}
