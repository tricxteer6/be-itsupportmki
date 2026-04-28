@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold">Users</h2>
        <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white">Create User</a>
    </div>

    <form method="GET" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-3">
        <select name="role" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Roles</option>
            <option value="admin" @selected(($filters['role'] ?? '') === 'admin')>admin</option>
            <option value="user" @selected(($filters['role'] ?? '') === 'user')>user</option>
        </select>
        <div></div>
        <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white">Apply Filter</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3">Employee ID</th>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Division</th>
                <th class="px-4 py-3">Position</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">No. Telp</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr class="border-t border-slate-100">
                    <td class="px-4 py-3">{{ $user->id_karyawan }}</td>
                    <td class="px-4 py-3">{{ $user->nama_karyawan }}</td>
                    <td class="px-4 py-3">{{ $user->divisi }}</td>
                    <td class="px-4 py-3">{{ $user->posisi_jabatan }}</td>
                    <td class="px-4 py-3">{{ $user->role }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->no_telp }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="rounded bg-amber-500 px-2.5 py-1 text-xs font-medium text-white">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded bg-red-600 px-2.5 py-1 text-xs font-medium text-white">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-6 text-center text-slate-500">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
