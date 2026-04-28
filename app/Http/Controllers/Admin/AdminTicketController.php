<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use App\Notifications\TicketActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTicketController extends Controller
{
    private const TICKET_STATUSES = ['pending', 'accepted', 'in_progress', 'finished'];

    public function index(Request $request): View
    {
        [$query, $summary] = $this->buildTicketListing($request);

        return view('admin.tickets.index', [
            'tickets' => $query->paginate(10)->withQueryString(),
            'summary' => $summary,
            'employees' => User::query()->where('role', 'user')->orderBy('nama_karyawan')->get(),
            'admins' => User::query()->where('role', 'admin')->orderBy('nama_karyawan')->get(),
            'filters' => $request->only(['status', 'category', 'created_by']),
        ]);
    }

    public function realtime(Request $request): JsonResponse
    {
        [$query, $summary] = $this->buildTicketListing($request);
        $tickets = $query->paginate(10)->withQueryString();
        $admins = User::query()->where('role', 'admin')->orderBy('nama_karyawan')->get();

        return response()->json([
            'summary_html' => view('admin.tickets.partials.summary', ['summary' => $summary])->render(),
            'rows_html' => view('admin.tickets.partials.rows', ['tickets' => $tickets, 'admins' => $admins])->render(),
            'pagination_html' => $tickets->links()->toHtml(),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.tickets.create', [
            'employees' => User::query()->where('role', 'user')->orderBy('nama_karyawan')->get(),
            'admins' => User::query()->where('role', 'admin')->orderBy('nama_karyawan')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'category' => ['required', Rule::in(['software', 'hardware'])],
            'created_by' => ['required', 'exists:users,id'],
            'assigned_admin_id' => ['nullable', 'exists:users,id'],
            'is_user_confirmed' => ['nullable', 'boolean'],
        ]);

        $ticket = Ticket::query()->create([
            ...$validated,
            'status' => 'pending',
            'is_user_confirmed' => (bool) ($validated['is_user_confirmed'] ?? false),
            'confirmed_at' => ($validated['is_user_confirmed'] ?? false) ? now() : null,
        ]);

        TicketStatusHistory::query()->create([
            'ticket_id' => $ticket->id,
            'status' => $ticket->status,
            'changed_by' => $request->user()->id,
            'notes' => 'Created from admin dashboard',
        ]);

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket created successfully.');
    }

    public function edit(Ticket $ticket): View
    {
        if ($ticket->isArchivedFromPortals()) {
            abort(404);
        }

        return view('admin.tickets.edit', [
            'ticket' => $ticket,
            'employees' => User::query()->where('role', 'user')->orderBy('nama_karyawan')->get(),
            'admins' => User::query()->where('role', 'admin')->orderBy('nama_karyawan')->get(),
        ]);
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->isArchivedFromPortals()) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'category' => ['required', Rule::in(['software', 'hardware'])],
            'created_by' => ['required', 'exists:users,id'],
            'assigned_admin_id' => ['nullable', 'exists:users,id'],
            'is_user_confirmed' => ['nullable', 'boolean'],
        ]);

        $ticket->update([
            ...$validated,
            'is_user_confirmed' => (bool) ($validated['is_user_confirmed'] ?? false),
            'confirmed_at' => ($validated['is_user_confirmed'] ?? false) ? ($ticket->confirmed_at ?? now()) : null,
        ]);

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        if ($ticket->isArchivedFromPortals()) {
            abort(404);
        }

        $ticket->delete();

        return redirect()->route('admin.tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    public function quickUpdate(Request $request, Ticket $ticket): RedirectResponse
    {
        if ($ticket->isArchivedFromPortals()) {
            abort(404);
        }

        if ($ticket->is_user_confirmed) {
            return back()->with('error', 'Tiket telah dikonfirmasi karyawan; ubah status atau penugasan tidak tersedia dari daftar.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(self::TICKET_STATUSES)],
            'assigned_admin_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'admin')),
            ],
        ]);

        $statusChanged = $ticket->status !== $validated['status'];

        $ticket->update([
            'status' => $validated['status'],
            'assigned_admin_id' => $validated['assigned_admin_id'] ?? null,
        ]);

        if ($statusChanged) {
            TicketStatusHistory::query()->create([
                'ticket_id' => $ticket->id,
                'status' => $ticket->status,
                'changed_by' => $request->user()->id,
                'notes' => 'Status updated from ticket list dropdown',
            ]);

            $ticket->loadMissing('creator');
            if ($ticket->creator) {
                $ticket->creator->notify(new TicketActivityNotification([
                    'title' => 'Status tiket diperbarui',
                    'body' => sprintf(
                        'Tiket #%d: %s',
                        $ticket->id,
                        match ($ticket->status) {
                            'pending' => 'menunggu tinjauan',
                            'accepted' => 'diterima',
                            'in_progress' => 'sedang dikerjakan',
                            'finished' => 'selesai',
                            default => str_replace('_', ' ', $ticket->status),
                        },
                    ),
                    'ticket_id' => $ticket->id,
                    'status' => $ticket->status,
                    'type' => 'ticket_status_changed',
                ]));
            }
        }

        return back()->with('success', 'Ticket assignment and status updated.');
    }

    private function buildTicketListing(Request $request): array
    {
        $summaryQuery = Ticket::query()->visibleInSupportPortals();

        $query = Ticket::query()
            ->visibleInSupportPortals()
            ->with(['creator', 'assignedAdmin'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->integer('created_by'));
        }

        return [
            $query,
            [
                'pending' => (clone $summaryQuery)->where('status', 'pending')->count(),
                'accepted' => (clone $summaryQuery)->where('status', 'accepted')->count(),
                'in_progress' => (clone $summaryQuery)->where('status', 'in_progress')->count(),
                'finished' => (clone $summaryQuery)->where('status', 'finished')->count(),
            ],
        ];
    }
}
