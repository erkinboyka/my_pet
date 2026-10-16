<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiddingTest extends TestCase
{
    use RefreshDatabase;

    private function client(array $attrs = []): User
    {
        return User::create(array_merge([
            'name' => 'Тест Участник',
            'email' => 'bidder@test.local',
            'password' => 'password',
            'role' => 'client',
            'is_active' => true,
        ], $attrs));
    }

    private function auction(array $attrs = []): Auction
    {
        return Auction::create(array_merge([
            'title' => 'Тестовый лот',
            'description' => 'Описание тестового лота.',
            'starting_price' => 1000,
            'minimum_bid_step' => 100,
            'start_at' => now()->subHour(),
            'end_at' => now()->addHours(3),
            'status' => 'active',
            'created_by' => $this->client(['email' => 'creator@test.local'])->id,
        ], $attrs));
    }

    public function test_guest_cannot_place_bid(): void
    {
        $auction = $this->auction();

        $this->post("/auctions/{$auction->id}/bids", ['amount' => 1000])
            ->assertRedirect('/login');

        $this->assertDatabaseCount('bids', 0);
    }

    public function test_first_bid_must_reach_starting_price(): void
    {
        $user = $this->client();
        $auction = $this->auction();

        $this->actingAs($user)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 999])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('bids', 0);
    }

    public function test_bid_below_minimum_step_is_rejected(): void
    {
        $first = $this->client(['email' => 'first@test.local']);
        $second = $this->client(['email' => 'second@test.local']);
        $auction = $this->auction(['current_price' => null]);

        $this->actingAs($first)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 1000])
            ->assertSessionHasNoErrors();

        // 1000 + шаг 100 => минимум 1100
        $this->actingAs($second)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 1099])
            ->assertSessionHasErrors('amount');

        $this->actingAs($second)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 1100])
            ->assertSessionHasNoErrors();

        $auction->refresh();
        $this->assertSame(1100.0, (float) $auction->current_price);
        $this->assertDatabaseCount('bids', 2);
    }

    public function test_bidder_cannot_raise_own_top_bid(): void
    {
        $user = $this->client();
        $auction = $this->auction();

        $this->actingAs($user)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 1000])
            ->assertSessionHasNoErrors();

        $this->actingAs($user)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 1500])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('bids', 1);
    }

    public function test_bid_on_finished_lot_is_rejected(): void
    {
        $user = $this->client(['email' => 'late@test.local']);
        $auction = $this->auction([
            'status' => 'finished',
            'end_at' => now()->subDay(),
        ]);

        $this->actingAs($user)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 5000])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('bids', 0);
    }

    public function test_blocked_user_cannot_login(): void
    {
        $user = $this->client(['email' => 'blocked@test.local', 'is_active' => false]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
