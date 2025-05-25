<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViQtyClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qtyClasses = [
            [
                'chip_size' => '03',
                'lot_qty' => 1000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chip_size' => '05',
                'lot_qty' => 800000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chip_size' => '10',
                'lot_qty' => 500000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chip_size' => '21',
                'lot_qty' => 300000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chip_size' => '31',
                'lot_qty' => 200000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chip_size' => '32',
                'lot_qty' => 100000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('vi_qty_class')->insert($qtyClasses);
    }
}