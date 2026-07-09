<?php
// app/Http/Controllers/Api/UserController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $users = $query->orderBy('name')->paginate(10);

        return $this->success(
            $users,
            'Usuarios obtenidos correctamente'
        );
    }

    public function show(User $user)
    {
        return $this->success(
            $user,
            'Usuario encontrado'
        );
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return $this->success(
            $user,
            'Usuario registrado correctamente',
            201
        );
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return $this->success(
            $user,
            'Usuario actualizado correctamente'
        );
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return $this->error(
                'No puedes eliminar tu propio usuario.',
                409
            );
        }

        $user->delete();

        return $this->success(
            null,
            'Usuario eliminado correctamente'
        );
    }
}