// Main Application JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initSidebar();
    initTooltips();
    initModals();
    initNotifications();
    initDataTables();
    initCharts();
    initFormAutoSave();
    initTableRowActions();
    initInputMasks();
    
    // Add floating label functionality
    const floatingLabels = document.querySelectorAll('.form-group.floating-label');
    floatingLabels.forEach(group => {
        const input = group.querySelector('.form-control');
        const label = group.querySelector('.form-label');
        
        if (input.value) {
            label.classList.add('active');
        }
        
        input.addEventListener('focus', () => label.classList.add('active'));
        input.addEventListener('blur', () => {
            if (!input.value) {
                label.classList.remove('active');
            }
        });
    });
});

// Initialize Sidebar
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'btn btn-primary sidebar-toggle';
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    toggleBtn.onclick = function() {
        sidebar.classList.toggle('active');
    };
    document.querySelector('.navbar').appendChild(toggleBtn);

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 992) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        }
    });
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

// Enhanced Data Tables
function initDataTables() {
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        if (table.dataset.datatable !== 'false') {
            // Wrap table in container
            const container = document.createElement('div');
            container.className = 'table-container';
            table.parentNode.insertBefore(container, table);
            
            // Create table header
            const header = document.createElement('div');
            header.className = 'table-header';
            
            // Create search box
            const searchBox = document.createElement('div');
            searchBox.className = 'search-box';
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Enter custom Search text';
            const clearSearch = document.createElement('button');
            clearSearch.className = 'clear-search';
            clearSearch.innerHTML = '×';
            searchBox.appendChild(searchInput);
            searchBox.appendChild(clearSearch);
            
            // Create action buttons
            const actions = document.createElement('div');
            actions.className = 'table-actions';
            
            // Left side buttons
            const refreshBtn = document.createElement('button');
            refreshBtn.className = 'btn-header';
            refreshBtn.textContent = 'Refresh';
            const resetBtn = document.createElement('button');
            resetBtn.className = 'btn-header';
            resetBtn.textContent = 'Reset Query';
            
            // Right side test buttons
            const testBtn = document.createElement('button');
            testBtn.className = 'btn-header btn-test';
            testBtn.textContent = 'test';
            const test2Btn = document.createElement('button');
            test2Btn.className = 'btn-header btn-test';
            test2Btn.textContent = 'test2';
            const test3Btn = document.createElement('button');
            test3Btn.className = 'btn-header btn-test';
            test3Btn.textContent = 'test3';
            
            actions.appendChild(refreshBtn);
            actions.appendChild(resetBtn);
            actions.appendChild(testBtn);
            actions.appendChild(test2Btn);
            actions.appendChild(test3Btn);
            
            header.appendChild(searchBox);
            header.appendChild(actions);
            container.appendChild(header);
            
            // Move table into container
            container.appendChild(table);
            
            // Initialize DataTable
            const dataTable = new DataTable(table, {
                dom: '<"table-responsive"t><"table-footer"<"table-info"i><"pagination-container"p>>',
                pageLength: 10,
                order: [[0, 'asc']],
                responsive: true,
                language: {
                    search: '',
                    searchPlaceholder: 'Search...',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'This page total is _END_ | Filtered results total is _TOTAL_ | Original data total is _MAX_ | _SELECTED_ rows selected',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '',
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    }
                },
                columnDefs: [
                    {
                        targets: '_all',
                        sortable: true
                    }
                ],
                initComplete: function() {
                    // Add column filters
                    this.api().columns().every(function() {
                        const column = this;
                        const header = $(column.header());
                        
                        // Add filter input
                        const input = document.createElement('input');
                        input.className = 'column-filter';
                        input.placeholder = header.text();
                        header.append(input);
                        
                        // Handle filter input
                        input.addEventListener('keyup', function(e) {
                            e.stopPropagation();
                            column.search(this.value).draw();
                        });
                    });

                    // Add pagination numbers
                    const paginationContainer = document.querySelector('.pagination-container');
                    const pagination = document.createElement('div');
                    pagination.className = 'pagination';
                    
                    // Add page numbers
                    for (let i = 1; i <= 5; i++) {
                        const pageItem = document.createElement('li');
                        pageItem.className = 'page-item' + (i === 1 ? ' active' : '');
                        const pageLink = document.createElement('a');
                        pageLink.className = 'page-link';
                        pageLink.href = '#';
                        pageLink.textContent = i;
                        pageItem.appendChild(pageLink);
                        pagination.appendChild(pageItem);
                    }
                    
                    paginationContainer.appendChild(pagination);
                }
            });
            
            // Handle search
            searchInput.addEventListener('keyup', function() {
                dataTable.search(this.value).draw();
            });
            
            // Handle clear search
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                dataTable.search('').draw();
            });
            
            // Handle refresh
            refreshBtn.addEventListener('click', function() {
                dataTable.ajax.reload();
            });
            
            // Handle reset
            resetBtn.addEventListener('click', function() {
                searchInput.value = '';
                dataTable.search('').order([0, 'asc']).draw();
                const filters = document.querySelectorAll('.column-filter');
                filters.forEach(filter => {
                    filter.value = '';
                });
                dataTable.columns().search('').draw();
            });

            // Handle test buttons
            [testBtn, test2Btn, test3Btn].forEach(btn => {
                btn.addEventListener('click', function() {
                    console.log('Test button clicked:', this.textContent);
                });
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

// Enhanced Form Validation
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

    // Email validation
    const emailInputs = form.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (input.value && !emailRegex.test(input.value)) {
            isValid = false;
            input.classList.add('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = 'Please enter a valid email address';
                feedback.style.display = 'block';
            }
        }
    });

    // Password validation
    const passwordInputs = form.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        if (input.value && input.value.length < 8) {
            isValid = false;
            input.classList.add('is-invalid');
            const feedback = input.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = 'Password must be at least 8 characters long';
                feedback.style.display = 'block';
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

// Form Auto-save
function initFormAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave]');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        const formId = form.id || 'form-' + Math.random().toString(36).substr(2, 9);
        
        // Load saved data
        const savedData = localStorage.getItem(formId);
        if (savedData) {
            const data = JSON.parse(savedData);
            inputs.forEach(input => {
                if (data[input.name]) {
                    input.value = data[input.name];
                }
            });
        }
        
        // Save on input change
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                const formData = {};
                inputs.forEach(i => {
                    formData[i.name] = i.value;
                });
                localStorage.setItem(formId, JSON.stringify(formData));
            });
        });
    });
}

