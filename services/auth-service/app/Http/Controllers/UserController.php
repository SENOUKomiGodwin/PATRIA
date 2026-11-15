<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'service_id' => 'nullable|string',
        ]);

        $user = User::create([
            'uuid' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'service_id' => $request->service_id,
            'active' => true,
        ]);

        return response()->json($user->load('role'), 201);
    }

    public function show($uuid)
    {
        $user = User::where('uuid', $uuid)->with('role')->firstOrFail();
        return response()->json($user);
    }

    public function update(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'role_id' => 'sometimes|exists:roles,id',
            'service_id' => 'sometimes|nullable|string',
            'active' => 'sometimes|boolean',
        ]);

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->update($request->except('password'));

        return response()->json($user->load('role'));
    }

    public function destroy($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        $user->delete();
        return response()->json(null, 204);
    }
}
