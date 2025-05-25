/**
 * Endtime Entry Management
 * This script handles the functionality for adding and managing endtime and submitted lot entries
 */

(function () {
    "use strict";

    // DOM elements
    const addEndtimeEntryBtn = document.getElementById('add-endtime-entry');
    const addSubmittedLotEntryBtn = document.getElementById('add-submitted-lot-entry');
    const endtimeEntriesContainer = document.getElementById('endtime-entries');
    const submittedLotEntriesContainer = document.getElementById('submitted-lot-entries');

    /**
     * Initialize the entry management functionality
     */
    function init() {
        // Add event listeners for the "Add Another Lot" buttons
        if (addEndtimeEntryBtn) {
            addEndtimeEntryBtn.addEventListener('click', addEndtimeEntry);
        }

        if (addSubmittedLotEntryBtn) {
            addSubmittedLotEntryBtn.addEventListener('click', addSubmittedLotEntry);
        }

        // Update the current cutoff display in the submitted lot modal
        updateSubmittedCutoffDisplay();

        // Add event delegation for delete buttons
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('delete-entry')) {
                deleteEntry(e.target);
            }
        });

        // Add event delegation for dropdown items
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('dropdown-item')) {
                updateDropdownValue(e.target);
            }
        });

        // Add event delegation for MC No input fields
        document.addEventListener('input', function(e) {
            if (e.target && e.target.placeholder === 'MC No') {
                formatMcNo(e.target);
            }
        });

        // Add event delegation for MC No input fields on blur (to ensure formatting)
        document.addEventListener('blur', function(e) {
            if (e.target && e.target.placeholder === 'MC No') {
                formatMcNo(e.target, true);
                lookupMachineInfo(e.target);
            }
        }, true);

        // Add event delegation for Lot No input fields on blur
        document.addEventListener('blur', function(e) {
            if (e.target && e.target.classList.contains('lot-no-input')) {
                lookupLotInfo(e.target);
            }
        }, true);

        // Add direct event listeners to existing lot-no-input fields
        const lotNoInputs = document.querySelectorAll('.lot-no-input');
        lotNoInputs.forEach(input => {
            input.addEventListener('blur', function() {
                lookupLotInfo(this);
            });

            // Add keydown event listener for Enter key
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    lookupLotInfo(this);

                    // Find the current row and index
                    const currentRow = this.closest('tr');
                    const allRows = Array.from(currentRow.parentElement.querySelectorAll('tr'));
                    const currentIndex = allRows.indexOf(currentRow);

                    // Find the next row if available
                    if (currentIndex < allRows.length - 1) {
                        const nextRow = allRows[currentIndex + 1];
                        // Find the same input field in the next row
                        const nextInput = nextRow.querySelector('.lot-no-input');
                        if (nextInput) {
                            nextInput.focus();
                            return;
                        }
                    }
                }
            });
        });

        // Add direct event listeners to existing MC No input fields
        const mcNoInputs = document.querySelectorAll('input[placeholder="MC No"]');
        mcNoInputs.forEach(input => {
            // Add keydown event listener for Enter key
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formatMcNo(this, true);
                    lookupMachineInfo(this);

                    // Find the current row and index
                    const currentRow = this.closest('tr');
                    const allRows = Array.from(currentRow.parentElement.querySelectorAll('tr'));
                    const currentIndex = allRows.indexOf(currentRow);

                    // Find the next row if available
                    if (currentIndex < allRows.length - 1) {
                        const nextRow = allRows[currentIndex + 1];
                        // Find the same input field in the next row
                        const nextInput = nextRow.querySelector('input[placeholder="MC No"]');
                        if (nextInput) {
                            nextInput.focus();
                            return;
                        }
                    }
                }
            });
        });

        // Add event delegation for keydown events to handle Enter key
        document.addEventListener('keydown', function(e) {
            // Check if the Enter key was pressed
            if (e.key === 'Enter') {
                // For Lot No input fields
                if (e.target && e.target.classList.contains('lot-no-input')) {
                    e.preventDefault(); // Prevent form submission
                    lookupLotInfo(e.target);

                    // Find the current row and index
                    const currentRow = e.target.closest('tr');
                    const allRows = Array.from(currentRow.parentElement.querySelectorAll('tr'));
                    const currentIndex = allRows.indexOf(currentRow);

                    // Find the next row if available
                    if (currentIndex < allRows.length - 1) {
                        const nextRow = allRows[currentIndex + 1];
                        // Find the same input field in the next row
                        const nextInput = nextRow.querySelector('.lot-no-input');
                        if (nextInput) {
                            nextInput.focus();
                            return;
                        }
                    }
                }

                // For MC No input fields
                if (e.target && e.target.placeholder === 'MC No') {
                    e.preventDefault(); // Prevent form submission
                    formatMcNo(e.target, true);
                    lookupMachineInfo(e.target);

                    // Find the current row and index
                    const currentRow = e.target.closest('tr');
                    const allRows = Array.from(currentRow.parentElement.querySelectorAll('tr'));
                    const currentIndex = allRows.indexOf(currentRow);

                    // Find the next row if available
                    if (currentIndex < allRows.length - 1) {
                        const nextRow = allRows[currentIndex + 1];
                        // Find the same input field in the next row
                        const nextInput = nextRow.querySelector('input[placeholder="MC No"]');
                        if (nextInput) {
                            nextInput.focus();
                            return;
                        }
                    }
                }
            }
        });

        // Initialize date-cutoff dropdowns
        initDateCutoffDropdowns();

        // Set default data-db-format attribute for existing dropdown buttons
        const dateCutoffButtons = document.querySelectorAll('.date-cutoff-dropdown .dropdown-toggle');
        dateCutoffButtons.forEach(button => {
            if (!button.hasAttribute('data-db-format')) {
                const text = button.textContent.trim();
                const parts = text.split('|');
                if (parts.length >= 2) {
                    const cutoffDisplay = parts[1].trim();
                    let dbFormat = '07:00~12:00'; // Default to 12NN

                    // Map the cutoff display value to the database format
                    switch (cutoffDisplay) {
                        case '4AM':
                            dbFormat = '00:00~04:00';
                            break;
                        case '7AM':
                            dbFormat = '04:00~07:00';
                            break;
                        case '12NN':
                            dbFormat = '07:00~12:00';
                            break;
                        case '4PM':
                            dbFormat = '12:00~16:00';
                            break;
                        case '7PM':
                            dbFormat = '16:00~19:00';
                            break;
                        case '12MN':
                            dbFormat = '19:00~00:00';
                            break;
                    }

                    button.setAttribute('data-db-format', dbFormat);
                }
            }
        });
    }

    /**
     * Initialize date-cutoff dropdowns with current and next day cutoffs
     */
    function initDateCutoffDropdowns() {
        const dateCutoffDropdowns = document.querySelectorAll('.date-cutoff-dropdown');

        if (dateCutoffDropdowns.length === 0) return;

        // Get current date and time
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();

        // Format current date
        const currentDate = formatDate(now);

        // Get tomorrow's date
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowDate = formatDate(tomorrow);

        // Define cutoff times in chronological order for a day
        const cutoffs = [
            { time: '4AM', hours: 4, minutes: 0, dbFormat: '00:00~04:00' },
            { time: '7AM', hours: 7, minutes: 0, dbFormat: '04:00~07:00' },
            { time: '12NN', hours: 12, minutes: 0, dbFormat: '07:00~12:00' },
            { time: '4PM', hours: 16, minutes: 0, dbFormat: '12:00~16:00' },
            { time: '7PM', hours: 19, minutes: 0, dbFormat: '16:00~19:00' },
            { time: '12MN', hours: 0, minutes: 0, dbFormat: '19:00~00:00' }
        ];

        // Find the current cutoff period
        let currentCutoffIndex = -1;

        for (let i = 0; i < cutoffs.length; i++) {
            const nextIndex = (i + 1) % cutoffs.length;
            const currentCutoff = cutoffs[i];
            const nextCutoff = cutoffs[nextIndex];

            // Special handling for midnight crossing
            if (nextCutoff.hours < currentCutoff.hours) {
                // Current time is between current cutoff and midnight
                if (currentHour > currentCutoff.hours ||
                    (currentHour === currentCutoff.hours && currentMinute >= currentCutoff.minutes)) {
                    currentCutoffIndex = i;
                    break;
                }
                // Current time is between midnight and next cutoff
                else if (currentHour < nextCutoff.hours ||
                        (currentHour === nextCutoff.hours && currentMinute < nextCutoff.minutes)) {
                    currentCutoffIndex = i;
                    break;
                }
            }
            // Normal case (no midnight crossing)
            else if ((currentHour > currentCutoff.hours ||
                    (currentHour === currentCutoff.hours && currentMinute >= currentCutoff.minutes)) &&
                    (currentHour < nextCutoff.hours ||
                    (currentHour === nextCutoff.hours && currentMinute < nextCutoff.minutes))) {
                currentCutoffIndex = i;
                break;
            }
        }

        // If we couldn't determine the current cutoff, default to the first one
        if (currentCutoffIndex === -1) {
            currentCutoffIndex = 0;
        }

        // Get the next cutoff index
        const nextCutoffIndex = (currentCutoffIndex + 1) % cutoffs.length;

        // Populate dropdowns
        dateCutoffDropdowns.forEach(dropdown => {
            const dropdownMenu = dropdown.querySelector('.date-cutoff-options');
            const dropdownButton = dropdown.querySelector('.dropdown-toggle');

            if (!dropdownMenu || !dropdownButton) return;

            // Clear existing options
            dropdownMenu.innerHTML = '';

            // Create an array to hold all available cutoffs with their dates
            const availableCutoffs = [];

            // Add current cutoff and future cutoffs for today
            for (let i = nextCutoffIndex; i < cutoffs.length; i++) {
                availableCutoffs.push({
                    date: currentDate,
                    cutoff: cutoffs[i],
                    isCurrent: i === nextCutoffIndex
                });
            }

            // Add tomorrow's cutoffs up to the current cutoff time
            for (let i = 0; i <= currentCutoffIndex; i++) {
                availableCutoffs.push({
                    date: tomorrowDate,
                    cutoff: cutoffs[i],
                    isCurrent: false
                });
            }

            // Sort the cutoffs by date and time
            availableCutoffs.sort((a, b) => {
                if (a.date !== b.date) {
                    return a.date.localeCompare(b.date);
                }

                // For same date, sort by hour
                const aHour = a.cutoff.hours;
                const bHour = b.cutoff.hours;

                // Special handling for midnight (0 hour)
                if (aHour === 0 && bHour !== 0) return 1;
                if (aHour !== 0 && bHour === 0) return -1;

                return aHour - bHour;
            });

            // Add the sorted cutoffs to the dropdown
            availableCutoffs.forEach(item => {
                const li = document.createElement('li');
                const link = document.createElement('a');
                link.className = 'dropdown-item';
                link.href = 'javascript:void(0);';
                link.textContent = `${item.date} | ${item.cutoff.time}`;

                // Store the database format in a data attribute
                link.setAttribute('data-db-format', item.cutoff.dbFormat);

                // Mark current cutoff
                if (item.isCurrent) {
                    link.classList.add('active');
                    dropdownButton.textContent = `${item.date} | ${item.cutoff.time}`;

                    // Also set the data-db-format attribute on the button
                    dropdownButton.setAttribute('data-db-format', item.cutoff.dbFormat);
                }

                li.appendChild(link);
                dropdownMenu.appendChild(li);
            });
        });
    }

    /**
     * Format date as YYYY-MM-DD
     * @param {Date} date - The date to format
     * @returns {string} Formatted date string
     */
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    /**
     * Add a new endtime entry row
     */
    function addEndtimeEntry() {
        if (!endtimeEntriesContainer) return;

        // Clone the first entry row
        const firstEntry = endtimeEntriesContainer.querySelector('.endtime-entry');
        if (!firstEntry) return;

        const newEntry = firstEntry.cloneNode(true);

        // Clear input values
        const inputs = newEntry.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
            // Clear any data attributes
            if (input.classList.contains('qty-input')) {
                input.removeAttribute('data-original-value');
            }
        });

        // Add the new entry to the container
        endtimeEntriesContainer.appendChild(newEntry);

        // Add event listener to the new lot-no-input
        const lotNoInput = newEntry.querySelector('.lot-no-input');
        if (lotNoInput) {
            lotNoInput.addEventListener('blur', function() {
                lookupLotInfo(this);
            });

            // Add keydown event listener for Enter key
            lotNoInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    lookupLotInfo(this);

                    // Find the current row and index
                    const currentRow = this.closest('tr');
                    const allRows = Array.from(currentRow.parentElement.querySelectorAll('tr'));
                    const currentIndex = allRows.indexOf(currentRow);

                    // Find the next row if available
                    if (currentIndex < allRows.length - 1) {
                        const nextRow = allRows[currentIndex + 1];
                        // Find the same input field in the next row
                        const nextInput = nextRow.querySelector('.lot-no-input');
                        if (nextInput) {
                            nextInput.focus();
                            return;
                        }
                    }
                }
            });
        }

        // Add event listeners to the new qty-input
        const qtyInput = newEntry.querySelector('.qty-input');
        if (qtyInput) {
            qtyInput.addEventListener('focus', function() {
                this.value = this.getAttribute('data-original-value') || this.value.replace(/,/g, '');
            });

            qtyInput.addEventListener('blur', function() {
                formatQtyDisplay(this);
            });
        }

        // Add event listener to the MC No input for machine lookup
        const mcNoInput = newEntry.querySelector('input[placeholder="MC No"]');
        if (mcNoInput) {
            mcNoInput.addEventListener('blur', function() {
                formatMcNo(this, true);
            });

            // Add keydown event listener for Enter key
            mcNoInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formatMcNo(this, true);
                    lookupMachineInfo(this);

                    // Find the current row and index
                    const currentRow = this.closest('tr');
                    const allRows = Array.from(currentRow.parentElement.querySelectorAll('tr'));
                    const currentIndex = allRows.indexOf(currentRow);

                    // Find the next row if available
                    if (currentIndex < allRows.length - 1) {
                        const nextRow = allRows[currentIndex + 1];
                        // Find the same input field in the next row
                        const nextInput = nextRow.querySelector('input[placeholder="MC No"]');
                        if (nextInput) {
                            nextInput.focus();
                            return;
                        }
                    }
                }
            });
        }
    }

    /**
     * Add a new submitted lot entry row
     */
    function addSubmittedLotEntry() {
        if (!submittedLotEntriesContainer) return;

        // Clone the first entry row
        const firstEntry = submittedLotEntriesContainer.querySelector('.submitted-lot-entry');
        if (!firstEntry) return;

        const newEntry = firstEntry.cloneNode(true);

        // Clear input values
        const inputs = newEntry.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
            // Clear any data attributes
            if (input.classList.contains('qty-input')) {
                input.removeAttribute('data-original-value');
            }
        });

        // Add the new entry to the container
        submittedLotEntriesContainer.appendChild(newEntry);

        // Update the cutoff display in the new row
        const cutoffDisplay = newEntry.querySelector('.current-cutoff-display');
        if (cutoffDisplay) {
            // Get the current cutoff time and map it to display format
            const currentCutoff = getCurrentCutoffTime();
            let cutoffText = '12NN'; // Default

            switch (currentCutoff) {
                case '00:00~04:00': cutoffText = '4AM'; break;
                case '04:00~07:00': cutoffText = '7AM'; break;
                case '07:00~12:00': cutoffText = '12NN'; break;
                case '12:00~16:00': cutoffText = '4PM'; break;
                case '16:00~19:00': cutoffText = '7PM'; break;
                case '19:00~00:00': cutoffText = '12MN'; break;
            }

            cutoffDisplay.textContent = cutoffText;

            // Also update the data-db-format attribute on the button
            const dateCutoffButton = newEntry.querySelector('.date-cutoff-dropdown .dropdown-toggle');
            if (dateCutoffButton) {
                dateCutoffButton.setAttribute('data-db-format', currentCutoff);
            }
        }

        // Add event listener to the new lot-no-input
        const lotNoInput = newEntry.querySelector('.lot-no-input');
        if (lotNoInput) {
            lotNoInput.addEventListener('blur', function() {
                lookupLotInfo(this);
            });

            // Add keydown event listener for Enter key
            lotNoInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    lookupLotInfo(this);

                    // Find the current row and index
                    const currentRow = this.closest('tr');
                    const allRows = Array.from(currentRow.parentElement.querySelectorAll('tr'));
                    const currentIndex = allRows.indexOf(currentRow);

                    // Find the next row if available
                    if (currentIndex < allRows.length - 1) {
                        const nextRow = allRows[currentIndex + 1];
                        // Find the same input field in the next row
                        const nextInput = nextRow.querySelector('.lot-no-input');
                        if (nextInput) {
                            nextInput.focus();
                            return;
                        }
                    }
                }
            });
        }

        // Add event listeners to the new qty-input
        const qtyInput = newEntry.querySelector('.qty-input');
        if (qtyInput) {
            qtyInput.addEventListener('focus', function() {
                this.value = this.getAttribute('data-original-value') || this.value.replace(/,/g, '');
            });

            qtyInput.addEventListener('blur', function() {
                formatQtyDisplay(this);
            });
        }

        // Add event listener to the MC No input for machine lookup
        const mcNoInput = newEntry.querySelector('input[placeholder="MC No"]');
        if (mcNoInput) {
            mcNoInput.addEventListener('blur', function() {
                formatMcNo(this, true);
                lookupMachineInfo(this);
            });

            // Add keydown event listener for Enter key
            mcNoInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formatMcNo(this, true);
                    lookupMachineInfo(this);

                    // Find the current row and index
                    const currentRow = this.closest('tr');
                    const allRows = Array.from(currentRow.parentElement.querySelectorAll('tr'));
                    const currentIndex = allRows.indexOf(currentRow);

                    // Find the next row if available
                    if (currentIndex < allRows.length - 1) {
                        const nextRow = allRows[currentIndex + 1];
                        // Find the same input field in the next row
                        const nextInput = nextRow.querySelector('input[placeholder="MC No"]');
                        if (nextInput) {
                            nextInput.focus();
                            return;
                        }
                    }
                }
            });
        }
    }

    /**
     * Delete an entry row
     * @param {HTMLElement} deleteButton - The delete button that was clicked
     */
    function deleteEntry(deleteButton) {
        // Find the parent row
        const row = deleteButton.closest('tr');
        if (!row) return;

        // Check if this is the only row
        const container = row.parentElement;
        if (container.children.length <= 1) {
            // Don't delete the last row, just clear its values
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                input.value = '';
                // Clear any data attributes
                if (input.classList.contains('qty-input')) {
                    input.removeAttribute('data-original-value');
                }
            });
            return;
        }

        // Remove the row
        row.remove();
    }

    /**
     * Update dropdown button text when an item is selected
     * @param {HTMLElement} item - The dropdown item that was clicked
     */
    function updateDropdownValue(item) {
        // Find the parent dropdown
        const dropdown = item.closest('.dropdown');
        if (!dropdown) return;

        // Update the button text
        const button = dropdown.querySelector('.dropdown-toggle');
        if (button) {
            button.textContent = item.textContent;

            // If the item has a data-db-format attribute, copy it to the button
            if (item.hasAttribute('data-db-format')) {
                const dbFormat = item.getAttribute('data-db-format');
                button.setAttribute('data-db-format', dbFormat);
                console.log('Copied data-db-format attribute from item:', dbFormat);
            } else {
                // Extract the cutoff time from the text (e.g., "2025-05-12 | 12NN")
                const text = item.textContent.trim();
                const parts = text.split('|');
                if (parts.length >= 2) {
                    const cutoffDisplay = parts[1].trim();

                    // Store the database format in a data attribute
                    // Map the cutoff display value to the database format
                    let dbFormat = '';
                    switch (cutoffDisplay) {
                        case '4AM':
                            dbFormat = '00:00~04:00';
                            break;
                        case '7AM':
                            dbFormat = '04:00~07:00';
                            break;
                        case '12NN':
                            dbFormat = '07:00~12:00';
                            break;
                        case '4PM':
                            dbFormat = '12:00~16:00';
                            break;
                        case '7PM':
                            dbFormat = '16:00~19:00';
                            break;
                        case '12MN':
                            dbFormat = '19:00~00:00';
                            break;
                        default:
                            dbFormat = '07:00~12:00'; // Default to 12NN
                    }

                    button.setAttribute('data-db-format', dbFormat);
                }
            }
        }

        // Update active class
        const dropdownItems = dropdown.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(dropdownItem => {
            dropdownItem.classList.remove('active');
        });
        item.classList.add('active');
    }

    /**
     * Format MC No input to ensure it follows the required format
     * Valid formats: VMI, VI followed by 3 digits (e.g., VI123)
     * If only digits are entered, they will be converted to VI format
     * @param {HTMLInputElement} input - The MC No input field
     * @param {boolean} forceFull - Whether to force full formatting on blur
     */
    function formatMcNo(input, forceFull = false) {
        if (!input) return;

        // Get the current value and cursor position
        const value = input.value.trim();
        const cursorPos = input.selectionStart;

        // If the value is empty, do nothing
        if (!value) return;

        // Check if the value is "VMI" or a variation of it (case insensitive)
        if (/^vmi$/i.test(value)) {
            input.value = 'VMI';

            // If we're forcing full format (on blur), look up the machine info
            if (forceFull) {
                lookupMachineInfo(input);
            }
            return;
        }

        // Check if the value starts with "VM" - likely trying to type "VMI"
        if (/^vm$/i.test(value)) {
            input.value = 'VMI';

            // Set cursor position at the end if user was typing
            if (!forceFull) {
                const newPos = input.value.length;
                input.setSelectionRange(newPos, newPos);
            } else {
                // If we're forcing full format (on blur), look up the machine info
                lookupMachineInfo(input);
            }
            return;
        }

        // Check if the value is just "V" - could be start of "VMI" or "VI"
        // When forcing full format (on blur), we'll assume "VI"
        if (value.toUpperCase() === 'V') {
            if (forceFull) {
                input.value = 'VI';
            } else {
                // Keep as is during typing
                input.value = 'V';
            }
            return;
        }

        // If the value starts with "VI" and is followed by digits
        if (/^VI\d{0,3}$/i.test(value)) {
            // Get the digits part
            const digits = value.substring(2);

            // If we're forcing full format (on blur) and digits are less than 3, pad with zeros
            if (forceFull && digits.length > 0 && digits.length < 3) {
                input.value = 'VI' + digits.padStart(3, '0');
                // Look up the machine info
                lookupMachineInfo(input);
            } else {
                // Just ensure VI is uppercase
                input.value = 'VI' + digits;

                // If we're forcing full format (on blur), look up the machine info
                if (forceFull) {
                    lookupMachineInfo(input);
                }
            }
            return;
        }

        // If the value is only digits
        if (/^\d+$/.test(value)) {
            // Extract up to 3 digits
            let digits = value.substring(0, 3);

            // If we're forcing full format (on blur) and digits are less than 3, pad with zeros
            if (forceFull && digits.length > 0 && digits.length < 3) {
                digits = digits.padStart(3, '0');
            }

            input.value = 'VI' + digits;

            // Set cursor position after the prefix if user was typing
            if (!forceFull) {
                const newPos = Math.min(cursorPos + 2, input.value.length);
                input.setSelectionRange(newPos, newPos);
            } else {
                // If we're forcing full format (on blur), look up the machine info
                lookupMachineInfo(input);
            }
            return;
        }

        // For any other format, try to extract digits and format properly
        const digits = value.replace(/\D/g, '').substring(0, 3);

        if (digits) {
            // If we're forcing full format (on blur) and digits are less than 3, pad with zeros
            if (forceFull && digits.length > 0 && digits.length < 3) {
                input.value = 'VI' + digits.padStart(3, '0');
            } else {
                input.value = 'VI' + digits;
            }

            // If we're forcing full format (on blur), look up the machine info
            if (forceFull) {
                lookupMachineInfo(input);
            }
        } else {
            // Check if the value might be trying to be "VMI"
            if (/^v.*m.*i.*$/i.test(value)) {
                input.value = 'VMI';

                // If we're forcing full format (on blur), look up the machine info
                if (forceFull) {
                    lookupMachineInfo(input);
                }
            } else {
                // If no digits found and not trying to be VMI, reset to empty
                input.value = '';
            }
        }
    }

    /**
     * Look up machine information from the database
     * @param {HTMLInputElement} input - The MC No input field
     */
    function lookupMachineInfo(input) {
        if (!input || !input.value.trim()) return;

        const mcNo = input.value.trim();
        const row = input.closest('tr');

        if (!row) return;

        // Find the Area input in the same row
        const areaInput = row.querySelector('.area-input');

        if (!areaInput) return;

        // Show loading state
        input.classList.add('is-loading');

        try {
            // Use direct AJAX call to the dedicated endpoint
            console.log('Making AJAX call to machine-lookup endpoint with mcNo:', mcNo);

            // Get the CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Make the AJAX call
            fetch('/api/machine-lookup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ mcNo: mcNo })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Machine lookup response:', data);

                // Handle the response
                if (data && data.success) {
                    // Update the Area input
                    areaInput.value = data.area || '';
                    console.log('Updated area field with:', data.area);
                } else {
                    // Show error message
                    console.error('Machine not found:', mcNo);

                    // Clear the Area input
                    areaInput.value = '';

                    // Show toast notification if SweetAlert2 is available
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Machine Not Found',
                            text: `The machine "${mcNo}" was not found in the database.`,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error looking up machine:', error);

                // Clear the Area input
                areaInput.value = '';

                // Show toast notification if SweetAlert2 is available
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while looking up the machine information.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .finally(() => {
                // Remove loading state
                input.classList.remove('is-loading');
            });
        } catch (error) {
            console.error('Error looking up machine:', error);

            // Clear the Area input
            areaInput.value = '';

            // Remove loading state
            input.classList.remove('is-loading');
        }
    }

    /**
     * Look up lot information from the database
     * @param {HTMLInputElement} input - The Lot No input field
     */
    function lookupLotInfo(input) {
        if (!input || !input.value.trim()) return;

        const lotId = input.value.trim();
        const row = input.closest('tr');

        if (!row) return;

        // Find the Model ID and Qty inputs in the same row
        const modelIdInput = row.querySelector('.model-id-input');
        const qtyInput = row.querySelector('.qty-input');

        if (!modelIdInput || !qtyInput) return;

        // Show loading state
        input.classList.add('is-loading');

        try {

            // Use direct AJAX call to the dedicated endpoint
            console.log('Making AJAX call to lot-lookup endpoint with lotId:', lotId);

            // Get the CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Make the AJAX call
            fetch('/api/lot-lookup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ lotId: lotId })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Lot lookup response:', data);

                // Handle the response
                if (data && data.success) {
                    // Update the Model ID and Qty inputs
                    modelIdInput.value = data.model_id || '';
                    qtyInput.value = data.lot_qty || '';
                    console.log('Updated fields with model:', data.model_id, 'qty:', data.lot_qty);
                } else {
                    // Show error message
                    console.error('Lot not found:', lotId);

                    // Clear the Model ID and Qty inputs
                    modelIdInput.value = '';
                    qtyInput.value = '';

                    // Show toast notification if SweetAlert2 is available
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Lot Not Found',
                            text: `The lot "${lotId}" was not found in the database.`,
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error looking up lot:', error);

                // Clear the Model ID and Qty inputs
                modelIdInput.value = '';
                qtyInput.value = '';

                // Show toast notification if SweetAlert2 is available
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        text: 'An error occurred while looking up the lot information.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .finally(() => {
                // Remove loading state
                input.classList.remove('is-loading');
            });
        } catch (error) {
            console.error('Error looking up lot:', error);

            // Clear the Model ID and Qty inputs
            modelIdInput.value = '';
            qtyInput.value = '';

            // Show toast notification if SweetAlert2 is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while looking up the lot information.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                // Show a simple alert if SweetAlert2 is not available
                alert('Error: An error occurred while looking up the lot information.');
            }

            // Remove loading state
            input.classList.remove('is-loading');
        }

        // Set a timeout to remove the loading state in case the event is never received
        setTimeout(() => {
            if (input.classList.contains('is-loading')) {
                console.warn('Timeout reached while waiting for lot lookup response');
                input.classList.remove('is-loading');
            }
        }, 5000); // 5 seconds timeout
    }

    /**
     * Format quantity display with commas
     * @param {HTMLInputElement} input - The quantity input field
     */
    function formatQtyDisplay(input) {
        if (!input) return;

        const value = input.value.trim();
        if (!value) return;

        // Store the original value as a data attribute
        input.setAttribute('data-original-value', value);

        // Format with commas
        const numValue = parseInt(value.replace(/,/g, ''), 10);
        if (!isNaN(numValue)) {
            input.value = numValue.toLocaleString();
        }
    }

    /**
     * Validate entries before submission
     * @param {string} entryType - The type of entry to validate ('endtime' or 'submitted')
     * @returns {Object} Validation result with status and message
     */
    function validateEndtimeEntries(entryType = 'endtime') {
        // Determine which entries to validate based on the type
        const selector = entryType === 'submitted' ? '.submitted-lot-entry' : '.endtime-entry';
        const entries = document.querySelectorAll(selector);
        const invalidFields = [];
        let hasEntries = false;

        entries.forEach((entry, index) => {
            const lotNoInput = entry.querySelector('.lot-no-input');
            const mcNoInput = entry.querySelector('input[placeholder="MC No"]');
            const modelIdInput = entry.querySelector('.model-id-input');
            const qtyInput = entry.querySelector('.qty-input');
            const lotTypeSelect = entry.querySelector('.lottype-select');
            const dateCutoffButton = entry.querySelector('.date-cutoff-dropdown .dropdown-toggle');
            const areaInput = entry.querySelector('.area-input');

            // Skip empty rows (all fields empty)
            if ((!lotNoInput || !lotNoInput.value.trim()) &&
                (!mcNoInput || !mcNoInput.value.trim()) &&
                (!modelIdInput || !modelIdInput.value.trim()) &&
                (!qtyInput || !qtyInput.value.trim())) {
                return;
            }

            hasEntries = true;

            // Check required fields
            if (!lotNoInput || !lotNoInput.value.trim()) {
                invalidFields.push({ field: 'Lot No', row: index + 1 });
                lotNoInput?.classList.add('is-invalid');
            } else {
                lotNoInput.classList.remove('is-invalid');
            }

            if (!mcNoInput || !mcNoInput.value.trim()) {
                invalidFields.push({ field: 'MC No', row: index + 1 });
                mcNoInput?.classList.add('is-invalid');
            } else {
                mcNoInput.classList.remove('is-invalid');
            }

            if (!modelIdInput || !modelIdInput.value.trim()) {
                invalidFields.push({ field: 'Model ID', row: index + 1 });
                modelIdInput?.classList.add('is-invalid');
            } else {
                modelIdInput.classList.remove('is-invalid');
            }

            if (!qtyInput || !qtyInput.value.trim()) {
                invalidFields.push({ field: 'Qty', row: index + 1 });
                qtyInput?.classList.add('is-invalid');
            } else {
                qtyInput.classList.remove('is-invalid');
            }

            if (!lotTypeSelect || !lotTypeSelect.value) {
                invalidFields.push({ field: 'Lot Type', row: index + 1 });
                lotTypeSelect?.classList.add('is-invalid');
            } else {
                lotTypeSelect.classList.remove('is-invalid');
            }

            if (!dateCutoffButton || !dateCutoffButton.textContent.trim()) {
                invalidFields.push({ field: 'Date & Cutoff', row: index + 1 });
                dateCutoffButton?.classList.add('is-invalid');
            } else {
                dateCutoffButton.classList.remove('is-invalid');
            }

            if (!areaInput || !areaInput.value.trim()) {
                invalidFields.push({ field: 'Area', row: index + 1 });
                areaInput?.classList.add('is-invalid');
            } else {
                areaInput.classList.remove('is-invalid');
            }
        });

        if (!hasEntries) {
            return { valid: false, message: 'No entries to save. Please add at least one entry.' };
        }

        if (invalidFields.length > 0) {
            const message = `Please fill in all required fields: ${invalidFields.map(f => `${f.field} in row ${f.row}`).join(', ')}`;
            return { valid: false, message };
        }

        return { valid: true };
    }

    /**
     * Get current cutoff time based on Manila time
     * @returns {string} Current cutoff time in database format (e.g., '19:00~00:00')
     */
    function getCurrentCutoffTime() {
        // Get current time in Manila
        const now = new Date();
        const manilaOptions = { timeZone: "Asia/Manila" };
        const manilaDateString = now.toLocaleString("en-US", manilaOptions);
        const manilaDate = new Date(manilaDateString);

        const hours = manilaDate.getHours();
        const minutes = manilaDate.getMinutes();
        const currentTime = hours * 60 + minutes; // Convert to minutes for easier comparison

        // Determine which cutoff period we're currently in
        let currentCutoff = "07:00~12:00"; // Default to 12NN

        if (currentTime >= 0 && currentTime < 240) {
            // 00:00~04:00 (4AM)
            currentCutoff = "00:00~04:00";
        } else if (currentTime >= 240 && currentTime < 420) {
            // 04:00~07:00 (7AM)
            currentCutoff = "04:00~07:00";
        } else if (currentTime >= 420 && currentTime < 720) {
            // 07:00~12:00 (12NN)
            currentCutoff = "07:00~12:00";
        } else if (currentTime >= 720 && currentTime < 960) {
            // 12:00~16:00 (4PM)
            currentCutoff = "12:00~16:00";
        } else if (currentTime >= 960 && currentTime < 1140) {
            // 16:00~19:00 (7PM)
            currentCutoff = "16:00~19:00";
        } else if (currentTime >= 1140 && currentTime < 1440) {
            // 19:00~00:00 (12MN)
            currentCutoff = "19:00~00:00";
        }

        return currentCutoff;
    }

    /**
     * Collect entries data for submission
     * @param {string} entryType - The type of entry to collect ('endtime' or 'submitted')
     * @returns {Array} Array of entry objects
     */
    function collectEndtimeEntries(entryType = 'endtime') {
        // Determine which entries to collect based on the type
        const selector = entryType === 'submitted' ? '.submitted-lot-entry' : '.endtime-entry';
        const entries = document.querySelectorAll(selector);
        const data = [];

        entries.forEach(entry => {
            const lotNoInput = entry.querySelector('.lot-no-input');
            const mcNoInput = entry.querySelector('input[placeholder="MC No"]');
            const modelIdInput = entry.querySelector('.model-id-input');
            const qtyInput = entry.querySelector('.qty-input');
            const lotTypeSelect = entry.querySelector('.lottype-select');
            const areaInput = entry.querySelector('.area-input');
            const dateCutoffButton = entry.querySelector('.date-cutoff-dropdown .dropdown-toggle');

            // Skip empty rows
            if ((!lotNoInput || !lotNoInput.value.trim()) &&
                (!mcNoInput || !mcNoInput.value.trim()) &&
                (!modelIdInput || !modelIdInput.value.trim()) &&
                (!qtyInput || !qtyInput.value.trim())) {
                return;
            }

            // Parse date and cutoff from the button text
            let endtimeDate = '';
            let cutoffTime = '';
            if (dateCutoffButton) {
                // Check if the data-db-format attribute is set
                if (dateCutoffButton.hasAttribute('data-db-format')) {
                    // Use the stored database format
                    cutoffTime = dateCutoffButton.getAttribute('data-db-format');

                    // Special handling for 12MN
                    const buttonText = dateCutoffButton.textContent.trim();
                    if (buttonText.includes('12MN')) {
                        // Force the correct mapping for 12MN
                        cutoffTime = '19:00~00:00';
                    }
                }

                // Parse the date from the button text
                if (dateCutoffButton.textContent.trim()) {
                    const parts = dateCutoffButton.textContent.trim().split('|');
                    if (parts.length >= 1) {
                        // Format the date as YYYY-MM-DD
                        const dateText = parts[0].trim();
                        const dateParts = dateText.split('-');
                        if (dateParts.length === 3) {
                            // Ensure the date is in YYYY-MM-DD format
                            endtimeDate = `${dateParts[0]}-${dateParts[1].padStart(2, '0')}-${dateParts[2].padStart(2, '0')}`;
                        } else {
                            // Try to parse the date in case it's in a different format
                            const dateObj = new Date(dateText);
                            if (!isNaN(dateObj.getTime())) {
                                const year = dateObj.getFullYear();
                                const month = (dateObj.getMonth() + 1).toString().padStart(2, '0');
                                const day = dateObj.getDate().toString().padStart(2, '0');
                                endtimeDate = `${year}-${month}-${day}`;
                            } else {
                                console.error('Invalid date format:', dateText);
                                endtimeDate = dateText; // Use as-is if parsing fails
                            }
                        }
                    }

                    // If we still don't have a cutoff time, try to extract it from the text
                    if (!cutoffTime && parts.length >= 2) {
                        const cutoffDisplay = parts[1].trim();

                        // Map the cutoff display value to the database format
                        switch (cutoffDisplay) {
                            case '4AM':
                                cutoffTime = '00:00~04:00';
                                break;
                            case '7AM':
                                cutoffTime = '04:00~07:00';
                                break;
                            case '12NN':
                                cutoffTime = '07:00~12:00';
                                break;
                            case '4PM':
                                cutoffTime = '12:00~16:00';
                                break;
                            case '7PM':
                                cutoffTime = '16:00~19:00';
                                break;
                            case '12MN':
                                cutoffTime = '19:00~00:00';
                                break;
                            default:
                                // Default to 12NN
                                cutoffTime = '07:00~12:00';
                        }
                    }
                }
            }

            // If we still don't have a cutoff time, use the current cutoff time based on Manila time
            if (!cutoffTime) {
                if (entryType === 'submitted') {
                    // For submitted entries, always use the current cutoff time
                    cutoffTime = getCurrentCutoffTime();
                    console.log('Using current cutoff time for submitted entry:', cutoffTime);
                } else {
                    // For endtime entries, default to 12NN
                    cutoffTime = '07:00~12:00';
                }
            }

            // Get the original quantity value without commas
            let lotQty = qtyInput ? qtyInput.value.trim().replace(/,/g, '') : '';
            lotQty = lotQty ? parseInt(lotQty, 10) : 0;

            // Final check for 12MN cutoff time
            if (dateCutoffButton && dateCutoffButton.textContent.includes('12MN') && cutoffTime !== '19:00~00:00') {
                cutoffTime = '19:00~00:00';
            }

            // For submitted entries, ensure we're using the current cutoff time
            if (entryType === 'submitted') {
                cutoffTime = getCurrentCutoffTime();
            }

            // Create the data object with common fields
            const entryData = {
                lot_id: lotNoInput ? lotNoInput.value.trim() : '',
                mc_no: mcNoInput ? mcNoInput.value.trim() : '',
                model_id: modelIdInput ? modelIdInput.value.trim() : '',
                lot_qty: lotQty,
                lot_type: lotTypeSelect ? lotTypeSelect.value : '',
                area: areaInput ? areaInput.value.trim() : '',
                endtime_date: endtimeDate,
                cutoff_time: cutoffTime
            };

            // Add type-specific fields
            if (entryType === 'submitted') {
                entryData.status = 'SUBMITTED'; // Default status for submitted lots
            }

            data.push(entryData);
        });

        return data;
    }

    /**
     * Reset the endtime form to its initial state
     */
    function resetEndtimeForm() {
        const container = document.getElementById('endtime-entries');
        if (!container) return;

        // Keep only the first row and clear its values
        const rows = container.querySelectorAll('.endtime-entry');
        if (rows.length === 0) return;

        // Keep the first row
        const firstRow = rows[0];

        // Remove all other rows
        for (let i = 1; i < rows.length; i++) {
            rows[i].remove();
        }

        // Clear values in the first row
        const inputs = firstRow.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
            input.classList.remove('is-invalid');
            // Clear any data attributes
            if (input.classList.contains('qty-input')) {
                input.removeAttribute('data-original-value');
            }
        });

        // Reset select elements
        const selects = firstRow.querySelectorAll('select');
        selects.forEach(select => {
            select.selectedIndex = 0;
            select.classList.remove('is-invalid');
        });

        // Reset dropdown buttons
        const dropdownButtons = firstRow.querySelectorAll('.dropdown-toggle');
        dropdownButtons.forEach(button => {
            button.classList.remove('is-invalid');
            // Don't reset the text as it should show the current date and cutoff
        });

        console.log('Endtime form has been reset');
    }

    /**
     * Save endtime entries to the database
     */
    function saveEndtime() {
        // Validate entries
        const validation = validateEndtimeEntries('endtime'); // Explicitly pass 'endtime' as the entry type
        if (!validation.valid) {
            // Show error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Validation Error',
                    text: validation.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Validation Error: ' + validation.message);
            }
            return;
        }

        // Collect entries data
        const entries = collectEndtimeEntries('endtime'); // Explicitly pass 'endtime' as the entry type
        if (entries.length === 0) {
            // Show error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'No Entries',
                    text: 'No entries to save. Please add at least one entry.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('No entries to save. Please add at least one entry.');
            }
            return;
        }

        // Show loading state
        const saveButton = document.querySelector('#addEndtimeModal .modal-footer .btn-primary');
        if (saveButton) {
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        }

        // Get the CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Make the AJAX call to save entries
        fetch('/api/save-endtime', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ entries })
        })
        .then(response => {
            // First try to get the response as JSON, even if status is not 200
            return response.json().then(data => {
                // If response is not ok, throw an error with the error message from the server
                if (!response.ok) {
                    const errorMessage = data && data.message
                        ? data.message
                        : 'Network response was not ok: ' + response.status;
                    throw new Error(errorMessage);
                }
                return data;
            }).catch(err => {
                // If we can't parse the response as JSON, throw the original error
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                throw err;
            });
        })
        .then(data => {
            console.log('Save endtime response:', data);

            // Handle the response
            if (data && data.success) {
                // Check if there were any duplicates
                let message = `Successfully saved ${data.saved_count} endtime entries.`;
                if (data.duplicate_count > 0) {
                    message += `\n${data.duplicate_count} duplicate entries were skipped.`;

                    // Log duplicate details
                    console.warn('Duplicate entries detected:', data.duplicates);

                    // Format duplicate details for display
                    const duplicateDetails = data.duplicates.map(d =>
                        `MC No: ${d.mc_no} (already has lot: ${d.existing_lot_id})`
                    ).join('\n');

                    message += `\n\nDuplicates:\n${duplicateDetails}`;
                }

                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success',
                        html: message.replace(/\n/g, '<br>'),
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reset the form
                        resetEndtimeForm();

                        // Refresh the dashboard data
                        if (typeof Livewire !== 'undefined') {
                            Livewire.dispatch('refreshData');
                        }
                    });
                } else {
                    alert(`Success: ${message}`);

                    // Reset the form
                    resetEndtimeForm();

                    // Refresh the dashboard data
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('refreshData');
                    }
                }
            } else {
                // Show error message
                let errorMessage = 'Failed to save endtime entries.';
                if (data.error_count > 0 && data.errors) {
                    errorMessage += ' Errors: ' + data.errors.map(e => e.message).join(', ');
                }

                // Add duplicate information if any
                if (data.duplicate_count > 0) {
                    errorMessage += `\n${data.duplicate_count} duplicate entries were detected.`;

                    // Format duplicate details for display
                    const duplicateDetails = data.duplicates.map(d => {
                        let message = `MC No: ${d.mc_no} (already has lot: ${d.existing_lot_id})`;
                        if (d.qty_class) {
                            message += ` - Lot size class: ${d.qty_class}`;
                            if (d.qty_class === 'large') {
                                message += ' (Cannot add multiple large lots to same machine)';
                            }
                        }
                        return message;
                    }).join('\n');

                    errorMessage += `\n\nDuplicates:\n${duplicateDetails}`;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        html: errorMessage.replace(/\n/g, '<br>'),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Error: ' + errorMessage);
                }
            }
        })
        .catch(error => {
            console.error('Error saving endtime entries:', error);

            // Show error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while saving the endtime entries: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Error: An error occurred while saving the endtime entries: ' + error.message);
            }
        })
        .finally(() => {
            // Restore save button state
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.textContent = 'Save Endtime';
            }
        });
    }

    /**
     * Reset the submitted lot form to its initial state
     */
    function resetSubmittedLotForm() {
        const container = document.getElementById('submitted-lot-entries');
        if (!container) return;

        // Keep only the first row and clear its values
        const rows = container.querySelectorAll('.submitted-lot-entry');
        if (rows.length === 0) return;

        // Keep the first row
        const firstRow = rows[0];

        // Remove all other rows
        for (let i = 1; i < rows.length; i++) {
            rows[i].remove();
        }

        // Clear values in the first row
        const inputs = firstRow.querySelectorAll('input');
        inputs.forEach(input => {
            input.value = '';
            input.classList.remove('is-invalid');
            // Clear any data attributes
            if (input.classList.contains('qty-input')) {
                input.removeAttribute('data-original-value');
            }
        });

        // Reset select elements
        const selects = firstRow.querySelectorAll('select');
        selects.forEach(select => {
            select.selectedIndex = 0;
            select.classList.remove('is-invalid');
        });

        // Reset dropdown buttons
        const dropdownButtons = firstRow.querySelectorAll('.dropdown-toggle');
        dropdownButtons.forEach(button => {
            button.classList.remove('is-invalid');
            // Don't reset the text as it should show the current date and cutoff
        });

        console.log('Submitted lot form has been reset');
    }

    /**
     * Save submitted lot entries to the database
     */
    function saveSubmitted() {
        // Clear any existing SweetAlert dialogs to prevent stale data
        if (typeof Swal !== 'undefined' && Swal.isVisible()) {
            Swal.close();
        }

        // Validate entries
        const validation = validateEndtimeEntries('submitted'); // Pass 'submitted' as the entry type
        if (!validation.valid) {
            // Show error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Validation Error',
                    text: validation.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Validation Error: ' + validation.message);
            }
            return;
        }

        // Collect entries data
        const entries = collectEndtimeEntries('submitted'); // Pass 'submitted' as the entry type
        if (entries.length === 0) {
            // Show error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'No Entries',
                    text: 'No entries to save. Please add at least one entry.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('No entries to save. Please add at least one entry.');
            }
            return;
        }

        // Show loading state
        const saveButton = document.querySelector('#addSubmittedLotModal .modal-footer .btn-primary');
        if (saveButton) {
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        }

        // Get the CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Make the AJAX call to save entries
        fetch('/api/save-submitted', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ entries })
        })
        .then(response => {
            // First try to get the response as JSON, even if status is not 200
            return response.json().then(data => {
                // If response is not ok, throw an error with the error message from the server
                if (!response.ok) {
                    const errorMessage = data && data.message
                        ? data.message
                        : 'Network response was not ok: ' + response.status;
                    throw new Error(errorMessage);
                }
                return data;
            }).catch(err => {
                // If we can't parse the response as JSON, throw the original error
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                throw err;
            });
        })
        .then(data => {
            console.log('Save submitted response:', data);

            // Handle the response
            if (data && data.success) {
                // Calculate total processed entries (new + updated)
                const totalProcessed = (data.saved_count || 0) + (data.updated_count || 0);

                // Check if there were any duplicates
                let message = `Successfully saved ${totalProcessed} submitted lot entries.`;

                // Add details about new vs updated entries if both exist
                if (data.saved_count > 0 && data.updated_count > 0) {
                    message += `\n(${data.saved_count} new entries, ${data.updated_count} updated from PENDING to SUBMITTED)`;
                }

                if (data.duplicate_count > 0) {
                    message += `\n${data.duplicate_count} duplicate entries were skipped.`;

                    // Log duplicate details
                    console.warn('Duplicate entries detected:', data.duplicates);

                    // Format duplicate details for display
                    const duplicateDetails = data.duplicates.map(d =>
                        `Lot ID: ${d.lot_id}, MC No: ${d.mc_no} (status: ${d.existing_status})`
                    ).join('\n');

                    message += `\n\nDuplicates:\n${duplicateDetails}`;
                }

                // Show success message
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Success',
                        html: message.replace(/\n/g, '<br>'),
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reset the form
                        resetSubmittedLotForm();

                        // Refresh the dashboard data
                        if (typeof Livewire !== 'undefined') {
                            Livewire.dispatch('refreshData');
                        }
                    });
                } else {
                    alert(`Success: ${message}`);

                    // Reset the form
                    resetSubmittedLotForm();

                    // Refresh the dashboard data
                    if (typeof Livewire !== 'undefined') {
                        Livewire.dispatch('refreshData');
                    }
                }
            } else {
                // Show error message
                let errorMessage = 'Failed to save submitted lot entries.';
                if (data.error_count > 0 && data.errors) {
                    errorMessage += ' Errors: ' + data.errors.map(e => e.message).join(', ');
                }

                // Add duplicate information if any
                if (data.duplicate_count > 0) {
                    errorMessage += `\n${data.duplicate_count} duplicate entries were detected.`;

                    // Format duplicate details for display
                    const duplicateDetails = data.duplicates.map(d => {
                        let message = `MC No: ${d.mc_no} (already has lot: ${d.existing_lot_id})`;
                        if (d.qty_class) {
                            message += ` - Lot size class: ${d.qty_class}`;
                            if (d.qty_class === 'large') {
                                message += ' (Cannot add multiple large lots to same machine)';
                            }
                        }
                        return message;
                    }).join('\n');

                    errorMessage += `\n\nDuplicates:\n${duplicateDetails}`;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Error',
                        html: errorMessage.replace(/\n/g, '<br>'),
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Error: ' + errorMessage);
                }
            }
        })
        .catch(error => {
            console.error('Error saving submitted lot entries:', error);

            // Show error message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while saving the submitted lot entries: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                alert('Error: An error occurred while saving the submitted lot entries: ' + error.message);
            }
        })
        .finally(() => {
            // Restore save button state
            if (saveButton) {
                saveButton.disabled = false;
                saveButton.textContent = 'Save Submitted';
            }
        });
    }

    /**
     * Update the cutoff time display in the submitted lot modal based on current Manila time
     */
    function updateSubmittedCutoffDisplay() {
        // Get the current cutoff time
        const currentCutoff = getCurrentCutoffTime();

        // Map the database format to display format
        let cutoffDisplay = '12NN'; // Default
        switch (currentCutoff) {
            case '00:00~04:00':
                cutoffDisplay = '4AM';
                break;
            case '04:00~07:00':
                cutoffDisplay = '7AM';
                break;
            case '07:00~12:00':
                cutoffDisplay = '12NN';
                break;
            case '12:00~16:00':
                cutoffDisplay = '4PM';
                break;
            case '16:00~19:00':
                cutoffDisplay = '7PM';
                break;
            case '19:00~00:00':
                cutoffDisplay = '12MN';
                break;
        }

        // Update all cutoff display elements in the submitted lot modal
        const cutoffDisplayElements = document.querySelectorAll('#addSubmittedLotModal .current-cutoff-display');
        cutoffDisplayElements.forEach(element => {
            element.textContent = cutoffDisplay;
        });

        // Also update the data-db-format attribute on the buttons
        const dateCutoffButtons = document.querySelectorAll('#addSubmittedLotModal .date-cutoff-dropdown .dropdown-toggle');
        dateCutoffButtons.forEach(button => {
            button.setAttribute('data-db-format', currentCutoff);
        });

        console.log('Updated submitted lot cutoff display to:', cutoffDisplay, '(', currentCutoff, ')');
    }

    // Initialize when the DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        init();

        // Add event listener to the Save Endtime button
        const saveEndtimeBtn = document.querySelector('#addEndtimeModal .modal-footer .btn-primary');
        if (saveEndtimeBtn) {
            saveEndtimeBtn.addEventListener('click', saveEndtime);
        }

        // Add event listener to the Save Submitted button
        const saveSubmittedBtn = document.querySelector('#addSubmittedLotModal .modal-footer .btn-primary');
        if (saveSubmittedBtn) {
            saveSubmittedBtn.addEventListener('click', saveSubmitted);
        }

        // Set up an interval to update the submitted cutoff display every minute
        setInterval(updateSubmittedCutoffDisplay, 60000); // 60000 ms = 1 minute
    });

    // Also initialize when Livewire updates the DOM
    document.addEventListener('livewire:initialized', function() {
        init();

        // Add event listener to the Save Endtime button
        const saveEndtimeBtn = document.querySelector('#addEndtimeModal .modal-footer .btn-primary');
        if (saveEndtimeBtn) {
            saveEndtimeBtn.addEventListener('click', saveEndtime);
        }

        // Add event listener to the Save Submitted button
        const saveSubmittedBtn = document.querySelector('#addSubmittedLotModal .modal-footer .btn-primary');
        if (saveSubmittedBtn) {
            saveSubmittedBtn.addEventListener('click', saveSubmitted);
        }
    });
    document.addEventListener('livewire:load', init);

    // Add event listener for when the submitted lot modal is shown
    document.addEventListener('DOMContentLoaded', function() {
        const submittedLotModal = document.getElementById('addSubmittedLotModal');
        if (submittedLotModal) {
            submittedLotModal.addEventListener('show.bs.modal', function() {
                // Update the cutoff display when the modal is opened
                updateSubmittedCutoffDisplay();
            });
        }
    });

})();
