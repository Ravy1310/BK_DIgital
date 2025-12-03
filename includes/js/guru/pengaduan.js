// File: pengaduan.js

// Variabel global untuk menyimpan data pengaduan saat ini
let currentPengaduanId = null;
let currentPengaduanStatus = null;

// Fungsi inisialisasi manajemen pengaduan
function initManajemenPengaduan() {
    console.log('Manajemen Pengaduan diinisialisasi...');
    
    // Setup pencarian
    setupSearch();
    
    // Setup modal dengan intervensi manual
    setupModalWithOverride();
    
    console.log('✅ Inisialisasi selesai');
}

// Setup fitur pencarian
function setupSearch() {
    const searchInput = document.getElementById("searchInput");
    
    if (searchInput) {
        searchInput.addEventListener("keyup", function() {
            let input = this.value.toLowerCase();
            let rows = document.querySelectorAll("#complaintTable tr");
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 4) {
                    const text = Array.from(cells).slice(0, 4)
                        .map(td => td.innerText.toLowerCase())
                        .join(' ');
                    row.style.display = text.includes(input) ? "" : "none";
                }
            });
        });
    }
}

// Setup modal dengan override manual
function setupModalWithOverride() {
    const detailModal = document.getElementById('detailModal');
    
    if (!detailModal) {
        console.error('❌ Modal tidak ditemukan');
        return;
    }
    
    // Tangkap event show.bs.modal
    detailModal.addEventListener('show.bs.modal', function(event) {
        console.log('🎯 Modal akan ditampilkan');
        
        const button = event.relatedTarget;
        if (!button) {
            console.warn('⚠️ Tidak ada tombol relatedTarget');
            return;
        }
        
        // Ambil data dari atribut
        currentPengaduanId = button.getAttribute('data-id');
        const subject = button.getAttribute('data-subject');
        const reporter = button.getAttribute('data-reporter');
        const date = button.getAttribute('data-date');
        const message = button.getAttribute('data-message');
        currentPengaduanStatus = button.getAttribute('data-status');
        const jenisKejadian = button.getAttribute('data-jenis-kejadian');
        
        console.log('📦 Data yang diambil:', {
            id: currentPengaduanId,
            status: currentPengaduanStatus,
            subject: subject
        });
        
        // Isi data ke modal (segera, sebelum modal muncul)
        setTimeout(() => {
            fillModalWithData({
                subject: subject,
                reporter: reporter,
                date: date,
                message: message,
                status: currentPengaduanStatus,
                jenisKejadian: jenisKejadian
            });
            
            // Setup tombol aksi (dengan delay sedikit)
            setTimeout(setupActionButtonNow, 50);
        }, 10);
    });
    
    // Tangkap event shown.bs.modal (setelah modal muncul)
    detailModal.addEventListener('shown.bs.modal', function() {
        console.log('✅ Modal sudah ditampilkan');
        // Pastikan tombol sudah di-setup
        setTimeout(setupActionButtonNow, 100);
    });
}

// Isi data ke modal
function fillModalWithData(data) {
    console.log('📝 Mengisi data modal:', data);
    
    if (data.subject) document.getElementById('subjectText').textContent = data.subject;
    if (data.reporter) document.getElementById('reporterText').textContent = "Pelapor: " + data.reporter;
    if (data.date) document.getElementById('dateText').textContent = data.date;
    if (data.message) document.getElementById('messageText').textContent = data.message;
    
    const jenisKejadianBadge = document.getElementById('jenisKejadianBadge');
    if (jenisKejadianBadge && data.jenisKejadian) {
        jenisKejadianBadge.textContent = data.jenisKejadian;
    }
    
    const statusBadge = document.getElementById('statusBadge');
    if (statusBadge && data.status) {
        statusBadge.textContent = data.status;
        statusBadge.className = 'status-btn';
        
        // Reset semua kelas status
        statusBadge.classList.remove('status-new', 'status-process', 'status-done');
        
        // Normalisasi status (case insensitive)
        const normalizedStatus = (data.status || '').toLowerCase().trim();
        
        // Tambahkan kelas berdasarkan status
        if (normalizedStatus === 'baru' || normalizedStatus === 'menunggu') {
            statusBadge.classList.add('status-new');
        } else if (normalizedStatus === 'diproses') {
            statusBadge.classList.add('status-process');
        } else if (normalizedStatus === 'selesai') {
            statusBadge.classList.add('status-done');
        }
    }
}

