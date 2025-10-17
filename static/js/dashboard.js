/**
 * Dashboard JavaScript untuk PT. Superior Teknik Indonesia
 * Menggunakan PHP API endpoints
 */

// Base API URL
const API_BASE = 'api/';

// Helper function untuk API calls
async function apiCall(endpoint, options = {}) {
    const url = API_BASE + endpoint;
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
        },
    };
    
    const config = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(url, config);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'API call failed');
        }
        
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Authentication functions
async function login(username, password) {
    return await apiCall('login.php', {
        method: 'POST',
        body: JSON.stringify({ username, password })
    });
}

async function logout() {
    return await apiCall('logout.php', {
        method: 'POST'
    });
}

async function checkAuth() {
    return await apiCall('check_auth.php');
}

// Dashboard functions
async function getDashboardData() {
    return await apiCall('dashboard.php');
}

// Content management functions
async function getAllContent() {
    return await apiCall('get_all.php');
}

async function updateHomeContent(data) {
    return await apiCall('home_section.php', {
        method: 'POST',
        body: JSON.stringify(data)
    });
}

async function updateAboutContent(formData) {
    return await apiCall('about_section.php', {
        method: 'POST',
        body: formData
    });
}

async function updateServicesContent(formData) {
    return await apiCall('services.php', {
        method: 'POST',
        body: formData
    });
}

async function updateVisionContent(formData) {
    return await apiCall('vision.php', {
        method: 'POST',
        body: formData
    });
}

async function updateMissionContent(data) {
    return await apiCall('mission.php', {
        method: 'POST',
        body: JSON.stringify(data)
    });
}

async function updateContactContent(data) {
    return await apiCall('contact.php', {
        method: 'POST',
        body: JSON.stringify(data)
    });
}

// Project management functions
async function getProjects() {
    return await apiCall('projects.php');
}

async function addProject(formData) {
    return await apiCall('projects_add.php', {
        method: 'POST',
        body: formData
    });
}

async function editProject(id, formData) {
    return await apiCall('projects_edit.php', {
        method: 'POST',
        body: formData
    });
}

async function deleteProject(id) {
    return await apiCall('projects_delete.php', {
        method: 'POST',
        body: JSON.stringify({ id })
    });
}

// Image upload function
async function uploadImage(file) {
    const formData = new FormData();
    formData.append('image', file);
    
    return await apiCall('upload_image.php', {
        method: 'POST',
        body: formData
    });
}

// Utility functions
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}

function showError(message) {
    showNotification(message, 'danger');
}

function showSuccess(message) {
    showNotification(message, 'success');
}

// Form handling utilities
function serializeForm(form) {
    const formData = new FormData(form);
    return formData;
}

function getFormData(form) {
    const formData = new FormData(form);
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    return data;
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check authentication status
    checkAuth().then(response => {
        if (!response.authenticated) {
            // Redirect to login if not authenticated
            window.location.href = 'login.html';
        }
    }).catch(error => {
        console.error('Auth check failed:', error);
        window.location.href = 'login.html';
    });
    
    // Load dashboard data
    loadDashboardData();
});

async function loadDashboardData() {
    try {
        const response = await getDashboardData();
        if (response.success) {
            // Update dashboard statistics
            updateDashboardStats(response.data);
        }
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
        showError('Gagal memuat data dashboard');
    }
}

function updateDashboardStats(data) {
    // Update statistics cards if they exist
    const projectCount = document.querySelector('[data-stat="projects"]');
    const serviceCount = document.querySelector('[data-stat="services"]');
    const contactCount = document.querySelector('[data-stat="contacts"]');
    
    if (projectCount) projectCount.textContent = data.total_projects || 0;
    if (serviceCount) serviceCount.textContent = data.total_services || 0;
    if (contactCount) contactCount.textContent = data.total_contacts || 0;
}

// Export functions for global use
window.DashboardAPI = {
    login,
    logout,
    checkAuth,
    getDashboardData,
    getAllContent,
    updateHomeContent,
    updateAboutContent,
    updateServicesContent,
    updateVisionContent,
    updateMissionContent,
    updateContactContent,
    getProjects,
    addProject,
    editProject,
    deleteProject,
    uploadImage,
    showNotification,
    showError,
    showSuccess,
    serializeForm,
    getFormData
};