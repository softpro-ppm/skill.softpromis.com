document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.querySelector('.search-box input');
    const dataTable = document.querySelector('.data-table tbody');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = dataTable.querySelectorAll('tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Action buttons functionality
    const actionButtons = document.querySelectorAll('.actions .btn-icon');
    
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('title');
            const row = this.closest('tr');
            const id = row.querySelector('td:first-child').textContent;
            
            switch(action) {
                case 'View':
                    window.location.href = `view.html?id=${id}`;
                    break;
                case 'Edit':
                    window.location.href = `edit.html?id=${id}`;
                    break;
                case 'Delete':
                    if (confirm('Are you sure you want to delete this training partner?')) {
                        // Here you would typically make an API call to delete
                        console.log('Deleting training partner:', id);
                        row.remove();
                    }
                    break;
            }
        });
    });

    // Pagination functionality
    const paginationButtons = document.querySelectorAll('.btn-pagination');
    
    paginationButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) return;
            
            // Remove active class from all buttons
            paginationButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Here you would typically make an API call to fetch the next page
            console.log('Fetching page:', this.textContent);
        });
    });

    // Export functionality
    const exportButton = document.querySelector('.table-actions .btn-secondary:last-child');
    
    exportButton.addEventListener('click', function() {
        // Here you would typically make an API call to export data
        console.log('Exporting training partners data');
        alert('Export functionality will be implemented here.');
    });

    // Filter functionality
    const filterButton = document.querySelector('.table-actions .btn-secondary:first-child');
    
    filterButton.addEventListener('click', function() {
        // Here you would typically show a filter modal
        console.log('Showing filter options');
        alert('Filter functionality will be implemented here.');
    });
}); 