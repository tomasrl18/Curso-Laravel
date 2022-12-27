<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_users_list()
    {
        $this->withoutExceptionHandling();

        factory(User::class)->create([
            'name' => 'Joel'
        ]);

        factory(User::class)->create([
            'name' => 'Ellie',
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de Usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }

    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    function it_displays_the_users_details()
    {
        $user = factory(User::class)->create([
            'name' => 'Tomas'
        ]);

        $this->get('/usuarios/'.$user->id) // usuarios/5
            ->assertStatus(200)
            ->assertSee('Tomas');
    }

    /** @test */
    function it_displays_a_404_error_if_the_user_is_not_found()
    {

        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }

    /** @test */
    function it_loads_the_new_users_page()
    {
        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario');
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $this->post('/usuarios/', [
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
            'bio' => 'Programador web',
            'twitter' => 'https://twitter.com/pedrosl',
        ])->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador web',
            'twitter' => 'https://twitter.com/pedrosl',
            'user_id' => User::findByEmail('pedro@mail.com')->id,
        ]);
    }

    /** @test */
    function the_name_is_required()
    {
        $this->from('usuarios/nuevo')
          ->post('/usuarios/', [
            'name' => '',
            'email' => 'pedro@mail.com',
            'password' => '123456'
        ])->assertRedirect(route('users.create'))
          ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Pedro',
                'email' => '',
                'password' => '123456'
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email' => 'El campo email es obligatorio']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Pedro',
                'email' => 'correo-no-valido',
                'password' => '123456'
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_must_be_unique()
    {
        factory(User::class)->create([
            'email' => 'tomas@mail.com',
        ]);

        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Tomas',
                'email' => 'tomas@mail.com',
                'password' => '123456'
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Pedro',
                'email' => 'pedro@mail.com',
                'password' => ''
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password' => 'El campo password es obligatorio']);

        $this->assertEquals(0, User::count());
    }

    /** @test */
    function password_must_be_longer_than_6_characters()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Hola',
                'email' => 'hola@mail.com',
                'password' => '123',
            ])->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password']);

        $this->assertEquals(0, User::count());
    }

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

        $this->withoutExceptionHandling();

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
    function the_name_is_required_when_updating_a_user()
    {
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
    function the_email_is_required_when_updating_a_user()
    {
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
    function the_email_must_be_valid_when_updating_a_user()
    {
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
    function the_email_must_be_unique_when_updating_a_user()
    {
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
    function the_password_is_optional_when_updating_a_user()
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
        $this->withoutExceptionHandling();

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
    function the_email_can_stay_the_same_when_updating_a_user()
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

    /** @test */
    function it_deletes_a_user()
    {
        $user = factory(User::class)->create();

        $this->delete("usuarios/{$user->id}")
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}