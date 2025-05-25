<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use App\Models\ViProdEndtimeSubmitted;
use App\Models\ViCapaRef;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SubmittedPerCutoffTable extends Component
{
    public $lineData = [];
    public $cutoffNames = [];
    public $lineCodes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'VMI'];
    public $totalTarget = 0;
    public $totalCutoffs = [];

    protected $listeners = [
        'dateChanged' => 'refreshData',
        'cutoffChanged' => 'refreshData',
        'worktypeChanged' => 'refreshData',
        'lottypeChanged' => 'refreshData',
        'refreshData' => 'refreshData'
    ];

    public function mount()
    {
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
            $worktype = $this->getWorktype();
            $lottype = $this->getLottype();

            Log::info("SubmittedPerCutoffTable refreshData - Using date: '$date', worktype: '$worktype', lottype: '$lottype'");

            // Define cutoff times and their display names
            $this->cutoffNames = [
                '00:00~04:00' => '4AM',
                '04:00~07:00' => '7AM',
                '07:00~12:00' => '12NN',
                '12:00~16:00' => '4PM',
                '16:00~19:00' => '7PM',
                '19:00~00:00' => '12MN'
            ];

            // Initialize the line data structure
            $this->lineData = [];
            $this->totalTarget = 0;
            $this->totalCutoffs = [];

            // Initialize total cutoffs array
            foreach ($this->cutoffNames as $cutoffTime => $cutoffDisplay) {
                $this->totalCutoffs[$cutoffTime] = [
                    'qty' => 0,
                    'percentage' => 0
                ];
            }

            // Get target data for each line
            $targetData = $this->getTargetDataByLine($worktype, $this->lineCodes);

            // For each line, get submitted data for each cutoff
            foreach ($this->lineCodes as $index => $lineCode) {
                $lineTarget = $targetData[$index] ?? 0;
                $this->totalTarget += $lineTarget;

                $lineInfo = [
                    'line' => $lineCode,
                    'target' => $lineTarget,
                    'cutoffs' => []
                ];

                // Get submitted data for each cutoff
                foreach ($this->cutoffNames as $cutoffTime => $cutoffDisplay) {
                    $submitted = $this->getSubmittedDataForLineCutoff($date, $lineCode, $cutoffTime, $worktype, $lottype);
                    $percentage = $lineTarget > 0 ? round(($submitted / $lineTarget) * 100, 1) : 0;

                    $lineInfo['cutoffs'][$cutoffTime] = [
                        'qty' => $submitted,
                        'percentage' => $percentage
                    ];

                    // Add to totals
                    $this->totalCutoffs[$cutoffTime]['qty'] += $submitted;
                }

                $this->lineData[] = $lineInfo;
            }

            // Calculate total percentages
            foreach ($this->cutoffNames as $cutoffTime => $cutoffDisplay) {
                $this->totalCutoffs[$cutoffTime]['percentage'] =
                    $this->totalTarget > 0
                    ? round(($this->totalCutoffs[$cutoffTime]['qty'] / $this->totalTarget) * 100, 1)
                    : 0;
            }
        } catch (\Exception $e) {
            Log::error("Error in SubmittedPerCutoffTable refreshData: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Get target data by line from vi_capa_ref
     *
     * @param string $worktype
     * @param array $lineCodes
     * @return array
     */
    protected function getTargetDataByLine($worktype, $lineCodes)
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

                // For specific cutoff times, divide by 6 (assuming 6 equal cutoff periods)
                $lineCapacity = $lineCapacity / 6;

                // Add to result array
                $result[] = (int)$lineCapacity;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting target data by line: " . $e->getMessage());
            return array_fill(0, count($lineCodes), 0);
        }
    }

    /**
     * Get submitted data for a specific line and cutoff
     *
     * @param string $date
     * @param string $lineCode
     * @param string $cutoffTime
     * @param string $worktype
     * @param string $lottype
     * @return int
     */
    protected function getSubmittedDataForLineCutoff($date, $lineCode, $cutoffTime, $worktype, $lottype)
    {
        try {
            // Create base query
            $query = ViProdEndtimeSubmitted::byDate($date)
                ->byWorkType($worktype)
                ->byLotType($lottype)
                ->where('cutoff_time', $cutoffTime)
                ->where('line', $lineCode)
                ->whereRaw('LOWER(status) = ?', ['submitted']);

            // Sum the lot_qty column to get total submitted
            $total = $query->sum('lot_qty');

            return (int)$total;
        } catch (\Exception $e) {
            Log::error("Error getting submitted data for line $lineCode and cutoff $cutoffTime: " . $e->getMessage());
            return 0;
        }
    }

    public function render()
    {
        return view('livewire.endtime-dashboard.submitted-per-cutoff-table');
    }
}
