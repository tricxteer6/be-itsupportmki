<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        return view('admin.users.index', [
            'users' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['role']),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_karyawan' => ['required', 'string', 'max:50', 'unique:users,id_karyawan'],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'divisi' => ['required', 'string', 'max:100'],
            'posisi_jabatan' => ['required', 'string', 'max:100'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'no_telp' => ['required', 'string', 'max:20', 'unique:users,no_telp'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::query()->create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'id_karyawan' => ['required', 'string', 'max:50', Rule::unique('users', 'id_karyawan')->ignore($user->id)],
            'nama_karyawan' => ['required', 'string', 'max:255'],
            'divisi' => ['required', 'string', 'max:100'],
            'posisi_jabatan' => ['required', 'string', 'max:100'],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_telp' => ['required', 'string', 'max:20', Rule::unique('users', 'no_telp')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('success', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
