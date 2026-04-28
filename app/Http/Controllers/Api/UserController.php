<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Enums\UserRoles;
use App\Enums\UserSort;
use Illuminate\Validation\Rules\Enum;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserCreation;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    public function getToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

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

    public function createUser(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', Password::min(8)],
            'name' => 'required',
            'role' => new Enum(UserRoles::class)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $auth = Auth::user();
        if ($auth->role === 'manager' && $request->has('role') && in_array($request->role, ['administrator','manager'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to create user with selected role'
            ], 422);
        }

        $newUser = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'role' => $request->has('role') && !empty($request->role) ? $request->role : 'user',
        ]);

        $newUser->notify(new UserCreation($newUser));

        // Send notification to all administrators
        $administrators = User::where('role', 'administrator')->get();
        Notification::send($administrators, new UserCreation($newUser));

        return response()->json($newUser, 201);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $users = User::where('active', true);

        if ($request->has('search')) {
            $keyword = $request->search;

            $users = $users->where('name','like', "%{$keyword}%")
                ->orWhere('name','like', "%{$keyword}%");
        }

        if ($request->has('sortBy')) {
            $validator = Validator::make($request->all(), [
                'sortBy' => new Enum(UserSort::class),
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $users = $users->orderBy($request->sortBy);
        } else {
            $users = $users->orderBy('created_at');
        }

        $users = $users->paginate(10);

        return response()->json($users, 200);
    }
}
