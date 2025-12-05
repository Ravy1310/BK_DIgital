// File: js/LaporanKonseling.js

// ============================================
// AUTO-INIT SCRIPT
// ============================================
(function() {
    console.log('🔧 Loading Laporan Konseling System...');
    
    function checkDependencies(callback) {
        if (window.jQuery && window.bootstrap && window.Swal) {
            console.log('✅ All dependencies loaded');
            callback();
        } else {
            console.log('⏳ Waiting for dependencies...');
            setTimeout(function() { 
                checkDependencies(callback); 
            }, 100);
        }
    }
    
    checkDependencies(function() {
        console.log('🚀 Initializing Laporan Konseling...');
        if (typeof initManajemenLaporanKonseling === 'function') {
            initManajemenLaporanKonseling();
        } else {
            console.error('❌ initManajemenLaporanKonseling function not found!');
            
            // Fallback: try to load manually
            setTimeout(function() {
                if (typeof initManajemenLaporanKonseling === 'function') {
                    initManajemenLaporanKonseling();
                } else {
                    console.error('❌ Still not found after waiting');
                }
            }, 1000);
        }
    });
})();

// ============================================
// GLOBAL CONFIGURATION
// ============================================
// Gunakan var untuk menghindari temporal dead zone
var LAPORAN_CONTROLLER_PATH = '../../includes/guru_control/LaporanKonselingController.php';

// ============================================
// UTILITY FUNCTIONS (DEFINE FIRST!)
// ============================================

// Emergency function untuk reset modal state - DEFINISI DULU!
function fixModalState() {
    console.log('🔄 Emergency modal fix called');
    
    // Hapus semua backdrop
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => {
        if (backdrop.parentNode) {
            backdrop.parentNode.removeChild(backdrop);
        }
    });
    
    // Reset semua modal
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.display = 'none';
        modal.classList.remove('show');
    });
    
    // Reset body
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    
    // Remove any inline styles
    document.body.removeAttribute('style');
    
    console.log('✅ Modal state reset complete');
}

function setDefaultDateTime() {
    const tanggalDibuatInput = document.getElementById('tanggalDibuat');
    if (tanggalDibuatInput) {
        const now = new Date();
        const timezoneOffset = now.getTimezoneOffset() * 60000;
        const localISOTime = new Date(now - timezoneOffset).toISOString().slice(0, 16);
        tanggalDibuatInput.value = localISOTime;
        console.log('📅 Set default datetime:', localISOTime);
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showSuccess(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        alert('✅ ' + message);
    }
}

function showError(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: message
        });
    } else {
        alert('❌ ' + message);
    }
}

function showSessionExpired() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: 'Sesi Berakhir',
            text: 'Sesi Anda telah berakhir. Silakan login kembali.',
            confirmButtonText: 'Login',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../../login.php';
            }
        });
    }
}

// ============================================
// AJAX WRAPPER WITH ERROR HANDLING
// ============================================

