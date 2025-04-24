// Sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle submenu toggles
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const menuItem = this.closest('.menu-item');
            menuItem.classList.toggle('open');
            
            // Close other open submenus
            const otherOpenMenus = document.querySelectorAll('.menu-item.open');
            otherOpenMenus.forEach(menu => {
                if (menu !== menuItem) {
                    menu.classList.remove('open');
                }
            });
        });
    });

    // Handle menu item clicks
    const menuItems = document.querySelectorAll('.menu-item > a:not(.submenu-toggle)');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(menu => {
                menu.classList.remove('active');
            });
            
            // Add active class to clicked menu item
            this.closest('.menu-item').classList.add('active');
        });
    });

    // Handle quick search
    const searchInput = document.querySelector('.sidebar-search .search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const menuItems = document.querySelectorAll('.menu-item');
            
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

    // Handle sidebar collapse
    const collapseBtn = document.querySelector('.collapse-btn');
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
        });
    }
}); 