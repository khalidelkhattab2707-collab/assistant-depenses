<?php

use App\Enums\StatutRecu;
use App\Jobs\ExtraireDepensesDuRecu;
use App\Models\Depense;
use App\Models\Recu;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('redirects unauthenticated users to login', function () {
    $this->get('/recus')->assertRedirect('/login');
    $this->get('/recus/create')->assertRedirect('/login');
    $this->post('/recus')->assertRedirect('/login');
});

it('dispatches extraction job on receipt creation', function () {
    Queue::fake();

    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/recus', ['text_brut' => 'Pain x2 12dh, Lait x1 8dh'])
        ->assertRedirect('/recus')
        ->assertSessionHas('success');

    $this->assertDatabaseHas('recus', [
        'user_id' => $user->id,
        'status' => StatutRecu::EnAttente->value,
    ]);

    Queue::assertPushed(ExtraireDepensesDuRecu::class);
});

it('validates required text_brut field', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/recus', ['text_brut' => ''])
        ->assertSessionHasErrors('text_brut');
});

it('shows paginated list of own receipts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Recu::factory()->count(3)->create(['user_id' => $user->id]);
    Recu::factory()->count(2)->create(['user_id' => $otherUser->id]);

    $this->actingAs($user)
        ->get('/recus')
        ->assertOk()
        ->assertViewHas('recus', function ($paginator) {
            return $paginator->count() === 3;
        });
});

it('shows a single receipt with depenses', function () {
    $user = User::factory()->create();
    $recu = Recu::factory()->create(['user_id' => $user->id]);
    $depense = Depense::factory()->create(['recu_id' => $recu->id]);

    $this->actingAs($user)
        ->get('/recus/' . $recu->id)
        ->assertOk()
        ->assertSee($recu->text_brut);
});

it('denies viewing another users receipt', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $recu = Recu::factory()->create(['user_id' => $other->id]);

    $this->actingAs($user)
        ->get('/recus/' . $recu->id)
        ->assertForbidden();
});

it('allows deleting own receipt with cascade', function () {
    $user = User::factory()->create();
    $recu = Recu::factory()->has(Depense::factory()->count(2))->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete('/recus/' . $recu->id)
        ->assertRedirect('/recus')
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('recus', ['id' => $recu->id]);
    $this->assertDatabaseMissing('depenses', ['recu_id' => $recu->id]);
});

it('denies deleting another users receipt', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $recu = Recu::factory()->create(['user_id' => $other->id]);

    $this->actingAs($user)
        ->delete('/recus/' . $recu->id)
        ->assertForbidden();
});