function makeAjaxRequest(config) {
    return new Promise((resolve, reject) => {
        const defaultConfig = {
            url: LAPORAN_CONTROLLER_PATH,
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
        };
        
        const finalConfig = { ...defaultConfig, ...config };
        
        // Handle FormData (for file uploads)
        if (config.data instanceof FormData) {
            finalConfig.processData = false;
            finalConfig.contentType = false;
        }
        
        console.log('📤 AJAX Request:', {
            url: finalConfig.url,
            type: finalConfig.type,
            data: finalConfig.data
        });
        
        $.ajax(finalConfig)
            .done(function(response) {
                console.log('✅ AJAX Response:', response);
                
                // Check if response is HTML (session expired)
                if (typeof response === 'string' && response.trim().startsWith('<!DOCTYPE')) {
                    console.error('⚠️ Server returned HTML instead of JSON');
                    showSessionExpired();
                    reject(new Error('Session expired or server error'));
                    return;
                }
                
                // Check for PHP errors in response
                if (typeof response === 'string' && 
                    (response.includes('Fatal error') || response.includes('Parse error') || response.includes('Warning'))) {
                    console.error('⚠️ PHP error in response');
                    showError('Terjadi kesalahan di server. Silakan hubungi administrator.');
                    reject(new Error('Server PHP error'));
                    return;
                }
                
                resolve(response);
            })
            .fail(function(xhr, status, error) {
                console.error('❌ AJAX Request failed:', {
                    url: finalConfig.url,
                    status: xhr.status,
                    error: error,
                    response: xhr.responseText ? xhr.responseText.substring(0, 500) : 'No response'
                });
                
                // Handle specific errors
                if (xhr.status === 401 || xhr.status === 403) {
                    showSessionExpired();
                } else if (xhr.status === 404) {
                    showError('Endpoint tidak ditemukan. Silakan hubungi administrator.');
                } else if (xhr.status === 500) {
                    showError('Server error. Silakan coba lagi nanti.');
                } else if (status === 'timeout') {
                    showError('Request timeout. Silakan coba lagi.');
                } else if (status === 'parsererror') {
                    // This is likely the HTML response error
                    showSessionExpired();
                } else {
                    showError('Terjadi kesalahan: ' + error);
                }
                
                reject({ xhr, status, error });
            });
    });
}

// ============================================
// PATH DETECTION AND CONTROLLER SETUP
// ============================================

// Fungsi untuk mendapatkan path controller yang benar
function getControllerPath() {
    const currentPath = window.location.pathname;
    console.log('📍 Current path:', currentPath);
    
    // Cari tahu struktur folder
    if (currentPath.includes('/pages/guru/')) {
        return '../../includes/guru_control/LaporanKonselingController.php';
    } else if (currentPath.includes('/guru/')) {
        return '../includes/guru_control/LaporanKonselingController.php';
    } else {
        return 'includes/guru_control/LaporanKonselingController.php';
    }
}

// Update path berdasarkan analisis
LAPORAN_CONTROLLER_PATH = getControllerPath();
console.log('📍 Controller path set to:', LAPORAN_CONTROLLER_PATH);

// ============================================
// TEST CONTROLLER CONNECTION
// ============================================
async function testControllerConnection() {
    console.log('🔍 Testing controller connection...');
    
    const possiblePaths = [
        // Relative paths based on common structures
        '../../includes/guru_control/LaporanKonselingController.php',
        '../includes/guru_control/LaporanKonselingController.php',
        'includes/guru_control/LaporanKonselingController.php',
        '/includes/guru_control/LaporanKonselingController.php',
        
        // Alternative controller locations
        '../../controllers/LaporanKonselingController.php',
        '../controllers/LaporanKonselingController.php',
        'controllers/LaporanKonselingController.php',
        
        // Absolute URL based on current location
        window.location.origin + '/includes/guru_control/LaporanKonselingController.php',
        window.location.origin + '/../../includes/guru_control/LaporanKonselingController.php'
    ];
    
    console.log('🔄 Testing', possiblePaths.length, 'possible paths...');
    
    for (let i = 0; i < possiblePaths.length; i++) {
        const path = possiblePaths[i];
        console.log(`🔄 Testing path ${i + 1}/${possiblePaths.length}: ${path}`);
        
        try {
            const response = await testSinglePath(path);
            if (response.valid) {
                console.log(`✅ Found valid controller at: ${path}`);
                LAPORAN_CONTROLLER_PATH = path;
                return true;
            }
        } catch (error) {
            console.log(`❌ Path ${path} failed:`, error.message);
        }
    }
    
    console.error('❌ No controller found in any path');
    return false;
}

function testSinglePath(path) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: path,
            type: 'GET',
            data: { action: 'test' },
            dataType: 'json',
            timeout: 3000,
            success: function(response) {
                // Valid JSON response received
                resolve({
                    valid: true,
                    path: path,
                    response: response
                });
            },
            error: function(xhr, status, error) {
                // Check if it's a 404 or other error
                if (xhr.status === 404) {
                    reject(new Error('File not found (404)'));
                } else if (status === 'parsererror') {
                    // Server returned something but not JSON
                    reject(new Error('Invalid JSON response'));
                } else {
                    reject(new Error(`${status}: ${error}`));
                }
            }
        });
    });
}

