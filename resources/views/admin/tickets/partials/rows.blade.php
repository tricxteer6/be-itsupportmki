@forelse ($tickets as $ticket)
    <tr class="border-t border-slate-100 align-top">
        <td class="px-4 py-3">{{ $ticket->title }}</td>
        <td class="px-4 py-3 capitalize">{{ $ticket->category }}</td>
        <td class="px-4 py-3">
            @if ($ticket->is_user_confirmed)
                <span class="inline-flex max-w-xs items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-800">
                    {{ str_replace('_', ' ', $ticket->status) }} · dikonfirmasi user
                </span>
            @else
                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
            @endif
        </td>
        <td class="px-4 py-3">
            @if ($ticket->is_user_confirmed)
                <span class="inline-flex rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">Selesai (dikonfirmasi user)</span>
            @else
                <form method="POST" action="{{ route('admin.tickets.quick-update', $ticket) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="assigned_admin_id" value="{{ $ticket->assigned_admin_id }}">
                    <select name="status" onchange="this.form.submit()" class="rounded border border-slate-300 px-2 py-1 text-xs">
                        @foreach (['pending', 'accepted', 'in_progress', 'finished'] as $status)
                            <option value="{{ $status }}" @selected($ticket->status === $status)>{{ str_replace('_', ' ', $status) }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </td>
        <td class="px-4 py-3">{{ $ticket->creator?->nama_karyawan }}</td>
        <td class="px-4 py-3">
            @if ($ticket->is_user_confirmed)
                <span class="text-xs text-slate-700">{{ $ticket->assignedAdmin?->nama_karyawan ?? '—' }}</span>
            @else
                <form method="POST" action="{{ route('admin.tickets.quick-update', $ticket) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $ticket->status }}">
                    <select name="assigned_admin_id" onchange="this.form.submit()" class="rounded border border-slate-300 px-2 py-1 text-xs">
                        <option value="">- Unassigned -</option>
                        @foreach ($admins as $admin)
                            <option value="{{ $admin->id }}" @selected((int) $ticket->assigned_admin_id === (int) $admin->id)>
                                {{ $admin->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </td>
        <td class="px-4 py-3">
            @if ($ticket->is_user_confirmed)
                <span class="block max-w-xs text-xs leading-5 text-slate-500">Tidak ada aksi · hilang dari daftar setelah ±{{ (int) config('tickets.archive_hours_after_user_confirm', 48) }} jam</span>
            @else
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.tickets.edit', $ticket) }}" class="rounded bg-amber-500 px-2.5 py-1 text-xs font-medium text-white">Edit</a>
                    <form method="POST" action="{{ route('admin.tickets.destroy', $ticket) }}" onsubmit="return confirm('Delete this ticket?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded bg-red-600 px-2.5 py-1 text-xs font-medium text-white">Delete</button>
                    </form>
                </div>
            @endif
        </td>
    </tr>
@empty
    <tr><td colspan="7" class="px-4 py-6 text-center text-slate-500">No tickets found.</td></tr>
@endforelse
