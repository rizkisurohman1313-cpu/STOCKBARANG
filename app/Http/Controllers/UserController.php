<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = ['admin', 'manajer_stok', 'operator', 'viewer'];
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'username' => 'required|string|unique:users|max:50',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|string|min:8|confirmed',
            'nama_lengkap' => 'required|string|max:100',
            'role' => 'required|in:admin,manajer_stok,operator,viewer',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = ['admin', 'manajer_stok', 'operator', 'viewer'];
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|max:100|unique:users,email,' . $user->user_id . ',user_id',
            'nama_lengkap' => 'required|string|max:100',
            'role' => 'required|in:admin,manajer_stok,operator,viewer',
            'status' => 'required|in:aktif,nonaktif',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diubah');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}
