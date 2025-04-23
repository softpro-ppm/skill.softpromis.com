// Main Application JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initSidebar();
    initTooltips();
    initModals();
    initNotifications();
    initDataTables();
    initCharts();
});

// Sidebar Toggle
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'btn btn-primary sidebar-toggle';
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    toggleBtn.onclick = function() {
        sidebar.classList.toggle('active');
    };
    document.querySelector('.navbar').appendChild(toggleBtn);
}

// Tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Modals
function initModals() {
    const modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
    modalTriggerList.map(function (modalTriggerEl) {
        return new bootstrap.Modal(modalTriggerEl);
    });
}

// Notifications
function initNotifications() {
    window.showNotification = function(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.innerHTML = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    };
}

// Data Tables
function initDataTables() {
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        if (table.dataset.datatable !== 'false') {
            new DataTable(table, {
                responsive: true,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        }
    });
}

// Charts
function initCharts() {
    const charts = document.querySelectorAll('.chart-container');
    charts.forEach(container => {
        const canvas = container.querySelector('canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            const config = JSON.parse(canvas.dataset.config || '{}');
            new Chart(ctx, config);
        }
    });
}

// Form Validation
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.style.display = 'block';
            }
        } else {
            input.classList.remove('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.style.display = 'none';
            }
        }
    });
    
    return isValid;
}

// File Upload Preview
function initFileUpload(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const preview = input.nextElementSibling;
        
        reader.onload = function(e) {
            if (preview && preview.classList.contains('file-preview')) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Print Functionality
function printReport() {
    window.print();
}

// Download PDF
function downloadPDF() {
    // This is a placeholder for PDF generation
    // In a real application, this would use a PDF generation library
    alert('PDF download started...');
}

// Search Functionality
function initSearch() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const items = document.querySelectorAll('.searchable-item');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
}

// Date Range Picker
function initDateRangePicker() {
    const datePickers = document.querySelectorAll('.date-range-picker');
    datePickers.forEach(picker => {
        // Initialize date range picker
        // This is a placeholder for actual date range picker implementation
    });
}

// Responsive Tables
function initResponsiveTables() {
    const tables = document.querySelectorAll('.table-responsive');
    tables.forEach(table => {
        const wrapper = document.createElement('div');
        wrapper.className = 'table-responsive-wrapper';
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);
    });
}

// Loading States
function showLoading() {
    const loading = document.createElement('div');
    loading.className = 'loading-overlay';
    loading.innerHTML = '<div class="loading-spinner"></div>';
    document.body.appendChild(loading);
}

function hideLoading() {
    const loading = document.querySelector('.loading-overlay');
    if (loading) {
        loading.remove();
    }
}

// Error Handling
window.onerror = function(msg, url, lineNo, columnNo, error) {
    console.error('Error: ' + msg + '\nURL: ' + url + '\nLine: ' + lineNo + '\nColumn: ' + columnNo + '\nError object: ' + JSON.stringify(error));
    return false;
};

// Add CSS for new components
const style = document.createElement('style');
style.textContent = `
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        z-index: 9999;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .file-preview {
        max-width: 200px;
        max-height: 200px;
        display: none;
        margin-top: 1rem;
        border-radius: var(--border-radius);
    }
    
    .is-invalid {
        border-color: var(--danger-color) !important;
    }
    
    .invalid-feedback {
        display: none;
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .sidebar-toggle {
        display: none;
    }
    
    @media (max-width: 992px) {
        .sidebar-toggle {
            display: block;
        }
    }
`;

document.head.appendChild(style); 