<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i=0;$i<3;$i++) :
            DB::table('jenis_project')->insert([
                'nama' =>  "Jenis Project $i",
                'status' => true,
                'created_at' => Carbon::now()
            ]);
        endfor;
    }
}