// Setup tombol aksi dengan cara yang pasti bekerja
function setupActionButtonNow() {
    console.log('🔄 Setup tombol aksi dengan status:', currentPengaduanStatus, 'ID:', currentPengaduanId);
    
    const actionButton = document.getElementById('actionButton');
    if (!actionButton) {
        console.error('❌ Tombol actionButton tidak ditemukan!');
        return;
    }
    
    // Reset tombol
    actionButton.style.display = 'inline-block';
    actionButton.disabled = false;
    actionButton.classList.remove('btn-warning', 'btn-success', 'btn-info');
    
    // Normalisasi status (case insensitive)
    const normalizedStatus = (currentPengaduanStatus || '').toLowerCase().trim();
    
    // Setup berdasarkan status
    if (normalizedStatus === 'baru' || normalizedStatus === 'menunggu') {
        actionButton.textContent = "Ubah ke Diproses";
        actionButton.classList.add('btn', 'btn-warning');
        actionButton.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🚀 Tombol "Ubah ke Diproses" diklik');
            ubahStatusKeDiproses(currentPengaduanId);
        };
        
    } else if (normalizedStatus === 'diproses') {
        actionButton.textContent = "Ubah ke Selesai";
        actionButton.classList.add('btn', 'btn-success');
        actionButton.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🚀 Tombol "Ubah ke Selesai" diklik');
            ubahStatusKeSelesai(currentPengaduanId);
        };
        
    } else if (normalizedStatus === 'selesai') {
        actionButton.style.display = 'none';
    } else {
        // Default: sembunyikan jika status tidak dikenali
        actionButton.style.display = 'none';
        console.warn('⚠️ Status tidak dikenali:', currentPengaduanStatus, '-> normalized:', normalizedStatus);
    }
    
    console.log('✅ Tombol diatur:', {
        text: actionButton.textContent,
        id: currentPengaduanId,
        status: currentPengaduanStatus,
        normalized: normalizedStatus,
        display: actionButton.style.display
    });
}

