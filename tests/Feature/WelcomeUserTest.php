<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WelcomeUserTest extends TestCase
{
    /** @test */
    function it_welcome_users_with_nickname()
    {
        $this->get('/saludo/tomas/tomasito')
            ->assertStatus(200)
            ->assertSee('Bienvenido Tomas tu apodo es tomasito.');
    }

    function it_welcome_users_without_nickname()
    {
        $this->get('/saludo/tomas/')
            ->assertStatus(200)
            ->assertSee('Bienvenido Tomas.');
    }
}
