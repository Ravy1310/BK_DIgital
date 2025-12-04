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
// MAIN INITIALIZATION FUNCTION
// ============================================
function initManajemenLaporanKonseling() {
    console.log('🎯 initManajemenLaporanKonseling() called!');
    
    // ==============================
    // CONFIGURATION
    // ==============================
    const CONTROLLER_PATH = '../../includes/guru_control/LaporanKonselingController.php';
    
    // ==============================
    // GLOBAL VARIABLES
    // ==============================
    let allLaporanData = [];
    let allJadwalData = [];
    let selectJadwalModal = null;
    let laporanModal = null;
    let detailModal = null;
    
    // ==============================
    // INITIALIZATION
    // ==============================
    try {
        // Initialize Bootstrap Modals
        const selectModalElement = document.getElementById('selectJadwalModal');
        const laporanModalElement = document.getElementById('laporanModal');
        const detailModalElement = document.getElementById('detailModal');
        
        if (selectModalElement && bootstrap) {
            selectJadwalModal = new bootstrap.Modal(selectModalElement);
            console.log('✅ selectJadwalModal initialized');
        }
        
        if (laporanModalElement && bootstrap) {
            laporanModal = new bootstrap.Modal(laporanModalElement);
            console.log('✅ laporanModal initialized');
        }
        
        if (detailModalElement && bootstrap) {
            detailModal = new bootstrap.Modal(detailModalElement);
            console.log('✅ detailModal initialized');
        }
        
        // Set default datetime
        setDefaultDateTime();
        
        // Setup event listeners
        setupEventListeners();
        
        // Load initial data
        loadLaporanData();
        
        console.log('✅ Laporan Konseling Management initialized successfully!');
        
    } catch (error) {
        console.error('❌ Error during initialization:', error);
        showError('Terjadi kesalahan saat memuat sistem. Silakan refresh halaman.');
    }
    
    // ==============================
    // HELPER FUNCTIONS
    // ==============================
    
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
                
                // Debug info
                console.log('Modal status:', {
                    selectJadwalModal: selectJadwalModal ? 'initialized' : 'not initialized',
                    modalElement: document.getElementById('selectJadwalModal') ? 'exists' : 'not exists'
                });
                
                if (!selectJadwalModal) {
                    console.error('❌ Modal not initialized, trying to initialize...');
                    try {
                        const modalEl = document.getElementById('selectJadwalModal');
                        if (modalEl && bootstrap) {
                            selectJadwalModal = new bootstrap.Modal(modalEl);
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
                if (selectJadwalModal) {
                    selectJadwalModal.show();
                    console.log('✅ Modal shown successfully');
                }
            });
            
            console.log('✅ Event listener added to openLaporanBtn');
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
        
        console.log('✅ All event listeners setup complete');
    }
    
    // ==============================
    // DATA LOADING FUNCTIONS
    // ==============================
    
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
        
        $.ajax({
            url: CONTROLLER_PATH,
            type: 'GET',
            data: {
                action: 'get_laporan',
                search: searchTerm
            },
            dataType: 'json',
            success: function(response) {
                console.log('✅ Laporan data response:', response);
                
                if (response.success) {
                    allLaporanData = response.data;
                    renderLaporanTable(response.data);
                    
                    // Update total info
                    const totalInfo = document.getElementById('totalInfo');
                    if (totalInfo) {
                        totalInfo.textContent = `Total: ${response.count} laporan`;
                    }
                } else {
                    showError('Gagal memuat data: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', error);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                Gagal memuat data. Silakan coba lagi.
                            </div>
                        </td>
                    </tr>
                `;
                
                if (xhr.status === 401) {
                    showSessionExpired();
                }
            }
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
                        </div>
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
    
    // Di fungsi loadJadwalAvailable():
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
    
    $.ajax({
        url: CONTROLLER_PATH,
        type: 'GET',
        data: { action: 'get_jadwal_available' },
        dataType: 'json',
        success: function(response) {
            console.log('✅ Jadwal data response:', response);
            
            // Debug info
            if (response.debug) {
                console.log('📊 Debug info:', response.debug);
            }
            
            if (response.success) {
                allJadwalData = response.data;
                
                if (response.data.length > 0) {
                    console.log(`✅ Found ${response.data.length} available jadwal`);
                    renderJadwalList(response.data);
                    if (noJadwalMessage) noJadwalMessage.style.display = 'none';
                } else {
                    console.log('ℹ️ No available jadwal found');
                    if (jadwalList) jadwalList.innerHTML = '';
                    if (noJadwalMessage) noJadwalMessage.style.display = 'block';
                    
                    // Show debug info in modal
                    if (response.debug && jadwalList) {
                        jadwalList.innerHTML = `
                            <div class="alert alert-info">
                                <h6>Debug Info:</h6>
                                <p>Total jadwal: ${response.debug.total_jadwal}</p>
                                <p>Available jadwal: ${response.debug.available_jadwal}</p>
                                <p class="mb-0">Tidak ada jadwal yang tersedia untuk dibuat laporan.</p>
                            </div>
                        `;
                    }
                }
            } else {
                console.error('❌ Server error:', response.message);
                showError(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error loading jadwal:', error);
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseText);
            
            if (jadwalList) {
                jadwalList.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Gagal memuat data jadwal: ${error}
                    </div>
                `;
            }
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
    // LAPORAN CREATION FUNCTIONS
    // ==============================
    
    function selectJadwal(idJadwal) {
        console.log('👉 Selecting jadwal:', idJadwal);
        
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
        
        // Switch modals
        if (selectJadwalModal) selectJadwalModal.hide();
        if (laporanModal) {
            setTimeout(() => {
                laporanModal.show();
            }, 300);
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
        
        $.ajax({
            url: CONTROLLER_PATH,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
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
                    if (laporanModal) laporanModal.hide();
                    
                    // Reload data
                    loadLaporanData();
                    loadJadwalAvailable();
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Create error:', error);
                
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (submitBtnText) submitBtnText.textContent = 'Simpan Laporan';
                if (submitLoading) submitLoading.style.display = 'none';
                
                if (xhr.status === 401) {
                    showSessionExpired();
                } else {
                    showError('Terjadi kesalahan: ' + error);
                }
            }
        });
    }
    
    // ==============================
    // DETAIL LAPORAN FUNCTIONS
    // ==============================
    
    function showLaporanDetail(idLaporan) {
        console.log('🔍 Showing detail for laporan:', idLaporan);
        
        if (!detailModal) {
            try {
                const modalEl = document.getElementById('detailModal');
                if (modalEl && bootstrap) {
                    detailModal = new bootstrap.Modal(modalEl);
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
        
        $.ajax({
            url: CONTROLLER_PATH,
            type: 'GET',
            data: { 
                action: 'get_detail',
                id: idLaporan
            },
            dataType: 'json',
            success: function(response) {
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
            },
            error: function(xhr, status, error) {
                console.error('❌ Detail error:', error);
                if (detailLoading) {
                    detailLoading.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            Gagal memuat detail
                        </div>
                    `;
                }
            }
        });
        
        // Show modal
        if (detailModal) detailModal.show();
    }
    
    // ==============================
    // EXPOSE FUNCTIONS TO GLOBAL SCOPE
    // ==============================
    
    window.selectJadwal = selectJadwal;
    window.showLaporanDetail = showLaporanDetail;
    
    console.log('🎉 Laporan Konseling Management ready!');
}

// ============================================
// EXPORT FOR NODE.JS (if needed)
// ============================================
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initManajemenLaporanKonseling };
}