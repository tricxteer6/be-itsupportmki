<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Employee ID</label>
    <input type="text" name="id_karyawan" value="{{ old('id_karyawan', isset($user) ? $user->id_karyawan : '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
    @error('id_karyawan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Employee Name</label>
    <input type="text" name="nama_karyawan" value="{{ old('nama_karyawan', isset($user) ? $user->nama_karyawan : '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
    @error('nama_karyawan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Division</label>
    <input type="text" name="divisi" value="{{ old('divisi', isset($user) ? $user->divisi : '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
    @error('divisi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Position</label>
    <input type="text" name="posisi_jabatan" value="{{ old('posisi_jabatan', isset($user) ? $user->posisi_jabatan : '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
    @error('posisi_jabatan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
    <select name="role" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
        @foreach (['admin', 'user'] as $role)
            <option value="{{ $role }}" @selected(old('role', isset($user) ? $user->role : 'user') === $role)>{{ $role }}</option>
        @endforeach
    </select>
    @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">Email</label>
    <input type="email" name="email" value="{{ old('email', isset($user) ? $user->email : '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div>
    <label class="mb-1 block text-sm font-medium text-slate-700">No. Telp</label>
    <input type="text" name="no_telp" value="{{ old('no_telp', isset($user) ? $user->no_telp : '') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required>
    @error('no_telp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>

<div class="md:col-span-2">
    <label class="mb-1 block text-sm font-medium text-slate-700">Password {{ isset($user) ? '(leave blank to keep current)' : '' }}</label>
    <input type="password" name="password" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" {{ isset($user) ? '' : 'required' }}>
    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
</div>
