<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'status' => $this->status,
            'is_user_confirmed' => $this->is_user_confirmed,
            'confirmed_at' => $this->confirmed_at,
            'removes_from_portal_at' => $this->when(
                $this->is_user_confirmed && $this->confirmed_at,
                fn () => $this->confirmed_at->copy()->addHours((int) config('tickets.archive_hours_after_user_confirm', 48))->toIso8601String(),
            ),
            'created_at' => $this->created_at,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'assigned_admin' => new UserResource($this->whenLoaded('assignedAdmin')),
            'histories' => $this->histories?->map(fn ($history) => [
                'id' => $history->id,
                'status' => $history->status,
                'notes' => $history->notes,
                'changed_by' => [
                    'id' => $history->changedBy?->id,
                    'nama_karyawan' => $history->changedBy?->nama_karyawan,
                    'role' => $history->changedBy?->role,
                ],
                'created_at' => $history->created_at,
            ]),
        ];
    }
}
