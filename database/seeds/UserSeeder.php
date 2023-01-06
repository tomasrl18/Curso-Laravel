<?php

use App\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Profession;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $professionId = Profession::where('title', 'Desarrollador back-end')->value('id');

        $user = factory(User::class)->create([
            'name' => 'Tomás Raigal',
            'email' => 'tomas@mail.com',
            'password' => bcrypt('123'),
            'role' => 'admin',
        ]);

        $user->profile()->create([
            'bio' => 'Programador, profesor, editor, escritor, social media manager',
            'profession_id' => $professionId,
        ]);

        factory(User::class, 29)->create()->each(function ($user) {
            $user->profile()->create(factory(UserProfile::class)->raw());
        });
    }
}
