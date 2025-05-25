/**
 * Modal Fix for Bootstrap Modals
 * This script fixes issues with modal backdrops not being properly removed
 * when modals are closed, especially after double-clicking to open them.
 */

(function() {
    "use strict";

    document.addEventListener('DOMContentLoaded', function() {
        // Function to fix modal backdrop issues
        function fixModalBackdropIssues() {
            // Get all modal elements
            const modals = document.querySelectorAll('.modal');
            
            // Add event listeners to each modal
            modals.forEach(modal => {
                // When the modal is hidden (closed)
                modal.addEventListener('hidden.bs.modal', function() {
                    // Remove any lingering backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    if (backdrops.length > 0) {
                        backdrops.forEach(backdrop => {
                            backdrop.remove();
                        });
                    }
                    
                    // Remove modal-open class from body if no modals are open
                    const openModals = document.querySelectorAll('.modal.show');
                    if (openModals.length === 0) {
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    }
                });
            });
        }

        // Initialize the fix
        fixModalBackdropIssues();

        // Also run the fix when Livewire updates the DOM
        if (typeof window.Livewire !== 'undefined') {
            document.addEventListener('livewire:initialized', fixModalBackdropIssues);
            document.addEventListener('livewire:load', fixModalBackdropIssues);
        }
    });
})();
