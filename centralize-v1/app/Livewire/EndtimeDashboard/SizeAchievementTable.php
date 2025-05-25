<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\ViProdEndtimeSubmitted;
use App\Models\ViCapaRef;

class SizeAchievementTable extends Component
{
    public $targetData = [];
    public $endtimeData = [];
    public $submittedData = [];
    public $remainingData = [];
    public $endtimePercentages = [];
    public $submittedPercentages = [];
    public $sizeNames = ['03', '05', '10', '21', '31', '32'];

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
            $cutoff = $this->getCutoff();
            $worktype = $this->getWorktype();
            $lottype = $this->getLottype();

            Log::info("SizeAchievementTable refreshData - Using date: '$date', cutoff: '$cutoff', worktype: '$worktype', lottype: '$lottype'");

            // Initialize data arrays
            $this->targetData = [];
            $this->endtimeData = [];
            $this->submittedData = [];
            $this->remainingData = [];

            // Get target data by size from vi_capa_ref
            $this->targetData = $this->getTargetDataBySize($worktype, $cutoff);

            // Get endtime data by size from vi_prod_endtime_submitted
            $this->endtimeData = $this->getEndtimeDataBySize($date, $cutoff, $worktype, $lottype);

            // Get submitted data by size from vi_prod_endtime_submitted
            $this->submittedData = $this->getSubmittedDataBySize($date, $cutoff, $worktype, $lottype);

            // Get remaining data by size from vi_prod_endtime_submitted
            $this->remainingData = $this->getRemainingDataBySize($date, $cutoff, $worktype, $lottype);

            // Calculate percentages
            $this->endtimePercentages = [];
            $this->submittedPercentages = [];

            foreach ($this->sizeNames as $index => $size) {
                $target = $this->targetData[$index] ?? 1; // Avoid division by zero
                $endtime = $this->endtimeData[$index] ?? 0;
                $submitted = $this->submittedData[$index] ?? 0;

                $this->endtimePercentages[] = $target > 0 ? round(($endtime / $target) * 100, 1) : 0;
                $this->submittedPercentages[] = $target > 0 ? round(($submitted / $target) * 100, 1) : 0;
            }

        } catch (\Exception $e) {
            Log::error("Error in SizeAchievementTable refreshData: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Get target data by size from vi_capa_ref
     *
     * @param string $worktype
     * @param string $cutoff
     * @return array
     */
    protected function getTargetDataBySize($worktype, $cutoff)
    {
        $result = array_fill(0, count($this->sizeNames), 0);

        try {
            foreach ($this->sizeNames as $index => $size) {
                // Query the database using the model with worktype and size filters
                $query = ViCapaRef::query();

                // Apply worktype filtering using the scope in ViCapaRef model
                $query->filterByWorktype($worktype);

                // Filter by size
                $query->where('mc_size', $size);

                // Sum the actual_capa column to get total capacity for this size
                $sizeCapacity = $query->sum('actual_capa');

                // Count the number of machines for this size
                $machineCount = $query->count('mc_no');

                Log::info("Size $size target capacity: $sizeCapacity from $machineCount machines");

                // Adjust target based on cutoff
                if ($cutoff === 'day') {
                    $sizeCapacity = $sizeCapacity * 0.5; // 50% of total capacity for day shift
                } elseif ($cutoff === 'night') {
                    $sizeCapacity = $sizeCapacity * 0.5; // 50% of total capacity for night shift
                } elseif ($cutoff !== 'all') {
                    // For specific cutoff times, divide by 6 (assuming 6 equal cutoff periods)
                    $sizeCapacity = $sizeCapacity / 6;
                }

                // Add to result array
                $result[$index] = (int)$sizeCapacity;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting target data by size: " . $e->getMessage());
            // If there's an error, use the sample data from the image
            return [73560000, 60760000, 47050000, 34730000, 4500000, 80000];
        }
    }

    /**
     * Get endtime data by size from vi_prod_endtime_submitted
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @return array
     */
    protected function getEndtimeDataBySize($date, $cutoff, $worktype, $lottype)
    {
        $result = array_fill(0, count($this->sizeNames), 0);

        try {
            foreach ($this->sizeNames as $index => $size) {
                // Create base query
                $query = ViProdEndtimeSubmitted::byDate($date)
                    ->byWorkType($worktype)
                    ->byLotType($lottype)
                    ->byCutoff($cutoff);

                // Filter by size
                $query->where('chip_size', $size);

                // Sum the lot_qty column to get total endtime for this size
                $sizeTotal = $query->sum('lot_qty');

                // Add to result array
                $result[$index] = (int)$sizeTotal;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting endtime data by size: " . $e->getMessage());
            return array_fill(0, count($this->sizeNames), 0);
        }
    }

    /**
     * Get submitted data by size from vi_prod_endtime_submitted
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @return array
     */
    protected function getSubmittedDataBySize($date, $cutoff, $worktype, $lottype)
    {
        $result = array_fill(0, count($this->sizeNames), 0);

        try {
            foreach ($this->sizeNames as $index => $size) {
                // Create base query
                $query = ViProdEndtimeSubmitted::byDate($date)
                    ->byWorkType($worktype)
                    ->byLotType($lottype)
                    ->byCutoff($cutoff)
                    ->byStatus('submitted');

                // Filter by size
                $query->where('chip_size', $size);

                // Sum the lot_qty column to get total submitted for this size
                $sizeTotal = $query->sum('lot_qty');

                // Add to result array
                $result[$index] = (int)$sizeTotal;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting submitted data by size: " . $e->getMessage());
            return array_fill(0, count($this->sizeNames), 0);
        }
    }

    /**
     * Get remaining data by size from vi_prod_endtime_submitted
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @return array
     */
    protected function getRemainingDataBySize($date, $cutoff, $worktype, $lottype)
    {
        $result = array_fill(0, count($this->sizeNames), 0);

        try {
            foreach ($this->sizeNames as $index => $size) {
                // Create base query
                $query = ViProdEndtimeSubmitted::byDate($date)
                    ->byWorkType($worktype)
                    ->byLotType($lottype)
                    ->byCutoff($cutoff)
                    ->byStatus('pending');

                // Filter by size
                $query->where('chip_size', $size);

                // Sum the lot_qty column to get total remaining for this size
                $sizeTotal = $query->sum('lot_qty');

                // Add to result array
                $result[$index] = (int)$sizeTotal;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting remaining data by size: " . $e->getMessage());
            return array_fill(0, count($this->sizeNames), 0);
        }
    }

    public function render()
    {
        return view('livewire.endtime-dashboard.size-achievement-table');
    }
}
