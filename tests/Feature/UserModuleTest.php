<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Table;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_users_list_page() {
        factory(User::class)->create([
            'name' => 'Joel',
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
    function it_displays_the_users_details() {
        $user = factory(User::class)->create([
            'name' => 'Tomas Raigal',
        ]);

        $this->get('/usuarios/' . $user->id)
            ->assertStatus(200)
            ->assertSee('Tomas Raigal');
    }

    /** @test */
    function it_displays_a_404_error_if_the_user_is_not_found() {
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }

    /** @test */
    function it_loads_the_new_user_page() {
        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Creando nuevo usuario');
    }

    /** @test */
    function it_loads_the_user_edit_page() {
        $this->get('usuarios/1/edit')
            ->assertStatus(200)
            ->assertSee('Editando usuario: 1');
    }
}