// Table Row Actions
function initTableRowActions() {
    const tables = document.querySelectorAll('.data-table');
    tables.forEach(table => {
        const actionButtons = table.querySelectorAll('.btn-action');
        actionButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const action = this.dataset.action;
                const row = this.closest('tr');
                const rowData = {};
                
                // Get row data
                row.querySelectorAll('td').forEach((cell, index) => {
                    const header = table.querySelector('th').item(index).textContent;
                    rowData[header] = cell.textContent;
                });
                
                // Handle different actions
                switch(action) {
                    case 'edit':
                        showEditModal(rowData);
                        break;
                    case 'delete':
                        if (confirm('Are you sure you want to delete this record?')) {
                            row.remove();
                            showNotification('Record deleted successfully', 'success');
                        }
                        break;
                    case 'view':
                        showViewModal(rowData);
                        break;
                }
            });
        });
    });
}

// Form Input Masks
function initInputMasks() {
    const maskedInputs = document.querySelectorAll('[data-mask]');
    maskedInputs.forEach(input => {
        const mask = input.dataset.mask;
        switch(mask) {
            case 'phone':
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 0) {
                        value = value.match(new RegExp('.{1,10}'))[0];
                        value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                    }
                    e.target.value = value;
                });
                break;
            case 'date':
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 0) {
                        value = value.match(new RegExp('.{1,8}'))[0];
                        value = value.replace(/(\d{2})(\d{2})(\d{4})/, '$1/$2/$3');
                    }
                    e.target.value = value;
                });
                break;
        }
    });
}

// Enhanced Sidebar Search
function initSidebarSearch() {
    const searchInput = document.querySelector('.search-input');
    const menuItems = document.querySelectorAll('.menu-item');
    
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            
            menuItems.forEach(item => {
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

// Quick Actions
function initQuickActions() {
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');
    
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const action = e.currentTarget.dataset.action;
            switch(action) {
                case 'new-report':
                    window.location.href = '/reports/new';
                    break;
                case 'export':
                    exportData();
                    break;
            }
        });
    });
}