// ============================================
// DATA LOADING FUNCTIONS
// ============================================

function loadLaporanData(searchTerm = '') {
    console.log('📊 Loading laporan data...');
    
    const tbody = document.getElementById('laporanTableBody');
    if (!tbody) {
        console.error('❌ laporanTableBody not found');
        return;
    }
    
    // Show loading state
    tbody.innerHTML = `
        <tr id="loadingRow">
            <td colspan="5" class="text-center py-5">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat data laporan...</p>
                </div>
            </td>
        </tr>
    `;
    
    makeAjaxRequest({
        type: 'GET',
        data: {
            action: 'get_laporan',
            search: searchTerm
        }
    })
    .then(response => {
        console.log('✅ Laporan data response:', response);
        
        if (response.success) {
            window.allLaporanData = response.data || [];
            renderLaporanTable(response.data);
            
            // Update total info
            const totalInfo = document.getElementById('totalInfo');
            if (totalInfo) {
                totalInfo.textContent = `Total: ${response.count || 0} laporan`;
            }
        } else {
            showError('Gagal memuat data: ' + (response.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('❌ Failed to load laporan data:', error);
        
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Tidak dapat memuat data laporan
                        <br>
                        <small>${error.message || 'Unknown error'}</small>
                    </div>
                </td>
            </tr>
        `;
    });
}

function renderLaporanTable(data) {
    const tbody = document.getElementById('laporanTableBody');
    if (!tbody) return;
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="text-center text-muted">
                        <i class="bi bi-file-earmark-text" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Belum ada laporan</h5>
                        <p>Klik "Buat Laporan Baru" untuk membuat laporan pertama</p>
                    </td>
                </tr>
            `;
        return;
    }
    
    let html = '';
    data.forEach((item, index) => {
        const waktu = item.Waktu_Konseling_formatted ? `pukul ${item.Waktu_Konseling_formatted}` : '';
        
        html += `
            <tr>
                <td class="text-start">${escapeHtml(item.nama_siswa || '-')}</td>
                <td>${escapeHtml(item.kelas || '-')}</td>
                <td>
                    <div>${item.Tanggal_Konseling_formatted || '-'}</div>
                    <div class="text-muted small">${waktu}</div>
                </td>
                <td class="text-start">${escapeHtml(item.Topik_konseling || '-')}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="showLaporanDetail(${item.id_laporan})">
                        <i class="bi bi-eye"></i> Detail
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    console.log(`✅ Rendered ${data.length} laporan`);
}

function searchLaporan(searchTerm) {
    console.log('🔍 Searching laporan:', searchTerm);
    loadLaporanData(searchTerm);
}

// ==============================
// JADWAL FUNCTIONS
// ==============================

function loadJadwalAvailable() {
    console.log('📅 Loading available jadwal...');
    
    const jadwalList = document.getElementById('jadwalList');
    const noJadwalMessage = document.getElementById('noJadwalMessage');
    
    if (jadwalList) {
        jadwalList.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat daftar jadwal...</p>
            </div>
        `;
    }
    
    if (noJadwalMessage) {
        noJadwalMessage.style.display = 'none';
    }
    
    makeAjaxRequest({
        type: 'GET',
        data: { action: 'get_jadwal_available' }
    })
    .then(response => {
        console.log('✅ Jadwal data response:', response);
        
        if (response.success) {
            window.allJadwalData = response.data || [];
            
            if (response.data.length > 0) {
                console.log(`✅ Found ${response.data.length} available jadwal`);
                renderJadwalList(response.data);
                if (noJadwalMessage) noJadwalMessage.style.display = 'none';
            } else {
                console.log('ℹ️ No available jadwal found');
                if (jadwalList) jadwalList.innerHTML = '';
                if (noJadwalMessage) noJadwalMessage.style.display = 'block';
            }
        } else {
            console.error('❌ Server error:', response.message);
            showError(response.message);
        }
    })
    .catch(error => {
        console.error('❌ Error loading jadwal:', error);
        
        if (jadwalList) {
            jadwalList.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Gagal memuat data jadwal
                </div>
            `;
        }
    });
}

function renderJadwalList(data) {
    const jadwalList = document.getElementById('jadwalList');
    if (!jadwalList) return;
    
    let html = '';
    data.forEach(item => {
        const waktu = item.Waktu_Konseling_formatted ? `pukul ${item.Waktu_Konseling_formatted}` : '';
        
        html += `
            <div class="card mb-3 border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-title mb-2">${escapeHtml(item.nama_siswa)}</h6>
                            <p class="card-text mb-1">
                                <small class="text-muted">
                                    <i class="bi bi-person-badge"></i> ${escapeHtml(item.kelas || '-')}
                                </small>
                            </p>
                            <p class="card-text mb-1">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> ${item.Tanggal_Konseling_formatted} ${waktu}
                                </small>
                            </p>
                            <p class="card-text">
                                <span class="badge bg-light text-dark">${escapeHtml(item.Topik_konseling)}</span>
                            </p>
                        </div>
                        <button class="btn btn-sm btn-success" onclick="selectJadwal(${item.id_jadwal})">
                            <i class="bi bi-check"></i> Pilih
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    jadwalList.innerHTML = html;
    console.log(`✅ Rendered ${data.length} jadwal`);
}

function filterJadwalList(searchTerm) {
    const allJadwalData = window.allJadwalData || [];
    
    if (!searchTerm.trim()) {
        renderJadwalList(allJadwalData);
        return;
    }
    
    const filtered = allJadwalData.filter(item => {
        const searchLower = searchTerm.toLowerCase();
        return (
            (item.nama_siswa && item.nama_siswa.toLowerCase().includes(searchLower)) ||
            (item.kelas && item.kelas.toLowerCase().includes(searchLower)) ||
            (item.Topik_konseling && item.Topik_konseling.toLowerCase().includes(searchLower))
        );
    });
    
    renderJadwalList(filtered);
}

// ==============================
// MODAL EVENT LISTENERS
// ==============================
function setupModalEventListeners() {
    console.log('🔧 Setting up modal event listeners...');
    
    // Event listeners untuk modal select jadwal
    const selectModalElement = document.getElementById('selectJadwalModal');
    if (selectModalElement) {
        selectModalElement.addEventListener('hidden.bs.modal', function() {
            console.log('🎬 Modal selectJadwal ditutup');
            const searchInput = document.getElementById('searchJadwalInput');
            if (searchInput) searchInput.value = '';
            filterJadwalList('');
        });
    }
    
    // Event listeners untuk modal laporan
    const laporanModalElement = document.getElementById('laporanModal');
    if (laporanModalElement) {
        laporanModalElement.addEventListener('hidden.bs.modal', function() {
            console.log('🎬 Modal laporan ditutup');
            const selectedJadwalId = document.getElementById('selectedJadwalId');
            if (selectedJadwalId && selectedJadwalId.value) {
                document.getElementById('formLaporanBaru').reset();
                selectedJadwalId.value = '';
                setDefaultDateTime();
            }
        });
    }
    
    // Event listeners untuk modal detail
    const detailModalElement = document.getElementById('detailModal');
    if (detailModalElement) {
        detailModalElement.addEventListener('hidden.bs.modal', function() {
            console.log('🎬 Modal detail ditutup');
            const detailLoading = document.getElementById('detailLoading');
            const detailContent = document.getElementById('detailContent');
            if (detailLoading) {
                detailLoading.style.display = 'block';
                detailLoading.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat detail laporan...</p>
                    </div>
                `;
            }
            if (detailContent) detailContent.style.display = 'none';
        });
    }
}

// ==============================
// CLOSE BUTTONS HANDLING
// ==============================
function setupCloseButtons() {
    console.log('🔧 Setting up close buttons...');
    
    // Tombol close untuk modal select jadwal
    const closeSelectButtons = document.querySelectorAll(
        '#selectJadwalModal .btn-close, #selectJadwalModal .btn-secondary'
    );
    closeSelectButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('❌ Tombol close modal select jadwal diklik');
            
            if (window.selectJadwalModal) {
                window.selectJadwalModal.hide();
            }
        });
    });
    
    // Tombol close untuk modal laporan
    const closeLaporanButtons = document.querySelectorAll(
        '#laporanModal .btn-close, #laporanModal .btn-secondary'
    );
    closeLaporanButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('❌ Tombol close modal laporan diklik');
            
            if (window.laporanModal) {
                window.laporanModal.hide();
            }
        });
    });
}

// ==============================
// EVENT LISTENERS SETUP
// ==============================
function setupEventListeners() {
    console.log('🔗 Setting up event listeners...');
    
    // 1. Button: Buat Laporan Baru
    const openLaporanBtn = document.getElementById('openLaporanBtn');
    if (openLaporanBtn) {
        openLaporanBtn.addEventListener('click', function() {
            console.log('🖱️ Button "Buat Laporan Baru" clicked');
            
            // Reset form ketika modal dibuka
            document.getElementById('formLaporanBaru').reset();
            setDefaultDateTime();
            document.getElementById('selectedJadwalId').value = '';
            
            if (!window.selectJadwalModal) {
                try {
                    const modalEl = document.getElementById('selectJadwalModal');
                    if (modalEl && bootstrap) {
                        window.selectJadwalModal = new bootstrap.Modal(modalEl, {
                            backdrop: true,
                            keyboard: true
                        });
                        console.log('✅ Modal initialized on demand');
                    }
                } catch (e) {
                    console.error('❌ Failed to initialize modal:', e);
                    showError('Modal tidak dapat dimuat. Silakan refresh halaman.');
                    return;
                }
            }
            
            // Load available jadwal
            loadJadwalAvailable();
            
            // Show modal
            if (window.selectJadwalModal) {
                window.selectJadwalModal.show();
                console.log('✅ Modal shown successfully');
            }
        });
    } else {
        console.error('❌ openLaporanBtn not found!');
    }
    
    // 2. Search input for laporan
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchLaporan(this.value);
        });
        console.log('✅ Event listener added to searchInput');
    }
    
    // 3. Search input for jadwal in modal
    const searchJadwalInput = document.getElementById('searchJadwalInput');
    if (searchJadwalInput) {
        searchJadwalInput.addEventListener('input', function() {
            filterJadwalList(this.value);
        });
        console.log('✅ Event listener added to searchJadwalInput');
    }
    
    // 4. Form submission
    const formLaporanBaru = document.getElementById('formLaporanBaru');
    if (formLaporanBaru) {
        formLaporanBaru.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('📋 Form submitted');
            createLaporan();
        });
        console.log('✅ Event listener added to formLaporanBaru');
    }
    
    // 5. Setup modal event listeners
    setupModalEventListeners();
    
    // 6. Setup close buttons
    setupCloseButtons();
    
    console.log('✅ All event listeners setup complete');
}

// ==============================
// LAPORAN CREATION FUNCTIONS
// ==============================

function selectJadwal(idJadwal) {
    console.log('👉 Selecting jadwal:', idJadwal);
    
    const allJadwalData = window.allJadwalData || [];
    const jadwal = allJadwalData.find(j => j.id_jadwal == idJadwal);
    if (!jadwal) {
        showError('Jadwal tidak ditemukan');
        return;
    }
    
    // Fill form with jadwal data
    document.getElementById('selectedJadwalId').value = idJadwal;
    document.getElementById('selectedSiswaInfo').textContent = jadwal.nama_siswa;
    document.getElementById('selectedKelasInfo').textContent = jadwal.kelas;
    document.getElementById('selectedTanggalInfo').textContent = 
        `${jadwal.Tanggal_Konseling_formatted} ${jadwal.Waktu_Konseling_formatted ? 'pukul ' + jadwal.Waktu_Konseling_formatted : ''}`;
    document.getElementById('selectedTopikInfo').textContent = jadwal.Topik_konseling;
    
    // Proper modal switching
    if (window.selectJadwalModal) {
        window.selectJadwalModal.hide();
        
        // Tunggu modal pertama benar-benar tertutup
        setTimeout(() => {
            if (window.laporanModal) {
                window.laporanModal.show();
            }
        }, 500);
    } else if (window.laporanModal) {
        window.laporanModal.show();
    }
}

function createLaporan() {
    console.log('📝 Creating laporan...');
    
    const idJadwal = document.getElementById('selectedJadwalId').value;
    const tanggalDibuat = document.getElementById('tanggalDibuat').value;
    const hasilLaporan = document.getElementById('hasilPertemuan').value.trim();
    const catatanTambahan = document.getElementById('catatanTambahan').value.trim();
    
    // Validation
    if (!idJadwal) {
        showError('Silakan pilih jadwal terlebih dahulu');
        return;
    }
    
    if (!hasilLaporan) {
        showError('Hasil pertemuan tidak boleh kosong');
        return;
    }
    
    if (hasilLaporan.length < 10) {
        showError('Hasil pertemuan minimal 10 karakter');
        return;
    }
    
    // Show loading state
    const submitBtn = document.querySelector('#formLaporanBaru button[type="submit"]');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitLoading = document.getElementById('submitLoading');
    
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    if (submitBtnText) submitBtnText.textContent = 'Menyimpan...';
    if (submitLoading) submitLoading.style.display = 'inline-block';
    
    // Prepare data
    const formData = new FormData();
    formData.append('action', 'create');
    formData.append('id_jadwal', idJadwal);
    formData.append('tanggal_dibuat', tanggalDibuat);
    formData.append('hasil_laporan', hasilLaporan);
    formData.append('catatan_tambahan', catatanTambahan);
    
    console.log('📤 Sending data:', {
        id_jadwal: idJadwal,
        tanggal_dibuat: tanggalDibuat
    });
    
    makeAjaxRequest({
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .then(response => {
        console.log('✅ Create response:', response);
        
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (submitBtnText) submitBtnText.textContent = 'Simpan Laporan';
        if (submitLoading) submitLoading.style.display = 'none';
        
        if (response.success) {
            showSuccess(response.message);
            
            // Reset form
            document.getElementById('formLaporanBaru').reset();
            setDefaultDateTime();
            
            // Close modal
            if (window.laporanModal) window.laporanModal.hide();
            
            // Reload data
            loadLaporanData();
            loadJadwalAvailable();
        } else {
            showError(response.message);
        }
    })
    .catch(error => {
        console.error('❌ Create error:', error);
        
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        if (submitBtnText) submitBtnText.textContent = 'Simpan Laporan';
        if (submitLoading) submitLoading.style.display = 'none';
    });
}

// ==============================
// DETAIL LAPORAN FUNCTIONS
// ==============================

function showLaporanDetail(idLaporan) {
    console.log('🔍 Showing detail for laporan:', idLaporan);
    
    if (!window.detailModal) {
        try {
            const modalEl = document.getElementById('detailModal');
            if (modalEl && bootstrap) {
                window.detailModal = new bootstrap.Modal(modalEl);
            }
        } catch (e) {
            console.error('❌ Failed to initialize detail modal:', e);
        }
    }
    
    // Show loading
    const detailLoading = document.getElementById('detailLoading');
    const detailContent = document.getElementById('detailContent');
    
    if (detailLoading) detailLoading.style.display = 'block';
    if (detailContent) detailContent.style.display = 'none';
    
    makeAjaxRequest({
        type: 'GET',
        data: { 
            action: 'get_detail',
            id: idLaporan
        }
    })
    .then(response => {
        console.log('✅ Detail response:', response);
        
        if (response.success) {
            const detail = response.data;
            
            // Set detail content
            document.getElementById('detailNama').textContent = detail.nama_siswa || '-';
            document.getElementById('detailKelas').textContent = detail.kelas || '-';
            document.getElementById('detailTanggalSesi').textContent = 
                `Tanggal Sesi: ${detail.Tanggal_Konseling_formatted || '-'} ${detail.Waktu_Konseling_formatted ? 'pukul ' + detail.Waktu_Konseling_formatted : ''}`;
            document.getElementById('detailTopik').textContent = `Topik: ${detail.Topik_konseling || '-'}`;
            document.getElementById('detailHasil').textContent = detail.hasil_laporan || '-';
            document.getElementById('detailCatatan').textContent = detail.catatan_tambahan || 'Tidak ada catatan tambahan';
            document.getElementById('detailTanggalLaporan').textContent = detail.tanggal_dibuat_formatted || '-';
            document.getElementById('detailGuru').textContent = detail.nama_guru || '-';
            
            // Show content
            if (detailLoading) detailLoading.style.display = 'none';
            if (detailContent) detailContent.style.display = 'block';
        } else {
            if (detailLoading) {
                detailLoading.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        ${response.message}
                    </div>
                `;
            }
        }
    })
    .catch(error => {
        console.error('❌ Detail error:', error);
        if (detailLoading) {
            detailLoading.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Gagal memuat detail
                </div>
            `;
        }
    });
    
    // Show modal
    if (window.detailModal) window.detailModal.show();
}

// ============================================
// MAIN INITIALIZATION FUNCTION
// ============================================
async function initManajemenLaporanKonseling() {
    console.log('🎯 initManajemenLaporanKonseling() called!');
    
    try {
        // Clear any existing modal backdrops first
        fixModalState();
        
        // Test controller connection first
        console.log('📍 Testing controller connection...');
        const connectionOk = await testControllerConnection();
        
        if (!connectionOk) {
            console.error('❌ Controller connection failed');
            showError('Tidak dapat terhubung ke controller. Pastikan file controller sudah dibuat.');
            return;
        }
        
        console.log('✅ Controller connected:', LAPORAN_CONTROLLER_PATH);
        
        // Initialize Bootstrap Modals
        console.log('🔧 Initializing modals...');
        const selectModalElement = document.getElementById('selectJadwalModal');
        const laporanModalElement = document.getElementById('laporanModal');
        const detailModalElement = document.getElementById('detailModal');
        
        // Initialize modals dengan error handling
        try {
            if (selectModalElement && bootstrap) {
                window.selectJadwalModal = new bootstrap.Modal(selectModalElement);
                console.log('✅ selectJadwalModal initialized');
            }
            
            if (laporanModalElement && bootstrap) {
                window.laporanModal = new bootstrap.Modal(laporanModalElement);
                console.log('✅ laporanModal initialized');
            }
            
            if (detailModalElement && bootstrap) {
                window.detailModal = new bootstrap.Modal(detailModalElement);
                console.log('✅ detailModal initialized');
            }
        } catch (modalError) {
            console.error('❌ Error initializing modals:', modalError);
        }
        
        // Set default datetime
        setDefaultDateTime();
        
        // Setup event listeners
        setupEventListeners();
        
        // Load initial data
        console.log('📊 Loading initial data...');
        loadLaporanData();
        
        console.log('✅ Laporan Konseling Management initialized successfully!');
        
    } catch (error) {
        console.error('❌ Error during initialization:', error);
        showError('Terjadi kesalahan saat memuat sistem. Silakan refresh halaman.');
    }
    
    // ==============================
    // EXPOSE FUNCTIONS TO GLOBAL SCOPE
    // ==============================
    
    window.selectJadwal = selectJadwal;
    window.showLaporanDetail = showLaporanDetail;
    window.fixModalState = fixModalState;
    
    console.log('🎉 Laporan Konseling Management ready!');
}

// ============================================
// EXPORT FOR NODE.JS (if needed)
// ============================================
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initManajemenLaporanKonseling };
}