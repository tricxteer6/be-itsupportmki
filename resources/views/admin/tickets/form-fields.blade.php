<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium text-slate-700">Title</label>
    <input type="text" name="title" value="{{ old('title', isset($ticket) ? $ticket->title : '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
    <textarea name="description" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>{{ old('description', isset($ticket) ? $ticket->description : '') }}</textarea>
    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Category</label>
    <select name="category" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
        @foreach (['software', 'hardware'] as $category)
            <option value="{{ $category }}" @selected(old('category', isset($ticket) ? $ticket->category : 'software') === $category)>{{ $category }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Employee</label>
    <select name="created_by" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
        @foreach ($employees as $employee)
            <option value="{{ $employee->id }}" @selected((string) old('created_by', isset($ticket) ? $ticket->created_by : '') === (string) $employee->id)>{{ $employee->nama_karyawan }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Assigned Admin</label>
    <select name="assigned_admin_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">- Unassigned -</option>
        @foreach ($admins as $admin)
            <option value="{{ $admin->id }}" @selected((string) old('assigned_admin_id', isset($ticket) ? $ticket->assigned_admin_id : '') === (string) $admin->id)>{{ $admin->nama_karyawan }}</option>
        @endforeach
    </select>
</div>

<div class="md:col-span-2">
    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
        <input type="checkbox" name="is_user_confirmed" value="1" @checked((bool) old('is_user_confirmed', isset($ticket) ? $ticket->is_user_confirmed : false))>
        Mark as confirmed by user
    </label>
</div>
