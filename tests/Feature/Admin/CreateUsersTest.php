<?php

namespace Tests\Feature\Admin;

use App\{Profession, Skill, User};
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUsersTest extends TestCase
{
    use RefreshDatabase;

    protected $defaultData = [
        'name' => 'Pedro',
        'email' => 'pedro@mail.com',
        'password' => '123456',
        'bio' => 'Programador web',
        'profession_id' => '',
        'twitter' => 'https://twitter.com/pedrosl',
        'role' => 'user',
    ];

    /** @test */
    function it_loads_the_new_users_page()
    {
        $profession = factory(Profession::class)->create();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario');
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $profession = factory(Profession::class)->create();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $skillC = factory(Skill::class)->create();

        $this->from('/usuarios/nuevo')
            ->post('/usuarios/', $this->withData([
                'skills' => [$skillA->id, $skillB->id],
                'profession_id' => $profession->id,
            ]))->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
            'role' => 'user',
        ]);

        $user = User::findByEmail('pedro@mail.com');

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador web',
            'twitter' => 'https://twitter.com/pedrosl',
            'user_id' => $user->id,
            'profession_id' => $profession->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id,
        ]);

        $this->assertDatabaseMissing('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillC->id,
        ]);
    }

    /** @test */
    function the_user_is_redirected_to_the_previous_page_when_the_validation_fails()
    {
        $this->handleValidationExceptions();

        $this->from("usuarios/nuevo")
            ->post("usuarios/", [])
            ->assertRedirect("usuarios/nuevo");

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_name_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'name' => '',
            ]))
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'email' => '',
            ]))
            ->assertSessionHasErrors(['email' => 'El campo email es obligatorio']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'email' => 'correo-no-valido',
            ]))
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_email_must_be_unique()
    {
        $this->handleValidationExceptions();

        factory(User::class)->create([
            'email' => 'tomas@mail.com',
        ]);

        $this->post('/usuarios/', $this->withData([
                'email' => 'tomas@mail.com',
            ]))
            ->assertSessionHasErrors(['email']);

        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'password' => ''
            ]))
            ->assertSessionHasErrors(['password' => 'El campo password es obligatorio']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_password_must_be_longer_than_6_characters()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', [
                'name' => 'Hola',
                'email' => 'hola@mail.com',
                'password' => '123',
            ])
            ->assertSessionHasErrors(['password']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_twitter_field_is_optional()
    {
        $this->post('/usuarios/', $this->withData([
            'twitter' => null,
        ]));

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
        $this->post('/usuarios/', $this->withData([
            'profession_id' => null,
        ]));

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

    /** @test */
    function the_profession_id_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'profession_id' => '999',
            ]))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function only_the_deleted_professions_can_be_selected()
    {
        $this->handleValidationExceptions();

        $deletedProfession = factory(Profession::class)->create([
            'deleted_at' => now()->format('Y-m-d'),
        ]);

        $this->post('/usuarios/', $this->withData([
                'profession_id' => $deletedProfession->id,
            ]))
            ->assertSessionHasErrors(['profession_id']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_an_array()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
                'skills' => 'PHP, JS',
            ]))
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_valid()
    {
        $this->handleValidationExceptions();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->post('/usuarios/', $this->withData([
                'skills' => [$skillA->id, $skillB->id + 1],
            ]))
            ->assertSessionHasErrors(['skills']);

        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_role_is_optional()
    {
        $this->post('/usuarios/', $this->withData([
            'role' => null,
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'pedro@mail.com',
            'role' => 'user',
        ]);
    }

    /** @test */
    function the_role_must_be_valid()
    {
        $this->handleValidationExceptions();

        $this->post('/usuarios/', $this->withData([
            'role' => 'invalid-role',
        ]));

        $this->assertDatabaseEmpty('users');
    }
}
