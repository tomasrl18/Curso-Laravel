<?php

namespace Tests\Feature\Admin;

use App\Profession;
use App\Skill;
use App\User;
use App\UserProfile;
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

        $oldProfession = factory(Profession::class)->create();

        $user->profile()->save(factory(UserProfile::class)->make([
            'profession_id' => $oldProfession->id,
        ]));

        $oldSkill1 = factory(Skill::class)->create();
        $oldSkill2 = factory(Skill::class)->create();

        $user->skills()->attach([$oldSkill1->id, $oldSkill2->id]);

        $newProfession = factory(Profession::class)->create();

        $newSkill1 = factory(Skill::class)->create();
        $newSkill2 = factory(Skill::class)->create();

        $this->put("usuarios/{$user->id}", [
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
            'bio' => 'Programador web',
            'twitter' => 'https://twitter.com/pedrosl',
            'role' => 'admin',
            'profession_id' => $newProfession->id,
            'skills' => [$newSkill1->id, $newSkill2->id],
        ])->assertRedirect("usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
            'password' => '123456',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('user_profiles', [
           'user_id' => $user->id,
           'bio' => 'Programador web',
           'twitter' => 'https://twitter.com/pedrosl',
           'profession_id' => $newProfession->id,
        ]);

        $this->assertDatabaseCount('user_skill', 2);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $newSkill1->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $newSkill2->id,
        ]);
    }

    /** @test */
    function it_detaches_all_skills_if_none_is_checked()
    {
        $user = factory(User::class)->create();

        $oldSkill1 = factory(Skill::class)->create();
        $oldSkill2 = factory(Skill::class)->create();

        $user->skills()->attach([$oldSkill1->id, $oldSkill2->id]);

        $this->put("usuarios/{$user->id}", $this->withData([]))
            ->assertRedirect("usuarios/{$user->id}");

        $this->assertDatabaseEmpty('user_skill');
    }

    /** @test */
    function the_name_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", $this->withData([
                'name' => '',
            ]))->assertRedirect("usuarios/{$user->id}/editar")
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
            ->put("usuarios/{$user->id}", $this->withData([
                'email' => 'correo-no-valido',
            ]))->assertRedirect("usuarios/{$user->id}/editar")
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
            ->put("usuarios/{$user->id}", $this->withData([
                'email' => 'existing-email@example.com',
            ]))->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);
    }

    /** @test */
    function the_password_is_optional()
    {
        $oldPassword = 'clave_anterior';

        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword),
        ]);

        $this->from("/usuarios/{$user->id}/editar")
            ->put("/usuarios/{$user->id}/", $this->withData([
                'password' => ''
            ]))->assertRedirect("usuarios/{$user->id}");

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
        $this->handleValidationExceptions();

        $user = factory(User::class)->create([
            'email' => 'pedro@mail.com',
        ]);

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}/", $this->withData([
                'name' => 'Pedro',
                'email' => 'pedro@mail.com',
            ]))->assertRedirect("usuarios/{$user->id}");

        $this->assertDatabaseHas('users', [
            'name' => 'Pedro',
            'email' => 'pedro@mail.com',
        ]);
    }

    /** @test */
    function the_role_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", $this->withData([
                'role' => '',
            ]))->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['role']);

        $this->assertDatabaseMissing('users', ['email' => 'pedro@mail.com']);
    }

    /** @test */
    function the_bio_is_required()
    {
        $this->handleValidationExceptions();

        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", $this->withData([
                'bio' => '',
            ]))->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['bio']);

        $this->assertDatabaseMissing('users', ['email' => 'pedro@mail.com']);
    }
}
