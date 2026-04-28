<?php

namespace App\Models;

use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'status',
        'created_by',
        'assigned_admin_id',
        'is_user_confirmed',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_user_confirmed' => 'boolean',
            'confirmed_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class);
    }

    public function scopeVisibleInSupportPortals(Builder $query): void
    {
        $hours = (int) config('tickets.archive_hours_after_user_confirm', 48);
        $cutoff = now()->subHours($hours);

        $query->where(function (Builder $q) use ($cutoff) {
            $q->where('is_user_confirmed', false)
                ->orWhereNull('confirmed_at')
                ->orWhere('confirmed_at', '>', $cutoff);
        });
    }

    public function isArchivedFromPortals(): bool
    {
        if (! $this->is_user_confirmed || $this->confirmed_at === null) {
            return false;
        }

        $hours = (int) config('tickets.archive_hours_after_user_confirm', 48);

        return $this->confirmed_at->lte(now()->subHours($hours));
    }
}
