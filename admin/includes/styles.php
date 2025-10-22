<?php
// Styles component untuk admin panel
?>
<style>
  body {
    background: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .navbar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }
  .card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
  }
  .card:hover {
    transform: translateY(-5px);
  }
  .stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }
  .btn-action {
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    z-index: 1000;
    transition: all 0.3s ease;
    display: block !important;
  }
  .sidebar.collapsed {
    width: 60px;
  }
  .sidebar .nav-link {
    color: white;
    padding: 15px 20px;
    border-radius: 0;
    transition: all 0.3s ease;
  }
  .sidebar .nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
  }
  .sidebar .nav-link.active {
    background: rgba(255,255,255,0.2);
    color: white;
  }
  .main-content {
    margin-left: 250px;
    transition: all 0.3s ease;
  }
  .main-content.expanded {
    margin-left: 60px;
  }
  .sidebar-toggle {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1001;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    padding: 10px;
    border-radius: 5px;
  }
  @media (max-width: 768px) {
    .sidebar {
      transform: translateX(-100%);
      display: block !important;
    }
    .sidebar.show {
      transform: translateX(0);
    }
    .main-content {
      margin-left: 0;
    }
  }
  
  @media (min-width: 769px) {
    .sidebar {
      display: block !important;
      transform: translateX(0);
    }
  }
</style>

