<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TicketPortalArchiveTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_confirmed_tickets_older_than_archive_window_are_hidden_from_api_index(): void
    {
        config(['tickets.archive_hours_after_user_confirm' => 48]);

        $user = User::factory()->create(['role' => 'user']);

        Ticket::query()->create([
            'title' => 'Old done',
            'description' => 'Long enough description text.',
            'category' => 'software',
            'status' => 'finished',
            'created_by' => $user->id,
            'assigned_admin_id' => null,
            'is_user_confirmed' => true,
            'confirmed_at' => now()->subHours(49),
        ]);

        Ticket::query()->create([
            'title' => 'Recent done',
            'description' => 'Long enough description text.',
            'category' => 'software',
            'status' => 'finished',
            'created_by' => $user->id,
            'assigned_admin_id' => null,
            'is_user_confirmed' => true,
            'confirmed_at' => now()->subHours(2),
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tickets');

        $response->assertOk();
        $titles = collect($response->json('data'))->pluck('title');

        $this->assertTrue($titles->contains('Recent done'));
        $this->assertFalse($titles->contains('Old done'));
    }
}
