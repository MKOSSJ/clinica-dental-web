<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->jsonResponse(false, 'credenciales inválidas', [], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->jsonResponse(true, 'Inicio de sesión correcto', [
            'token' => $token,
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
        $user = Auth::user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return $this->jsonResponse(true, 'Sesión cerrada correctamente', []);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        // En local se deja MAIL_MAILER=log para no requerir un servidor SMTP real.
        // En producción, cambiar a un driver real como smtp o mailgun.
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->jsonResponse(true, 'Si el correo está registrado, recibirás un enlace de recuperación.', []);
        }

        return $this->jsonResponse(true, 'Si el correo está registrado, recibirás un enlace de recuperación.', []);
    }

    public function me(): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return $this->jsonResponse(false, 'No autenticado', [], 401);
        }

        return $this->jsonResponse(true, 'Usuario autenticado', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    private function jsonResponse(bool $status, string $message, array $data = [], int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
