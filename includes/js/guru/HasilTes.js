// HasilTes.js
class HasilTes {
    constructor() {
        console.log('HasilTes initialized');
        this.currentPage = 1;
        this.filters = {
            search: '',
            kelas: '',
            jenis_tes: ''
        };
        
        this.eventListeners = [];
        this.isInitialized = false;
        
        this.init();
    }

    init() {
        console.log('Initializing HasilTes...');
        
        // Tunggu sedikit untuk memastikan DOM benar-benar siap
        setTimeout(() => {
            this.startApp();
        }, 100);
    }

    startApp() {
        console.log('Starting HasilTes app...');
        
        // Cek elemen yang diperlukan
        const requiredElements = ['resultsList', 'emptyState', 'totalSiswa', 'totalTes'];
        for (const id of requiredElements) {
            if (!document.getElementById(id)) {
                console.error(`Required element #${id} not found`);
                return;
            }
        }
        
        // Sembunyikan loading spinner jika ada
        const loadingSpinner = document.getElementById('loadingSpinner');
        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
        
        this.bindEvents();
        this.loadStatistics();
        this.loadData();
        
        this.isInitialized = true;
        console.log('HasilTes app started successfully');
    }

    bindEvents() {
        console.log('Binding events...');
        
        // Search input dengan debounce
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            const handler = (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.filters.search = e.target.value;
                    this.currentPage = 1;
                    this.loadData();
                }, 500);
            };
            searchInput.addEventListener('input', handler);
            this.eventListeners.push({ element: searchInput, type: 'input', handler });
        }

        // Filter kelas
        const filterKelas = document.getElementById('filterKelas');
        if (filterKelas) {
            const handler = (e) => {
                this.filters.kelas = e.target.value;
                this.currentPage = 1;
                this.loadData();
            };
            filterKelas.addEventListener('change', handler);
            this.eventListeners.push({ element: filterKelas, type: 'change', handler });
        }

        // Filter jenis tes
        const filterJenisTes = document.getElementById('filterJenisTes');
        if (filterJenisTes) {
            const handler = (e) => {
                this.filters.jenis_tes = e.target.value;
                this.currentPage = 1;
                this.loadData();
            };
            filterJenisTes.addEventListener('change', handler);
            this.eventListeners.push({ element: filterJenisTes, type: 'change', handler });
        }

        // Apply filter button
        const applyFilterBtn = document.getElementById('applyFilterBtn');
        if (applyFilterBtn) {
            const handler = () => {
                this.currentPage = 1;
                this.loadData();
            };
            applyFilterBtn.addEventListener('click', handler);
            this.eventListeners.push({ element: applyFilterBtn, type: 'click', handler });
            
            // Enter key untuk search
            if (searchInput) {
                const enterHandler = (e) => {
                    if (e.key === 'Enter') {
                        this.currentPage = 1;
                        this.loadData();
                    }
                };
                searchInput.addEventListener('keypress', enterHandler);
                this.eventListeners.push({ element: searchInput, type: 'keypress', handler: enterHandler });
            }
        }
        
        console.log(`${this.eventListeners.length} event listeners bound`);
    }

    async loadStatistics() {
        try {
            console.log('Loading statistics...');
            
            const formData = new FormData();
            formData.append('action', 'get_statistik');
            
            // Path controller
            const controllerPath = '../../includes/guru_control/HasilTesController.php';
            console.log('Fetching statistics from:', controllerPath);
            
            const response = await fetch(controllerPath, {
                method: 'POST',
                body: formData
            });
            
            console.log('Statistics response status:', response.status);
            
            // Cek content type
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            // Baca response sebagai text dulu
            const responseText = await response.text();
            console.log('Response text (first 500 chars):', responseText.substring(0, 500));
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Coba parse JSON
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Full response:', responseText);
                throw new Error('Server returned non-JSON response (likely PHP error)');
            }
            
            console.log('Statistics data:', result);
            
            if (result.success) {
                this.updateStatistics(result.data);
            } else {
                console.error('Error in statistics:', result.message);
                this.showError('Gagal memuat statistik: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
            this.showError('Gagal memuat statistik: ' + error.message);
        }
    }

    updateStatistics(data) {
        try {
            const totalSiswaElem = document.getElementById('totalSiswa');
            const totalTesElem = document.getElementById('totalTes');
            
            if (totalSiswaElem) {
                // Total seluruh siswa (bukan hanya yang tes)
                totalSiswaElem.textContent = data.total_siswa || 0;
            }
            
            if (totalTesElem) {
                // Total siswa yang mengerjakan tes
                totalTesElem.textContent = data.selesai || 0;
            }
            
            console.log('Statistics updated:', {
                total_siswa: data.total_siswa,
                selesai: data.selesai
            });
        } catch (error) {
            console.error('Error updating statistics:', error);
        }
    }

    async loadData() {
        try {
            const formData = new FormData();
            formData.append('action', 'get_data');
            formData.append('search', this.filters.search);
            formData.append('kelas', this.filters.kelas);
            formData.append('jenis_tes', this.filters.jenis_tes);
            formData.append('page', this.currentPage);
            
            const controllerPath = '../../includes/guru_control/HasilTesController.php';
            console.log('Fetching data from:', controllerPath);
            
            const response = await fetch(controllerPath, {
                method: 'POST',
                body: formData
            });
            
            console.log('Data response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            console.log('Data loaded:', result);
            
            if (result.success) {
                this.renderData(result.data);
                this.renderPagination(result.totalPages);
                this.toggleEmptyState(result.data.length === 0);
            } else {
                this.showError(result.message);
                this.toggleEmptyState(true);
            }
        } catch (error) {
            console.error('Error loading data:', error);
            this.showError('Gagal memuat data: ' + error.message);
            this.toggleEmptyState(true);
        }
    }

    renderData(data) {
        const resultsList = document.getElementById('resultsList');
        
        if (!resultsList) {
            console.error('resultsList element not found');
            return;
        }
        
        if (!data || data.length === 0) {
            resultsList.innerHTML = '';
            return;
        }
        
        console.log('Rendering', data.length, 'items');
        
        let html = '';
        
        data.forEach(item => {
            // Get initials for avatar
            const initials = this.getInitials(item.nama_siswa);
            
            // Determine badge class
            let badgeClass = 'ipa';
            if (item.kategori_tes) {
                if (item.kategori_tes.includes('Minat')) badgeClass = 'bahasa';
                if (item.kategori_tes.includes('Gaya')) badgeClass = 'ips';
            }
            
            // Format nilai
            const nilai = item.nilai !== null && item.nilai !== undefined 
                ? `${item.nilai}` 
                : '<span class="text-muted">Belum dinilai</span>';
            
            html += `
                <div class="result-card">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="student-info">
                                <div class="student-avatar">${initials}</div>
                                <div>
                                    <h6 class="mb-1 fw-semibold">${this.escapeHtml(item.nama_siswa || 'N/A')}</h6>
                                    <p class="text-muted mb-0 small">${this.escapeHtml(item.kelas || '-')}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="score-badge ${badgeClass}">${this.escapeHtml(item.kategori_tes || 'Tes')}</span>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-0 fw-semibold">${nilai}</p>
                            <p class="text-muted mb-0 small">${item.tanggal_formatted || '-'}</p>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-detail btn-sm text-white" 
                                    onclick="window.appHasilTes.showDetail(${item.id_hasil})">
                                <i class="fas fa-eye me-1"></i>Detail
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        resultsList.innerHTML = html;
    }

    renderPagination(totalPages) {
        const paginationContainer = document.getElementById('paginationContainer');
        const pagination = document.getElementById('pagination');
        
        if (!paginationContainer || !pagination) return;
        
        if (totalPages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }
        
        paginationContainer.style.display = 'block';
        
        let html = '';
        const maxPages = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxPages / 2));
        let endPage = Math.min(totalPages, startPage + maxPages - 1);
        
        if (endPage - startPage + 1 < maxPages) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }
        
        // Previous button
        if (this.currentPage > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="window.appHasilTes.changePage(${this.currentPage - 1}); return false;">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            `;
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            const active = i === this.currentPage ? 'active' : '';
            html += `
                <li class="page-item ${active}">
                    <a class="page-link" href="#" onclick="window.appHasilTes.changePage(${i}); return false;">${i}</a>
                </li>
            `;
        }
        
        // Next button
        if (this.currentPage < totalPages) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="window.appHasilTes.changePage(${this.currentPage + 1}); return false;">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            `;
        }
        
        pagination.innerHTML = html;
    }

    changePage(page) {
        this.currentPage = page;
        this.loadData();
        // Scroll ke atas
        const resultsList = document.getElementById('resultsList');
        if (resultsList) {
            resultsList.scrollIntoView({ behavior: 'smooth' });
        }
    }

    toggleEmptyState(show) {
        const emptyState = document.getElementById('emptyState');
        const resultsList = document.getElementById('resultsList');
        const paginationContainer = document.getElementById('paginationContainer');
        
        if (emptyState && resultsList && paginationContainer) {
            if (show) {
                emptyState.style.display = 'block';
                resultsList.innerHTML = '';
                paginationContainer.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
            }
        }
    }

    async showDetail(id_hasil) {
        try {
            const formData = new FormData();
            formData.append('action', 'get_detail_jawaban');
            formData.append('id_hasil', id_hasil);
            
            const controllerPath = '../../includes/guru_control/HasilTesController.php';
            const response = await fetch(controllerPath, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                this.renderDetailModal(result.data);
                const modalElement = document.getElementById('detailModal');
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error('Error loading detail:', error);
            this.showError('Gagal memuat detail: ' + error.message);
        }
    }

    renderDetailModal(data) {
        const modalBody = document.getElementById('modalBody');
        if (!modalBody) return;
        
        let jawabanHtml = '';
        
        if (data.jawaban_detail && Array.isArray(data.jawaban_detail)) {
            // Tampilkan detail jawaban lengkap
            jawabanHtml = '<div class="jawaban-container">';
            
            data.jawaban_detail.forEach((jawaban, index) => {
                const nomor = index + 1;
                const jawabanBenar = jawaban.bobot_dipilih > 0 ? 'text-success' : 'text-danger';
                const iconJawaban = jawaban.bobot_dipilih > 0 ? '✓' : '✗';
                
                jawabanHtml += `
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">Soal ${nomor}</h6>
                                <span class="badge ${jawabanBenar} bg-light">
                                    <i class="fas ${jawaban.bobot_dipilih > 0 ? 'fa-check text-success' : 'fa-times text-danger'} me-1"></i>
                                    Bobot: ${jawaban.bobot_dipilih}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Pertanyaan -->
                            <div class="mb-3">
                                <p class="fw-semibold mb-2">Pertanyaan:</p>
                                <p class="mb-0">${this.escapeHtml(jawaban.pertanyaan)}</p>
                            </div>
                            
                            <!-- Jawaban yang dipilih -->
                            <div class="mb-3">
                                <p class="fw-semibold mb-2">Jawaban Siswa:</p>
                                <div class="alert ${jawaban.bobot_dipilih > 0 ? 'alert-success' : 'alert-danger'} p-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 fs-5">${iconJawaban}</span>
                                        <span>${this.escapeHtml(jawaban.opsi_dipilih)}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Semua opsi jawaban -->
                            <div>
                                <p class="fw-semibold mb-2">Semua Opsi Jawaban:</p>
                                <div class="list-group">
                `;
                
                if (jawaban.semua_opsi && Array.isArray(jawaban.semua_opsi)) {
                    jawaban.semua_opsi.forEach(opsi => {
                        const isSelected = opsi.id_opsi == jawaban.id_opsi_dipilih;
                        const bgClass = isSelected ? 
                            (jawaban.bobot_dipilih > 0 ? 'list-group-item-success' : 'list-group-item-danger') : 
                            '';
                        const textClass = isSelected ? 'fw-bold' : '';
                        
                        jawabanHtml += `
                            <div class="list-group-item ${bgClass}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="${textClass}">${this.escapeHtml(opsi.opsi)}</div>
                                    <div>
                                        <span class="badge ${opsi.bobot > 0 ? 'bg-success' : 'bg-secondary'}">
                                            Bobot: ${opsi.bobot}
                                        </span>
                                        ${isSelected ? '<span class="badge bg-primary ms-1">Dipilih</span>' : ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                jawabanHtml += `
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            jawabanHtml += '</div>';
        } else if (data.jawaban) {
            // Fallback: tampilkan jawaban mentah jika tidak ada detail
            try {
                const jawaban = JSON.parse(data.jawaban);
                jawabanHtml = '<ul class="list-group list-group-flush">';
                Object.entries(jawaban).forEach(([key, value]) => {
                    jawabanHtml += `
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Soal ID: ${this.escapeHtml(key)}</span>
                            <span class="fw-semibold">Opsi ID: ${this.escapeHtml(value)}</span>
                        </li>
                    `;
                });
                jawabanHtml += '</ul>';
            } catch (e) {
                jawabanHtml = `<p class="text-muted">${this.escapeHtml(data.jawaban)}</p>`;
            }
        } else {
            jawabanHtml = '<p class="text-muted">Tidak ada data jawaban</p>';
        }
        
        const html = `
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Informasi Siswa</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Nama:</strong> ${this.escapeHtml(data.nama_siswa || '-')}
                        </li>
                        <li class="mb-2">
                            <strong>Kelas:</strong> ${this.escapeHtml(data.kelas || '-')}
                        </li>
                        <li class="mb-2">
                            <strong>Jenis Tes:</strong> 
                            <span class="badge bg-primary">${this.escapeHtml(data.kategori_tes || '-')}</span>
                        </li>
                        <li class="mb-2">
                            <strong>Nama Tes:</strong> ${this.escapeHtml(data.nama_tes || '-')}
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Hasil Tes</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Nilai:</strong> 
                            ${data.nilai !== null && data.nilai !== undefined 
                                ? `<span class="fw-bold fs-5">${data.nilai}</span>` 
                                : '<span class="text-warning">Belum dinilai</span>'}
                        </li>
                        <li class="mb-2">
                            <strong>Tanggal Submit:</strong> ${data.tanggal_formatted || '-'}
                        </li>
                        <li class="mb-2">
                            <strong>ID Hasil:</strong> ${data.id_hasil || '-'}
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr>
            
            <div class="mt-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Detail Jawaban</h6>
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.appHasilTes.toggleAllJawaban()">
                        <i class="fas fa-expand-alt me-1"></i>Toggle All
                    </button>
                </div>
                <div class="mt-2">
                    ${jawabanHtml}
                </div>
            </div>
        `;
        
        modalBody.innerHTML = html;
    }

    toggleAllJawaban() {
        const cards = document.querySelectorAll('.jawaban-container .card');
        cards.forEach(card => {
            const body = card.querySelector('.card-body');
            if (body) {
                body.style.display = body.style.display === 'none' ? 'block' : 'none';
            }
        });
    }

    showError(message) {
        console.error('Error:', message);
        
        // Coba buat toast notification
        try {
            const toastHtml = `
                <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${this.escapeHtml(message)}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            
            const toastContainer = document.createElement('div');
            toastContainer.innerHTML = toastHtml;
            toastContainer.style.position = 'fixed';
            toastContainer.style.top = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
            
            const toastElement = toastContainer.querySelector('.toast');
            if (toastElement && typeof bootstrap !== 'undefined') {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
                
                // Remove after hide
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastContainer.remove();
                });
            }
        } catch (e) {
            // Fallback ke alert biasa
            alert('Error: ' + message);
        }
    }

    // Method untuk cleanup (untuk SideMenu)
    destroy() {
        console.log('Cleaning up HasilTes...');
        
        // Remove event listeners
        this.eventListeners.forEach(({ element, type, handler }) => {
            if (element && element.removeEventListener) {
                element.removeEventListener(type, handler);
            }
        });
        
        this.eventListeners = [];
        this.isInitialized = false;
        
        console.log('HasilTes cleaned up');
    }

    // Helper functions
    getInitials(name) {
        if (!name || typeof name !== 'string') return '??';
        return name
            .split(' ')
            .map(word => word.charAt(0))
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Fungsi inisialisasi untuk SideMenu
function initHasilTes() {
    return new HasilTes();
}

// Auto-init jika diakses langsung (bukan melalui SideMenu)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.appHasilTes) {
            window.appHasilTes = new HasilTes();
        }
    });
} else {
    if (!window.appHasilTes) {
        window.appHasilTes = new HasilTes();
    }
}

// Export untuk SideMenu
window.initHasilTes = initHasilTes;