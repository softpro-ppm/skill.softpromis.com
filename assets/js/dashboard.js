// Dashboard update interval (5 minutes)
const UPDATE_INTERVAL = 5 * 60 * 1000;

// Chart colors
const CHART_COLORS = {
    primary: '#4e73df',
    success: '#1cc88a',
    info: '#36b9cc',
    warning: '#f6c23e',
    danger: '#e74a3b',
    secondary: '#858796',
    light: '#f8f9fc',
    dark: '#5a5c69'
};

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Initial data load
    updateDashboard();
    
    // Set up periodic updates
    setInterval(updateDashboard, UPDATE_INTERVAL);
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Update dashboard data
function updateDashboard() {
    fetch('inc/ajax/dashboard_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=get_dashboard_data'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateStatistics(data.data);
            updateCharts(data.data);
            updateTables(data.data);
        } else {
            console.error('Dashboard update failed:', data.message);
        }
    })
    .catch(error => {
        console.error('Dashboard update error:', error);
    });
}

// Update statistics cards
function updateStatistics(data) {
    // Admin statistics
    if (data.totalPartners !== undefined) {
        updateStatCard('totalPartners', data.totalPartners, data.partnersChange);
        updateStatCard('totalCenters', data.totalCenters, data.centersChange);
        updateStatCard('totalStudents', data.totalStudents, data.studentsChange);
        updateStatCard('totalCourses', data.totalCourses, data.coursesChange);
    }
    
    // Partner statistics
    if (data.totalCenters !== undefined && data.centersChange !== undefined) {
        updateStatCard('totalCenters', data.totalCenters, data.centersChange);
        updateStatCard('totalStudents', data.totalStudents, data.studentsChange);
        updateStatCard('totalCourses', data.totalCourses, data.coursesChange);
        updateStatCard('totalBatches', data.totalBatches, data.batchesChange);
    }
    
    // Center statistics
    if (data.totalStudents !== undefined && data.studentsChange !== undefined) {
        updateStatCard('totalStudents', data.totalStudents, data.studentsChange);
        updateStatCard('totalCourses', data.totalCourses, data.coursesChange);
        updateStatCard('totalBatches', data.totalBatches, data.batchesChange);
        updateStatCard('completedCourses', data.completedCourses, data.completedChange);
    }
}

// Update a single statistics card
function updateStatCard(id, value, change) {
    const card = document.getElementById(id);
    if (!card) return;
    
    // Update value
    const valueElement = card.querySelector('.stat-value');
    if (valueElement) {
        valueElement.textContent = value.toLocaleString();
    }
    
    // Update change percentage
    const changeElement = card.querySelector('.stat-change');
    if (changeElement) {
        const changeValue = parseFloat(change);
        const changeText = changeValue >= 0 ? 
            `+${changeValue.toFixed(1)}%` : 
            `${changeValue.toFixed(1)}%`;
        
        changeElement.textContent = changeText;
        changeElement.className = `stat-change ${changeValue >= 0 ? 'text-success' : 'text-danger'}`;
    }
}

// Update charts
function updateCharts(data) {
    // Enrollment chart
    if (data.enrollment) {
        updateEnrollmentChart(data.enrollment);
    }
    
    // Course distribution chart
    if (data.courseDistribution) {
        updateCourseDistributionChart(data.courseDistribution);
    }
    
    // Center performance chart (for partners)
    if (data.centerPerformance) {
        updateCenterPerformanceChart(data.centerPerformance);
    }
}

// Update enrollment chart
function updateEnrollmentChart(data) {
    const ctx = document.getElementById('enrollmentChart');
    if (!ctx) return;
    
    if (window.enrollmentChart) {
        window.enrollmentChart.destroy();
    }
    
    window.enrollmentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Student Enrollments',
                data: data.data,
                borderColor: CHART_COLORS.primary,
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: CHART_COLORS.primary,
                pointBorderColor: CHART_COLORS.primary,
                pointHoverRadius: 5,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Update course distribution chart
function updateCourseDistributionChart(data) {
    const ctx = document.getElementById('courseDistributionChart');
    if (!ctx) return;
    
    if (window.courseDistributionChart) {
        window.courseDistributionChart.destroy();
    }
    
    window.courseDistributionChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.data,
                backgroundColor: [
                    CHART_COLORS.primary,
                    CHART_COLORS.success,
                    CHART_COLORS.info,
                    CHART_COLORS.warning,
                    CHART_COLORS.danger
                ],
                hoverBackgroundColor: [
                    CHART_COLORS.primary,
                    CHART_COLORS.success,
                    CHART_COLORS.info,
                    CHART_COLORS.warning,
                    CHART_COLORS.danger
                ],
                hoverBorderColor: 'rgba(234, 236, 244, 1)',
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            cutout: '70%'
        }
    });
}

// Update center performance chart
function updateCenterPerformanceChart(data) {
    const ctx = document.getElementById('centerPerformanceChart');
    if (!ctx) return;
    
    if (window.centerPerformanceChart) {
        window.centerPerformanceChart.destroy();
    }
    
    window.centerPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Students',
                data: data.data,
                backgroundColor: CHART_COLORS.primary,
                borderColor: CHART_COLORS.primary,
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Update tables
function updateTables(data) {
    // Recent activities table
    if (data.recentActivities) {
        updateActivitiesTable(data.recentActivities);
    }
    
    // Active batches table (for centers)
    if (data.activeBatches) {
        updateBatchesTable(data.activeBatches);
    }
}

// Update activities table
function updateActivitiesTable(activities) {
    const tbody = document.querySelector('#activitiesTable tbody');
    if (!tbody) return;
    
    tbody.innerHTML = activities.map(activity => `
        <tr>
            <td>${activity.date}</td>
            <td>${activity.description}</td>
            <td>${activity.user}</td>
            <td>
                <span class="badge bg-${getStatusColor(activity.status)}">
                    ${activity.status}
                </span>
            </td>
        </tr>
    `).join('');
}

// Update batches table
function updateBatchesTable(batches) {
    const tbody = document.querySelector('#batchesTable tbody');
    if (!tbody) return;
    
    tbody.innerHTML = batches.map(batch => `
        <tr>
            <td>${batch.code}</td>
            <td>${batch.course}</td>
            <td>${batch.startDate}</td>
            <td>${batch.endDate}</td>
            <td>${batch.students}</td>
            <td>
                <span class="badge bg-${getStatusColor(batch.status)}">
                    ${batch.status}
                </span>
            </td>
        </tr>
    `).join('');
}

// Helper function to get status color
function getStatusColor(status) {
    switch (status.toLowerCase()) {
        case 'active':
            return 'success';
        case 'pending':
            return 'warning';
        case 'completed':
            return 'info';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
} 