// Fungsi untuk mengubah status ke Diproses
function ubahStatusKeDiproses(idPengaduan) {
    console.log('🔄 ubahStatusKeDiproses dipanggil, ID:', idPengaduan);
    
    if (!idPengaduan) {
        alert('❌ ID pengaduan tidak valid!');
        return;
    }
    
    if (!confirm('Apakah Anda yakin ingin mengubah status pengaduan ini menjadi Diproses?')) {
        console.log('❌ User membatalkan');
        return;
    }
    
    // Tampilkan loading
    const actionButton = document.getElementById('actionButton');
    if (actionButton) {
        actionButton.innerHTML = '<span class="loading-spinner me-2"></span>Memproses...';
        actionButton.disabled = true;
    }
    
    // Kirim request AJAX
    const formData = new FormData();
    formData.append('id_pengaduan', idPengaduan);
    formData.append('status', 'Diproses');
    
    console.log('📤 Mengirim request ke: ../../includes/guru_control/PengaduanController.php?action=update_status');
    
    fetch('../../includes/guru_control/PengaduanController.php?action=update_status', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📥 Response diterima:', {
            status: response.status,
            statusText: response.statusText,
            url: response.url,
            ok: response.ok
        });
        
        // Cek jika response kosong
        if (response.status === 204 || response.status === 205) {
            console.warn('⚠️ Response kosong (204/205)');
            return Promise.resolve('{}'); // Return JSON kosong
        }
        
        // Cek content type
        const contentType = response.headers.get('content-type');
        console.log('📄 Content-Type:', contentType);
        
        if (contentType && contentType.includes('text/html')) {
            console.warn('⚠️ Server mengembalikan HTML, bukan JSON');
            return Promise.reject(new Error('Server mengembalikan HTML page'));
        }
        
        return response.text();
    })
    .then(text => {
        console.log('📄 Response text (panjang):', text ? text.length : 0, 'karakter');
        console.log('📄 Response text (preview):', text ? text.substring(0, 200) : 'EMPTY');
        
        // Jika response kosong
        if (!text || text.trim() === '') {
            console.warn('⚠️ Response kosong dari server');
            throw new Error('Response kosong');
        }
        
        try {
            const data = JSON.parse(text);
            console.log('✅ JSON parsed:', data);
            
            if (data.success) {
                alert('✅ Status berhasil diubah menjadi Diproses!');
                
                // Update tampilan tanpa reload
                updateTableStatus(idPengaduan, 'Diproses');
                
                // Tutup modal
                setTimeout(() => {
                    closeModal();
                }, 500);
                
            } else {
                alert('❌ Gagal: ' + (data.message || 'Terjadi kesalahan'));
                resetButton();
            }
        } catch (e) {
            console.error('❌ Error parsing JSON:', e);
            console.error('❌ Raw response (first 500 chars):', text ? text.substring(0, 500) : 'EMPTY');
            alert('❌ Format response tidak valid dari server! Response bukan JSON.');
            resetButton();
        }
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        console.error('❌ Error stack:', error.stack);
        
        alert('❌ Error: ' + error.message + '\n\nPeriksa console untuk detail.');
        resetButton();
    });
}

// Fungsi untuk mengubah status ke Selesai
function ubahStatusKeSelesai(idPengaduan) {
    console.log('🔄 ubahStatusKeSelesai dipanggil, ID:', idPengaduan);
    
    if (!idPengaduan) {
        alert('❌ ID pengaduan tidak valid!');
        return;
    }
    
    if (!confirm('Apakah Anda yakin ingin mengubah status pengaduan ini menjadi Selesai?')) {
        console.log('❌ User membatalkan');
        return;
    }
    
    // Tampilkan loading
    const actionButton = document.getElementById('actionButton');
    if (actionButton) {
        actionButton.innerHTML = '<span class="loading-spinner me-2"></span>Memproses...';
        actionButton.disabled = true;
    }
    
    // Kirim request AJAX
    const formData = new FormData();
    formData.append('id_pengaduan', idPengaduan);
    formData.append('status', 'Selesai');
    
    console.log('📤 Mengirim request ke: ../../includes/guru_control/PengaduanController.php?action=update_status');
    
    fetch('../../includes/guru_control/PengaduanController.php?action=update_status', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📥 Response diterima, status:', response.status);
        
        if (response.status === 204 || response.status === 205) {
            return Promise.resolve('{}');
        }
        
        return response.text();
    })
    .then(text => {
        console.log('📄 Response:', text ? text.substring(0, 200) : 'EMPTY');
        
        if (!text || text.trim() === '') {
            throw new Error('Response kosong');
        }
        
        try {
            const data = JSON.parse(text);
            
            if (data.success) {
                alert('✅ Status berhasil diubah menjadi Selesai!');
                
                // Hapus baris dari tabel (karena status selesai tidak ditampilkan)
                removeRowFromTable(idPengaduan);
                
                // Tutup modal
                setTimeout(() => {
                    closeModal();
                }, 300);
                
            } else {
                alert('❌ Gagal: ' + (data.message || 'Terjadi kesalahan'));
                resetButton();
            }
        } catch (e) {
            console.error('❌ Error parsing JSON:', e);
            alert('❌ Format response tidak valid!');
            resetButton();
        }
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        alert('❌ Error: ' + error.message);
        resetButton();
    });
}

