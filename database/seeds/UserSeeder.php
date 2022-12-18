<?php

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
        //$professions = DB::select('SELECT id FROM professions WHERE title = ? LIMIT 0,1', ['Desarrollador back-end']);

//        $professionId = DB::table('professions')
//            ->whereTitle('Desarrollador back-end')
//            ->value('id');

        $professionId = Profession::whereTitle('Desarrollador back-end')
            ->value('id');

//        DB::table('users')->insert([
//            'name' => 'Tomas',
//            'email' => 'tomas@mail.com',
//            'password' => bcrypt('123'),
//            'profession_id' => $professionId,
//        ]);

        User::create([
            'name' => 'Tomas',
            'email' => 'tomas@mail.com',
            'password' => bcrypt('123'),
            'profession_id' => $professionId,
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Another User',
            'email' => 'anotheruser@gmail.com',
            'password' => bcrypt('123'),
            'profession_id' => $professionId,
        ]);

        User::create([
            'name' => 'Another User',
            'email' => 'anotheruser2@gmail.com',
            'password' => bcrypt('123'),
            'profession_id' => null,
        ]);
    }
}
