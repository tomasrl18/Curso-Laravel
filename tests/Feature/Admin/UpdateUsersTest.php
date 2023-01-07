<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUsersTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Pedro',
        'email' => 'pedro@mail.com',
        'password' => '123456',
        'profession_id' => '',
        'bio' => 'Programador web',
        'twitter' => 'https://twitter.com/pedrosl',
        'role' => 'user',
    ];

    /** @test */
    function it_loads_the_edit_user_page ()
    {
        $user = factory(User::class)->create();

        $this->get("/usuarios/{$user->id}/editar")
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editar usuario')
            ->assertViewHas('user', function ($viewUser) use ($user) {
                return $viewUser->id == $user->id;
            });
    }

    /** @test */
    function it_edits_a_user()
    {
        $user = factory(User::class)->create();

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456'
        ])->assertRedirect("/usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
        ]);
    }

    /** @test */
    function the_name_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => '',
                'email' => 'pedro@mail.com',
                'password' => '123456'
            ])->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email' => 'pedro@mail.com']);
    }

    /** @test */
    function the_email_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Pedro Sanchez',
                'email' => '',
                'password' => '123456'
            ])->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name' => 'Pedro Sanchez']);
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Pedro',
                'email' => 'correo-no-valido',
                'password' => '123456'
            ])->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseMissing('users', ['name' => 'Pedro']);
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email' => 'existing-email@example.com'
        ]);

        $user = factory(User::class)->create([
            'email' => 'tomas@mail.com',
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Tomas',
                'email' => 'existing-email@example.com',
                'password' => '123456'
            ])->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);

        //$this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_optional()
    {
        $oldPassword = 'clave_anterior';

        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword),
        ]);

        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}/", [
                'name' => 'Pedro',
                'email' => 'pedro@mail.com',
                'password' => ''
            ])->assertRedirect("usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => $oldPassword,
        ]);
    }

    /** @test */
    /*
    function password_must_be_longer_than_6_characters_when_updating_a_user()
    {
        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}/", [
                'name' => 'Hola',
                'email' => 'hola@mail.com',
                'password' => '123',
            ])->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['password']);

        $this->assertEquals(0, User::count());
    }*/

    /** @test */
    function the_email_can_stay_the_same()
    {
        $user = factory(User::class)->create([
            'email' => 'pedro@mail.com',
        ]);

        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}/", [
                'name' => 'Pedro Sanchez',
                'email' => 'pedro@mail.com',
                'password' => '123456789'
            ])->assertRedirect("usuarios/{$user->id}");

        $this->assertDatabaseHas('users', [
            'name' => 'Pedro Sanchez',
            'email' => 'pedro@mail.com',
        ]);
    }
}
