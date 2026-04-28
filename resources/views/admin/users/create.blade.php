@extends('admin.layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold">Create User</h2>
        <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">Back</a>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="grid gap-4 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-2">
        @csrf
        @include('admin.users.form-fields')
        <div class="md:col-span-2">
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white">Save User</button>
        </div>
    </form>
@endsection