function exportData() {
    // Implementation for data export
    console.log('Exporting data...');
}

// Notifications
function initNotifications() {
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationDropdown = document.querySelector('.notification-dropdown');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    
    if (notificationBtn && notificationDropdown) {
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.style.display = 'none';
            }
        });
        
        // Toggle dropdown
        notificationBtn.addEventListener('click', () => {
            const isVisible = notificationDropdown.style.display === 'block';
            notificationDropdown.style.display = isVisible ? 'none' : 'block';
        });
    }
    
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', () => {
            const notifications = document.querySelectorAll('.notification-item');
            notifications.forEach(notification => {
                notification.classList.remove('unread');
            });
            updateNotificationBadge();
        });
    }
}

function updateNotificationBadge() {
    const badge = document.querySelector('.notification-badge');
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
    }
}

// User Profile
function initUserProfile() {
    const userProfile = document.querySelector('.user-profile');
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    if (userProfile && profileDropdown) {
        document.addEventListener('click', (e) => {
            if (!userProfile.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.style.display = 'none';
            }
        });
        
        userProfile.addEventListener('click', () => {
            const isVisible = profileDropdown.style.display === 'block';
            profileDropdown.style.display = isVisible ? 'none' : 'block';
        });
    }
}

// Breadcrumbs
function updateBreadcrumbs() {
    const breadcrumbList = document.querySelector('.breadcrumb-list');
    if (!breadcrumbList) return;
    
    const pathSegments = window.location.pathname.split('/').filter(Boolean);
    breadcrumbList.innerHTML = '<li><a href="/">Home</a></li>';
    
    let currentPath = '';
    pathSegments.forEach((segment, index) => {
        currentPath += `/${segment}`;
        const isLast = index === pathSegments.length - 1;
        const formattedSegment = segment.split('-').map(word => 
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
        
        const li = document.createElement('li');
        if (isLast) {
            li.innerHTML = `<span>${formattedSegment}</span>`;
        } else {
            li.innerHTML = `<a href="${currentPath}">${formattedSegment}</a>`;
        }
        breadcrumbList.appendChild(li);
    });
}

// Sidebar Menu Navigation
function initSidebarMenu() {
    // Handle all menu item clicks including those in the main menu and submenu
    document.querySelector('.sidebar-menu').addEventListener('click', (e) => {
        const link = e.target.closest('a');
        
        if (!link) return; // If click wasn't on a link, ignore it
        
        // If it's a submenu toggle, handle the toggle
        if (link.classList.contains('submenu-toggle')) {
            e.preventDefault();
            const menuItem = link.closest('.has-submenu');
            
            // Close other open submenus
            document.querySelectorAll('.has-submenu.open').forEach(item => {
                if (item !== menuItem) {
                    item.classList.remove('open');
                    const submenu = item.querySelector('.submenu');
                    if (submenu) {
                        submenu.style.display = 'none';
                    }
                }
            });
            
            // Toggle current submenu
            menuItem.classList.toggle('open');
            const submenu = menuItem.querySelector('.submenu');
            if (submenu) {
                submenu.style.display = menuItem.classList.contains('open') ? 'block' : 'none';
            }
            return;
        }
        
        // For regular menu items, navigate to the href
        const href = link.getAttribute('href');
        if (href && href !== '#') {
            e.preventDefault();
            window.location.href = href;
        }
    });
    
    // Set active menu item based on current URL
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && href !== '#' && (currentPath === href || currentPath.startsWith(href + '/'))) {
            // Add active class to the menu item
            item.closest('.menu-item')?.classList.add('active');
            
            // If it's in a submenu, open the parent menu
            const parentSubmenu = item.closest('.submenu');
            if (parentSubmenu) {
                const parentMenuItem = parentSubmenu.closest('.has-submenu');
                if (parentMenuItem) {
                    parentMenuItem.classList.add('open');
                    parentSubmenu.style.display = 'block';
                }
            }
        }
    });
}

// Initialize all features
document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initSidebarMenu();
    initSidebarSearch();
    initQuickActions();
    initNotifications();
    initUserProfile();
    updateBreadcrumbs();
    updateNotificationBadge();
}); 