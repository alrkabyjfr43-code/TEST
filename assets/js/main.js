/**
 * Main Application Logic
 * Architecture: Single Page Application (SPA) with LocalStorage persistence.
 * Features: Dark Mode, Admin Panel, Activity Tracking, and Dynamic Views.
 */

import { createClient } from 'https://esm.sh/@supabase/supabase-js@2';

const SUPABASE_URL = 'https://jecreaimsuocilkrohuy.supabase.co';
const SUPABASE_KEY = 'sb_publishable_E1q5oy5VhHVz_mAos4XnHQ_17hZT9gk'; // User provided anon key
const supabase = createClient(SUPABASE_URL, SUPABASE_KEY);

class AppManager {
    constructor() {
        // Initialize State
        this.initData();

        // Initialize UI
        this.cacheDOM();
        this.bindEvents();

        // Restore Session
        this.restoreState();

        // Initialize External Libraries
        this.initAOS();
        this.initTilt();
        this.initSpotlight();

        // Remove Preloader
        window.addEventListener('load', () => {
            const loader = document.getElementById('preloader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(() => loader.remove(), 800);
            }
        });
    }

    // --- Initialization ---
    initData() {
        if (!localStorage.getItem('theme')) localStorage.setItem('theme', 'light');
        if (!localStorage.getItem('settings')) {
            localStorage.setItem('settings', JSON.stringify({
                facebook_url: 'https://www.facebook.com/a1mostashar/',
                instagram_url: 'https://www.instagram.com/consultant.co/',
                company_logo: 'https://karbalaholding.com/wp-content/uploads/2024/04/LOGO.jpeg'
            }));
        }
        if (!localStorage.getItem('access_logs')) localStorage.setItem('access_logs', JSON.stringify([]));
    }

    cacheDOM() {
        this.dom = {
            views: document.querySelectorAll('.view-section'),
            themeIcon: document.getElementById('theme-icon'),
            themeToggle: document.querySelector('.theme-toggle'),
            loginForm: document.getElementById('employee-login-form'),
            adminTrigger: document.querySelector('.secret-area'),
            adminModal: document.getElementById('adminModal'),
            adminForm: document.getElementById('admin-login-form'),
            settingsForm: document.getElementById('settings-form'),
            socialLinks: document.querySelectorAll('.social-icon'),
            logoutBtns: document.querySelectorAll('[onclick="logout()"]'), // Validating fallback
            logsTable: document.getElementById('logs-tbody'),
            // Search Elements
            searchInput: document.getElementById('admin-search-input'),
            searchBtn: document.getElementById('admin-search-btn'),
            searchStats: document.getElementById('search-stats'),
            statsName: document.getElementById('stats-name'),
            statsCount: document.getElementById('stats-count')
        };
    }

