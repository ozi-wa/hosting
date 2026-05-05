<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $user = User::create($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
        ]));

        return response()->json($this->tokenResponse($user), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);

        if (! Auth::validate($credentials)) {
            return response()->json(['message' => 'Giriş bilgileri hatalı.'], 422);
        }

        return response()->json($this->tokenResponse(User::where('email', $credentials['email'])->firstOrFail()));
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()]);
    }

    public function logout(Request $request): JsonResponse
    {
        $bearer = $request->bearerToken();

        if ($bearer) {
            ApiToken::all()->first(fn (ApiToken $token): bool => Hash::check($bearer, $token->token))?->delete();
        }

        return response()->json(['message' => 'Çıkış yapıldı.']);
    }

    private function tokenResponse(User $user): array
    {
        $plain = Str::random(64);

        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'api',
            'token' => Hash::make($plain),
            'abilities' => ['*'],
            'expires_at' => now()->addDays(30),
        ]);

        return ['token_type' => 'Bearer', 'access_token' => $plain, 'user' => $user];
    }
}
