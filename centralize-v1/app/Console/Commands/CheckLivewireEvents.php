<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckLivewireEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:livewire-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Livewire events in the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Livewire events...');

        // Check the EndtimeDashboard component
        $this->info('Checking EndtimeDashboard component...');
        $this->checkComponent('App\Livewire\EndtimeDashboard');

        // Check the TargetCard component
        $this->info('Checking TargetCard component...');
        $this->checkComponent('App\Livewire\EndtimeDashboard\TargetCard');

        return 0;
    }

    /**
     * Check a Livewire component
     */
    private function checkComponent($componentClass)
    {
        try {
            // Check if the component class exists
            if (!class_exists($componentClass)) {
                $this->error("Component class $componentClass does not exist!");
                return;
            }

            $this->info("Component class $componentClass exists.");

            // Create an instance of the component
            $component = new $componentClass();

            // Check the listeners
            if (property_exists($component, 'listeners')) {
                $this->info('Listeners:');
                foreach ($component->listeners as $event => $method) {
                    $this->info("  $event => $method");
                }
            } else {
                $this->info('No listeners defined.');
            }

            // Check the public properties
            $this->info('Public properties:');
            $reflection = new \ReflectionClass($component);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                $name = $property->getName();
                $value = $property->isInitialized($component) ? $component->$name : 'uninitialized';
                $this->info("  $name = " . (is_scalar($value) ? $value : gettype($value)));
            }

            // Check the methods
            $this->info('Public methods:');
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                if ($method->class === $componentClass) {
                    $this->info("  " . $method->getName());
                }
            }
        } catch (\Exception $e) {
            $this->error('Error checking component: ' . $e->getMessage());
        }
    }
}
