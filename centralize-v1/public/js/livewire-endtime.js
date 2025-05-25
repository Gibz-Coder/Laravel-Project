/**
 * Livewire Endtime Dashboard Integration
 * This script handles the integration between Livewire and the Endtime Dashboard
 */

(function () {
    "use strict";

    // Listen for Livewire events
    document.addEventListener('livewire:initialized', function () {
        // Listen for toast notifications from Livewire
        Livewire.on('showToast', (params) => {
            showToast(params[0].title, params[0].message, params[0].type);
        });

        // Listen for date changes from Livewire
        Livewire.on('dateChanged', (params) => {
            // Update any JavaScript components that need to know about the date change
            if (typeof updateSelectedDate === 'function') {
                updateSelectedDate(params[0].date);
            }
        });

        // Listen for cutoff changes from Livewire
        Livewire.on('cutoffChanged', (params) => {
            // Update any JavaScript components that need to know about the cutoff change
            const cutoffButtons = document.querySelectorAll('[data-time]');
            cutoffButtons.forEach(button => {
                if (button.dataset.time === params[0].cutoff) {
                    if (typeof window.updateCutoffSelection === 'function') {
                        window.updateCutoffSelection(button);
                    }
                }
            });
        });

        // Listen for worktype changes from Livewire
        Livewire.on('worktypeChanged', (params) => {
            // Update any JavaScript components that need to know about the worktype change
        });

        // Listen for lottype changes from Livewire
        Livewire.on('lottypeChanged', (params) => {
            // Update any JavaScript components that need to know about the lottype change
        });

        // Listen for refresh data event from Livewire
        Livewire.on('refreshData', () => {
            // Call all update functions
            if (typeof updateLinePerformanceTable === 'function') {
                updateLinePerformanceTable();
            }
            if (typeof updateSizePerformanceTable === 'function') {
                updateSizePerformanceTable();
            }
            if (typeof updateSubmittedPerCutoffTable === 'function') {
                updateSubmittedPerCutoffTable();
            }
            if (typeof updateTotalSubmittedTable === 'function') {
                updateTotalSubmittedTable();
            }
            if (typeof fetchTargetCapacity === 'function') {
                fetchTargetCapacity();
            }
            if (typeof fetchEndtimeTotal === 'function') {
                fetchEndtimeTotal();
            }
            if (typeof fetchSubmittedTotal === 'function') {
                fetchSubmittedTotal();
            }
            if (typeof fetchRemainingTotal === 'function') {
                fetchRemainingTotal();
            }
        });
    });

    /**
     * Show a toast notification
     * @param {string} title - The title of the toast
     * @param {string} message - The message to display
     * @param {string} type - The type of toast (success, error, warning, info)
     */
    function showToast(title, message, type = 'success') {
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 is not loaded');
            return;
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type,
            title: title,
            text: message
        });
    }

})();
