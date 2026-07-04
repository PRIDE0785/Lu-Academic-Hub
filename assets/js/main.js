/**
 * LU Academic Hub - Main JavaScript
 * Complete functionality for the site
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initDarkMode();
    initSearchAutocomplete();
    initToastNotifications();
    initPasswordToggle();
    initFileUpload();
    initRatingSystem();
    initInfiniteScroll();
    initResponsiveSidebar();
    initTooltips();
});

/**
 * Dark Mode Toggle
 */
function initDarkMode() {
    const toggleBtn = document.getElementById('darkModeToggle');
    if (!toggleBtn) return;
    
    const html = document.documentElement;
    const icon = toggleBtn.querySelector('i');
    
    // Load saved preference
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    updateIcon(icon, savedTheme);
    
    toggleBtn.addEventListener('click', function() {
        const current = html.getAttribute('data-theme');
        const newTheme = current === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon(icon, newTheme);
        
        // Dispatch event for other components
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: newTheme } }));
    });
}

function updateIcon(icon, theme) {
    if (!icon) return;
    icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

/**
 * Search Autocomplete
 */
function initSearchAutocomplete() {
    const input = document.querySelector('.search-input');
    const results = document.querySelector('.search-results');
    if (!input || !results) return;
    
    let timeout;
    let currentQuery = '';
    
    input.addEventListener('input', function() {
        const query = this.value.trim();
        currentQuery = query;
        
        clearTimeout(timeout);
        
        if (query.length < 2) {
            results.style.display = 'none';
            return;
        }
        
        timeout = setTimeout(() => {
            fetchSearchResults(query);
        }, 300);
    });
    
    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !results.contains(e.target)) {
            results.style.display = 'none';
        }
    });
    
    // Keyboard navigation
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const firstResult = results.querySelector('.list-group-item');
            if (firstResult) {
                window.location.href = firstResult.getAttribute('href');
            } else if (currentQuery.length >= 2) {
                window.location.href = `/search.php?q=${encodeURIComponent(currentQuery)}`;
            }
        }
    });
}

async function fetchSearchResults(query) {
    const results = document.querySelector('.search-results');
    if (!results) return;
    
    try {
        const response = await fetch(`/api/search-suggestions.php?q=${encodeURIComponent(query)}`);
        if (!response.ok) throw new Error('Search failed');
        const data = await response.json();
        renderSearchResults(data, results);
    } catch (error) {
        console.error('Search error:', error);
        results.innerHTML = '<div class="p-3 text-muted">Search unavailable</div>';
        results.style.display = 'block';
    }
}

function renderSearchResults(data, container) {
    if (!container) return;
    
    if (!data || data.length === 0) {
        container.innerHTML = '<div class="p-3 text-muted">No results found</div>';
        container.style.display = 'block';
        return;
    }
    
    let html = '';
    data.forEach(item => {
        html += `
            <a href="/paper/view.php?id=${item.id}" class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${escapeHtml(item.title)}</strong>
                        <div class="text-muted small">${escapeHtml(item.course_code || '')}</div>
                    </div>
                    <span class="badge bg-primary">${item.downloads || 0} downloads</span>
                </div>
            </a>
        `;
    });
    
    container.innerHTML = html;
    container.style.display = 'block';
}

/**
 * Toast Notifications
 */
function initToastNotifications() {
    // Auto-dismiss toasts after 5 seconds
    document.querySelectorAll('.toast').forEach(toast => {
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    });
}

function showToast(message, type = 'info', duration = 5000) {
    const container = document.querySelector('.toast-container') || createToastContainer();
    
    const colors = {
        success: '#48bb78',
        error: '#fc8181',
        warning: '#ed8936',
        info: '#667eea'
    };
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.borderLeftColor = colors[type] || colors.info;
    toast.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <span>${escapeHtml(message)}</span>
            <button class="btn-close btn-close-sm" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

window.showToast = showToast;

/**
 * Password Toggle
 */
function initPasswordToggle() {
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    });
}

/**
 * File Upload
 */
function initFileUpload() {
    document.querySelectorAll('.file-upload-input').forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            
            const preview = this.parentElement.querySelector('.file-preview');
            if (preview) {
                const size = (file.size / 1024 / 1024).toFixed(2);
                preview.innerHTML = `
                    <div class="file-info d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-danger fa-2x"></i>
                        <div>
                            <div>${escapeHtml(file.name)}</div>
                            <small class="text-muted">${size} MB</small>
                        </div>
                    </div>
                `;
                preview.style.display = 'block';
            }
            
            // Validate
            validateFile(file);
        });
    });
}

