<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'employee_id' => '21278703',
                'employee_name' => 'Gibz Hapita',
                'date_hired' => '2012-06-04',
                'employee_knox' => 'gibo.hapita@samsung.com',
                'employee_process' => 'Visual Inspection',
                'employee_dept' => 'MLCC',
                'position' => 'Specialist, Dev',
                'gender' => 'Male',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('employees')->insert($employees);
    }
}