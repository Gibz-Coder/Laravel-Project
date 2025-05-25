<?php

namespace App\Livewire\EndtimeDashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ProgressChart extends Component
{
    // We don't need to store chart data anymore as we're using JavaScript to read from the cards

    // Listen for filter change events to trigger chart updates
    protected $listeners = [
        'dateChanged',
        'cutoffChanged',
        'worktypeChanged',
        'lottypeChanged',
        'refreshData'
    ];

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.endtime-dashboard.progress-chart');
    }
}
