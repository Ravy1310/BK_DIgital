
// Global base URL
const BASE_URL = window.location.origin + '/BK_DIGITAL';


class LogAktivitas {
    constructor() {
        // Cek jika instance sudah ada
        if (LogAktivitas.instance) {
            return LogAktivitas.instance;
        }
        
        this.allLogs = [];
        this.currentFilter = 'all';
        this.isInitialized = false;
        this.eventListeners = [];
        
        // Bind methods
        this.loadLogData = this.loadLogData.bind(this);
        this.handleFilterClick = this.handleFilterClick.bind(this);
        this.applyFilter = this.applyFilter.bind(this);
        
        // Simpan instance
        LogAktivitas.instance = this;
    }

    init() {
        if (this.isInitialized) {
            console.log('LogAktivitas already initialized, skipping...');
            this.loadLogData(); // Still load data, but don't re-init
            return;
        }
        
        console.log('Log Aktivitas System Initialized');
        this.bindEvents();
        this.loadLogData();
        this.isInitialized = true;
    }

    bindEvents() {
        // HAPUS event listeners lama jika ada
        this.removeEventListeners();
        
        // Event untuk refresh button
        const refreshHandler = (e) => {
            if (e.target.id === 'refreshBtn' || e.target.closest('#refreshBtn')) {
                this.loadLogData();
            }
        };
        
        // Event untuk filter buttons
        const filterHandler = (e) => {
            if (e.target.classList.contains('filter-btn') || e.target.closest('.filter-btn')) {
                const button = e.target.classList.contains('filter-btn') ? e.target : e.target.closest('.filter-btn');
                this.handleFilterClick(button);
            }
        };
        
        // Tambah event listeners baru
        document.addEventListener('click', refreshHandler);
        document.addEventListener('click', filterHandler);
        
        // Simpan reference untuk cleanup
        this.eventListeners.push({ type: 'click', handler: refreshHandler });
        this.eventListeners.push({ type: 'click', handler: filterHandler });
    }

    removeEventListeners() {
        // Remove all registered event listeners
        this.eventListeners.forEach(({ type, handler }) => {
            document.removeEventListener(type, handler);
        });
        this.eventListeners = [];
    }

    destroy() {
        // Cleanup method
        this.removeEventListeners();
        this.isInitialized = false;
    }

    handleFilterClick(button) {
        if (!button) return;
        
        // Remove active class from all filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        button.classList.add('active');
        
        this.currentFilter = button.dataset.filter;
        this.applyFilter();
    }

    applyFilter() {
        let filteredLogs = this.allLogs;

        if (this.currentFilter !== 'all') {
            filteredLogs = this.allLogs.filter(log => {
                const action = log.action.toLowerCase();
                
                switch (this.currentFilter) {
                    case 'create':
                        // Filter untuk aksi tambah/create
                        return action.includes('tambah') || 
                               action.includes('buat') || 
                               action.includes('create') ||
                               action.includes('insert') ||
                               action.includes('add');
                    
                    case 'edit':
                        // Filter untuk aksi edit/update
                        return action.includes('edit') || 
                               action.includes('update') || 
                               action.includes('ubah') ||
                               action.includes('modify') ||
                               action.includes('change');
                    
                    case 'delete':
                        // Filter untuk aksi hapus/delete
                        return action.includes('hapus') || 
                               action.includes('delete') || 
                               action.includes('remove') ||
                               action.includes('destroy');
                    
                    default:
                        return true;
                }
            });
        }

        this.displayLogs(filteredLogs);
    }