function validateFile(file) {
    const maxSize = 50 * 1024 * 1024;
    const allowedTypes = ['application/pdf', 'application/msword', 
                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    if (file.size > maxSize) {
        showToast('File size exceeds 50MB limit', 'error');
        return false;
    }
    
    const ext = file.name.split('.').pop().toLowerCase();
    const allowedExts = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'jpg', 'jpeg', 'png'];
    if (!allowedExts.includes(ext)) {
        showToast('File type not allowed', 'error');
        return false;
    }
    
    return true;
}

/**
 * Rating System
 */
function initRatingSystem() {
    document.querySelectorAll('.rating-stars').forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.querySelector('.rating-input');
        const paperId = container.dataset.paperId;
        
        if (!paperId) return;
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = parseInt(this.dataset.value);
                updateStars(stars, value);
                if (input) input.value = value;
                submitRating(paperId, value);
            });
            
            star.addEventListener('mouseenter', function() {
                const value = parseInt(this.dataset.value);
                updateStars(stars, value, true);
            });
            
            star.addEventListener('mouseleave', function() {
                const current = parseInt(input?.value) || 0;
                updateStars(stars, current, false);
            });
        });
    });
}

function updateStars(stars, value, hover = false) {
    stars.forEach(star => {
        const val = parseInt(star.dataset.value);
        if (val <= value) {
            star.className = 'fas fa-star';
            star.style.color = hover ? '#f6ad55' : '#ecc94b';
        } else {
            star.className = 'far fa-star';
            star.style.color = '#d1d5db';
        }
    });
}

async function submitRating(paperId, rating) {
    try {
        const response = await fetch('/api/rate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ paper_id: paperId, rating })
        });
        
        const data = await response.json();
        if (data.success) {
            showToast('Rating submitted!', 'success');
            // Update average
            const avgEl = document.querySelector('.rating-average');
            if (avgEl && data.average) {
                avgEl.textContent = data.average.toFixed(1);
            }
        } else {
            showToast(data.message || 'Failed to submit rating', 'error');
        }
    } catch (error) {
        showToast('Error submitting rating', 'error');
    }
}

/**
 * Infinite Scroll
 */
function initInfiniteScroll() {
    const container = document.querySelector('.infinite-scroll-container');
    if (!container) return;
    
    let loading = false;
    let page = parseInt(container.dataset.page) || 1;
    const totalPages = parseInt(container.dataset.totalPages) || 1;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !loading && page < totalPages) {
                loadMoreItems(container, page + 1);
            }
        });
    }, { rootMargin: '100px' });
    
    const trigger = document.querySelector('.load-more-trigger');
    if (trigger) observer.observe(trigger);
}

async function loadMoreItems(container, nextPage) {
    const loading = document.querySelector('.loading-indicator');
    if (loading) loading.style.display = 'block';
    
    try {
        const url = new URL(window.location.href);
        url.searchParams.set('page', nextPage);
        url.searchParams.set('ajax', '1');
        
        const response = await fetch(url);
        const html = await response.text();
        
        // Parse and append items
        const temp = document.createElement('div');
        temp.innerHTML = html;
        const items = temp.querySelectorAll('.paper-card');
        
        items.forEach(item => {
            container.appendChild(item);
        });
        
        page = nextPage;
        container.dataset.page = page;
        
        if (page >= totalPages) {
            const trigger = document.querySelector('.load-more-trigger');
            if (trigger) trigger.remove();
        }
    } catch (error) {
        console.error('Error loading more:', error);
    } finally {
        if (loading) loading.style.display = 'none';
    }
}

/**
 * Responsive Sidebar
 */
function initResponsiveSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    const toggleBtn = document.querySelector('.sidebar-toggle');
    
    if (!sidebar) return;
    
    function toggleSidebar(show) {
        sidebar.classList.toggle('show', show);
        if (overlay) overlay.classList.toggle('show', show);
        document.body.style.overflow = show ? 'hidden' : '';
    }
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => toggleSidebar(true));
    }
    
    if (overlay) {
        overlay.addEventListener('click', () => toggleSidebar(false));
    }
    
    // Close on escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') toggleSidebar(false);
    });
}

/**
 * Tooltips
 */
function initTooltips() {
    document.querySelectorAll('[data-tooltip]').forEach(el => {
        el.addEventListener('mouseenter', function(e) {
            const tip = document.createElement('div');
            tip.className = 'tooltip-custom';
            tip.textContent = this.dataset.tooltip;
            tip.style.cssText = `
                position: fixed;
                background: var(--text-primary);
                color: var(--bg-primary);
                padding: 4px 10px;
                border-radius: 4px;
                font-size: 12px;
                pointer-events: none;
                z-index: 9999
