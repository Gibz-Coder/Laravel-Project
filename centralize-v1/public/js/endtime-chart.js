(function () {
    "use strict";

    /* Today's Performance */
    var options = {
        series: [
            {
                name: "Target",
                data: Array(6).fill(10), // Initialize with zeros, will be updated
                type: "line",
            },
            {
                name: "Endtime",
                data: [0, 0, 0, 0, 0, 0], // Initialize with zeros, will be updated
                type: "column",
            },
            {
                name: "Submitted",
                data: [0, 0, 0, 0, 0, 0], // Initialize with zeros, will be updated
                type: "column",
            },
        ],
        chart: {
            height: 225,
            type: "line",
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: false,
            },
            dropShadow: {
                enabled: true,
                enabledOnSeries: [0, 2],
                top: 7,
                left: 0,
                blur: 1,
                color: [
                    "rgba(12, 199, 99, 1)",
                    "transparent",
                    "rgb(255, 90, 41)",
                ],
                opacity: 0.05,
            },
        },
        plotOptions: {
            bar: {
                columnWidth: "65%",
                borderRadius: [2],
            },
        },
        colors: [
            "rgb(255, 90, 41)",
            "var(--primary-color)",
            "rgba(12, 199, 99, 1)",
        ],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: "smooth",
            width: [3, 0, 2],
            dashArray: [0, 0, 5],
        },
        markers: {
            size: [0, 0, 4],
            colors: undefined,
            strokeColors: "#fff",
            strokeOpacity: 0.6,
            strokeDashArray: 0,
            fillOpacity: 1,
            discrete: [],
            shape: "circle",
            radius: [0, 0, 2],
            offsetX: 0,
            offsetY: 0,
            showNullDataPoints: true,
        },
        grid: {
            borderColor: "#f1f1f1",
            strokeDashArray: 2,
            xaxis: {
                lines: {
                    show: true,
                },
            },
            yaxis: {
                lines: {
                    show: false,
                },
            },
        },
        yaxis: {
            show: false,
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        xaxis: {
            categories: ["4 AM", "7 AM", "12 NN", "4 PM", "7 PM", "12 MN"],
            show: false,
            axisBorder: {
                show: false,
                color: "rgba(119, 119, 142, 0.05)",
                offsetX: 0,
                offsetY: 0,
            },
            axisTicks: {
                show: false,
                borderType: "solid",
                color: "rgba(119, 119, 142, 0.05)",
                width: 6,
                offsetX: 0,
                offsetY: 0,
            },

            labels: {
                rotate: -90,
            },
        },
        legend: {
            show: true,
            position: "top",
            offsetX: 0,
            offsetY: 8,
            markers: {
                size: 4,
                strokeWidth: 0,
                strokeColor: "#fff",
                fillColors: undefined,
                radius: 5,
                customHTML: undefined,
                onClick: undefined,
                offsetX: 0,
                offsetY: 0,
            },
        },
    };
    var chart = new ApexCharts(
        document.querySelector("#daily-performance-chart"),
        options
    );
    chart.render();
    /* Today's Performance */

    /* Line Performance Statistics */
    var options = {
        chart: {
            height: 300,
            type: "line",
            stacked: false,
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: false,
            },
        },
        dataLabels: {
            enabled: false,
        },
        colors: [
            "rgb(255, 90, 41)",
            "var(--primary-color)",
            "rgba(12, 199, 99, 1)",
        ],
        series: [
            {
                name: "Target",
                type: "line",
                data: Array(11).fill(0), // Initialize with zeros, will be updated
            },
            {
                name: "Endtime",
                type: "column",
                data: Array(11).fill(0), // Initialize with zeros, will be updated
            },
            {
                name: "Submitted",
                type: "column",
                data: Array(11).fill(0), // Initialize with zeros, will be updated
            },
        ],
        stroke: {
            curve: "smooth",
            width: [2, 0, 0],
            dashArray: [0, 0, 0],
        },
        plotOptions: {
            bar: {
                columnWidth: "50%",
            },
        },
        markers: {
            size: [5, 0, 0],
            colors: undefined,
            strokeColors: "#fff",
            strokeOpacity: 0.6,
            strokeDashArray: 0,
            fillOpacity: 1,
            discrete: [],
            shape: "circle",
            radius: [0, 0, 2],
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
            opacity: [1, 1, 1],
        },
        grid: {
            borderColor: "#f2f6f7",
        },
        legend: {
            show: true,
            position: "top",
            markers: {
                size: 8,
                fontSize: "14px",
                strokeWidth: 0,
                strokeColor: "#fff",
                fillColors: undefined,
                radius: 5,
                customHTML: undefined,
                onClick: undefined,
                offsetX: 0,
                offsetY: 0,
            },
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
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
                    // Format as millions with M suffix
                    return (value / 1000000).toFixed(2) + "M";
                },
            },
        },
        xaxis: {
            type: "category",
            categories: [
                "Line A",
                "Line B",
                "Line C",
                "Line D",
                "Line E",
                "Line F",
                "Line G",
                "Line H",
                "Line I",
                "Line J",
                "VMI",
            ],
            axisBorder: {
                show: true,
                color: "rgba(119, 119, 142, 0.05)",
                offsetX: 0,
                offsetY: 0,
            },
            axisTicks: {
                show: true,
                borderType: "solid",
                color: "rgba(119, 119, 142, 0.05)",
                width: 6,
                offsetX: 0,
                offsetY: 0,
            },
            labels: {
                rotate: -90,
            },
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
                    // Format tooltip values in millions with M suffix
                    return (value / 1000000).toFixed(2) + "M";
                },
            },
        },
    };
    var chart = new ApexCharts(
        document.querySelector("#line-performance-statistics"),
        options
    );
    chart.render();

    // Function to fetch target data from vi_capa_ref
    function fetchTargetData() {
        // Get current filter values
        const worktype = document.getElementById("selectedWorktype")
            ? document.getElementById("selectedWorktype").textContent
            : "all";

        const cutoff = localStorage.getItem("selectedCutoff") || "all";

        // Fetch target data from server
        fetch(`/chart-data/line-target?worktype=${worktype}&cutoff=${cutoff}`)
            .then((response) => response.json())
            .then((data) => {
                // Convert values to millions
                const targetInMillions = data.target.map((value) => value);

                // Update chart with new target values
                chart.updateSeries([
                    {
                        name: "Target",
                        type: "line",
                        data: targetInMillions,
                    },
                    chart.w.config.series[1],
                    chart.w.config.series[2],
                ]);
            })
            .catch((error) => {
                console.error("Error fetching target data:", error);
            });
    }

    // Function to fetch endtime and submitted data
    function fetchProductionData() {
        // Get current filter values
        const date = document.getElementById("endtime")
            ? document.getElementById("endtime").value
            : document.querySelector('input[name="endtime"]').value;

        const worktype = document.getElementById("selectedWorktype")
            ? document.getElementById("selectedWorktype").textContent
            : "all";

        const lottype = document.getElementById("selectedLottype")
            ? document.getElementById("selectedLottype").textContent
            : "all";

        const cutoff = localStorage.getItem("selectedCutoff") || "all";

        // Fetch production data from server
        fetch(
            `/chart-data/line-production?date=${date}&worktype=${worktype}&lottype=${lottype}&cutoff=${cutoff}`
        )
            .then((response) => response.json())
            .then((data) => {
                // Convert values to millions
                const endtimeInMillions = data.endtime.map((value) => value);
                const submittedInMillions = data.submitted.map(
                    (value) => value
                );

                // Update chart with new values
                chart.updateSeries([
                    chart.w.config.series[0],
                    {
                        name: "Endtime",
                        type: "column",
                        data: endtimeInMillions,
                    },
                    {
                        name: "Submitted",
                        type: "column",
                        data: submittedInMillions,
                    },
                ]);
            })
            .catch((error) => {
                console.error("Error fetching production data:", error);
            });
    }

    // Initialize data on page load
    document.addEventListener("DOMContentLoaded", function () {
        fetchTargetData();
        fetchProductionData();

        // Update when date changes
        const dateInput =
            document.getElementById("endtime") ||
            document.querySelector('input[name="endtime"]');
        if (dateInput) {
            dateInput.addEventListener("change", function () {
                fetchProductionData();
            });
        }

        // Update when worktype changes
        const worktypeElements = document.querySelectorAll(
            ".worktype-dropdown-item"
        );
        worktypeElements.forEach((element) => {
            element.addEventListener("click", function () {
                setTimeout(() => {
                    fetchTargetData();
                    fetchProductionData();
                }, 100);
            });
        });

        // Update when lottype changes
        const lottypeElements = document.querySelectorAll(
            ".lottype-dropdown-item"
        );
        lottypeElements.forEach((element) => {
            element.addEventListener("click", function () {
                setTimeout(() => {
                    fetchProductionData();
                }, 100);
            });
        });

        // Update when cutoff changes
        const cutoffElements = document.querySelectorAll("[data-time]");
        cutoffElements.forEach((element) => {
            element.addEventListener("click", function () {
                setTimeout(() => {
                    fetchTargetData();
                    fetchProductionData();
                }, 100);
            });
        });
    });
    /* Line Performance Statistics */

    /* Endtime & Submitted Progress */
    var options = {
        series: [1, 1, 1], // Initialize with non-zero values to show segments
        labels: ["Endtime", "Submitted", "Remaining"],
        chart: {
            height: 270,
            type: "donut",
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value.toFixed(2) + '%';
                }
            }
        },
        dataLabels: {
            enabled: false,
            dropShadow: {
                enabled: false,
            },
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
                                // Display raw value instead of percentage
                                return val;
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
                                // Calculate progress percentage
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
    var chart1 = new ApexCharts(
        document.querySelector("#top-categories"),
        options
    );
    chart1.render();

    // Declare variables at a wider scope to make them accessible to tooltip formatter
    let totalEndtime = 0, totalSubmitted = 0, remaining = 0;

    // Function to update the donut chart
    function updateDonutChart() {
        // Get current filter values
        const date = document.getElementById("endtime")
            ? document.getElementById("endtime").value
            : document.querySelector('input[name="endtime"]').value;
        const cutoff = localStorage.getItem("selectedCutoff") || "all";
        const worktype = document.getElementById("selectedWorktype")
            ? document.getElementById("selectedWorktype").textContent
            : "all";
        const lottype = document.getElementById("selectedLottype")
            ? document.getElementById("selectedLottype").textContent
            : "all";

        // Fetch endtime data
        fetch(`/endtime-total?date=${date}&cutoff=${cutoff}&worktype=${worktype}&lottype=${lottype}`)
            .then(response => response.json())
            .then(endtimeData => {
                // Fetch submitted data
                fetch(`/submitted-total?date=${date}&cutoff=${cutoff}&worktype=${worktype}&lottype=${lottype}`)
                    .then(response => response.json())
                    .then(submittedData => {
                        // Store raw values in wider scope variables
                        totalEndtime = endtimeData.total || 1;
                        totalSubmitted = submittedData.total || 0;
                        
                        // Per requirements: whole donut value = total endtime * 2
                        const donutTotal = totalEndtime * 2;
                        
                        // Calculate remaining (ensure it's never negative)
                        const remaining = Math.max(0, donutTotal - totalEndtime - totalSubmitted);
                        
                        // Convert to percentages for the donut chart
                        const endtimePercent = (totalEndtime / donutTotal) * 100 * 2;
                        const submittedPercent = (totalSubmitted / donutTotal) * 100 * 2;
                        const remainingPercent = (remaining / donutTotal) * 100 * 2;
                        
                        // Update chart with percentage values
                        chart1.updateSeries([
                            endtimePercent,
                            submittedPercent,
                            remainingPercent
                        ]);
                    })
                    .catch(error => console.error("Error fetching submitted data:", error));
            })
            .catch(error => console.error("Error fetching endtime data:", error));
    }

    // Initialize donut chart on page load
    document.addEventListener("DOMContentLoaded", function() {
        if (document.querySelector("#top-categories")) {
            updateDonutChart();
            
            // Update when filters change
            const dateInput = document.getElementById("endtime") || document.querySelector('input[name="endtime"]');
            if (dateInput) {
                dateInput.addEventListener("change", updateDonutChart);
            }
            
            // Update when worktype changes
            const worktypeElements = document.querySelectorAll(".worktype-dropdown-item");
            worktypeElements.forEach(element => {
                element.addEventListener("click", function() {
                    setTimeout(updateDonutChart, 100);
                });
            });
            
            // Update when lottype changes
            const lottypeElements = document.querySelectorAll(".lottype-dropdown-item");
            lottypeElements.forEach(element => {
                element.addEventListener("click", function() {
                    setTimeout(updateDonutChart, 100);
                });
            });
            
            // Update when cutoff changes
            const cutoffElements = document.querySelectorAll("[data-time]");
            cutoffElements.forEach(element => {
                element.addEventListener("click", function() {
                    setTimeout(updateDonutChart, 100);
                });
            });
        }
    });
    /* Endtime & Submitted Progress */
    
})();
