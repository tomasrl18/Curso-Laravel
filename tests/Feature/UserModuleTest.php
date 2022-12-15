<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModuleTest extends TestCase
{
    /** @test */
    function it_loads_the_users_list_page()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Usuarios');
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