    initAOS() {
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 1000,
                easing: 'ease-out-cubic',
                once: true,
                offset: 50
            });
        }
    }

    initTilt() {
        if (typeof VanillaTilt !== 'undefined') {
            VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {
                max: 12,
                speed: 400,
                glare: true,
                "max-glare": 0.2,
                scale: 1.05
            });
        }
    }

    initSpotlight() {
        document.querySelectorAll('.glass-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });
    }

    // --- Core Logic: Navigation ---
    showView(viewId) {
        this.dom.views.forEach(view => {
            view.classList.remove('active');
            view.style.display = 'none'; // Force hide
        });

        const target = document.getElementById(viewId);
        if (target) {
            target.style.display = 'flex';
            // Small delay to allow display:flex to apply before adding active class for fade-in
            setTimeout(() => target.classList.add('active'), 10);

            // View specific adjustments
            if (viewId === 'admin-view') {
                document.body.style.justifyContent = 'flex-start';
                document.body.style.paddingTop = '60px';
                this.loadAdminData();
            } else {
                document.body.style.justifyContent = 'center';
                document.body.style.paddingTop = '0';
            }
        }
    }

    // --- Core Logic: Theme ---
    toggleTheme() {
        const current = localStorage.getItem('theme') || 'light';
        const next = current === 'light' ? 'dark' : 'light';
        localStorage.setItem('theme', next);
        this.applyTheme(next);
    }

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (this.dom.themeIcon) {
            this.dom.themeIcon.className = theme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }
    }

    // --- Core Logic: Authentication ---
    handleEmployeeLogin(name) {
        if (!name.trim()) return alert('الرجاء إدخال اسمك'); // Simple validation

        sessionStorage.setItem('currentUser', name);
        sessionStorage.setItem('userType', 'employee');

        this.logActivity(name, 'تسجيل دخول');
        this.logToSupabase(name, 'تسجيل دخول'); // Log login action
        this.showView('home-view');
        this.applySettingsToHome(); // Ensure links are fresh
    }

    handleAdminLogin(password) {
        if (password === '313alsb3') {
            sessionStorage.setItem('userType', 'admin');
            this.showView('admin-view');
        } else {
            alert('كود الأدمن غير صحيح');
        }
    }

    logout() {
        sessionStorage.clear();
        this.showView('login-view');
    }

    restoreState() {
        this.applyTheme(localStorage.getItem('theme') || 'light');
        this.applySettingsToHome();

        const userType = sessionStorage.getItem('userType');
        if (userType === 'admin') this.showView('admin-view');
        else if (userType === 'employee') this.showView('home-view');
        else this.showView('login-view');
    }

    // --- Core Logic: Data & Logs ---
    logActivity(name, action) {
        const logs = JSON.parse(localStorage.getItem('access_logs') || '[]');
        logs.push({
            id: logs.length + 1,
            name: name,
            action: action, // 'Login', 'Facebook', 'Instagram'
            time: new Date().toLocaleString('ar-EG'),
            device: this.getDeviceType()
        });
        localStorage.setItem('access_logs', JSON.stringify(logs));
    }

    getDeviceType(ua = navigator.userAgent) {
        if (/android/i.test(ua)) return 'Android';
        if (/iphone|ipad|ipod/i.test(ua)) return 'iOS';
        if (/windows/i.test(ua)) return 'Windows';
        if (/mac/i.test(ua)) return 'Mac';
        if (/linux/i.test(ua)) return 'Linux';
        if (/mobile/i.test(ua)) return 'Mobile';
        return 'Desktop';
    }

    async logToSupabase(name, action = 'تسجيل دخول') {
        try {
            // Get IP
            const ipRes = await fetch('https://api.ipify.org?format=json');
            const ipData = await ipRes.json();
            const ip = ipData.ip;

            // Send to Supabase
            const { error } = await supabase
                .from('logs')
                .insert({
                    name: name,
                    action: action,
                    ip: ip,
                    device: navigator.userAgent
                });


            if (error) console.error('Supabase Error:', error);

        } catch (err) {
            console.error('Supabase Logging Failed:', err);
        }
    }

    updateSettings(data) {
        localStorage.setItem('settings', JSON.stringify(data));
        this.applySettingsToHome();
        alert('تم حفظ الإعدادات بنجاح!');
        this.loadAdminData(); // Refresh admin view
    }

    applySettingsToHome() {
        const settings = JSON.parse(localStorage.getItem('settings') || '{}');

        // Logos
        document.querySelectorAll('[data-setting-logo]').forEach(img => {
            img.src = settings.company_logo || '';
        });

        // Social Links Updates
        const fbLinks = document.querySelectorAll('[data-setting-facebook]');
        const igLinks = document.querySelectorAll('[data-setting-instagram]');

        fbLinks.forEach(a => a.href = settings.facebook_url || '#');
        igLinks.forEach(a => a.href = settings.instagram_url || '#');
    }

    async loadAdminData() {
        // Render Logs from Supabase
        if (this.dom.logsTable) {
            this.dom.logsTable.innerHTML = '<tr><td colspan="5">جاري التحميل...</td></tr>';

            const { data, error } = await supabase
                .from('logs')
                .select('*')
                .order('created_at', { ascending: false });

            if (error) {
                console.error('Error fetching logs:', error);
                this.dom.logsTable.innerHTML = '<tr><td colspan="5" class="text-danger">فشل تحميل البيانات</td></tr>';
            } else {
                this.dom.logsTable.innerHTML = data.map(log => {
                    const time = new Date(log.created_at).toLocaleString('ar-EG');
                    const deviceType = this.getDeviceType(log.device);
                    const actionText = log.action || 'غير معروف';
                    return `
                    <tr>
                        <td>${log.id}</td>
                        <td>${log.name}</td>
                        <td>${time}</td>
                        <td><span class="badge ${this.getActionColor(log.action)}">${actionText}</span></td>
                        <td title="${log.device}">${deviceType} (${log.ip})</td>
                    </tr>
                `}).join('');
            }
        }

        // Populate Settings Form
        const settings = JSON.parse(localStorage.getItem('settings') || '{}');
        const form = this.dom.settingsForm;
        if (form) {
            if (form.facebook_url) form.facebook_url.value = settings.facebook_url || '';
            if (form.instagram_url) form.instagram_url.value = settings.instagram_url || '';
            if (form.logo_url) form.logo_url.value = settings.company_logo || '';
        }
    }

    async handleAdminSearch(name) {
        if (!name.trim()) {
            if (this.dom.searchStats) this.dom.searchStats.style.display = 'none';
            this.loadAdminData(); // Reset to all logs
            return;
        }

        if (this.dom.logsTable) {
            this.dom.logsTable.innerHTML = '<tr><td colspan="5">جاري البحث...</td></tr>';
        }

        // 1. Get Total Count
        const { count, error: countError } = await supabase
            .from('logs')
            .select('*', { count: 'exact', head: true })
            .ilike('name', `%${name}%`);

        // 2. Get Last 5 Records
        const { data, error: dataError } = await supabase
            .from('logs')
            .select('*')
            .ilike('name', `%${name}%`)
            .order('created_at', { ascending: false })
            .limit(5);

        if (countError || dataError) {
            console.error('Search Error:', countError || dataError);
            if (this.dom.logsTable) this.dom.logsTable.innerHTML = '<tr><td colspan="5" class="text-danger">فشل البحث</td></tr>';
            return;
        }

        // Update Stats
        if (this.dom.searchStats) {
            this.dom.searchStats.style.display = 'flex';
            this.dom.statsName.textContent = name;
            this.dom.statsCount.textContent = count;
        }

        // Render Table
        if (this.dom.logsTable) {
            if (!data || data.length === 0) {
                this.dom.logsTable.innerHTML = '<tr><td colspan="5">لا توجد سجلات لهذا الموظف</td></tr>';
            } else {
                this.dom.logsTable.innerHTML = data.map(log => {
                    const time = new Date(log.created_at).toLocaleString('ar-EG');
                    const deviceType = this.getDeviceType(log.device);
                    const actionText = log.action || 'غير معروف';
                    return `
                    <tr>
                        <td>${log.id}</td>
                        <td>${log.name}</td>
                        <td>${time}</td>
                        <td><span class="badge ${this.getActionColor(log.action)}">${actionText}</span></td>
                        <td title="${log.device}">${deviceType} (${log.ip})</td>
                    </tr>
                `}).join('');
            }
        }
    }

    getActionColor(action) {
        if (!action) return 'bg-secondary';
        if (action.includes('دخول')) return 'bg-success';
        if (action.includes('فيسبوك')) return 'bg-primary';
        if (action.includes('انستغرام')) return 'bg-danger';
        return 'bg-secondary';
    }

    // --- Event Binding ---
    bindEvents() {
        // Login Form
        if (this.dom.loginForm) {
            this.dom.loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const input = this.dom.loginForm.querySelector('input');
                this.handleEmployeeLogin(input.value);
            });
        }

        // Admin Login Form
        if (this.dom.adminForm) {
            this.dom.adminForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const input = this.dom.adminForm.querySelector('input');
                this.handleAdminLogin(input.value);
                input.value = '';
            });
        }

        // Settings Form
        if (this.dom.settingsForm) {
            this.dom.settingsForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.updateSettings({
                    facebook_url: document.getElementById('facebook_url').value,
                    instagram_url: document.getElementById('instagram_url').value,
                    company_logo: document.getElementById('logo_url').value
                });
            });
        }

        // Tracking Clicks (Delegation or Direct)
        // We re-bind to ensure we catch dynamic updates if any, but since it's SPA, direct bind works
        document.querySelectorAll('[data-setting-facebook]').forEach(btn => {
            btn.addEventListener('click', () => {
                const user = sessionStorage.getItem('currentUser');
                if (user) {
                    this.logActivity(user, 'زيارة فيسبوك');
                    this.logToSupabase(user, 'زيارة فيسبوك');
                }
            });
        });

        document.querySelectorAll('[data-setting-instagram]').forEach(btn => {
            btn.addEventListener('click', () => {
                const user = sessionStorage.getItem('currentUser');
                if (user) {
                    this.logActivity(user, 'زيارة انستغرام');
                    this.logToSupabase(user, 'زيارة انستغرام');
                }
            });
        });

        // Theme Toggle (if not using onclick in HTML, which is safer here)
        // Actually, let's bind it here to be clean
        if (this.dom.themeToggle) {
            this.dom.themeToggle.onclick = () => this.toggleTheme();
        }

        // Admin Search (Live + Debounce)
        if (this.dom.searchBtn) {
            this.dom.searchBtn.addEventListener('click', () => {
                const query = this.dom.searchInput.value;
                this.handleAdminSearch(query);
            });
        }

        let searchTimeout;
        if (this.dom.searchInput) {
            this.dom.searchInput.addEventListener('input', (e) => {
                const query = e.target.value;
                clearTimeout(searchTimeout);
                // Debounce 500ms
                searchTimeout = setTimeout(() => {
                    this.handleAdminSearch(query);
                }, 500);
            });
        }
    }
}

// Initialize
const app = new AppManager();
// Expose logout for HTML onclicks
window.logout = () => app.logout();
window.app = app; // For debugging or inline calls
