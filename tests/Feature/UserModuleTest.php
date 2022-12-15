<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModuleTest extends TestCase
{
    /** @test */
    function it_shows_the_users_list_page() {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de Usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }

    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios?empty')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    function it_loads_the_user_detail_page() {
        $this->get('/usuarios/1')
            ->assertStatus(200)
            ->assertSee('Mostrando detalles del usuario: 1');
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
