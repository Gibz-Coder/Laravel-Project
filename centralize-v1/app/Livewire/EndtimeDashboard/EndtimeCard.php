<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use App\Models\ViProdEndtimeSubmitted;
use App\Services\EndtimeDashboardService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class EndtimeCard extends Component
{
    public $total = 0;
    public $count = 0;
    public $percentage = 0;

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
        \Log::info("EndtimeCard mount - Initializing component");
        $this->refreshData();
    }

    /**
     * Called after the component is hydrated but before it's rendered
     */
    public function hydrate()
    {
        \Log::info("EndtimeCard hydrate - Component hydrated after page refresh");
        $this->refreshData();
    }

    /**
     * Handle date change event
     */
    public function handleDateChanged($data)
    {
        try {
            \Log::info("EndtimeCard received dateChanged event", ['data' => json_encode($data)]);

            // Extract date from event data
            $date = null;
            if (is_array($data) && isset($data['date'])) {
                $date = $data['date'];
            } elseif (is_object($data) && isset($data->date)) {
                $date = $data->date;
            }

            if ($date) {
                \Log::info("EndtimeCard updating date to: {$date}");
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
     * Handle cutoff change event
     */
    public function handleCutoffChanged($data)
    {
        try {
            \Log::info("EndtimeCard received cutoffChanged event", ['data' => json_encode($data)]);

            // Extract cutoff from event data
            $cutoff = null;
            if (is_array($data) && isset($data['cutoff'])) {
                $cutoff = $data['cutoff'];
            } elseif (is_object($data) && isset($data->cutoff)) {
                $cutoff = $data->cutoff;
            }

            if ($cutoff) {
                \Log::info("EndtimeCard updating cutoff to: {$cutoff}");
                // Store the cutoff in session for persistence
                session(['cutoff' => $cutoff]);
            }

            // Refresh data
            $this->refreshData();
        } catch (\Exception $e) {
            \Log::error("Error handling cutoffChanged event: " . $e->getMessage());
            $this->refreshData();
        }
    }

    /**
     * Handle worktype change event
     */
    public function handleWorktypeChanged($data)
    {
        try {
            \Log::info("EndtimeCard received worktypeChanged event", ['data' => json_encode($data)]);

            // Extract worktype from event data
            $worktype = null;
            if (is_array($data) && isset($data['worktype'])) {
                $worktype = $data['worktype'];
            } elseif (is_object($data) && isset($data->worktype)) {
                $worktype = $data->worktype;
            }

            if ($worktype) {
                \Log::info("EndtimeCard updating worktype to: {$worktype}");
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
     * Handle lottype change event
     */
    public function handleLottypeChanged($data)
    {
        try {
            \Log::info("EndtimeCard received lottypeChanged event", ['data' => json_encode($data)]);

            // Extract lottype from event data
            $lottype = null;
            if (is_array($data) && isset($data['lottype'])) {
                $lottype = $data['lottype'];
            } elseif (is_object($data) && isset($data->lottype)) {
                $lottype = $data->lottype;
            }

            if ($lottype) {
                \Log::info("EndtimeCard updating lottype to: {$lottype}");
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
            // Get current filter values from parent component or session
            $date = $this->getDate();
            $cutoff = $this->getCutoff();
            $worktype = $this->getWorktype();
            $lottype = $this->getLottype();

            // Log the filter values for debugging
            Log::info("EndtimeCard refreshData - Using date: '$date', cutoff: '$cutoff', worktype: '$worktype', lottype: '$lottype'");

            // Use the service to get the data
            $data = EndtimeDashboardService::getEndtimeData($date, $cutoff, $worktype, $lottype);

            // Update component properties
            $this->total = $data['total'];
            $this->count = $data['count'];
            $this->percentage = $data['percentage'];

            Log::info("EndtimeCard data - Total: {$this->total}, Count: {$this->count}, Percentage: {$this->percentage}");
        } catch (\Exception $e) {
            Log::error("Error in EndtimeCard refreshData: " . $e->getMessage());
            // Set default values in case of error
            $this->total = 0;
            $this->count = 0;
            $this->percentage = 0;
        }
    }

    /**
     * Get the current date from session or parent component
     */
    private function getDate()
    {
        // Get date from session with proper key
        $date = Session::get('date', now()->format('Y-m-d'));
        \Log::info("EndtimeCard getDate - Retrieved date from session: $date");
        return $date;
    }

    /**
     * Get the current cutoff from session or parent component
     */
    private function getCutoff()
    {
        // Get cutoff from session with proper key
        $cutoff = Session::get('cutoff', 'all');
        \Log::info("EndtimeCard getCutoff - Retrieved cutoff from session: $cutoff");
        return $cutoff;
    }

    /**
     * Get the current worktype from session or parent component
     */
    private function getWorktype()
    {
        // Get worktype from session with proper key
        $worktype = Session::get('worktype', 'all');
        \Log::info("EndtimeCard getWorktype - Retrieved worktype from session: $worktype");
        return $worktype;
    }

    /**
     * Get the current lottype from session or parent component
     */
    private function getLottype()
    {
        // Get lottype from session with proper key
        $lottype = Session::get('lottype', 'all');
        \Log::info("EndtimeCard getLottype - Retrieved lottype from session: $lottype");
        return $lottype;
    }

    /**
     * Show lot details in modal
     */
    public function showLotDetails()
    {
        try {
            // Get current filter values
            $date = $this->getDate();
            $cutoff = $this->getCutoff();
            $worktype = $this->getWorktype();
            $lottype = $this->getLottype();

            // Log the action
            Log::info("EndtimeCard showLotDetails - Showing lot details modal for endtime lots");

            // Prepare parameters for the modal
            $params = [
                'date' => $date,
                'cutoff' => $cutoff,
                'worktype' => $worktype,
                'lottype' => $lottype,
                'type' => 'endtime',
                'title' => 'Endtime Lots'
            ];

            // Emit event to show the lot list modal
            $this->dispatch('showLotList', $params);
        } catch (\Exception $e) {
            Log::error("Error in EndtimeCard showLotDetails: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.endtime-dashboard.endtime-card');
    }
}
