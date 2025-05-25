/**
 * Offline Fallback for Livewire and Alpine
 * This script ensures that Livewire and Alpine components work even when offline
 */

(function() {
    "use strict";

    // Function to check if a script failed to load
    function handleScriptError(event) {
        const failedSrc = event.target.src;
        console.warn('Script failed to load:', failedSrc);

        // Check if it's a Livewire script
        if (failedSrc.includes('livewire')) {
            console.log('Loading local Livewire fallback...');
            loadLocalLivewire();
        }
        
        // Check if it's an Alpine script
        if (failedSrc.includes('alpine')) {
            console.log('Loading local Alpine fallback...');
            loadLocalAlpine();
        }
    }

    // Function to load local Livewire script
    function loadLocalLivewire() {
        const script = document.createElement('script');
        script.src = '/vendor/livewire/livewire.js';
        script.defer = true;
        document.head.appendChild(script);
    }

    // Function to load local Alpine script
    function loadLocalAlpine() {
        const script = document.createElement('script');
        script.src = '/vendor/alpine/alpine.min.js';
        script.defer = true;
        document.head.appendChild(script);
    }

    // Listen for script load errors
    window.addEventListener('error', function(event) {
        if (event.target.tagName === 'SCRIPT') {
            handleScriptError(event);
        }
    }, true);

    // Check if we're offline
    if (!navigator.onLine) {
        console.log('Offline detected, loading local assets...');
        loadLocalLivewire();
        loadLocalAlpine();
    }

    // Listen for offline events
    window.addEventListener('offline', function() {
        console.log('Went offline, loading local assets...');
        loadLocalLivewire();
        loadLocalAlpine();
    });
})();
