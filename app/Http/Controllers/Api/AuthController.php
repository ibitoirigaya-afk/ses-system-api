<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'string', 'min:4', 'confirmed'],
        'role' => ['required', 'in:user,company'],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $validated['role'],
    ]);

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ], 201);
}
    public function login(Request $request): JsonResponse
{
    $validated = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $user = User::where('email', $validated['email'])->first();

    if (! $user || ! Hash::check($validated['password'], $user->password)) {
        return response()->json([
            'message' => 'メールアドレスまたはパスワードが違います。',
        ], 422);
    }

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ]);
}

    public function me(Request $request): JsonResponse
{
    $userId = $request->query('user_id');

    if (! $userId) {
        return response()->json([
            'message' => 'user_id is required.',
        ], 422);
    }

    $user = User::find($userId);

    if (! $user) {
        return response()->json([
            'message' => 'ユーザーが見つかりません。',
        ], 404);
    }

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ],
    ]);
}

    public function logout(): JsonResponse
    {
        return response()->json([
            'message' => 'ログアウトしました。',
        ]);
    }
}