<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use App\Models\User;
use App\Notifications\TicketActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Ticket::query()
            ->visibleInSupportPortals()
            ->with(['creator', 'assignedAdmin', 'histories.changedBy'])
            ->latest();

        if ($request->user()->role === 'user') {
            $query->where('created_by', $request->user()->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }
        if ($request->filled('user_id') && $request->user()->role === 'admin') {
            $query->where('created_by', $request->integer('user_id'));
        }

        return $this->successResponse(TicketResource::collection($query->get()));
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = Ticket::query()->create([
            ...$request->validated(),
            'created_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        TicketStatusHistory::query()->create([
            'ticket_id' => $ticket->id,
            'status' => 'pending',
            'changed_by' => $request->user()->id,
            'notes' => 'Ticket created',
        ]);

        User::query()
            ->where('role', 'admin')
            ->get()
            ->each(fn (User $admin) => $admin->notify(new TicketActivityNotification([
                'title' => 'New ticket created',
                'body' => sprintf('Permintaan baru dari %s', $request->user()->nama_karyawan),
                'ticket_id' => $ticket->id,
                'requester_name' => $request->user()->nama_karyawan,
                'status' => 'pending',
            ])));

        $request->user()->notify(new TicketActivityNotification([
            'title' => 'Request anda berhasil di buat',
            'body' => 'Permintaan dukungan Anda telah diterima dan akan ditinjau tim IT.',
            'ticket_id' => $ticket->id,
            'status' => 'pending',
            'type' => 'ticket_created',
        ]));

        return $this->successResponse(new TicketResource($ticket->load(['creator', 'assignedAdmin', 'histories.changedBy'])), 'Ticket created.', 201);
    }

    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        if ($request->user()->role === 'user' && $ticket->created_by !== $request->user()->id) {
            return $this->errorResponse('You do not have access to this ticket.', 403);
        }

        if ($ticket->isArchivedFromPortals()) {
            return $this->errorResponse('Ticket not found.', 404);
        }

        return $this->successResponse(new TicketResource($ticket->load(['creator', 'assignedAdmin', 'histories.changedBy'])));
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->isArchivedFromPortals()) {
            return $this->errorResponse('Ticket not found.', 404);
        }

        $current = $ticket->status;
        $next = $request->validated('status');
        $allowed = [
            'pending' => ['accepted'],
            'accepted' => ['in_progress'],
            'in_progress' => ['finished'],
            'finished' => [],
        ];

        if (! in_array($next, $allowed[$current], true)) {
            throw ValidationException::withMessages([
                'status' => ["Invalid status transition from {$current} to {$next}."],
            ]);
        }

        $ticket->update([
            'status' => $next,
            'assigned_admin_id' => $request->validated('assigned_admin_id') ?: $ticket->assigned_admin_id ?: $request->user()->id,
        ]);

        TicketStatusHistory::query()->create([
            'ticket_id' => $ticket->id,
            'status' => $next,
            'changed_by' => $request->user()->id,
            'notes' => $request->validated('notes'),
        ]);

        $ticket->creator->notify(new TicketActivityNotification([
            'title' => 'Status tiket diperbarui',
            'body' => sprintf('Tiket #%d: %s', $ticket->id, self::userFacingStatusLabel($next)),
            'ticket_id' => $ticket->id,
            'status' => $next,
            'type' => 'ticket_status_changed',
        ]));

        return $this->successResponse(new TicketResource($ticket->fresh()->load(['creator', 'assignedAdmin', 'histories.changedBy'])), 'Ticket status updated.');
    }

    public function confirmFinished(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->isArchivedFromPortals()) {
            return $this->errorResponse('Ticket not found.', 404);
        }

        if ($ticket->created_by !== $request->user()->id) {
            return $this->errorResponse('You do not have access to this ticket.', 403);
        }
        if ($ticket->status !== 'finished') {
            return $this->errorResponse('Ticket is not finished yet.', 422);
        }

        $ticket->update([
            'is_user_confirmed' => true,
            'confirmed_at' => now(),
        ]);

        if ($ticket->assignedAdmin) {
            $ticket->assignedAdmin->notify(new TicketActivityNotification([
                'title' => 'Ticket completion confirmed by user',
                'ticket_id' => $ticket->id,
                'status' => 'finished',
            ]));
        }

        return $this->successResponse(new TicketResource($ticket->fresh()->load(['creator', 'assignedAdmin', 'histories.changedBy'])), 'Ticket completion confirmed.');
    }

    private static function userFacingStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'menunggu tinjauan',
            'accepted' => 'diterima',
            'in_progress' => 'sedang dikerjakan',
            'finished' => 'selesai',
            default => str_replace('_', ' ', $status),
        };
    }
}
