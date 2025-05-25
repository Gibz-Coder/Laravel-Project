<div>
    <div class="card custom-card">
        <div class="card-header">
            <h6 class="mb-0 fw-bold fs-18">Line Progress Status</h6>
        </div>
        <div class="card-body">
            <div wire:ignore>
                <div id="top-categories" style="height: 394px;"></div>
            </div>
            <div class="row mt-3" wire:ignore>
                <div class="col-4 text-center">
                    <h6 class="mb-1 fw-bold">Endtime</h6>
                    <h3 class="mb-0 fw-bold text-primary" id="progress-endtime-value">0.0M</h3>
                </div>
                <div class="col-4 text-center">
                    <h6 class="mb-1 fw-bold">Submitted</h6>
                    <h3 class="mb-0 fw-bold text-success" id="progress-submitted-value">0.0M</h3>
                </div>
                <div class="col-4 text-center">
                    <h6 class="mb-1 fw-bold">Remaining</h6>
                    <h3 class="mb-0 fw-bold text-danger" id="progress-remaining-value">0.0M</h3>
                </div>
            </div>
        </div>
    </div>

    <script wire:ignore>
    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', function() {
        createDonutChart();
    });

    // Create the donut chart
    function createDonutChart() {
        // Get values directly from the card components
        const endtimeElement = document.getElementById('endtime-total');
        const submittedElement = document.getElementById('submitted-total');
        const remainingElement = document.getElementById('remaining-total');

        // Extract numeric values (removing 'M' suffix and commas, then converting back to full number)
        let endtimeValue = endtimeElement ? parseFloat(endtimeElement.textContent.replace(/,/g, '').replace('M', '')) * 1000000 : 0;
        let submittedValue = submittedElement ? parseFloat(submittedElement.textContent.replace(/,/g, '').replace('M', '')) * 1000000 : 0;
        let remainingValue = remainingElement ? parseFloat(remainingElement.textContent.replace(/,/g, '').replace('M', '')) * 1000000 : 0;

        // Update the progress chart text values
        document.getElementById('progress-endtime-value').textContent = endtimeElement ? endtimeElement.textContent : '0.0M';
        document.getElementById('progress-submitted-value').textContent = submittedElement ? submittedElement.textContent : '0.0M';
        document.getElementById('progress-remaining-value').textContent = remainingElement ? remainingElement.textContent : '0.0M';

        // Create chart options
        var options = {
            series: [endtimeValue, submittedValue, remainingValue],
            labels: ["Endtime", "Submitted", "Remaining"],
            chart: {
                id: 'progress-donut-chart',
                height: 394,
                type: "donut",
                animations: {
                    enabled: true // Disable animations for faster rendering
                },
                redrawOnWindowResize: false // Prevent unnecessary redraws
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        // Format with commas and 1 decimal place
                        return ((value / 1000000).toFixed(1)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + 'M';
                    }
                }
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            plotOptions: {
                pie: {
                    expandOnClick: false,
                    donut: {
                        size: "60%",
                        background: "transparent",
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: "20px",
                                color: "#495057",
                                fontFamily: "Montserrat, sans-serif",
                                offsetY: -5,
                            },
                            value: {
                                show: true,
                                fontSize: "22px",
                                color: undefined,
                                offsetY: 5,
                                fontWeight: 600,
                                fontFamily: "Montserrat, sans-serif",
                                formatter: function (val) {
                                    // Format with commas and 1 decimal place
                                    return ((val / 1000000).toFixed(1)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + 'M';
                                },
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: "Progress",
                                fontSize: "14px",
                                fontWeight: 400,
                                color: "#495057",
                                formatter: function (w) {
                                    const totalEndtime = w.globals.seriesTotals[0];
                                    const totalSubmitted = w.globals.seriesTotals[1];
                                    const progressPercentage = totalEndtime > 0
                                        ? Math.round((totalSubmitted / totalEndtime) * 100)
                                        : 0;
                                    return progressPercentage + "%";
                                }
                            },
                        },
                    },
                },
            },
            colors: [
                "var(--primary-color)",
                "rgba(12, 199, 99, 1)",
                "rgba(255, 90, 41, 1)",
            ],
        };

        // Check if chart already exists
        if (window.ApexCharts.getChartByID('progress-donut-chart')) {
            // Update existing chart instead of destroying and recreating
            window.ApexCharts.getChartByID('progress-donut-chart').updateSeries([
                endtimeValue, submittedValue, remainingValue
            ], true); // true = animate the update

            // Also update the chart options to ensure the formatter uses the latest data
            window.ApexCharts.getChartByID('progress-donut-chart').updateOptions({
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                total: {
                                    formatter: function (w) {
                                        const totalEndtime = w.globals.seriesTotals[0];
                                        const totalSubmitted = w.globals.seriesTotals[1];
                                        const progressPercentage = totalEndtime > 0
                                            ? Math.round((totalSubmitted / totalEndtime) * 100)
                                            : 0;
                                        return progressPercentage + "%";
                                    }
                                }
                            }
                        }
                    }
                }
            }, false, true); // false = don't redraw, true = animate
        } else {
            // Create new chart
            window.progressDonutChart = new ApexCharts(
                document.querySelector("#top-categories"),
                options
            );
            window.progressDonutChart.render();
        }
    }

    // Listen for Livewire events
    document.addEventListener('livewire:initialized', function() {
        // Function to update the progress chart from card values
        function updateProgressChartFromCards() {
            // Get values directly from the card components
            const endtimeElement = document.getElementById('endtime-total');
            const submittedElement = document.getElementById('submitted-total');
            const remainingElement = document.getElementById('remaining-total');

            // Check if any card is in loading state
            if (isLoading(endtimeElement) || isLoading(submittedElement) || isLoading(remainingElement)) {
                // Don't update with partial data during loading
                return;
            }

            if (endtimeElement && submittedElement && remainingElement) {
                // Extract numeric values (removing 'M' suffix and converting back to full number)
                let endtimeText = endtimeElement.textContent.trim();
                let submittedText = submittedElement.textContent.trim();
                let remainingText = remainingElement.textContent.trim();

                // Check if we have valid text (not loading spinners)
                if (endtimeText.includes('M') && submittedText.includes('M') && remainingText.includes('M')) {
                    let endtimeValue = parseFloat(endtimeText.replace(/,/g, '').replace('M', '')) * 1000000;
                    let submittedValue = parseFloat(submittedText.replace(/,/g, '').replace('M', '')) * 1000000;
                    let remainingValue = parseFloat(remainingText.replace(/,/g, '').replace('M', '')) * 1000000;

                    // Update the progress chart text values
                    document.getElementById('progress-endtime-value').textContent = endtimeText;
                    document.getElementById('progress-submitted-value').textContent = submittedText;
                    document.getElementById('progress-remaining-value').textContent = remainingText;

                    // Update the chart
                    if (window.ApexCharts.getChartByID('progress-donut-chart')) {
                        window.ApexCharts.getChartByID('progress-donut-chart').updateSeries([
                            endtimeValue, submittedValue, remainingValue
                        ], true); // true = animate
                    } else {
                        // If chart doesn't exist yet, create it
                        createDonutChart();
                    }
                }
            }
        }

        // Set up a MutationObserver to watch for changes in the card values
        const observer = new MutationObserver(function(mutations) {
            // Add a small delay to ensure values are fully updated
            setTimeout(updateProgressChartFromCards, 100);
        });

        // Start observing the card elements
        const endtimeElement = document.getElementById('endtime-total');
        const submittedElement = document.getElementById('submitted-total');
        const remainingElement = document.getElementById('remaining-total');

        if (endtimeElement) {
            observer.observe(endtimeElement, {
                childList: true,
                characterData: true,
                subtree: true
            });
        }
        if (submittedElement) {
            observer.observe(submittedElement, {
                childList: true,
                characterData: true,
                subtree: true
            });
        }
        if (remainingElement) {
            observer.observe(remainingElement, {
                childList: true,
                characterData: true,
                subtree: true
            });
        }

        // Initial update with a slight delay to ensure cards are loaded
        setTimeout(function() {
            updateProgressChartFromCards();
        }, 500);

        // Function to check if an element is in loading state
        function isLoading(element) {
            return element && element.innerHTML.includes('spinner-border');
        }

        // Function to update progress chart when card elements are in loading state
        function updateProgressChartLoadingState() {
            const endtimeElement = document.getElementById('endtime-total');
            const submittedElement = document.getElementById('submitted-total');
            const remainingElement = document.getElementById('remaining-total');

            // Check if any card is in loading state
            if (isLoading(endtimeElement) || isLoading(submittedElement) || isLoading(remainingElement)) {
                // Show loading state in progress chart
                document.getElementById('progress-endtime-value').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                document.getElementById('progress-submitted-value').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                document.getElementById('progress-remaining-value').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                // Update chart with minimal values during loading
                if (window.ApexCharts.getChartByID('progress-donut-chart')) {
                    window.ApexCharts.getChartByID('progress-donut-chart').updateSeries([1, 1, 1], true);
                }
            }
        }

        // Add loading state check to the observer
        const loadingObserver = new MutationObserver(function(mutations) {
            updateProgressChartLoadingState();
        });

        // Observe the card container for loading state changes
        const cardContainer = document.querySelector('.row');
        if (cardContainer) {
            loadingObserver.observe(cardContainer, { childList: true, subtree: true });
        }

        // Listen for filter change events to update loading state
        ['dateChanged', 'cutoffChanged', 'worktypeChanged', 'lottypeChanged', 'refreshData'].forEach(function(eventName) {
            Livewire.on(eventName, function() {
                // Show loading state immediately in both cards and progress chart
                const endtimeElement = document.getElementById('endtime-total');
                const submittedElement = document.getElementById('submitted-total');
                const remainingElement = document.getElementById('remaining-total');

                // Show loading state in cards
                if (endtimeElement) endtimeElement.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                if (submittedElement) submittedElement.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                if (remainingElement) remainingElement.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                // Show loading state in progress chart
                document.getElementById('progress-endtime-value').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                document.getElementById('progress-submitted-value').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                document.getElementById('progress-remaining-value').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

                // Update chart with minimal values during loading
                if (window.ApexCharts.getChartByID('progress-donut-chart')) {
                    window.ApexCharts.getChartByID('progress-donut-chart').updateSeries([1, 1, 1], true);
                }

                // Set a timeout to check for data updates after loading
                setTimeout(function() {
                    updateProgressChartFromCards();
                }, 1000);
            });
        });
    });
    </script>
</div>