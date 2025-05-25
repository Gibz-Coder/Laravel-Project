<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EndtimeDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Define cutoff times
        $cutoffTimes = [
            '00:00~04:00',
            '04:00~07:00',
            '07:00~12:00',
            '12:00~16:00',
            '16:00~19:00',
            '19:00~00:00'
        ];

        // Define work types
        $workTypes = [
            'Normal',
            'Process Rework',
            'Warehouse',
            'Outgoing NG'
        ];

        // Define lot types
        $lotTypes = [
            'MAIN',
            'RL',
            'LY',
            'ADV'
        ];

        // Define lines
        $lines = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'VMI'];

        // Define chip sizes
        $chipSizes = ['03', '05', '10', '21', '31', '32'];

        // Define statuses (most will be endtime, some will be submitted)
        $statuses = ['Endtime', 'Submitted'];

        // Generate 500 records
        $records = [];

        for ($i = 0; $i < 500; $i++) {
            $lotQty = $faker->numberBetween(5000, 50000);
            $status = $faker->randomElement($statuses);
            $workType = $faker->randomElement($workTypes);
            $lotType = $faker->randomElement($lotTypes);
            $line = $faker->randomElement($lines);
            $chipSize = $faker->randomElement($chipSizes);
            $cutoffTime = $faker->randomElement($cutoffTimes);

            // Generate a date within the last 7 days
            $date = Carbon::now()->subDays($faker->numberBetween(0, 7))->format('Y-m-d');

            $records[] = [
                'lot_id' => 'LOT' . $faker->unique()->numberBetween(10000, 99999),
                'model_id' => 'MODEL' . $faker->numberBetween(1000, 9999),
                'lot_qty' => $lotQty,
                'qty_class' => $faker->randomElement(['A', 'B', 'C']),
                'chip_size' => $chipSize,
                'work_type' => $workType,
                'lot_type' => $lotType,
                'mc_no' => 'MC' . $faker->numberBetween(100, 999),
                'line' => $line,
                'area' => $faker->randomElement(['AREA1', 'AREA2', 'AREA3']),
                'mc_type' => $faker->randomElement(['TYPE1', 'TYPE2', 'TYPE3']),
                'inspection_type' => $faker->randomElement(['INSP1', 'INSP2']),
                'lipas_yn' => $faker->randomElement(['Y', 'N']),
                'ham_yn' => $faker->randomElement(['Y', 'N']),
                'status' => $status,
                'week_no' => $this->calculateWeekNumber($date),
                'endtime_date' => $date,
                'cutoff_time' => $cutoffTime,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Insert records in chunks to avoid memory issues
        foreach (array_chunk($records, 50) as $chunk) {
            DB::table('vi_prod_endtime_submitted')->insert($chunk);
        }
    }

    /**
     * Calculate ISO week number to match Excel's WEEKNUM(date, 14) function
     *
     * @param string $date Date in Y-m-d format
     * @return int Week number
     */
    private function calculateWeekNumber($date)
    {
        try {
            // Create a DateTime object from the input date
            $dateObj = new \DateTime($date);

            // Use PHP's built-in ISO week calculation but add 1 to match Excel's WEEKNUM
            // Excel's WEEKNUM(date, 14) is ISO-8601 compliant but with a different week numbering
            $weekNumber = (int)$dateObj->format('W') + 1;

            // Ensure the week number is between 1 and 53
            if ($weekNumber > 53) {
                $weekNumber = 1;
            }

            return $weekNumber;
        } catch (\Exception $e) {
            // Return current week number as fallback
            return (int)date('W');
        }
    }
}
