<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use App\Models\ViCapaRef;
use App\Services\EndtimeDashboardService;
use Illuminate\Support\Facades\Request;

class TargetCard extends Component
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
        \Log::info("TargetCard mount - Initializing component");
        $this->refreshData();
    }

    /**
     * Called after the component is hydrated but before it's rendered
     */
    public function hydrate()
    {
        \Log::info("TargetCard hydrate - Component hydrated after page refresh");
        $this->refreshData();
    }

    /**
     * Handle date changed event
     *
     * @param mixed $data Event data
     */
    public function handleDateChanged($data = null)
    {
        try {
            \Log::info("TargetCard received dateChanged event", ['data' => json_encode($data)]);

            // Extract date from event data
            $date = null;
            if (is_array($data) && isset($data['date'])) {
                $date = $data['date'];
            } elseif (is_object($data) && isset($data->date)) {
                $date = $data->date;
            }

            if ($date) {
                \Log::info("TargetCard updating date to: {$date}");
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
     * Handle cutoff changed event
     *
     * @param mixed $data Event data
     */
    public function handleCutoffChanged($data = null)
    {
        try {
            \Log::info("TargetCard received cutoffChanged event", ['data' => json_encode($data)]);

            // Extract cutoff from event data
            $cutoff = null;
            if (is_array($data) && isset($data['cutoff'])) {
                $cutoff = $data['cutoff'];
            } elseif (is_object($data) && isset($data->cutoff)) {
                $cutoff = $data->cutoff;
            }

            if ($cutoff) {
                \Log::info("TargetCard updating cutoff to: {$cutoff}");
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
     * Handle worktype changed event
     *
     * @param mixed $data Event data
     */
    public function handleWorktypeChanged($data = null)
    {
        try {
            \Log::info("TargetCard received worktypeChanged event", ['data' => json_encode($data)]);

            // Extract worktype from event data
            $worktype = null;
            if (is_array($data) && isset($data['worktype'])) {
                $worktype = $data['worktype'];
            } elseif (is_object($data) && isset($data->worktype)) {
                $worktype = $data->worktype;
            }

            if ($worktype) {
                \Log::info("TargetCard updating worktype to: {$worktype}");
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
     * Handle lottype changed event
     *
     * @param mixed $data Event data
     */
    public function handleLottypeChanged($data = null)
    {
        try {
            \Log::info("TargetCard received lottypeChanged event", ['data' => json_encode($data)]);

            // Extract lottype from event data
            $lottype = null;
            if (is_array($data) && isset($data['lottype'])) {
                $lottype = $data['lottype'];
            } elseif (is_object($data) && isset($data->lottype)) {
                $lottype = $data->lottype;
            }

            if ($lottype) {
                \Log::info("TargetCard updating lottype to: {$lottype}");
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
     * Refresh the data based on the current worktype and cutoff
     */
    public function refreshData()
    {
        try {
            // Get the current worktype and cutoff from the parent component or session
            $worktype = $this->getWorktype();
            $cutoff = $this->getCutoff();
            $lottype = 'all'; // Default lottype for target card
            $date = session('date', now()->format('Y-m-d')); // Get date from session or use current date

            // Log the values for debugging
            \Log::info("TargetCard refreshData - Using worktype: '$worktype', cutoff: '$cutoff', date: '$date'");

            // Use the EndtimeDashboardService to get the target capacity data
            $targetData = EndtimeDashboardService::getTargetCapacity($date, $cutoff, $worktype, $lottype);

            // Update component properties
            $this->total = $targetData['total'];
            $this->count = $targetData['count'];
            $this->percentage = $targetData['percentage'];

            // Log the results for debugging
            \Log::info("TargetCard refreshData - Final results: total={$this->total}, count={$this->count}, percentage={$this->percentage}");

        } catch (\Exception $e) {
            // Handle any exceptions (e.g., database connection issues)
            $this->total = 0;
            $this->count = 0;
            $this->percentage = 0;

            // Log the error for debugging
            \Log::error('Error in TargetCard refreshData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Get the current worktype from the parent component, session, or request
     *
     * @return string|array
     */
    private function getWorktype()
    {
        try {
            // First priority: Check session
            $worktype = session('worktype');
            if ($worktype) {
                \Log::info("Getting worktype from session: $worktype");
                return $worktype;
            }

            // Second priority: Check parent component
            $parent = $this->getParentComponent();
            if ($parent && isset($parent->worktype)) {
                $parentWorktype = $parent->worktype;
                \Log::info("Getting worktype from parent: " . (is_string($parentWorktype) ? $parentWorktype : json_encode($parentWorktype)));

                // Store in session for future use
                if (is_string($parentWorktype)) {
                    session(['worktype' => $parentWorktype]);
                } elseif (is_array($parentWorktype) && isset($parentWorktype['worktype'])) {
                    session(['worktype' => $parentWorktype['worktype']]);
                }

                return $parentWorktype;
            }

            // Third priority: Check request
            $requestWorktype = Request::input('worktype');
            if ($requestWorktype) {
                \Log::info("Getting worktype from request: $requestWorktype");

                // Store in session for future use
                session(['worktype' => $requestWorktype]);

                return $requestWorktype;
            }

            // Default to 'all'
            \Log::info("No worktype found, using default: all");
            return 'all';
        } catch (\Exception $e) {
            // Log the error
            \Log::warning('Error getting worktype: ' . $e->getMessage());

            // Default to 'all' if there's an error
            return 'all';
        }
    }

    /**
     * Get the current cutoff from the parent component, session, or request
     *
     * @return string|array
     */
    private function getCutoff()
    {
        try {
            // First priority: Check session
            $cutoff = session('cutoff');
            if ($cutoff) {
                \Log::info("Getting cutoff from session: $cutoff");
                return $cutoff;
            }

            // Second priority: Check parent component
            $parent = $this->getParentComponent();
            if ($parent && isset($parent->cutoff)) {
                $parentCutoff = $parent->cutoff;
                \Log::info("Getting cutoff from parent: " . (is_string($parentCutoff) ? $parentCutoff : json_encode($parentCutoff)));

                // Store in session for future use
                if (is_string($parentCutoff)) {
                    session(['cutoff' => $parentCutoff]);
                } elseif (is_array($parentCutoff) && isset($parentCutoff['cutoff'])) {
                    session(['cutoff' => $parentCutoff['cutoff']]);
                }

                return $parentCutoff;
            }

            // Third priority: Check request
            $requestCutoff = Request::input('cutoff');
            if ($requestCutoff) {
                \Log::info("Getting cutoff from request: $requestCutoff");

                // Store in session for future use
                session(['cutoff' => $requestCutoff]);

                return $requestCutoff;
            }

            // Default to 'all'
            \Log::info("No cutoff found, using default: all");
            return 'all';
        } catch (\Exception $e) {
            // Log the error
            \Log::warning('Error getting cutoff: ' . $e->getMessage());

            // Default to 'all' if there's an error
            return 'all';
        }
    }

    /**
     * Get the parent component
     *
     * @return mixed
     */
    private function getParentComponent()
    {
        // Try different ways to access the parent component
        // Livewire v3 uses $this->parent
        if (isset($this->parent)) {
            return $this->parent;
        }

        // Livewire v2 uses $this->getParent() method
        if (method_exists($this, 'getParent')) {
            return $this->getParent();
        }

        // If we can't access the parent component, return null
        return null;
    }

    public function render()
    {
        return view('livewire.endtime-dashboard.target-card');
    }
}