    async loadLogData() {
        this.showLoading();
        this.disableButtons(true);

        try {
            console.log('Loading log data...');
            
            // PATH YANG BENAR
            const apiUrl = BASE_URL + '/includes/developer_control/get_activity_logs.php?limit=50';
            
            console.log('Trying API URL:', apiUrl);
            
            let response = await fetch(apiUrl);
            
            console.log('Response status:', response.status);
            console.log('Response OK:', response.ok);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const responseText = await response.text();
            console.log('Raw response:', responseText);
            
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                this.useMockData();
                return;
            }
            
            if (data.success) {
                this.allLogs = data.logs || [];
                this.applyFilter();
                this.updateStatistics(data);
                console.log('Data loaded successfully:', this.allLogs.length, 'logs');
            } else {
                throw new Error(data.message || 'Unknown error from API');
            }
            
        } catch (error) {
            console.error('Error loading from API:', error);
            this.useMockData();
        } finally {
            this.disableButtons(false);
        }
    }

    displayLogs(logs) {
        const logContainer = document.getElementById('logContainer');
        if (!logContainer) return;
        
        if (!logs || logs.length === 0) {
            logContainer.innerHTML = this.getEmptyState();
            return;
        }
        
        let html = '';
        
        logs.forEach(log => {
            html += this.getLogItemHTML(log);
        });
        
        logContainer.innerHTML = html;
        this.updateLogCount(logs.length);
    }

    getLogItemHTML(log) {
        const timeAgo = this.getTimeAgo(log.created_at);
        const badgeClass = this.getBadgeClass(log.action);
        
        // Parse meta data dengan error handling
        let meta = null;
        let metaDisplay = '';
        
        if (log.meta) {
            try {
                meta = typeof log.meta === 'string' ? JSON.parse(log.meta) : log.meta;
                metaDisplay = this.formatMeta(meta);
            } catch (error) {
                console.error('Error parsing meta:', error);
                metaDisplay = 'Invalid metadata format';
            }
        }
        
        return `
            <div class="card log-item mb-3 border-${badgeClass}-subtle" data-action="${log.action}">
                <div class="card-body bg-${badgeClass}-subtle rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <strong class="me-2">${log.admin_name}</strong>
                                <span class="badge bg-${badgeClass} badge-action">${log.action.toUpperCase()}</span>
                            </div>
                            <p class="mb-1">${log.description}</p>
                            ${metaDisplay ? `
                                <div class="mt-2 p-2 bg-white rounded border">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Detail:</strong> ${metaDisplay}
                                    </small>
                                </div>
                                <br>
                            ` : ''}
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                ${timeAgo} • ${this.formatDateTime(log.created_at)}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    formatMeta(meta) {
        try {
            // Jika meta sudah string, coba parse dulu
            if (typeof meta === 'string') {
                try {
                    meta = JSON.parse(meta);
                } catch (e) {
                    // Jika bukan JSON, return langsung
                    return meta;
                }
            }
            
            // Jika meta adalah objek
            if (typeof meta === 'object' && meta !== null) {
                const formatted = [];
                
                // Format data_baru dan data_lama khusus dengan JSON.stringify
                if (meta.data_baru) {
                    if (typeof meta.data_baru === 'object') {
                        formatted.push(`data_baru: ${JSON.stringify(meta.data_baru)}`);
                    } else {
                        formatted.push(`data_baru: ${meta.data_baru}`);
                    }
                }
                
                if (meta.data_lama) {
                    if (typeof meta.data_lama === 'object') {
                        formatted.push(`data_lama: ${JSON.stringify(meta.data_lama)}`);
                    } else {
                        formatted.push(`data_lama: ${meta.data_lama}`);
                    }
                }
                
                // Format field lainnya
                Object.entries(meta).forEach(([key, value]) => {
                    if (key !== 'data_baru' && key !== 'data_lama') {
                        if (typeof value === 'object' && value !== null) {
                            formatted.push(`${key}: ${JSON.stringify(value)}`);
                        } else {
                            formatted.push(`${key}: ${value}`);
                        }
                    }
                });
                
                return formatted.join(', ');
            }
            
            return String(meta);
        } catch (error) {
            console.error('Error formatting meta:', error);
            return 'Error parsing metadata';
        }
    }

    getEmptyState() {
        return `
            <div class="empty-log">
                <i class="fas fa-clipboard-list fa-3x mb-3 text-muted"></i>
                <h5>Belum ada aktivitas</h5>
                <p class="text-muted">Tidak ada log aktivitas yang tercatat</p>
            </div>
        `;
    }

    updateStatistics(data) {
        this.safeSetText('#totalAktivitas', data.total || '0');
        
        // Hitung aktivitas hari ini
        const today = new Date().toDateString();
        const todayLogs = data.logs.filter(log => 
            new Date(log.created_at).toDateString() === today
        );
        this.safeSetText('#aktivitasHariIni', todayLogs.length);
        
        // Hitung admin unik
        const uniqueAdmins = [...new Set(data.logs.map(log => log.admin_name))];
        this.safeSetText('#adminAktif', uniqueAdmins.length);
        
        // Aksi terpopuler
        const popularAction = this.getPopularAction(data.logs);
        this.safeSetText('#aksiPopuler', popularAction);
    }

    safeSetText(selector, text) {
        const element = document.querySelector(selector);
        if (element) {
            element.textContent = text;
        }
    }

    getPopularAction(logs) {
        const actionCount = {};
        logs.forEach(log => {
            actionCount[log.action] = (actionCount[log.action] || 0) + 1;
        });
        
        const popular = Object.keys(actionCount).reduce((a, b) => 
            actionCount[a] > actionCount[b] ? a : b, '-'
        );
        return popular.toUpperCase();
    }

    useMockData() {
        const mockData = {
            success: true,
            logs: [
                {
                    id: 1,
                    admin_name: 'superadmin',
                    action: 'UPDATE_ADMIN',
                    description: 'Admin superadmin memperbarui data admin: zahra',
                    created_at: new Date().toISOString(),
                    meta: JSON.stringify({
                        admin_id: 1,
                        id_admin: 5,
                        data_baru: { name: 'zahra updated', role: 'admin', email: 'zahra@email.com' },
                        data_lama: { name: 'zahra', role: 'user', email: 'zahra@email.com' },
                        admin_name: 'superadmin'
                    })
                },
                {
                    id: 2,
                    admin_name: 'Rafi Isnanto Syahlefi',
                    action: 'UPDATE_GURU',
                    description: 'Admin Rafi Isnanto Syahlefi memperbarui data guru: Siti Rahayu',
                    created_at: new Date(Date.now() - 13 * 60 * 1000).toISOString(),
                    meta: JSON.stringify({
                        id_guru: 7,
                        admin_id: 4,
                        data_baru: { nama: 'Siti Rahayu', mapel: 'Matematika', nip: '1987654321' },
                        data_lama: { nama: 'Siti Rahayu', mapel: 'Fisika', nip: '1987654321' },
                        admin_name: 'Rafi Isnanto Syahlefi'
                    })
                },
                {
                    id: 3,
                    admin_name: 'Developer',
                    action: 'CREATE_USER',
                    description: 'Membuat user baru: Budi Santoso',
                    created_at: new Date(Date.now() - 25 * 60 * 1000).toISOString(),
                    meta: JSON.stringify({
                        user_id: 15,
                        data_baru: { username: 'budi.santoso', role: 'teacher', status: 'active' },
                        admin_name: 'Developer'
                    })
                },
                {
                    id: 4,
                    admin_name: 'Admin Sekolah',
                    action: 'DELETE_SISWA',
                    description: 'Menghapus data siswa: Andi Pratama',
                    created_at: new Date(Date.now() - 45 * 60 * 1000).toISOString(),
                    meta: JSON.stringify({
                        siswa_id: 23,
                        data_lama: { nama: 'Andi Pratama', kelas: '12 IPA 1', nis: '123456' },
                        admin_name: 'Admin Sekolah'
                    })
                },
                {
                    id: 5,
                    admin_name: 'System',
                    action: 'INFO',
                    description: 'Menggunakan data demo - API sedang dalam perbaikan',
                    created_at: new Date(Date.now() - 60 * 60 * 1000).toISOString(),
                    meta: JSON.stringify({
                        type: 'demo_data',
                        timestamp: new Date().toISOString()
                    })
                }
            ],
            total: 5
        };
        
        this.allLogs = mockData.logs;
        this.applyFilter();
        this.updateStatistics(mockData);
        this.showMessage('Menggunakan data demo sementara', 'warning');
    }

    // Helper methods
    getBadgeClass(action) {
        const actionLower = action.toLowerCase();
        
        if (actionLower.includes('tambah') || actionLower.includes('buat') || 
            actionLower.includes('create') || actionLower.includes('insert') || 
            actionLower.includes('add')) {
            return 'success';
        }
        
        if (actionLower.includes('edit') || actionLower.includes('update') || 
            actionLower.includes('ubah') || actionLower.includes('modify') || 
            actionLower.includes('change')) {
            return 'warning';
        }
        
        if (actionLower.includes('hapus') || actionLower.includes('delete') || 
            actionLower.includes('remove') || actionLower.includes('destroy')) {
            return 'danger';
        }
        
        if (actionLower.includes('login') || actionLower.includes('logout')) {
            return 'info';
        }
        
        return 'primary';
    }

    getTimeAgo(timestamp) {
        const now = new Date();
        const past = new Date(timestamp);
        const diffInSeconds = Math.floor((now - past) / 1000);
        
        if (diffInSeconds < 60) return 'Baru saja';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit lalu`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam lalu`;
        return `${Math.floor(diffInSeconds / 86400)} hari lalu`;
    }

    formatDateTime(timestamp) {
        return new Date(timestamp).toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    updateLogCount(count) {
        this.safeSetText('#logCount', `${count} aktivitas`);
    }

    showLoading() {
        const logContainer = document.getElementById('logContainer');
        if (!logContainer) return;
        
        logContainer.innerHTML = `
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 text-muted">Memuat log aktivitas...</p>
            </div>
        `;
    }

    showError(message) {
        const logContainer = document.getElementById('logContainer');
        if (!logContainer) return;
        
        logContainer.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `;
    }

    showMessage(message, type = 'info') {
        const logContainer = document.getElementById('logContainer');
        if (!logContainer) return;
        
        const alertClass = type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        logContainer.insertBefore(alert, logContainer.firstChild);
        
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 3000);
    }

    disableButtons(disabled) {
        const buttons = document.querySelectorAll('#refreshBtn, #createSampleBtn, .filter-btn');
        buttons.forEach(btn => {
            if (btn) {
                btn.disabled = disabled;
                if (disabled) {
                    btn.innerHTML = btn.innerHTML.replace('fa-sync-alt', 'fa-spinner fa-spin')
                                                .replace('fa-plus', 'fa-spinner fa-spin');
                } else {
                    btn.innerHTML = btn.innerHTML.replace('fa-spinner fa-spin', 'fa-sync-alt')
                                                .replace('fa-spinner fa-spin', 'fa-plus');
                }
            }
        });
    }
}

// Global instance
let logAktivitasInstance = null;
function getLogAktivitasInstance() {
    if (!logAktivitasInstance) {
        logAktivitasInstance = new LogAktivitas();
    }
    return logAktivitasInstance;
}

// Initialize when DOM is ready
function initLogAktivitas() {
    setTimeout(() => {
        const logSystem = getLogAktivitasInstance();
        logSystem.init();
    }, 100);
}

// Auto-init jika halaman log aktivitas
if (document.querySelector('#logContainer')) {
    document.addEventListener('DOMContentLoaded', initLogAktivitas);
}
