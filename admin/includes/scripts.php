<?php
// Scripts component untuk admin panel
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Sidebar toggle functionality
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    // Ensure sidebar is always visible on desktop
    if (window.innerWidth > 768) {
      sidebar.style.display = 'block';
      sidebar.classList.remove('show');
    }
    
    // Mobile sidebar toggle
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
          sidebar.classList.toggle('show');
        }
      });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
      if (window.innerWidth <= 768) {
        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
          sidebar.classList.remove('show');
        }
      }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        sidebar.style.display = 'block';
        sidebar.classList.remove('show');
      } else {
        sidebar.style.display = 'block';
      }
    });
  });
</script>

