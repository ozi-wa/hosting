<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use App\Services\Whmcs\WhmcsApiException;
use App\Services\Whmcs\WhmcsGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request, WhmcsGateway $whmcs): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'company_name' => ['nullable', 'string', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $clientId = $whmcs->enabled() ? $whmcs->registerClient($data) : null;

        $user = User::create($data + [
            'whmcs_client_id' => $clientId,
            'email_verified_at' => $whmcs->enabled() ? now() : null,
        ]);

        return response()->json($this->tokenResponse($user), 201);
    }

    public function login(Request $request, WhmcsGateway $whmcs): JsonResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);

        if ($whmcs->enabled()) {
            try {
                $clientId = $whmcs->validateLogin($credentials['email'], $credentials['password']);
            } catch (WhmcsApiException) {
                return response()->json(['message' => 'Giriş bilgileri hatalı.'], 422);
            }

            $user = User::updateOrCreate(
                ['email' => $credentials['email']],
                [
                    'name' => $credentials['email'],
                    'password' => $credentials['password'],
                    'role' => 'client',
                    'status' => 'active',
                    'whmcs_client_id' => $clientId,
                    'email_verified_at' => now(),
                ],
            );

            return response()->json($this->tokenResponse($user));
        }

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
            ApiToken::where('token', hash('sha256', $bearer))->delete();
        }

        return response()->json(['message' => 'Çıkış yapıldı.']);
    }

    private function tokenResponse(User $user): array
    {
        $plain = Str::random(64);

        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'api',
            'token' => hash('sha256', $plain),
            'abilities' => ['*'],
            'expires_at' => now()->addDays(30),
        ]);

        return ['token_type' => 'Bearer', 'access_token' => $plain, 'user' => $user];
    }
}
