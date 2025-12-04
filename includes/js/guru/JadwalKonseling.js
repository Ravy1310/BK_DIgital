// JadwalKonseling.js - Versi dengan Status "Disetujui"
function initManajemenJadwalKonseling() {
    console.log('Initializing Jadwal Konseling Management...');
    
    // ==============================
    // KONFIGURASI
    // ==============================
    const CONTROLLER_PATH = '../../includes/guru_control/JadwalController.php'; // Sesuaikan!
    
    // ==============================
    // SETUP MODAL
    // ==============================
    const rescheduleModal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    
    // ==============================
    // EVENT HANDLERS
    // ==============================
    
    // 1. Tombol Setujui
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('setujui-btn') || e.target.closest('.setujui-btn')) {
            e.preventDefault();
            const button = e.target.classList.contains('setujui-btn') ? 
                          e.target : e.target.closest('.setujui-btn');
            handleSetujui(button);
        }
        
        // Tombol Jadwalkan Ulang
        if (e.target.classList.contains('reschedule-btn') || e.target.closest('.reschedule-btn')) {
            e.preventDefault();
            const button = e.target.classList.contains('reschedule-btn') ? 
                          e.target : e.target.closest('.reschedule-btn');
            handleReschedule(button);
        }
        
        // Tombol Detail
        if (e.target.classList.contains('detail-btn') || e.target.closest('.detail-btn')) {
            e.preventDefault();
            const button = e.target.classList.contains('detail-btn') ? 
                          e.target : e.target.closest('.detail-btn');
            handleDetail(button);
        }
    });
    
    // 2. Form Reschedule
    document.getElementById('rescheduleForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        confirmReschedule();
    });
    
    // ==============================
    // FUNGSI UTAMA
    // ==============================
    
    function handleSetujui(button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        
        if (confirm(`Apakah Anda yakin ingin menyetujui jadwal konseling dari ${name}?`)) {
            updateStatus(id, 'setujui', name);
        }
    }
    
    function handleReschedule(button) {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        
        document.getElementById('rescheduleId').value = id;
        document.getElementById('studentName').textContent = name;
        rescheduleModal.show();
    }
    
    function confirmReschedule() {
        const id = document.getElementById('rescheduleId').value;
        const name = document.getElementById('studentName').textContent;
        
        rescheduleModal.hide();
        
        if (confirm(`Ubah status jadwal konseling dari ${name} menjadi "Jadwalkan Ulang"?`)) {
            updateStatus(id, 'jadwalkan_ulang', name);
        } else {
            rescheduleModal.show();
        }
    }
    
    function handleDetail(button) {
        const id = button.getAttribute('data-id');
        
        document.getElementById('detailContent').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat detail...</p>
            </div>
        `;
        
        detailModal.show();
        loadDetail(id);
    }
    
    function loadDetail(id) {
        console.log('Loading detail for ID:', id);
        
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
                    document.getElementById('detailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            ${response.message || 'Gagal memuat detail data.'}
                        </div>
                    `;
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseText);
                
                if (xhr.status === 401) {
                    document.getElementById('detailContent').innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Sesi Anda telah berakhir. Silakan <a href="../../login.php">login kembali</a>.
                        </div>
                    `;
                } else {
                    document.getElementById('detailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Terjadi kesalahan saat memuat detail.<br>
                            <small>Error: ${error}</small>
                        </div>
                    `;
                }
            }
        });
    }
    
    function displayDetail(data) {
        // Format tanggal
        const formatDate = (dateString) => {
            if (!dateString || dateString === '0000-00-00' || dateString === '0000-00-00 00:00:00') {
                return '-';
            }
            
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } catch (e) {
                return dateString;
            }
        };
        
        // Format waktu
        const formatTime = (timeString) => {
            if (!timeString || timeString === '00:00:00') return '-';
            return timeString.substring(0, 5);
        };
        
        // Escape HTML untuk keamanan
        const escapeHtml = (text) => {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        };
        
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
    
    // ==============================
    // FUNGSI UPDATE STATUS
    // ==============================
    
    function updateStatus(id, action, name) {
        console.log(`Updating status: ${action} for ID: ${id}`);
        
        // Tampilkan loading
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loadingOverlay';
        loadingDiv.innerHTML = `
            <div style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;justify-content:center;align-items:center;">
                <div style="background:white;padding:20px;border-radius:10px;text-align:center;">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Memproses...</p>
                </div>
            </div>
        `;
        document.body.appendChild(loadingDiv);
        
        $.ajax({
            url: CONTROLLER_PATH,
            type: 'POST',
            data: { 
                action: action, 
                id_jadwal: id 
            },
            dataType: 'json',
            success: function(response) {
                // Hapus loading
                document.getElementById('loadingOverlay')?.remove();
                
                console.log('Update response:', response);
                
                if (response.success) {
                    // Tentukan status yang akan ditampilkan di UI
                    let newStatus = 'Menunggu';
                    if (action === 'setujui') {
                        newStatus = 'Disetujui'; // INI YANG DIUBAH
                    } else if (action === 'jadwalkan_ulang') {
                        newStatus = 'Jadwalkan Ulang';
                    }
                    
                    // Update UI
                    updateRowStatus(id, newStatus);
                    updateActionButtons(id);
                    
                    // Tampilkan pesan sukses
                    alert(`✅ ${response.message}`);
                } else {
                    alert(`❌ ${response.message}`);
                }
            },
            error: function(xhr, status, error) {
                // Hapus loading
                document.getElementById('loadingOverlay')?.remove();
                
                console.error('Update Error:', error);
                
                if (xhr.status === 401) {
                    if (confirm('Sesi Anda telah berakhir. Ingin login kembali?')) {
                        window.location.href = '../../login.php';
                    }
                } else {
                    alert(`❌ Error: Terjadi kesalahan pada server.\n${error}`);
                }
            }
        });
    }
    
    // ==============================
    // FUNGSI HELPER UI
    // ==============================
    
    function updateRowStatus(id, newStatus) {
        const statusElement = document.getElementById(`status-${id}`);
        const rowElement = document.querySelector(`tr[data-id="${id}"]`);
        
        if (statusElement && rowElement) {
            // Update teks status (gunakan "Disetujui" bukan "Setujui")
            statusElement.textContent = newStatus;
            
            // Update class status
            const statusClass = getStatusClass(newStatus);
            statusElement.className = `status-badge ${statusClass}`;
            
            // Update data-status pada row
            rowElement.setAttribute('data-status', newStatus);
        }
    }
    
    function updateActionButtons(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            const dropdownMenu = row.querySelector('.dropdown-menu');
            if (dropdownMenu) {
                // Hanya tampilkan tombol Detail setelah status berubah
                dropdownMenu.innerHTML = `
                    <li>
                        <a class="dropdown-item text-info detail-btn" href="#" data-id="${id}">
                            <i class="bi bi-info-circle"></i> Detail
                        </a>
                    </li>
                `;
            }
        }
    }
    
    function getStatusClass(status) {
        if (!status) return 'status-menunggu';
        
        status = status.toLowerCase().trim();
        
        if (status === 'menunggu') {
            return 'status-menunggu';
        } else if (status === 'disetujui') { // INI YANG DIUBAH
            return 'status-disetujui';
        } else if (status.includes('jadwalkan') || status.includes('ulang')) {
            return 'status-jadwalkan-ulang';
        } else {
            return 'status-menunggu';
        }
    }
    
    // ==============================
    // FUNGSI PENCARIAN
    // ==============================
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#dataTable tr[data-id]');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    console.log('✅ Jadwal Konseling Management initialized successfully!');
}

// Ekspor untuk penggunaan global
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initManajemenJadwalKonseling };
}