<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Profession;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Profession::create([
            'title' => 'Desarrollador back-end',
        ]);

        Profession::create([
            'title' => 'Desarrollador front-end',
        ]);

        Profession::create([
            'title' => 'Diseñador web',
        ]);

        factory(Profession::class, 17)->create();
    }
}
