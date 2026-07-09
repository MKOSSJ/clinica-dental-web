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
            return $this->error(
                'Credenciales inválidas',
                401
            );
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 'Inicio de sesión correcto');
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }

        return $this->success(
            [],
            'Sesión cerrada correctamente'
        );
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        // En local se deja MAIL_MAILER=log para no requerir un servidor SMTP real.
        // En producción, cambiar a un driver real como smtp o mailgun.
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(
                [],
                'Si el correo está registrado, recibirás un enlace de recuperación.'
            );
        }

        // Por seguridad se responde lo mismo aunque el correo no exista.
        return $this->success(
            [],
            'Si el correo está registrado, recibirás un enlace de recuperación.'
        );
    }

    public function me(): JsonResponse
    {
        $user = Auth::user();

        if (! $user) {
            return $this->error(
                'No autenticado',
                401
            );
        }

        return $this->success([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ], 'Usuario autenticado');
    }
}