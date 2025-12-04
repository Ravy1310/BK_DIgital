// JadwalKonseling.js - Versi Sederhana dan Pasti Berjalan
function initManajemenJadwalKonseling() {
    console.log('🚀 Initializing Jadwal Konseling Management...');
    
    // ==============================
    // KONFIGURASI
    // ==============================
    const CONTROLLER_PATH = '../../includes/guru_control/JadwalController.php';
    
    // ==============================
    // INISIALISASI MODAL - PASTIKAN BERHASIL
    // ==============================
    let detailModal = null;
    let rescheduleModal = null;
    
    try {
        const detailModalEl = document.getElementById('detailModal');
        const rescheduleModalEl = document.getElementById('rescheduleModal');
        
        if (detailModalEl && bootstrap) {
            detailModal = new bootstrap.Modal(detailModalEl);
            console.log('✅ Detail modal initialized');
        } else {
            console.error('❌ Detail modal element or Bootstrap not found');
        }
        
        if (rescheduleModalEl && bootstrap) {
            rescheduleModal = new bootstrap.Modal(rescheduleModalEl);
            console.log('✅ Reschedule modal initialized');
        }
    } catch (error) {
        console.error('❌ Error initializing modals:', error);
    }
    
    // ==============================
    // DIRECT EVENT LISTENERS - TANPA DELEGATION
    // ==============================
    
    // 1. Setup semua tombol detail yang sudah ada
    setupDetailButtons();
    
    // 2. Setup tombol setujui dan reschedule
    setupActionButtons();
    
    // 3. Setup form reschedule
    const rescheduleForm = document.getElementById('rescheduleForm');
    if (rescheduleForm) {
        rescheduleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            confirmReschedule();
        });
    }
    
    // 4. Setup search
    setupSearch();
    
    // ==============================
    // FUNGSI SETUP BUTTONS
    // ==============================
    
    function setupDetailButtons() {
        console.log('🔍 Setting up detail buttons...');
        
        // Cari semua tombol detail
        const detailButtons = document.querySelectorAll('.detail-btn');
        console.log(`Found ${detailButtons.length} detail buttons`);
        
        // Hapus event listener lama (jika ada)
        detailButtons.forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        
        // Ambil ulang setelah clone
        const freshDetailButtons = document.querySelectorAll('.detail-btn');
        
        // Tambahkan event listener baru
        freshDetailButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Detail button clicked:', this);
                handleDetailClick(this);
            });
            
            // Debug: tandai button yang sudah di-setup
            button.setAttribute('data-setup', 'true');
        });
        
        console.log(`✅ ${freshDetailButtons.length} detail buttons setup complete`);
    }
    
    function setupActionButtons() {
        console.log('🔧 Setting up action buttons...');
        
        // Tombol Setujui
        const setujuiButtons = document.querySelectorAll('.setujui-btn');
        setujuiButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                handleSetujui(this);
            });
        });
        
        // Tombol Reschedule
        const rescheduleButtons = document.querySelectorAll('.reschedule-btn');
        rescheduleButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                handleReschedule(this);
            });
        });
        
        console.log(`✅ Action buttons setup complete: ${setujuiButtons.length} setujui, ${rescheduleButtons.length} reschedule`);
    }
    
    // ==============================
    // FUNGSI HANDLER
    // ==============================
    
    function handleDetailClick(button) {
        console.log('📋 Handling detail click...');
        
        const id = button.getAttribute('data-id');
        console.log('Detail ID:', id);
        
        if (!id) {
            console.error('❌ No data-id found on detail button');
            alert('Error: ID tidak ditemukan');
            return;
        }
        
        // Tampilkan loading di modal
        document.getElementById('detailContent').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-3">Memuat detail jadwal...</p>
            </div>
        `;
        
        // Tampilkan modal
        if (detailModal) {
            console.log('Showing detail modal...');
            detailModal.show();
        } else {
            console.error('❌ Detail modal not initialized');
            // Fallback: coba inisialisasi ulang
            try {
                const modalEl = document.getElementById('detailModal');
                if (modalEl && bootstrap) {
                    detailModal = new bootstrap.Modal(modalEl);
                    detailModal.show();
                } else {
                    alert('Modal tidak dapat dimuat. Silakan refresh halaman.');
                    return;
                }
            } catch (error) {
                console.error('Failed to initialize modal:', error);
                alert('Terjadi kesalahan. Silakan refresh halaman.');
                return;
            }
        }
        
        // Load data detail
        loadDetail(id);
    }
    
    function handleSetujui(button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        
        console.log('Setujui clicked:', { id, name });
        
        if (confirm(`Setujui jadwal dari ${name}?`)) {
            updateStatus(id, 'setujui', name);
        }
    }
    
    function handleReschedule(button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        
        console.log('Reschedule clicked:', { id, name });
        
        document.getElementById('rescheduleId').value = id;
        document.getElementById('studentName').textContent = name;
        
        if (rescheduleModal) {
            rescheduleModal.show();
        }
    }
    
    function confirmReschedule() {
        const id = document.getElementById('rescheduleId').value;
        const name = document.getElementById('studentName').textContent;
        
        if (rescheduleModal) {
            rescheduleModal.hide();
        }
        
        if (confirm(`Ubah status jadwal ${name} menjadi "Jadwalkan Ulang"?`)) {
            updateStatus(id, 'jadwalkan_ulang', name);
        } else if (rescheduleModal) {
            setTimeout(() => rescheduleModal.show(), 300);
        }
    }
    
    // ==============================
    // FUNGSI LOAD DETAIL
    // ==============================
    
    function loadDetail(id) {
        console.log('📡 Loading detail for ID:', id);
        
        $.ajax({
            url: CONTROLLER_PATH,
            type: 'GET',
            data: { 
                action: 'get_detail', 
                id: id 
            },
            dataType: 'json',
            success: function(response) {
                console.log('Detail response:', response);
                
                if (response.success && response.data) {
                    displayDetail(response.data);
                } else {
                    showErrorInModal(response.message || 'Gagal memuat detail');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                
                let errorMsg = 'Terjadi kesalahan saat memuat detail.';
                if (xhr.status === 401) {
                    errorMsg = 'Sesi Anda telah berakhir. Silakan login kembali.';
                }
                
                showErrorInModal(errorMsg);
            }
        });
    }
    
    function displayDetail(data) {
        console.log('Displaying detail:', data);
        
        // Helper functions
        function formatDate(dateString) {
            if (!dateString || dateString === '0000-00-00' || dateString === '0000-00-00 00:00:00') {
                return '-';
            }
            
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } catch (e) {
                return dateString;
            }
        }
        
        function formatTime(timeString) {
            if (!timeString || timeString === '00:00:00') return '-';
            return timeString.substring(0, 5);
        }
        
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function getStatusClass(status) {
            if (!status) return 'status-menunggu';
            status = status.toLowerCase().trim();
            
            if (status === 'menunggu') return 'status-menunggu';
            if (status === 'disetujui') return 'status-disetujui';
            if (status.includes('jadwalkan') || status.includes('ulang')) return 'status-jadwalkan-ulang';
            return 'status-menunggu';
        }
        
        const statusClass = getStatusClass(data.Status);
        const statusText = data.Status || 'Menunggu';
        
        const html = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informasi Siswa</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Nama Siswa</th>
                            <td>${escapeHtml(data.nama || '-')}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>${escapeHtml(data.kelas || '-')}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <td>${formatDate(data.created_at)}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informasi Jadwal</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Tanggal Konseling</th>
                            <td>${formatDate(data.Tanggal_Konseling)}</td>
                        </tr>
                        <tr>
                            <th>Waktu Konseling</th>
                            <td>${formatTime(data.Waktu_Konseling)}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="status-badge ${statusClass}">
                                    ${escapeHtml(statusText)}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Guru BK</th>
                            <td>${escapeHtml(data.nama_guru || '-')}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="text-muted mb-3">Detail Konseling</h6>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Topik Bimbingan</h6>
                            <p class="card-text">${escapeHtml(data.Topik_konseling || 'Tidak ada topik yang dicantumkan')}</p>
                            
                            ${data.keterangan ? `
                            <h6 class="card-title mt-3">Keterangan</h6>
                            <p class="card-text">${escapeHtml(data.keterangan)}</p>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('detailContent').innerHTML = html;
    }
    
    function showErrorInModal(message) {
        document.getElementById('detailContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                ${message}
                <button class="btn btn-sm btn-outline-danger mt-2" onclick="retryLoadDetail()">
                    Coba Lagi
                </button>
            </div>
        `;
    }
    
    // ==============================
    // FUNGSI UPDATE STATUS
    // ==============================
    
    function updateStatus(id, action, name) {
        console.log(`Updating status: ${action} for ID: ${id}`);
        
        // Tampilkan loading sederhana
        const originalText = document.title;
        document.title = 'Memproses...';
        
        $.ajax({
            url: CONTROLLER_PATH,
            type: 'POST',
            data: { 
                action: action, 
                id_jadwal: id 
            },
            dataType: 'json',
            success: function(response) {
                document.title = originalText;
                
                if (response.success) {
                    const newStatus = action === 'setujui' ? 'Disetujui' : 'Jadwalkan Ulang';
                    updateRowStatus(id, newStatus);
                    updateActionButtons(id);
                    
                    alert(`✅ ${response.message}`);
                } else {
                    alert(`❌ ${response.message}`);
                }
            },
            error: function(xhr, status, error) {
                document.title = originalText;
                
                if (xhr.status === 401) {
                    if (confirm('Sesi berakhir. Login kembali?')) {
                        window.location.href = '../../login.php';
                    }
                } else {
                    alert(`❌ Error: ${error}`);
                }
            }
        });
    }
    
    function updateRowStatus(id, newStatus) {
        const statusElement = document.getElementById(`status-${id}`);
        if (statusElement) {
            statusElement.textContent = newStatus;
            statusElement.className = `status-badge ${getStatusClass(newStatus)}`;
        }
    }
    
    function updateActionButtons(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            const dropdownMenu = row.querySelector('.dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.innerHTML = `
                    <li>
                        <a class="dropdown-item text-info detail-btn" href="#" data-id="${id}">
                            <i class="bi bi-info-circle"></i> Detail
                        </a>
                    </li>
                `;
                
                // Setup ulang tombol detail yang baru
                const newDetailBtn = dropdownMenu.querySelector('.detail-btn');
                if (newDetailBtn) {
                    newDetailBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        handleDetailClick(this);
                    });
                }
            }
        }
    }
    
    function getStatusClass(status) {
        if (!status) return 'status-menunggu';
        status = status.toLowerCase().trim();
        
        if (status === 'menunggu') return 'status-menunggu';
        if (status === 'disetujui') return 'status-disetujui';
        if (status.includes('jadwalkan') || status.includes('ulang')) return 'status-jadwalkan-ulang';
        return 'status-menunggu';
    }
    
    // ==============================
    // FUNGSI PENCARIAN
    // ==============================
    
    function setupSearch() {
        const searchInput = document.getElementById('searchInput');
        if (!searchInput) return;
        
        let timeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('#dataTable tr[data-id]');
                
                rows.forEach(row => {
                    row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            }, 300);
        });
    }
    
    // ==============================
    // GLOBAL FUNCTIONS UNTUK RETRY
    // ==============================
    
    window.retryLoadDetail = function() {
        const button = document.querySelector('.detail-btn[data-setup="true"]');
        if (button) {
            handleDetailClick(button);
        }
    };
    
    // ==============================
    // DEBUG INFORMATION
    // ==============================
    
    console.log('🔍 Debug Info:');
    console.log('Detail buttons found:', document.querySelectorAll('.detail-btn').length);
    console.log('Detail modal element:', document.getElementById('detailModal'));
    console.log('Bootstrap available:', typeof bootstrap);
    console.log('jQuery available:', typeof $);
    
    // Test: coba klik tombol detail secara programmatic
    setTimeout(() => {
        const testButton = document.querySelector('.detail-btn');
        if (testButton) {
            console.log('Test button available:', testButton);
            // Optional: test dengan kode ini untuk debug
            // testButton.click();
        }
    }, 1000);
    
    console.log('✅ Jadwal Konseling initialized!');
}

// ==============================
// AUTO-INIT SEDERHANA
// ==============================
(function() {
    console.log('🔧 Loading Jadwal Konseling System...');
    
    function initialize() {
        if (typeof initManajemenJadwalKonseling === 'function') {
            console.log('🚀 Calling init function...');
            initManajemenJadwalKonseling();
        } else {
            console.error('❌ initManajemenJadwalKonseling not found');
            
            // Coba load ulang script
            const scripts = document.getElementsByTagName('script');
            for (let script of scripts) {
                if (script.src.includes('JadwalKonseling.js')) {
                    console.log('Found script, reloading...');
                    const newScript = document.createElement('script');
                    newScript.src = script.src + '?t=' + Date.now();
                    newScript.onload = function() {
                        if (typeof initManajemenJadwalKonseling === 'function') {
                            initManajemenJadwalKonseling();
                        }
                    };
                    document.head.appendChild(newScript);
                    break;
                }
            }
        }
    }
    
    // Tunggu sampai DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
    
    // Fallback
    setTimeout(initialize, 2000);
})();