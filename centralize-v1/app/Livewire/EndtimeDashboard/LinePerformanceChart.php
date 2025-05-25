<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use App\Models\ViProdEndtimeSubmitted;
use App\Models\ViCapaRef;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LinePerformanceChart extends Component
{
    public $chartId = 'linePerformanceChart';
    public $chartData = [];

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

            Log::info("LinePerformanceChart refreshData - Using date: '$date', cutoff: '$cutoff', worktype: '$worktype', lottype: '$lottype'");

            // Define the lines we want to show in the chart
            $lines = ['Line A', 'Line B', 'Line C', 'Line D', 'Line E', 'Line F', 'Line G', 'Line H', 'Line I', 'Line J', 'VMI'];
            $lineCodes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'VMI'];

            // Get target data by line from vi_capa_ref
            $targetData = $this->getTargetDataByLine($worktype, $cutoff, $lineCodes);

            // Get endtime data by line from vi_prod_endtime_submitted
            $endtimeData = $this->getEndtimeDataByLine($date, $cutoff, $worktype, $lottype, $lineCodes);

            // Get submitted data by line from vi_prod_endtime_submitted
            $submittedData = $this->getSubmittedDataByLine($date, $cutoff, $worktype, $lottype, $lineCodes);

            // Get remaining data by line from vi_prod_endtime_submitted
            $remainingData = $this->getRemainingDataByLine($date, $cutoff, $worktype, $lottype, $lineCodes);

            // Format the chart data
            $this->chartData = [
                'categories' => $lines,
                'series' => [
                    [
                        'name' => 'Target',
                        'data' => $targetData
                    ],
                    [
                        'name' => 'Endtime',
                        'data' => $endtimeData
                    ],
                    [
                        'name' => 'Submitted',
                        'data' => $submittedData
                    ],
                    [
                        'name' => 'Remaining',
                        'data' => $remainingData
                    ]
                ]
            ];

            Log::info("LinePerformanceChart data prepared", [
                'categories' => $lines,
                'targetData' => $targetData,
                'endtimeData' => $endtimeData,
                'submittedData' => $submittedData,
                'remainingData' => $remainingData
            ]);

            // Dispatch event to update the chart
            $this->dispatch('updateLinePerformanceChart', ['chartData' => $this->chartData]);
        } catch (\Exception $e) {
            Log::error("Error in LinePerformanceChart refreshData: " . $e->getMessage());
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
     * Get endtime data by line from vi_prod_endtime_submitted
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @param array $lineCodes
     * @return array
     */
    protected function getEndtimeDataByLine($date, $cutoff, $worktype, $lottype, $lineCodes)
    {
        $result = [];

        try {
            foreach ($lineCodes as $lineCode) {
                // Create base query
                $query = ViProdEndtimeSubmitted::byDate($date)
                    ->byWorkType($worktype)
                    ->byLotType($lottype)
                    ->byCutoff($cutoff);

                // Filter by line
                $query->where('line', $lineCode);

                // Sum the lot_qty column to get total endtime for this line
                $lineTotal = $query->sum('lot_qty');

                // Add to result array
                $result[] = (int)$lineTotal;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting endtime data by line: " . $e->getMessage());
            return array_fill(0, count($lineCodes), 0);
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

    /**
     * Get remaining data by line from vi_prod_endtime_submitted
     *
     * @param string $date
     * @param string $cutoff
     * @param string $worktype
     * @param string $lottype
     * @param array $lineCodes
     * @return array
     */
    protected function getRemainingDataByLine($date, $cutoff, $worktype, $lottype, $lineCodes)
    {
        $result = [];

        try {
            foreach ($lineCodes as $lineCode) {
                // Create base query
                $query = ViProdEndtimeSubmitted::byDate($date)
                    ->byWorkType($worktype)
                    ->byLotType($lottype)
                    ->byCutoff($cutoff)
                    ->byStatus('pending');

                // Filter by line
                $query->where('line', $lineCode);

                // Sum the lot_qty column to get total remaining for this line
                $lineTotal = $query->sum('lot_qty');

                // Add to result array
                $result[] = (int)$lineTotal;
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Error getting remaining data by line: " . $e->getMessage());
            return array_fill(0, count($lineCodes), 0);
        }
    }

    public function render()
    {
        return view('livewire.endtime-dashboard.line-performance-chart');
    }
}
