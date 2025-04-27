$(document).ready(function() {
    // Initialize AdminLTE Tree Menu
    if (typeof $.fn.Treeview !== 'undefined') {
        $('.nav-sidebar').Treeview('init');
    }

    // Store menu state in localStorage
    $('.nav-treeview').on('expanded.lte.treeview collapsed.lte.treeview', function(e) {
        const menuItem = $(this).closest('.nav-item');
        const menuId = menuItem.data('menu-id');
        if (e.type === 'expanded') {
            localStorage.setItem('menu_' + menuId, 'open');
        } else {
            localStorage.removeItem('menu_' + menuId);
        }
    });

    // Restore menu state from localStorage
    $('.nav-item').each(function() {
        const menuId = $(this).data('menu-id');
        if (localStorage.getItem('menu_' + menuId) === 'open') {
            $(this).addClass('menu-open');
            $(this).children('.nav-treeview').show();
        }
    });

    // Handle active menu items
    const currentUrl = window.location.pathname;
    const menuItem = $('.nav-sidebar a[href$="' + currentUrl.substring(currentUrl.lastIndexOf('/') + 1) + '"]');
    
    if (menuItem.length) {
        menuItem.addClass('active');
        
        // Expand parent menu items
        menuItem.parents('.nav-treeview').show();
        menuItem.parents('.nav-item').addClass('menu-open');
        menuItem.parents('.nav-item').children('.nav-link').addClass('active');
    }

    // Handle menu toggle button
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        const body = $('body');
        
        if (body.hasClass('sidebar-collapse')) {
            body.removeClass('sidebar-collapse');
            localStorage.setItem('sidebar_collapsed', 'false');
        } else {
            body.addClass('sidebar-collapse');
            localStorage.setItem('sidebar_collapsed', 'true');
        }
    });

    // Restore sidebar state
    if (localStorage.getItem('sidebar_collapsed') === 'true') {
        $('body').addClass('sidebar-collapse');
    }

    // Handle submenu animations
    $('.nav-sidebar .nav-link').on('click', function(e) {
        const hasSubmenu = $(this).next('.nav-treeview').length > 0;
        
        if (hasSubmenu) {
            e.preventDefault();
            const menuItem = $(this).parent('.nav-item');
            
            if (menuItem.hasClass('menu-open')) {
                menuItem.removeClass('menu-open');
                $(this).next('.nav-treeview').slideUp(300);
            } else {
                // Close other open menus at the same level
                menuItem.siblings('.menu-open').removeClass('menu-open').children('.nav-treeview').slideUp(300);
                
                // Open this menu
                menuItem.addClass('menu-open');
                $(this).next('.nav-treeview').slideDown(300);
            }
        }
    });
}); 