// Fungsi untuk menghapus baris dari tabel (ketika status menjadi Selesai)
function removeRowFromTable(pengaduanId) {
    console.log('🗑️ Menghapus baris untuk ID:', pengaduanId);
    
    // Cari baris yang sesuai dengan ID pengaduan
    const rows = document.querySelectorAll('#complaintTable tr');
    let targetRow = null;
    
    rows.forEach(row => {
        const link = row.querySelector('.action-link');
        if (link && link.getAttribute('data-id') == pengaduanId) {
            targetRow = row;
        }
    });
    
    if (targetRow) {
        // Tambahkan animasi fade out
        targetRow.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        targetRow.style.opacity = '0';
        targetRow.style.transform = 'translateX(-20px)';
        
        // Hapus baris setelah animasi selesai
        setTimeout(() => {
            targetRow.remove();
            console.log('✅ Baris pengaduan dihapus dari tabel karena status Selesai');
            
            // Cek jika tabel kosong
            const remainingRows = document.querySelectorAll('#complaintTable tr');
            if (remainingRows.length === 0) {
                // Tampilkan pesan tabel kosong
                const tableBody = document.getElementById('complaintTable');
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Tidak ada pengaduan yang perlu ditangani</td></tr>';
            }
        }, 300);
    }
    
    // Reset tombol loading
    resetButton();
}

// Fungsi untuk update tampilan tabel tanpa reload (untuk status Diproses)
function updateTableStatus(pengaduanId, newStatus) {
    console.log('🔄 Update tampilan untuk ID:', pengaduanId, 'Status baru:', newStatus);
    
    // Cari baris yang sesuai dengan ID pengaduan
    const rows = document.querySelectorAll('#complaintTable tr');
    let targetRow = null;
    
    rows.forEach(row => {
        const link = row.querySelector('.action-link');
        if (link && link.getAttribute('data-id') == pengaduanId) {
            targetRow = row;
        }
    });
    
    // Update status badge di tabel
    if (targetRow) {
        const statusCell = targetRow.querySelector('td:nth-child(4)');
        if (statusCell) {
            const statusBadge = statusCell.querySelector('.status-btn');
            if (statusBadge) {
                statusBadge.textContent = newStatus;
                
                // Update class status
                statusBadge.className = 'status-btn';
                const normalizedStatus = newStatus.toLowerCase().trim();
                
                if (normalizedStatus === 'baru' || normalizedStatus === 'menunggu') {
                    statusBadge.classList.add('status-new');
                } else if (normalizedStatus === 'diproses') {
                    statusBadge.classList.add('status-process');
                } else if (normalizedStatus === 'selesai') {
                    statusBadge.classList.add('status-done');
                }
                
                console.log('✅ Status badge di tabel diperbarui');
            }
        }
        
        // Update data-status di tombol lihat
        const viewButton = targetRow.querySelector('.action-link');
        if (viewButton) {
            viewButton.setAttribute('data-status', newStatus);
        }
    }
    
    // Update current status jika ini pengaduan yang sama
    if (currentPengaduanId == pengaduanId) {
        currentPengaduanStatus = newStatus;
    }
    
    // Update tombol aksi di modal (jika masih terbuka)
    const actionButton = document.getElementById('actionButton');
    if (actionButton && actionButton.style.display !== 'none') {
        const normalizedStatus = newStatus.toLowerCase().trim();
        
        if (normalizedStatus === 'diproses') {
            actionButton.textContent = "Ubah ke Selesai";
            actionButton.className = 'btn btn-success';
            actionButton.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🚀 Tombol "Ubah ke Selesai" diklik');
                ubahStatusKeSelesai(pengaduanId);
            };
            
            // Update status badge di modal
            const modalStatusBadge = document.getElementById('statusBadge');
            if (modalStatusBadge) {
                modalStatusBadge.textContent = newStatus;
                modalStatusBadge.className = 'status-btn';
                modalStatusBadge.classList.remove('status-new', 'status-process', 'status-done');
                modalStatusBadge.classList.add('status-process');
            }
        }
    }
    
    // Reset tombol loading
    resetButton();
}

