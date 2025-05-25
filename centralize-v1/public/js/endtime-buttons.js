/**
 * Endtime Dashboard Buttons
 * This script handles the functionality for buttons in the Endtime Dashboard
 */

(function () {
    "use strict";

    /**
     * Auto Refresh Toggle Button
     *
     * Functionality:
     * - Saves toggle state to localStorage
     * - When ON: Sets up intervals to refresh data every 30 seconds
     * - When OFF: Clears all refresh intervals
     * - Works with Livewire's wire:model.live="autoRefresh" binding
     */
    function initAutoRefreshToggle() {
        // Get the auto-refresh toggle
        const autoRefreshToggle = document.getElementById("toggleswitchSuccess");
        if (!autoRefreshToggle) return;

        // Store all intervals in this object for easy clearing
        window.refreshIntervals = window.refreshIntervals || {};

        // Load saved state from localStorage
        const savedState = localStorage.getItem("autoRefreshState");
        if (savedState !== null) {
            // This will trigger the Livewire binding
            autoRefreshToggle.checked = savedState === "true";

            // If auto-refresh is enabled, set up intervals immediately
            if (autoRefreshToggle.checked) {
                setupAutoRefreshIntervals();
            }
        }

        // Add event listener to the toggle
        autoRefreshToggle.addEventListener("change", function () {
            // Save state to localStorage
            localStorage.setItem("autoRefreshState", this.checked);

            if (this.checked) {
                // When turning on auto-refresh, set up intervals
                setupAutoRefreshIntervals();

                // Update to current date if needed
                updateToCurrentDateIfNeeded();
            } else {
                // When turning off auto-refresh, clear all intervals
                clearAllRefreshIntervals();
            }
        });
    }

    /**
     * Sets up all auto-refresh intervals for different components
     */
    function setupAutoRefreshIntervals() {
        // Clear any existing intervals first
        clearAllRefreshIntervals();

        // Set up interval for line performance table
        if (typeof updateLinePerformanceTable === "function") {
            window.refreshIntervals.linePerformance = setInterval(
                updateLinePerformanceTable,
                30000 // 30 seconds
            );
        }

        // Set up interval for size performance table
        if (typeof updateSizePerformanceTable === "function") {
            window.refreshIntervals.sizePerformance = setInterval(
                updateSizePerformanceTable,
                30000 // 30 seconds
            );
        }

        // Set up interval for submitted per cutoff table
        if (typeof updateSubmittedPerCutoffTable === "function") {
            window.refreshIntervals.submittedPerCutoff = setInterval(
                updateSubmittedPerCutoffTable,
                30000 // 30 seconds
            );
        }

        // Set up interval for total submitted table
        if (typeof updateTotalSubmittedTable === "function") {
            window.refreshIntervals.totalSubmitted = setInterval(
                updateTotalSubmittedTable,
                30000 // 30 seconds
            );
        }

        // Set up date check interval (every minute)
        window.refreshIntervals.dateCheck = setInterval(
            checkForDateChange,
            60000 // 1 minute
        );

        // Set up browser refresh interval (every 5 minutes)
        setupBrowserRefreshInterval();
    }

    /**
     * Sets up browser refresh interval (equivalent to Ctrl+R)
     * This will refresh the entire page every 5 minutes (300,000 ms)
     */
    function setupBrowserRefreshInterval() {
        // Set up interval for full browser refresh (every 5 minutes)
        window.refreshIntervals.browserRefresh = setInterval(
            function() {
                // Reload the current page (equivalent to Ctrl+R)
                window.location.reload();
            },
            300000 // 5 minutes in milliseconds
        );
    }

    /**
     * Clears all auto-refresh intervals
     */
    function clearAllRefreshIntervals() {
        if (!window.refreshIntervals) return;

        // Clear all intervals
        Object.keys(window.refreshIntervals).forEach(key => {
            clearInterval(window.refreshIntervals[key]);
        });

        // Reset the intervals object
        window.refreshIntervals = {};
    }

    /**
     * Updates to current date if needed
     * Only updates if auto-refresh is enabled and no saved date exists
     */
    function updateToCurrentDateIfNeeded() {
        const dateInput = document.getElementById("endtime");
        if (!dateInput) return;

        // Check if we have a saved date in localStorage
        const savedDate = localStorage.getItem("selectedEndtimeDate");
        if (savedDate) {
            // Validate the saved date
            const isValidDate = !isNaN(new Date(savedDate).getTime());
            if (isValidDate) {
                // Use the saved date
                return;
            }
        }

        // No valid saved date, update to current date in Manila timezone
        updateToCurrentDate();
    }

    /**
     * Updates to current date in Manila timezone
     */
    function updateToCurrentDate() {
        const dateInput = document.getElementById("endtime");
        if (!dateInput) return;

        // Get current date in Manila timezone
        const now = new Date();
        const manilaOptions = { timeZone: "Asia/Manila" };
        const manilaDateString = now.toLocaleString("en-US", manilaOptions);
        const manilaDate = new Date(manilaDateString);

        // Create YYYY-MM-DD format using Manila date components
        const year = manilaDate.getFullYear();
        const month = String(manilaDate.getMonth() + 1).padStart(2, '0');
        const day = String(manilaDate.getDate()).padStart(2, '0');
        const formattedValue = `${year}-${month}-${day}`;

        // Set the date input value
        dateInput.value = formattedValue;

        // Save to localStorage
        localStorage.setItem("selectedEndtimeDate", formattedValue);

        // Update displayed date if needed
        if (typeof updateSelectedDate === "function") {
            updateSelectedDate(formattedValue);
        }

        // Update to current cutoff time period
        updateToCurrentCutoff();
    }

    /**
     * Checks if the date has changed (e.g., after midnight) and updates if needed
     * This is called every minute when auto-refresh is enabled
     */
    function checkForDateChange() {
        // Only proceed if auto-refresh is enabled
        const autoRefreshToggle = document.getElementById("toggleswitchSuccess");
        if (!autoRefreshToggle || !autoRefreshToggle.checked) return;

        // Get the date input element
        const dateInput = document.getElementById("date-picker-input");
        if (!dateInput) return;

        // Get current date in Manila timezone
        const now = new Date();
        const manilaOptions = { timeZone: "Asia/Manila" };
        const manilaDateString = now.toLocaleString("en-US", manilaOptions);
        const manilaDate = new Date(manilaDateString);

        // Create YYYY-MM-DD format using Manila date components
        const year = manilaDate.getFullYear();
        const month = String(manilaDate.getMonth() + 1).padStart(2, '0');
        const day = String(manilaDate.getDate()).padStart(2, '0');
        const currentDate = `${year}-${month}-${day}`;

        // Get the current date from the input
        const currentInputDate = dateInput.value;

        // If dates are different, update the date
        if (currentDate !== currentInputDate) {
            console.log(`Date changed from ${currentInputDate} to ${currentDate} - updating date in JS`);

            // Update the date input value
            dateInput.value = currentDate;

            // Trigger the Livewire change event to update the backend
            dateInput.dispatchEvent(new Event('change'));

            // Also update localStorage
            localStorage.setItem("selectedEndtimeDate", currentDate);
        }
    }

    /**
     * Updates to current cutoff time period based on Manila time
     */
    function updateToCurrentCutoff() {
        // Get current time in Manila
        const now = new Date();
        const manilaTime = new Date(
            now.toLocaleString("en-US", { timeZone: "Asia/Manila" })
        );
        const hours = manilaTime.getHours();
        const minutes = manilaTime.getMinutes();
        const currentTime = hours * 60 + minutes; // Convert to minutes for easier comparison

        // Determine which cutoff period we're currently in
        let currentCutoff = "all";

        if (currentTime >= 0 && currentTime < 240) {
            // 00:00~04:00
            currentCutoff = "00:00~04:00";
        } else if (currentTime >= 240 && currentTime < 420) {
            // 04:00~07:00
            currentCutoff = "04:00~07:00";
        } else if (currentTime >= 420 && currentTime < 720) {
            // 07:00~12:00
            currentCutoff = "07:00~12:00";
        } else if (currentTime >= 720 && currentTime < 960) {
            // 12:00~16:00
            currentCutoff = "12:00~16:00";
        } else if (currentTime >= 960 && currentTime < 1140) {
            // 16:00~19:00
            currentCutoff = "16:00~19:00";
        } else if (currentTime >= 1140 && currentTime < 1440) {
            // 19:00~00:00
            currentCutoff = "19:00~00:00";
        }

        // Find the button for the current cutoff
        const cutoffButton = document.querySelector(
            `[data-time="${currentCutoff}"]`
        );

        if (cutoffButton) {
            // Call the updateCutoffSelection function with the current cutoff button
            if (typeof window.updateCutoffSelection === "function") {
                window.updateCutoffSelection(cutoffButton);
            }
        }
    }

    // Initialize when DOM is loaded
    document.addEventListener("DOMContentLoaded", function () {
        initAutoRefreshToggle();
    });

})();
