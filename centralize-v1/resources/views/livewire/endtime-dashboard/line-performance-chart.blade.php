<div wire:ignore>
    <div class="card custom-card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold fs-18">Line Performance Status</h6>
            <div class="chart-legend d-flex align-items-center pe-4">
                <div class="d-flex align-items-center me-4">
                    <span class="legend-marker" style="background-color: rgb(12, 156, 252); width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                    <span class="legend-text" style="font-size: 14px; font-weight: 500;">Target</span>
                </div>
                <div class="d-flex align-items-center me-4">
                    <span class="legend-marker" style="background-color: rgb(115, 93, 255); width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                    <span class="legend-text" style="font-size: 14px; font-weight: 500;">Endtime</span>
                </div>
                <div class="d-flex align-items-center me-4">
                    <span class="legend-marker" style="background-color: rgb(12, 199, 99); width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                    <span class="legend-text" style="font-size: 14px; font-weight: 500;">Submitted</span>
                </div>
                <div class="d-flex align-items-center me-2">
                    <span class="legend-marker" style="background-color:rgb(255, 56, 60); width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 5px;"></span>
                    <span class="legend-text" style="font-size: 14px; font-weight: 500;">Remaining</span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="{{ $chartId }}" style="height: 321px;"></div>
        </div>
    </div>
</div>

<script>
    // Initialize chart when the DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initLinePerformanceChart(@json($chartData), '{{ $chartId }}');
    });

    // Re-initialize chart when Livewire component is updated
    document.addEventListener('livewire:navigated', function() {
        initLinePerformanceChart(@json($chartData), '{{ $chartId }}');
    });

    // Function to initialize or re-initialize the chart
    function initLinePerformanceChart(chartData, chartId) {
        // Function to create the chart with the given data
        function createChart(chartData, chartId) {
            // Check if the chart container exists
            const chartContainer = document.getElementById(chartId);
            if (!chartContainer) {
                console.error('Chart container not found:', chartId);
                return null;
            }

            // Get the maximum value from all data series to set appropriate Y-axis max
            const allValues = [
                ...chartData.series[0].data, // Target
                ...chartData.series[1].data, // Endtime
                ...chartData.series[2].data, // Submitted
                ...chartData.series[3].data  // Remaining
            ];

            // Find the maximum value
            const maxValue = Math.max(...allValues);

            // Calculate a nice rounded max value for the Y-axis
            // Round up to the nearest 10 million for values over 10M
            // or to the nearest 5 million for smaller values
            let yAxisMax;
            if (maxValue > 10000000) {
                yAxisMax = Math.ceil(maxValue / 10000000) * 10000000;
            } else {
                yAxisMax = Math.ceil(maxValue / 5000000) * 5000000;
            }

            // Add a little extra space (10%) to ensure bars don't touch the top
            yAxisMax = Math.ceil(yAxisMax * 1.1);

            // Initialize the chart options
            var options = {
                chart: {
                    id: chartId,
                    height: 300,
                    type: "line",
                    stacked: false,
                    toolbar: {
                        show: false,
                    },
                    zoom: {
                        enabled: false,
                    },
                    offsetY: -10,
                    animations: {
                        enabled: false // Disable animations for smoother updates
                    }
                },
                dataLabels: {
                    enabled: false,
                },
                colors: [
                    "rgb(12, 156, 252)",      // Target (line) - info color
                    "rgb(115, 93, 255)",  // Endtime (column) - Primary color (purple)
                    "rgb(12, 199, 99)",  // Submitted (column) - success color
                    "rgb(255, 56, 60)"        // Remaining (column) - danger color
                ],
                series: [
                    {
                        name: "Target",
                        type: "line",
                        data: chartData.series[0].data
                    },
                    {
                        name: "Endtime",
                        type: "column",
                        data: chartData.series[1].data
                    },
                    {
                        name: "Submitted",
                        type: "column",
                        data: chartData.series[2].data
                    },
                    {
                        name: "Remaining",
                        type: "column",
                        data: chartData.series[3].data
                    }
                ],
                stroke: {
                    curve: "smooth",
                    width: [2, 0, 0, 0],
                    dashArray: [3, 0, 0, 0],
                },
                plotOptions: {
                    bar: {
                        columnWidth: "65%",
                        endingShape: "rounded",
                        borderRadius: 4,
                        colors: {
                            backgroundBarColors: [],
                            backgroundBarOpacity: 1,
                        }
                    },
                },
                markers: {
                    size: [8, 0, 0, 0],
                    colors: undefined,
                    strokeColors: "#fff",
                    strokeOpacity: 0.6,
                    strokeDashArray: 0,
                    fillOpacity: 1,
                    discrete: [],
                    shape: "circle",
                    radius: [0, 0, 0, 0],
                    offsetX: 0,
                    offsetY: 0,
                    onClick: undefined,
                    onDblClick: undefined,
                    showNullDataPoints: true,
                    hover: {
                        size: undefined,
                        sizeOffset: 3,
                    },
                },
                fill: {
                    opacity: [1, 1, 1, 1],
                },
                grid: {
                    borderColor: "#f2f6f7",
                    padding: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 10
                    },
                    yaxis: {
                        lines: {
                            show: true,
                            opacity: 0.1
                        }
                    },
                    xaxis: {
                        lines: {
                            show: false
                        }
                    }
                },
                legend: {
                    show: false, // Hide the default legend since we're using a custom one
                },
                yaxis: {
                    min: 0,
                    max: yAxisMax, // Dynamic max based on data
                    forceNiceScale: false, // Disable nice scale to use our exact max value
                    tickAmount: 4, // 4 ticks will give us 0M, 20M, 40M, 60M
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    title: {
                        style: {
                            color: "#adb5be",
                            fontSize: "14px",
                            fontFamily: "poppins, sans-serif",
                            fontWeight: 600,
                            cssClass: "apexcharts-yaxis-label",
                        },
                    },
                    labels: {
                        formatter: function (value) {
                            // Format as millions with M suffix without decimals
                            return (value / 1000000).toFixed(0) + "M";
                        },
                        offsetX: -10,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                xaxis: {
                    type: "category",
                    categories: chartData.categories,
                    axisBorder: {
                        show: true,
                        color: "rgba(119, 119, 142, 0.05)",
                        offsetX: 0,
                        offsetY: 0,
                    },
                    axisTicks: {
                        show: false,
                    },
                    labels: {
                        rotate: -90,
                        style: {
                            fontSize: '11px',
                            fontWeight: 400,
                        },
                        offsetY: 0,
                    },
                    crosshairs: {
                        show: false
                    }
                },
                tooltip: {
                    enabled: true,
                    shared: false,
                    intersect: true,
                    x: {
                        show: false,
                    },
                    y: {
                        formatter: function (value) {
                            // Format tooltip values in millions with M suffix without decimals
                            return (value / 1000000).toFixed(0) + "M";
                        },
                    },
                },
            };

            // Check if chart already exists with this ID and destroy it
            if (window.ApexCharts.getChartByID(chartId)) {
                window.ApexCharts.getChartByID(chartId).destroy();
            }

            // Create and render the chart
            var chart = new ApexCharts(
                document.getElementById(chartId),
                options
            );
            chart.render();

            return chart;
        }

        // Create the chart
        let chart = createChart(chartData, chartId);

        // Listen for chart data updates from Livewire
        if (window.Livewire) {
            window.Livewire.on('updateLinePerformanceChart', (params) => {
                if (chart) {
                    // Destroy the existing chart
                    chart.destroy();

                    // Create a new chart with the updated data
                    chart = createChart(params[0].chartData, chartId);
                }
            });
        }
    }
</script>
