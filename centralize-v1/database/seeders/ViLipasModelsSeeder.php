<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViLipasModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            [
                'no' => '1',
                'model_id' => 'CL03A225MQ3ODRB',
                'lipas_yn' => 'Y',
                'ham_yn' => 'N',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no' => '2',
                'model_id' => 'CL05A475MQ5NRNB',
                'lipas_yn' => 'N',
                'ham_yn' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no' => '3',
                'model_id' => 'CL10A105KA8N8NB',
                'lipas_yn' => 'Y',
                'ham_yn' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no' => '4',
                'model_id' => 'CL21A106KAYNNNB',
                'lipas_yn' => 'Y',
                'ham_yn' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no' => '5',
                'model_id' => 'CL31A107MQHNNNB',
                'lipas_yn' => 'Y',
                'ham_yn' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'no' => '6',
                'model_id' => 'CL32B475KBUYNYB',
                'lipas_yn' => 'Y',
                'ham_yn' => 'Y',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('vi_lipas_models')->insert($models);
    }
}