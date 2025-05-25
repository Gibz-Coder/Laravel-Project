<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create vi_capa_ref table
        Schema::create('vi_capa_ref', function (Blueprint $table) {
            $table->id();
            $table->string('work_type')->nullable();
            $table->string('mc_condition')->nullable();
            $table->integer('actual_capa')->default(0);
            $table->timestamps();
        });

        // Create vi_prod_endtime_submitted table
        Schema::create('vi_prod_endtime_submitted', function (Blueprint $table) {
            $table->id();
            $table->string('lot_id')->nullable();
            $table->string('model_id')->nullable();
            $table->integer('lot_qty')->default(0);
            $table->string('qty_class')->nullable();
            $table->string('chip_size')->nullable();
            $table->string('work_type')->nullable();
            $table->string('lot_type')->nullable();
            $table->string('mc_no')->nullable();
            $table->string('line')->nullable();
            $table->string('area')->nullable();
            $table->string('mc_type')->nullable();
            $table->string('inspection_type')->nullable();
            $table->string('lipas_yn')->nullable();
            $table->string('ham_yn')->nullable();
            $table->string('status')->nullable();
            $table->integer('week_no')->nullable();
            $table->date('endtime_date')->nullable();
            $table->string('cutoff_time')->nullable();
            $table->timestamps();
        });

        // Insert sample data into vi_capa_ref
        DB::table('vi_capa_ref')->insert([
            [
                'work_type' => 'Normal',
                'mc_condition' => 'Good',
                'actual_capa' => 10000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'work_type' => 'Process Rework',
                'mc_condition' => 'Good',
                'actual_capa' => 5000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'work_type' => 'Warehouse',
                'mc_condition' => 'Good',
                'actual_capa' => 3000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert sample data into vi_prod_endtime_submitted
        $statuses = ['SUBMITTED', 'PENDING'];
        $workTypes = ['Normal', 'Process Rework', 'Warehouse'];
        $lotTypes = ['MAIN', 'RL', 'LY', 'ADV'];
        $lines = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'VMI'];
        $chipSizes = ['03', '05', '10', '21', '31', '32'];
        $cutoffTimes = ['00:00~04:00', '04:00~07:00', '07:00~12:00', '12:00~16:00', '16:00~19:00', '19:00~00:00'];

        for ($i = 1; $i <= 100; $i++) {
            DB::table('vi_prod_endtime_submitted')->insert([
                'lot_id' => 'LOT' . $i,
                'model_id' => 'MODEL' . rand(1, 10),
                'lot_qty' => rand(10000, 100000),
                'qty_class' => chr(rand(65, 67)), // A, B, or C
                'chip_size' => $chipSizes[array_rand($chipSizes)],
                'work_type' => $workTypes[array_rand($workTypes)],
                'lot_type' => $lotTypes[array_rand($lotTypes)],
                'mc_no' => 'MC' . rand(100, 999),
                'line' => $lines[array_rand($lines)],
                'area' => 'AREA' . rand(1, 3),
                'mc_type' => 'TYPE' . rand(1, 3),
                'inspection_type' => 'INSP' . rand(1, 2),
                'lipas_yn' => rand(0, 1) ? 'Y' : 'N',
                'ham_yn' => rand(0, 1) ? 'Y' : 'N',
                'status' => $statuses[array_rand($statuses)],
                'week_no' => $this->calculateWeekNumber(date('Y-m-d')),
                'endtime_date' => date('Y-m-d'),
                'cutoff_time' => $cutoffTimes[array_rand($cutoffTimes)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vi_prod_endtime_submitted');
        Schema::dropIfExists('vi_capa_ref');
    }
};
