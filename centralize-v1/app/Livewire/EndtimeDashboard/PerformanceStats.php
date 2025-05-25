<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use App\Models\ViProdEndtimeSubmitted;
use App\Services\EndtimeDashboardService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class PerformanceStats extends Component
{
    public $stats = [];
    public $selectedDate;
    public $displayDate;

    protected $listeners = [
        'dateChanged' => 'handleDateChanged',
        'cutoffChanged' => 'handleCutoffChanged',
        'worktypeChanged' => 'handleWorktypeChanged',
        'lottypeChanged' => 'handleLottypeChanged',
        'refreshData' => 'refreshData'
    ];

    /**
     * Initialize the component
     */
    public function mount()
    {
        \Log::info("PerformanceStats mount - Initializing component");
        $this->refreshData();
    }

    /**
     * Called after the component is hydrated but before it's rendered
     */
    public function hydrate()
    {
        \Log::info("PerformanceStats hydrate - Component hydrated after page refresh");
        $this->refreshData();
    }

    /**
     * Handle date change event
     */
    public function handleDateChanged($data)
    {
        try {
            \Log::info("PerformanceStats received dateChanged event", ['data' => json_encode($data)]);

            // Extract date from event data
            $date = null;
            if (is_array($data) && isset($data['date'])) {
                $date = $data['date'];
            } elseif (is_object($data) && isset($data->date)) {
                $date = $data->date;
            }

            if ($date) {
                \Log::info("PerformanceStats updating date to: {$date}");
                // Store the date in session for persistence
                session(['date' => $date]);
            }

            // Refresh data
            $this->refreshData();
        } catch (\Exception $e) {
            \Log::error("Error handling dateChanged event: " . $e->getMessage());
            $this->refreshData();
        }
    }

    /**
     * Handle worktype change event
     */
    public function handleWorktypeChanged($data)
    {
        try {
            \Log::info("PerformanceStats received worktypeChanged event", ['data' => json_encode($data)]);

            // Extract worktype from event data
            $worktype = null;
            if (is_array($data) && isset($data['worktype'])) {
                $worktype = $data['worktype'];
            } elseif (is_object($data) && isset($data->worktype)) {
                $worktype = $data->worktype;
            }

            if ($worktype) {
                \Log::info("PerformanceStats updating worktype to: {$worktype}");
                // Store the worktype in session for persistence
                session(['worktype' => $worktype]);
            }

            // Refresh data
            $this->refreshData();
        } catch (\Exception $e) {
            \Log::error("Error handling worktypeChanged event: " . $e->getMessage());
            $this->refreshData();
        }
    }

    /**
     * Handle cutoff change event
     */
    public function handleCutoffChanged($data)
    {
        try {
            \Log::info("PerformanceStats received cutoffChanged event", ['data' => json_encode($data)]);

            // We don't use cutoff for performance stats, but we need to handle the event
            // No need to refresh data as cutoff doesn't affect performance stats

            // Log that we're ignoring this event for performance stats
            \Log::info("PerformanceStats ignoring cutoff change as it doesn't affect whole day stats");
        } catch (\Exception $e) {
            \Log::error("Error handling cutoffChanged event: " . $e->getMessage());
        }
    }

    /**
     * Handle lottype change event
     */
    public function handleLottypeChanged($data)
    {
        try {
            \Log::info("PerformanceStats received lottypeChanged event", ['data' => json_encode($data)]);

            // Extract lottype from event data
            $lottype = null;
            if (is_array($data) && isset($data['lottype'])) {
                $lottype = $data['lottype'];
            } elseif (is_object($data) && isset($data->lottype)) {
                $lottype = $data->lottype;
            }

            if ($lottype) {
                \Log::info("PerformanceStats updating lottype to: {$lottype}");
                // Store the lottype in session for persistence
                session(['lottype' => $lottype]);
            }

            // Refresh data
            $this->refreshData();
        } catch (\Exception $e) {
            \Log::error("Error handling lottypeChanged event: " . $e->getMessage());
            $this->refreshData();
        }
    }

    /**
     * Refresh data based on current filters
     */
    public function refreshData()
    {
        try {
            // Get current filter values from session
            $date = $this->getDate();
            $worktype = $this->getWorktype();
            $lottype = $this->getLottype();

            // Set the selected date for display
            $this->selectedDate = $date;
            $this->displayDate = date('M j, Y', strtotime($date));

            \Log::info("PerformanceStats refreshData - Using date: '$date', worktype: '$worktype', lottype: '$lottype'");

            // Get target data (for the whole day, not affected by cutoff)
            $targetData = EndtimeDashboardService::getTargetCapacity($date, 'all', $worktype, $lottype);
            $targetValue = $targetData['total'];

            // Get endtime data (both pending and submitted)
            $endtimeData = EndtimeDashboardService::getEndtimeData($date, 'all', $worktype, $lottype);
            $endtimeValue = $endtimeData['total'];
            // Endtime percentage = endtime / target
            $endtimePercentage = $targetValue > 0 ? round(($endtimeValue / $targetValue) * 100, 1) : 0;

            // Get submitted data
            $submittedData = EndtimeDashboardService::getSubmittedData($date, 'all', $worktype, $lottype);
            $submittedValue = $submittedData['total'];
            // Submitted percentage = submitted / endtime
            $submittedPercentage = $endtimeValue > 0 ? round(($submittedValue / $targetValue) * 100, 1) : 0;

            // Get remaining data
            $remainingData = EndtimeDashboardService::getRemainingData($date, 'all', $worktype, $lottype);
            $remainingValue = $remainingData['total'];
            // Remaining percentage = remaining / endtime
            $remainingPercentage = $endtimeValue > 0 ? round(($remainingValue / $endtimeValue) * 100, 1) : 0;

            // Build the stats array
            $this->stats = [
                [
                    'name' => 'Target',
                    'value' => $targetValue,
                    'percentage' => 100,
                    'color' => 'info'
                ],
                [
                    'name' => 'Endtime',
                    'value' => $endtimeValue,
                    'percentage' => $endtimePercentage,
                    'color' => 'primary'
                ],
                [
                    'name' => 'Submitted',
                    'value' => $submittedValue,
                    'percentage' => $submittedPercentage,
                    'color' => 'success'
                ],
                [
                    'name' => 'Remaining',
                    'value' => $remainingValue,
                    'percentage' => $remainingPercentage,
                    'color' => 'danger'
                ]
            ];

            \Log::info("PerformanceStats data updated", [
                'target' => $targetValue,
                'endtime' => $endtimeValue,
                'submitted' => $submittedValue,
                'remaining' => $remainingValue
            ]);
        } catch (\Exception $e) {
            \Log::error("Error in PerformanceStats refreshData: " . $e->getMessage());
            // Set default values in case of error
            $this->stats = [
                [
                    'name' => 'Target',
                    'value' => 0,
                    'percentage' => 100, // Target is always 100%
                    'color' => 'secondary'
                ],
                [
                    'name' => 'Endtime',
                    'value' => 0,
                    'percentage' => 0, // Endtime / Target
                    'color' => 'primary'
                ],
                [
                    'name' => 'Submitted',
                    'value' => 0,
                    'percentage' => 0, // Submitted / Endtime
                    'color' => 'success'
                ],
                [
                    'name' => 'Remaining',
                    'value' => 0,
                    'percentage' => 0, // Remaining / Endtime
                    'color' => 'danger'
                ]
            ];
        }
    }

    /**
     * Get the current date from session
     */
    private function getDate()
    {
        // Get date from session with proper key
        $date = Session::get('date', now()->format('Y-m-d'));
        \Log::info("PerformanceStats getDate - Retrieved date from session: $date");
        return $date;
    }

    /**
     * Get the current worktype from session
     */
    private function getWorktype()
    {
        // Get worktype from session with proper key
        $worktype = Session::get('worktype', 'all');
        \Log::info("PerformanceStats getWorktype - Retrieved worktype from session: $worktype");
        return $worktype;
    }

    /**
     * Get the current lottype from session
     */
    private function getLottype()
    {
        // Get lottype from session with proper key
        $lottype = Session::get('lottype', 'all');
        \Log::info("PerformanceStats getLottype - Retrieved lottype from session: $lottype");
        return $lottype;
    }

    public function render()
    {
        return view('livewire.endtime-dashboard.performance-stats');
    }
}
