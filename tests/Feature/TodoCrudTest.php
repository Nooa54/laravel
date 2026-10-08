<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_welcome_page_and_is_redirected_from_todos(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('get started');

        $this->get(route('todo.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_view_update_and_delete_an_owned_todo(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherTodo = Todo::factory()->for($otherUser)->create(['title' => 'Toisen käyttäjän tehtävä']);
        $description = '<script>alert("xss")</script>';

        $this->actingAs($user)
            ->get(route('todo.index'))
            ->assertOk()
            ->assertDontSee($otherTodo->title);

        $this->get(route('todo.create'))
            ->assertOk()
            ->assertSee('hylätään ehdotus')
            ->assertSee('tehdään seuraavaan versioon')
            ->assertSee('eilen');

        $this->post(route('todo.store'), [
            'title' => 'Korjaa kirjautumislomake',
            'description' => $description,
            'status' => 'aloitettu',
            'due_date' => '2026-10-20',
            'urgency' => 'heti',
            'user_id' => $otherUser->id,
        ])->assertRedirect(route('todo.index'));

        $todo = Todo::query()->where('title', 'Korjaa kirjautumislomake')->firstOrFail();

        $this->assertSame($user->id, $todo->user_id);
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'description' => $description,
            'status' => 'aloitettu',
            'urgency' => 'heti',
        ]);
        $this->assertSame('2026-10-20', $todo->due_date->toDateString());

        $this->get(route('todo.show', $todo))
            ->assertOk()
            ->assertSee($description)
            ->assertDontSee($description, false);

        $this->get(route('todo.edit', $todo))
            ->assertOk()
            ->assertSee('Korjaa kirjautumislomake');

        $this->put(route('todo.update', $todo), [
            'title' => 'Kirjautumislomake korjattu',
            'description' => 'Testit kattavat lomakkeen.',
            'status' => 'valmis',
            'due_date' => '2026-10-25',
            'urgency' => 'Ei kiirettä',
        ])->assertRedirect(route('todo.show', $todo));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Kirjautumislomake korjattu',
            'status' => 'valmis',
            'urgency' => 'Ei kiirettä',
        ]);

        $this->delete(route('todo.destroy', $todo))
            ->assertRedirect(route('todo.index'));

        $this->assertModelMissing($todo);
        $this->assertModelExists($otherTodo);
    }

    public function test_invalid_todo_fields_are_rejected_without_creating_a_record(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('todo.create'))
            ->post(route('todo.store'), [
                'title' => '',
                'description' => 'Kuvaus',
                'status' => 'tuntematon tila',
                'due_date' => 'ei-päivä',
                'urgency' => 'mahdoton kiireellisyys',
            ])
            ->assertRedirect(route('todo.create'))
            ->assertSessionHasErrors(['title', 'status', 'due_date', 'urgency']);

        $this->assertDatabaseEmpty('todos');
    }

    public function test_user_cannot_view_edit_update_or_delete_another_users_todo(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $todo = Todo::factory()->for($owner)->create(['title' => 'Yksityinen tehtävä']);

        $this->actingAs($otherUser);

        $this->get(route('todo.show', $todo))->assertNotFound();
        $this->get(route('todo.edit', $todo))->assertNotFound();
        $this->put(route('todo.update', $todo), [
            'title' => 'Kaapattu tehtävä',
            'description' => null,
            'status' => 'valmis',
            'due_date' => null,
            'urgency' => 'eilen',
        ])->assertNotFound();
        $this->delete(route('todo.destroy', $todo))->assertNotFound();

        $this->assertModelExists($todo);
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'Yksityinen tehtävä',
            'user_id' => $owner->id,
        ]);
    }
}
