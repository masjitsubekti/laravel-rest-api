<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            AppConfigSeeder::class,
            ClientSeeder::class,
            JenisProjectSeeder::class,
            LayananSeeder::class,
        ]);
    }
}
