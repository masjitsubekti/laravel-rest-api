<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('app_config')->insert([
            'nama_sistem' =>  "Shipt Company Profile",
            'instansi' =>  "PT. SELARAS HANDASA INTI PERSADA",
            'alamat' =>  "Surabaya",
            'email' =>  "konstruksimep.ship@gmail.com",
            'telepon' =>  "(031)87856942",
            'fax' =>  "-",
            'url_root' =>  "https://shiptpt.com/",
            'logo' =>  "logo.png",
            'favicon' =>  "favicon.png",
            'status' =>  true,
        ]);
    }
}
