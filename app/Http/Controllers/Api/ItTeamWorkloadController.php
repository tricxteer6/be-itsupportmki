<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItTeamWorkloadController extends Controller
{
    /**
     * For employees: show which IT staff (e.g. Bagas, Yusuf) have tickets currently in progress,
     * so users can see queue visibility when their request is being handled.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = (int) $request->user()->id;

        $admins = User::query()
            ->where('role', 'admin')
            ->with([
                'assignedTickets' => fn ($q) => $q
                    ->where('status', 'in_progress')
                    ->orderByDesc('updated_at')
                    ->select(['id', 'title', 'category', 'status', 'assigned_admin_id', 'created_by', 'updated_at']),
            ])
            ->orderBy('nama_karyawan')
            ->get();

        // Show Bagas & Yusuf first when present, then other IT staff alphabetically
        $priority = ['Bagas' => 0, 'Yusuf' => 1];
        $sorted = $admins->sortBy(fn (User $u) => $priority[$u->nama_karyawan] ?? 99)->values();

        $payload = $sorted
            ->filter(function (User $admin) {
                $isPriorityName = in_array($admin->nama_karyawan, ['Bagas', 'Yusuf'], true);

                return $admin->assignedTickets->isNotEmpty() || $isPriorityName;
            })
            ->values()
            ->map(function (User $admin) use ($userId) {
                return [
                    'id' => $admin->id,
                    'nama_karyawan' => $admin->nama_karyawan,
                    'divisi' => $admin->divisi,
                    'in_progress_count' => $admin->assignedTickets->count(),
                    'in_progress_tickets' => $admin->assignedTickets->map(fn ($t) => [
                        'id' => $t->id,
                        'title' => $t->title,
                        'category' => $t->category,
                        'is_your_request' => (int) $t->created_by === $userId,
                    ]),
                ];
            });

        return $this->successResponse($payload);
    }
}
