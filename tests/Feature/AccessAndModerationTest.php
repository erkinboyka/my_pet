<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessAndModerationTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, array $attrs = []): User
    {
        return User::create(array_merge([
            'name' => 'Пользователь',
            'email' => uniqid($role).'@test.local',
            'password' => 'password',
            'role' => $role,
            'is_active' => true,
        ], $attrs));
    }

    private function auction(User $author, array $attrs = []): Auction
    {
        return Auction::create(array_merge([
            'title' => 'Лот для проверки доступа',
            'description' => 'Описание лота.',
            'starting_price' => 500,
            'minimum_bid_step' => 50,
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(4),
            'status' => 'draft',
            'created_by' => $author->id,
            'moderator_id' => $author->role === 'moderator' ? $author->id : null,
        ], $attrs));
    }

    public function test_guest_is_redirected_from_admin_area(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/moderator')->assertRedirect('/login');
    }

    public function test_client_cannot_open_admin_or_moderator_areas(): void
    {
        $client = $this->user('client');

        $this->actingAs($client)->get('/admin')->assertForbidden();
        $this->actingAs($client)->get('/moderator')->assertForbidden();
        $this->actingAs($client)->get('/admin/users')->assertForbidden();
    }

    public function test_moderator_cannot_open_admin_area(): void
    {
        $moderator = $this->user('moderator');

        $this->actingAs($moderator)->get('/admin')->assertForbidden();
        $this->actingAs($moderator)->get('/moderator')->assertOk();
    }

    public function test_admin_can_open_admin_area(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_moderator_sees_only_own_lots(): void
    {
        $moderatorA = $this->user('moderator', ['email' => 'mod-a@test.local']);
        $moderatorB = $this->user('moderator', ['email' => 'mod-b@test.local']);

        $lotB = $this->auction($moderatorB, ['title' => 'Чужой лот модератора Б']);

        // Свой лот модератор правит, чужой — нет
        $this->actingAs($moderatorA)
            ->get(route('moderator.auctions.edit', $lotB))
            ->assertForbidden();

        $lotA = $this->auction($moderatorA, ['title' => 'Свой лот модератора А']);

        $this->actingAs($moderatorA)
            ->get(route('moderator.auctions.edit', $lotA))
            ->assertOk();

        $this->actingAs($moderatorA)
            ->get(route('moderator.auctions.index'))
            ->assertOk()
            ->assertDontSee($lotB->title);
    }

    public function test_moderator_cannot_approve_lots(): void
    {
        $admin = $this->user('admin');
        $moderator = $this->user('moderator');

        $pending = $this->auction($moderator, [
            'status' => 'pending_moderation',
            'moderator_id' => $moderator->id,
        ]);

        $this->actingAs($moderator)
            ->post(route('admin.auctions.approve', $pending))
            ->assertForbidden();

        $this->actingAs($admin)
            ->post(route('admin.auctions.approve', $pending))
            ->assertSessionHas('success');

        $this->assertSame('scheduled', $pending->fresh()->status);
    }

    public function test_registration_always_creates_client(): void
    {
        $this->post('/register', [
            'name' => 'Новый Участник',
            'email' => 'newuser@test.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree' => '1',
        ])->assertRedirect('/auctions');

        $user = User::where('email', 'newuser@test.local')->first();
        $this->assertNotNull($user);
        $this->assertSame('client', $user->role);
        $this->assertTrue($user->is_active);
    }

    public function test_moderator_cannot_set_role_during_registration(): void
    {
        $this->post('/register', [
            'name' => 'Хитрый Пользователь',
            'email' => 'sly@test.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree' => '1',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'sly@test.local')->first();
        $this->assertSame('client', $user->role);
    }
}
