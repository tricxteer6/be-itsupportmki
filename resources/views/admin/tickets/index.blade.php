@extends('admin.layouts.app')

@section('title', 'Ticket Management')

@section('content')
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold">Tickets</h2>
        <div class="flex items-center gap-2">
            <a href="{{ url()->full() }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="ri-refresh-line mr-1"></i> Reload Permintaan
            </a>
            <a href="{{ route('admin.tickets.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white">
                <i class="ri-add-circle-line mr-1"></i> Create Ticket
            </a>
        </div>
    </div>

    <section id="ticketSummaryCards" class="mb-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @include('admin.tickets.partials.summary', ['summary' => $summary])
    </section>

    <form method="GET" class="mb-4 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 md:grid-cols-4">
        <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Status</option>
            @foreach (['pending', 'accepted', 'in_progress', 'finished'] as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str_replace('_', ' ', $status) }}</option>
            @endforeach
        </select>
        <select name="category" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Category</option>
            <option value="software" @selected(($filters['category'] ?? '') === 'software')>software</option>
            <option value="hardware" @selected(($filters['category'] ?? '') === 'hardware')>hardware</option>
        </select>
        <select name="created_by" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">All Users</option>
            @foreach ($employees as $employee)
                <option value="{{ $employee->id }}" @selected((string) ($filters['created_by'] ?? '') === (string) $employee->id)>{{ $employee->nama_karyawan }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white">Apply Filter</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Status Ticket</th>
                    <th class="px-4 py-3">Status (Action)</th>
                    <th class="px-4 py-3">Employee</th>
                    <th class="px-4 py-3">Assigned Admin</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="ticketRows">
                @include('admin.tickets.partials.rows', ['tickets' => $tickets, 'admins' => $admins])
            </tbody>
        </table>
    </div>

    <div id="ticketPagination" class="mt-4">{{ $tickets->links() }}</div>
    <p id="ticketRealtimeInfo" class="mt-2 text-xs text-slate-500">Realtime update aktif (setiap 10 detik).</p>
@endsection

@push('scripts')
    <script>
        (() => {
            const summaryEl = document.getElementById('ticketSummaryCards');
            const rowsEl = document.getElementById('ticketRows');
            const paginationEl = document.getElementById('ticketPagination');
            const realtimeInfoEl = document.getElementById('ticketRealtimeInfo');
            if (!summaryEl || !rowsEl || !paginationEl) return;

            const realtimeUrl = "{{ route('admin.tickets.realtime') }}";
            const params = window.location.search;
            const endpoint = `${realtimeUrl}${params ? params : ''}`;

            const refreshTickets = async () => {
                try {
                    const response = await fetch(endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!response.ok) return;
                    const data = await response.json();
                    summaryEl.innerHTML = data.summary_html ?? '';
                    rowsEl.innerHTML = data.rows_html ?? '';
                    paginationEl.innerHTML = data.pagination_html ?? '';
                    if (realtimeInfoEl && data.updated_at) {
                        realtimeInfoEl.textContent = `Realtime update aktif (10 detik). Terakhir sinkron: ${data.updated_at}`;
                    }
                } catch (_) {
                    // ignore intermittent network errors
                }
            };

            setInterval(refreshTickets, 10000);
        })();
    </script>
@endpush
