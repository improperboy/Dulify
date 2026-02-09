/**
 * Dulify Admin Panel JavaScript
 * Handles responsive sidebar and other admin panel functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const adminContent = document.querySelector('.admin-content');
    const overlay = document.getElementById('sidebar-overlay');
    
    // Check if sidebar is open in mobile view (using localStorage for persistence)
    function isSidebarOpen() {
        return localStorage.getItem('dulify_sidebar_open') === 'true';
    }
    
    // Set sidebar state
    function setSidebarState(isOpen) {
        localStorage.setItem('dulify_sidebar_open', isOpen ? 'true' : 'false');
        
        if (isOpen) {
            adminSidebar.classList.add('open');
            adminContent.classList.add('sidebar-open');
            if (overlay) overlay.classList.add('active');
            if (sidebarToggle) sidebarToggle.classList.add('open');
        } else {
            adminSidebar.classList.remove('open');
            adminContent.classList.remove('sidebar-open');
            if (overlay) overlay.classList.remove('active');
            if (sidebarToggle) sidebarToggle.classList.remove('open');
        }
    }
    
    // Initialize sidebar state based on screen size
    function initSidebar() {
        const isMobile = window.innerWidth < 992;
        
        if (isMobile) {
            // On mobile, default to closed unless explicitly opened
            setSidebarState(isSidebarOpen());
        } else {
            // On desktop, always open
            setSidebarState(true);
        }
    }
    
    // Toggle sidebar
    function toggleSidebar() {
        setSidebarState(!isSidebarOpen());
    }
    
    // Event listeners
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    if (overlay) {
        overlay.addEventListener('click', function() {
            setSidebarState(false);
        });
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        initSidebar();
    });
    
    // Initialize on page load
    initSidebar();
});
