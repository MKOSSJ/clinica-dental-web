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

        return response()->json([
            'status'  => true,
            'message' => 'Usuarios obtenidos correctamente',
            'data'    => $users,
        ]);
    }

    public function show(User $user)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Usuario encontrado',
            'data'    => $user,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Usuario registrado correctamente',
            'data'    => $user,
        ], 201);
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

        return response()->json([
            'status'  => true,
            'message' => 'Usuario actualizado correctamente',
            'data'    => $user,
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return response()->json([
                'status'  => false,
                'message' => 'No puedes eliminar tu propio usuario.',
                'data'    => null,
            ], 409);
        }

        $user->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Usuario eliminado correctamente',
            'data'    => null,
        ]);
    }
}