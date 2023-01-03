<?php

namespace Tests\Feature;

use App\Profession;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $profession;

    /** @test */
    function it_shows_the_users_list()
    {
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
        $profession = factory(Profession::class)->create();

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions', function ($professions) use ($profession){
                return $professions->contains($profession);
            });
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $this->from('/usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData())->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador web',
            'twitter' => 'https://twitter.com/pedrosl',
            'user_id' => User::findByEmail('pedro@mail.com')->id,
            'profession_id' => $this->profession->id,
        ]);
    }

    /** @test */
    function the_name_is_required()
    {
        $this->from('usuarios/nuevo')
          ->post('/usuarios/', $this->getValidData([
              'name' => '',
          ]))
          ->assertRedirect(route('users.create'))
          ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        $this->assertDatabaseEmpty('users');
    }


    /** @test */
    function the_email_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'email' => '',
            ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email' => 'El campo email es obligatorio']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'email' => 'correo-no-valido',
            ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_unique()
    {
        factory(User::class)->create([
            'email' => 'tomas@mail.com',
        ]);

        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'email' => 'tomas@mail.com',
            ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'password' => ''
            ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['password' => 'El campo password es obligatorio']);

        $this->assertDatabaseEmpty('users');
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

        $this->assertDatabaseEmpty('users');
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

    /** @test */
    function the_twitter_field_is_optional()
    {
        $this->post('/usuarios/', $this->getValidData([
            'twitter' => null,
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador web',
            'twitter' => null,
            'user_id' => User::findByEmail('pedro@mail.com')->id,
        ]);
    }

    /** @test */
    function the_profession_id_field_is_optional()
    {
        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => null,
        ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador web',
            'user_id' => User::findByEmail('pedro@mail.com')->id,
            'profession_id' => null,
        ]);
    }

    public function getValidData(array $custom = [])
    {
        $this->profession = factory(Profession::class)->create();

        return array_filter(array_merge([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador web',
            'twitter' => 'https://twitter.com/pedrosl',
        ], $custom));
    }

    /** @test */
    function the_profession_must_be_valid()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'profession_id' => '999',
            ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function only_the_deleted_professions_can_be_selected()
    {
        $deletedProfession = factory(Profession::class)->create([
            'deleted_at' => now()->format('Y-m-d'),
        ]);

        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'profession_id' => $deletedProfession->id,
            ]))
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }
}