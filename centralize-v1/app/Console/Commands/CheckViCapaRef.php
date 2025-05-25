<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckViCapaRef extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:vi-capa-ref';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the vi_capa_ref table structure and data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking vi_capa_ref table...');

        // Check if the table exists
        if (!Schema::hasTable('vi_capa_ref')) {
            $this->error('Table vi_capa_ref does not exist!');
            return 1;
        }

        $this->info('Table vi_capa_ref exists.');

        // Get the columns
        $columns = Schema::getColumnListing('vi_capa_ref');
        $this->info('Columns in vi_capa_ref: ' . implode(', ', $columns));

        // Check if the required columns exist
        $requiredColumns = ['mc_no', 'actual_capa', 'mc_condition'];
        foreach ($requiredColumns as $column) {
            if (!in_array($column, $columns)) {
                $this->error("Required column '$column' does not exist in vi_capa_ref table!");
            } else {
                $this->info("Required column '$column' exists.");
            }
        }

        // Count the records
        $count = DB::table('vi_capa_ref')->count();
        $this->info("Total records in vi_capa_ref: $count");

        // Get a sample of the data
        $this->info('Sample data from vi_capa_ref:');
        $records = DB::table('vi_capa_ref')->limit(5)->get();
        foreach ($records as $record) {
            $this->info(json_encode($record));
        }

        // Check for null values in important columns
        $nullMcNo = DB::table('vi_capa_ref')->whereNull('mc_no')->count();
        $this->info("Records with null mc_no: $nullMcNo");

        $nullActualCapa = DB::table('vi_capa_ref')->whereNull('actual_capa')->count();
        $this->info("Records with null actual_capa: $nullActualCapa");

        $nullMcCondition = DB::table('vi_capa_ref')->whereNull('mc_condition')->count();
        $this->info("Records with null mc_condition: $nullMcCondition");

        // Check for the specific mc_condition values we're looking for
        $normalCount = DB::table('vi_capa_ref')->where('mc_condition', 'Normal')->count();
        $this->info("Records with mc_condition = 'Normal': $normalCount");

        $whReworkCount = DB::table('vi_capa_ref')->where('mc_condition', 'WH Rework')->count();
        $this->info("Records with mc_condition = 'WH Rework': $whReworkCount");

        $processReworkCount = DB::table('vi_capa_ref')->where('mc_condition', 'Process Rework')->count();
        $this->info("Records with mc_condition = 'Process Rework': $processReworkCount");

        $rlReworkCount = DB::table('vi_capa_ref')->where('mc_condition', 'R/L Rework')->count();
        $this->info("Records with mc_condition = 'R/L Rework': $rlReworkCount");

        // Get all distinct mc_condition values
        $this->info('All distinct mc_condition values:');
        $mcConditions = DB::table('vi_capa_ref')->select('mc_condition')->distinct()->get();
        foreach ($mcConditions as $condition) {
            $this->info($condition->mc_condition ?? 'NULL');
        }

        return 0;
    }
}
