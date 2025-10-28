/**
 * Influencers Marketing Module - Analytics & Charts
 */

(function() {
    'use strict';

    let charts = {};

    $(document).ready(function() {
        initAnalytics();
    });

    function initAnalytics() {
        // Initialize all charts on the page
        initFollowersChart();
        initEngagementChart();
        initROIChart();
        initPlatformDistribution();
        initCampaignPerformance();
        initInfluencerComparison();

        // Refresh data periodically
        setInterval(refreshAnalytics, 300000); // Every 5 minutes
    }

    /**
     * Followers growth chart
     */
    function initFollowersChart() {
        const $canvas = $('#im-followers-chart');
        if ($canvas.length === 0) return;

        const ctx = $canvas[0].getContext('2d');
        const data = getFollowersData();

        charts.followers = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Followers',
                    data: data.values,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Followers: ' + formatNumber(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return formatNumber(value);
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }

    /**
     * Engagement rate chart
     */
    function initEngagementChart() {
        const $canvas = $('#im-engagement-chart');
        if ($canvas.length === 0) return;

        const ctx = $canvas[0].getContext('2d');
        const data = getEngagementData();

        charts.engagement = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Taux d\'engagement (%)',
                    data: data.values,
                    backgroundColor: [
                        'rgba(46, 204, 113, 0.8)',
                        'rgba(52, 152, 219, 0.8)',
                        'rgba(155, 89, 182, 0.8)',
                        'rgba(241, 196, 15, 0.8)',
                        'rgba(231, 76, 60, 0.8)'
                    ],
                    borderColor: [
                        '#2ecc71',
                        '#3498db',
                        '#9b59b6',
                        '#f1c40f',
                        '#e74c3c'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
    }

    /**
     * ROI Chart
     */
    function initROIChart() {
        const $canvas = $('#im-roi-chart');
        if ($canvas.length === 0) return;

        const ctx = $canvas[0].getContext('2d');
        const data = getROIData();

        charts.roi = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Investissement',
                        data: data.investment,
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Retour',
                        data: data.return,
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        borderWidth: 2,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + formatCurrency(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Platform distribution (Pie chart)
     */
    function initPlatformDistribution() {
        const $canvas = $('#im-platform-chart');
        if ($canvas.length === 0) return;

        const ctx = $canvas[0].getContext('2d');
        const data = getPlatformData();

        charts.platform = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        '#E1306C', // Instagram
                        '#FF0000', // YouTube
                        '#000000', // TikTok
                        '#1877F2', // Facebook
                        '#1DA1F2', // Twitter
                        '#0A66C2', // LinkedIn
                        '#9146FF'  // Twitch
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            font: {
                                size: 13
                            },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map(function(label, i) {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${formatNumber(context.parsed)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * Campaign performance comparison
     */
    function initCampaignPerformance() {
        const $canvas = $('#im-campaign-chart');
        if ($canvas.length === 0) return;

        const ctx = $canvas[0].getContext('2d');
        const data = getCampaignData();

        charts.campaign = new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Impressions', 'Engagement', 'Conversions', 'ROI', 'Qualité'],
                datasets: data.campaigns.map((campaign, index) => ({
                    label: campaign.name,
                    data: campaign.metrics,
                    borderColor: getChartColor(index),
                    backgroundColor: getChartColor(index, 0.2),
                    borderWidth: 2,
                    pointBackgroundColor: getChartColor(index),
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: getChartColor(index)
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        }
                    }
                }
            }
        });
    }

    /**
     * Influencer comparison
     */
    function initInfluencerComparison() {
        const $canvas = $('#im-comparison-chart');
        if ($canvas.length === 0) return;

        const ctx = $canvas[0].getContext('2d');
        const data = getComparisonData();

        charts.comparison = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.influencers,
                datasets: [
                    {
                        label: 'Score d\'influence',
                        data: data.scores,
                        backgroundColor: 'rgba(52, 152, 219, 0.8)',
                        borderColor: '#3498db',
                        borderWidth: 2,
                        borderRadius: 8
                    },
                    {
                        label: 'Engagement (%)',
                        data: data.engagement,
                        backgroundColor: 'rgba(46, 204, 113, 0.8)',
                        borderColor: '#2ecc71',
                        borderWidth: 2,
                        borderRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }

    /**
     * Data fetching functions
     */
    function getFollowersData() {
        // This would normally fetch from server
        const influencerId = $('#im-influencer-id').val();
        const days = $('#im-days-filter').val() || 30;

        // Placeholder data
        return {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            values: [12000, 15000, 18000, 22000, 28000, 32000]
        };
    }

    function getEngagementData() {
        return {
            labels: ['Instagram', 'YouTube', 'TikTok', 'Facebook', 'LinkedIn'],
            values: [5.2, 3.8, 8.5, 2.1, 4.3]
        };
    }

    function getROIData() {
        return {
            labels: ['Campagne 1', 'Campagne 2', 'Campagne 3', 'Campagne 4'],
            investment: [5000, 7500, 10000, 8000],
            return: [12000, 18000, 25000, 20000]
        };
    }

    function getPlatformData() {
        return {
            labels: ['Instagram', 'YouTube', 'TikTok', 'Facebook', 'Twitter', 'LinkedIn', 'Twitch'],
            values: [45, 25, 15, 8, 4, 2, 1]
        };
    }

    function getCampaignData() {
        return {
            campaigns: [
                {
                    name: 'Campagne Beauty 2024',
                    metrics: [85, 78, 92, 88, 90]
                },
                {
                    name: 'Campagne Tech Launch',
                    metrics: [72, 85, 68, 75, 80]
                }
            ]
        };
    }

    function getComparisonData() {
        return {
            influencers: ['Marie D.', 'Jean P.', 'Sophie M.', 'Lucas B.', 'Emma R.'],
            scores: [85, 72, 91, 68, 78],
            engagement: [6.2, 4.8, 8.1, 3.5, 5.9]
        };
    }

    /**
     * Refresh analytics data
     */
    function refreshAnalytics() {
        Object.keys(charts).forEach(function(key) {
            if (charts[key]) {
                // Update chart data
                updateChartData(charts[key], key);
            }
        });
    }

    function updateChartData(chart, type) {
        // Fetch new data and update chart
        $.ajax({
            url: admin_url + 'influencers_marketing/get_analytics_data',
            method: 'GET',
            data: {
                type: type,
                id: $('#im-influencer-id').val() || $('#im-campaign-id').val(),
                days: $('#im-days-filter').val() || 30
            },
            success: function(data) {
                // Update chart with new data
                chart.data = data;
                chart.update();
            }
        });
    }

    /**
     * Export chart as image
     */
    function exportChart(chartId) {
        const chart = charts[chartId];
        if (!chart) return;

        const url = chart.toBase64Image();
        const link = document.createElement('a');
        link.download = `chart-${chartId}-${Date.now()}.png`;
        link.href = url;
        link.click();
    }

    /**
     * Helper functions
     */
    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(amount);
    }

    function getChartColor(index, alpha = 1) {
        const colors = [
            `rgba(52, 152, 219, ${alpha})`,
            `rgba(46, 204, 113, ${alpha})`,
            `rgba(155, 89, 182, ${alpha})`,
            `rgba(241, 196, 15, ${alpha})`,
            `rgba(231, 76, 60, ${alpha})`,
            `rgba(26, 188, 156, ${alpha})`,
            `rgba(230, 126, 34, ${alpha})`
        ];
        return colors[index % colors.length];
    }

    // Export functions
    window.IMAnalytics = {
        refresh: refreshAnalytics,
        exportChart: exportChart,
        charts: charts
    };

})();
