<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use App\Models\ViProdEndtimeSubmitted;
use App\Models\ViCapaRef;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SubmittedPerLineTable extends Component
{
    public $targetData = [];
    public $submittedData = [];
    public $percentages = [];
    public $shortages = [];
    public $date;
    public $cutoff = 'total';
    public $cutoffDisplay;

    protected $listeners = [
        'dateChanged' => 'refreshData',
        'cutoffChanged' => 'refreshData',
        'worktypeChanged' => 'refreshData',
        'lottypeChanged' => 'refreshData',
        'refreshData' => 'refreshData'
    ];

    public function mount()
    {
        $this->date = now()->format('M d, Y');
        $this->refreshData();
    }

    /**
     * Get the current date from session
     *
     * @return string
     */
    protected function getDate()
    {
        return session('date', now()->format('Y-m-d'));
    }

    /**
     * Get the current cutoff from session
     *
     * @return string
     */
    protected function getCutoff()
    {
        return session('cutoff', 'all');
    }

    /**
     * Get the current worktype from session
     *
     * @return string
     */
    protected function getWorktype()
    {
        return session('worktype', 'all');
    }

    /**
     * Get the current lottype from session
     *
     * @return string
     */
    protected function getLottype()
    {
        return session('lottype', 'all');
    }

    public function refreshData()
    {
        try {
            // Get filter values from session
            $date = $this->getDate();
            $this->date = date('M d, Y', strtotime($date));
            $cutoff = $this->getCutoff();
            $worktype = $this->getWorktype();
            $lottype = $this->getLottype();

            // Set cutoff display text
            $this->setCutoffDisplay($cutoff);

            Log::info("SubmittedPerLineTable refreshData - Using date: '$date', cutoff: '$cutoff', worktype: '$worktype', lottype: '$lottype'");

            // Define the lines we want to show in the table
            $lineCodes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'VMI'];

            // Get target data by line from vi_capa_ref
            $this->targetData = $this->getTargetDataByLine($worktype, $cutoff, $lineCodes);

            // Get submitted data by line from vi_prod_endtime_submitted
            $this->submittedData = $this->getSubmittedDataByLine($date, $cutoff, $worktype, $lottype, $lineCodes);

            // Calculate percentages and shortages
            $this->percentages = [];
            $this->shortages = [];

            foreach ($lineCodes as $index => $lineCode) {
                $target = $this->targetData[$index] ?? 1; // Avoid division by zero
                $submitted = $this->submittedData[$index] ?? 0;

                $this->percentages[] = $target > 0 ? round(($submitted / $target) * 100, 1) : 0;
                $this->shortages[] = max(0, $target - $submitted);
            }

        } catch (\Exception $e) {
            Log::error("Error in SubmittedPerLineTable refreshData: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Get target data by line from vi_capa_ref
     *
     * @param string $worktype
     * @param string $cutoff
     * @param array $lineCodes
     * @return array
     */
    protected function getTargetDataByLine($worktype, $cutoff, $lineCodes)
    {
        $result = [];

        try {
            foreach ($lineCodes as $lineCode) {
                // Query the database using the model with worktype and line filters
                $query = ViCapaRef::query();

                // Apply worktype filtering using the scope in ViCapaRef model
                $query->filterByWorktype($worktype);

                // Filter by line
                $query->where('line', $lineCode);

                // Sum the actual_capa column to get total capacity for this line
                $lineCapacity = $query->sum('actual_capa');

                // Count the number of machines for this line
                $machineCount = $query->count('mc_no');

                Log::info("Line $lineCode target capacity: $lineCapacity from $machineCount machines");

                // Adjust target based on cutoff
                if ($cutoff === 'day') {
                    $lineCapacity = $lineCapacity * 0.5; // 50% of total capacity for day shift
                } elseif ($cutoff === 'night') {
                    $lineCapacity = $lineCapacity * 0.5; // 50% of total capacity for night shift
                } elseif ($cutoff !== 'all') {
                    // For specific cutoff times, divide by 6 (assuming 6 equal cutoff periods)
                    $lineCapacity = $lineCapacity / 6;
                }

                // Add to result array
                $result[] = (int)$lineCapacity;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting target data by line: " . $e->getMessage());
            return array_fill(0, count($lineCodes), 10000000); // Default target value
        }
    }

    /**
     * Get submitted data by line from vi_prod_endtime_submitted
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @param array $lineCodes
     * @return array
     */
    protected function getSubmittedDataByLine($date, $cutoff, $worktype, $lottype, $lineCodes)
    {
        $result = [];

        try {
            foreach ($lineCodes as $lineCode) {
                // Create base query
                $query = ViProdEndtimeSubmitted::byDate($date)
                    ->byWorkType($worktype)
                    ->byLotType($lottype)
                    ->byCutoff($cutoff)
                    ->byStatus('submitted');

                // Filter by line
                $query->where('line', $lineCode);

                // Sum the lot_qty column to get total submitted for this line
                $lineTotal = $query->sum('lot_qty');

                // Add to result array
                $result[] = (int)$lineTotal;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting submitted data by line: " . $e->getMessage());
            return array_fill(0, count($lineCodes), 0);
        }
    }

    public function setCutoff($value)
    {
        $this->cutoff = $value;
        $this->refreshData();
    }

    /**
     * Set the cutoff display text based on the cutoff value
     *
     * @param string $cutoff
     * @return void
     */
    protected function setCutoffDisplay($cutoff)
    {
        switch ($cutoff) {
            case 'total':
                $this->cutoffDisplay = 'Total';
                break;
            case 'day':
                $this->cutoffDisplay = 'Day Shift';
                break;
            case 'night':
                $this->cutoffDisplay = 'Night Shift';
                break;
            case '00:00~04:00':
                $this->cutoffDisplay = '4AM Cutoff';
                break;
            case '04:00~07:00':
                $this->cutoffDisplay = '7AM Cutoff';
                break;
            case '07:00~12:00':
                $this->cutoffDisplay = '12NN Cutoff';
                break;
            case '12:00~16:00':
                $this->cutoffDisplay = '4PM Cutoff';
                break;
            case '16:00~19:00':
                $this->cutoffDisplay = '7PM Cutoff';
                break;
            case '19:00~00:00':
                $this->cutoffDisplay = '12MN Cutoff';
                break;
            default:
                $this->cutoffDisplay = 'All Cutoffs';
                break;
        }
    }

    public function render()
    {
        return view('livewire.endtime-dashboard.submitted-per-line-table');
    }
}