// Helper function untuk reset tombol
function resetButton() {
    const actionButton = document.getElementById('actionButton');
    if (actionButton) {
        actionButton.disabled = false;
        // Kembalikan teks sesuai dengan status saat ini
        const normalizedStatus = (currentPengaduanStatus || '').toLowerCase().trim();
        
        if (normalizedStatus === 'baru' || normalizedStatus === 'menunggu') {
            actionButton.innerHTML = 'Ubah ke Diproses';
        } else if (normalizedStatus === 'diproses') {
            actionButton.innerHTML = 'Ubah ke Selesai';
        }
    }
}

// Helper function untuk tutup modal
function closeModal() {
    const modalElement = document.getElementById('detailModal');
    if (modalElement) {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        } else {
            // Jika instance tidak ditemukan, sembunyikan manual
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    }
}

// Fungsi untuk test endpoint langsung
function testEndpointDirectly() {
    console.log('🧪 Testing endpoint langsung...');
    
    // Test GET request dulu
    fetch('../../includes/guru_control/PengaduanController.php')
        .then(response => {
            console.log('GET Test - Status:', response.status, response.statusText);
            return response.text();
        })
        .then(text => {
            console.log('GET Test - Response (first 500 chars):', text ? text.substring(0, 500) : 'EMPTY');
        })
        .catch(error => {
            console.error('Test Error:', error);
        });
}

// Fungsi test untuk debug
function testModal() {
    console.log('=== TEST MODAL ===');
    
    // Setup data test dengan status lowercase
    currentPengaduanId = '999';
    currentPengaduanStatus = 'menunggu';
    
    // Isi data test
    fillModalWithData({
        subject: 'TEST Pengaduan',
        reporter: 'John Doe (XII IPA 1)',
        date: '15 Des 2024 pukul 14:30',
        message: 'Ini adalah pesan test',
        status: 'menunggu',
        jenisKejadian: 'Test'
    });
    
    // Setup tombol
    setupActionButtonNow();
    
    console.log('✅ Test selesai - tombol seharusnya muncul dengan teks "Ubah ke Diproses"');
}

// Fungsi untuk cek status tombol
function checkButton() {
    const actionButton = document.getElementById('actionButton');
    if (actionButton) {
        console.log('🔍 Status tombol:', {
            exists: true,
            text: actionButton.textContent,
            display: actionButton.style.display,
            hidden: actionButton.hidden,
            disabled: actionButton.disabled,
            onclick: actionButton.onclick ? 'SET' : 'NOT SET'
        });
    } else {
        console.log('❌ Tombol tidak ditemukan!');
    }
}

// Inisialisasi otomatis saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM siap, menjalankan initManajemenPengaduan...');
    console.log('📍 Current URL:', window.location.href);
    console.log('📍 Current Path:', window.location.pathname);
    
    // Tunggu sedikit agar semua elemen tersedia
    setTimeout(() => {
        if (typeof initManajemenPengaduan === 'function') {
            initManajemenPengaduan();
        }
        
        // Tambahkan fungsi helper ke global
        window.checkButton = checkButton;
        window.testEndpointDirectly = testEndpointDirectly;
    }, 100);
});

// Ekspor fungsi utama
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initManajemenPengaduan };
}

// Buat fungsi test tersedia di global
window.testModal = testModal;
window.currentPengaduanId = null;
window.currentPengaduanStatus = null;
window.checkButton = checkButton;
window.testEndpointDirectly = testEndpointDirectly;