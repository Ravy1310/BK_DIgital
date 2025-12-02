// includes/js/admin/tambahTes.js - VERSI BARU TANPA showAlert
let isSubmitting = false;

function initTambahTes() {
    console.log('✅ Tambah Tes initialized');

    
    const form = document.getElementById('formTambahTes');
    const downloadBtn = document.getElementById('downloadTemplateBtn');
    const csvFileInput = document.getElementById('csvFileInput');
    
    if (form) form.addEventListener('submit', handleFormSubmit);
    if (downloadBtn) downloadBtn.addEventListener('click', downloadTemplate);
    if (csvFileInput) csvFileInput.addEventListener('change', handleFileSelect);
    
    // Initialize Bootstrap Toast
    initializeToast();
}

function initializeToast() {
    // Toast sudah ada di HTML, tidak perlu dibuat ulang
    console.log('Toast initialized');
}

// Fungsi untuk menampilkan notifikasi dengan Toast
function showNotification(type, message, title = '') {
    console.log(`Showing ${type} notification: ${message}`);
    
    const toastEl = document.getElementById('liveToast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastBody = document.getElementById('toastBody');
    const toastTime = document.getElementById('toastTime');
    
    if (!toastEl) {
        console.error('Toast element not found!');
        // Fallback ke alert biasa
        alert(message);
        return;
    }
    
    // Set icon dan warna berdasarkan type
    let iconClass = '';
    let toastClass = '';
    
    switch(type.toLowerCase()) {
        case 'success':
            iconClass = 'fa-check-circle text-success';
            toastClass = 'toast-custom-success';
            title = title || 'Berhasil';
            break;
        case 'error':
        case 'danger':
            iconClass = 'fa-exclamation-triangle text-danger';
            toastClass = 'toast-custom-error';
            title = title || 'Error';
            break;
        case 'warning':
            iconClass = 'fa-exclamation-circle text-warning';
            toastClass = 'toast-custom-warning';
            title = title || 'Peringatan';
            break;
        default:
            iconClass = 'fa-info-circle text-info';
            toastClass = 'toast-custom-info';
            title = title || 'Info';
    }
    
    // Update toast content
    toastIcon.className = `fas ${iconClass} me-2`;
    toastTitle.textContent = title;
    toastBody.textContent = message;
    toastTime.textContent = 'Baru saja';
    
    // Remove existing classes
    toastEl.classList.remove('toast-custom-success', 'toast-custom-error', 'toast-custom-warning', 'toast-custom-info');
    toastEl.classList.add(toastClass);
    
    // Show toast
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: type === 'success' ? 3000 : 5000
    });
    
    toast.show();
    
    return toast;
}

// Fungsi untuk menampilkan modal konfirmasi sederhana
function showConfirmModal(message, onConfirm, onCancel = null) {
    // Hapus modal sebelumnya jika ada
    const existingModal = document.getElementById('confirmModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    const modalHTML = `
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${message}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelBtn" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id="confirmBtn">Ya, Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    const modalElement = document.getElementById('confirmModal');
    const modal = new bootstrap.Modal(modalElement);
    
    modal.show();
    
    // Setup event listeners
    document.getElementById('confirmBtn').addEventListener('click', function() {
        modal.hide();
        if (typeof onConfirm === 'function') {
            onConfirm();
        }
    });
    
    document.getElementById('cancelBtn').addEventListener('click', function() {
        modal.hide();
        if (typeof onCancel === 'function') {
            onCancel();
        }
    });
    
    // Remove modal from DOM when hidden
    modalElement.addEventListener('hidden.bs.modal', function() {
        modalElement.remove();
    });
}

async function handleFormSubmit(event) {
    event.preventDefault();
    
    if (isSubmitting) {
        showNotification('warning', 'Sedang memproses...');
        return;
    }
    
    // Validasi form
    const namaTes = document.getElementById('namaTesInput')?.value.trim();
    const csvFile = document.getElementById('csvFileInput')?.files[0];
    
    if (!namaTes || namaTes.length < 3) {
        showNotification('error', 'Nama tes minimal 3 karakter');
        return;
    }
    
    if (!csvFile) {
        showNotification('error', 'File CSV wajib diupload');
        return;
    }
    
    // Konfirmasi sebelum submit
    showConfirmModal(
        'Apakah Anda yakin ingin menambahkan tes baru ini?',
        async () => {
            // User klik konfirmasi
            isSubmitting = true;
            setSubmitButtonState(true);
            
            try {
                const formData = new FormData(event.target);
                
                // Tampilkan notifikasi proses
                showNotification('info', 'Mengupload data, harap tunggu...', 'Memproses');
                
                const response = await fetch('../../includes/admin_control/proses_tambah_tes.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                console.log('Server response:', result);
                
                if (result.status && result.status.toLowerCase() === 'success') {
                    // SUKSES - tampilkan notifikasi hijau
                    showNotification('success', result.message || 'Tes berhasil ditambahkan!', 'Berhasil');
                    
                    // Redirect setelah 2 detik
                    setTimeout(() => {
                        window.loadContent('kelolaTes.php');
                    }, 2000);
                    
                } else {
                    // ERROR - tampilkan notifikasi merah
                    showNotification('error', result.message || 'Gagal menambahkan tes', 'Error');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showNotification('error', 'Terjadi kesalahan: ' + error.message, 'Error');
            } finally {
                isSubmitting = false;
                setSubmitButtonState(false);
            }
        },
        () => {
            // User klik batal
            showNotification('info', 'Proses dibatalkan');
        }
    );
}

function setSubmitButtonState(loading) {
    const submitBtn = document.getElementById('submitBtn');
    if (!submitBtn) return;
    
    if (loading) {
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;
        submitBtn.querySelector('.button-text').style.display = 'none';
        submitBtn.querySelector('.loading-spinner').style.display = 'inline';
    } else {
        submitBtn.classList.remove('btn-loading');
        submitBtn.disabled = false;
        submitBtn.querySelector('.button-text').style.display = 'inline';
        submitBtn.querySelector('.loading-spinner').style.display = 'none';
    }
}

function downloadTemplate() {
    console.log('📥 Downloading template...');
    
    // Tampilkan notifikasi
    showNotification('info', 'Mengunduh template CSV...', 'Memproses');
    
    // Buat elemen anchor untuk download
    const link = document.createElement('a');
    link.href = '../../includes/admin_control/download_template_soal.php';
    link.download = 'template_soal_tes.csv';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Tampilkan notifikasi sukses setelah delay
    setTimeout(() => {
        showNotification('success', 'Template berhasil diunduh!', 'Berhasil');
    }, 500);
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    const fileValidation = document.getElementById('fileValidation');
    
    if (!fileValidation) return;
    
    if (file) {
        // Validasi ekstensi
        if (!file.name.toLowerCase().endsWith('.csv')) {
            fileValidation.innerHTML = `
                <div class="alert alert-danger mt-2 p-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    File harus berformat .csv
                </div>
            `;
            event.target.value = '';
            showNotification('error', 'File harus berformat CSV', 'Format Salah');
            return;
        }
        
        // Validasi ukuran
        if (file.size > 2 * 1024 * 1024) {
            fileValidation.innerHTML = `
                <div class="alert alert-danger mt-2 p-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Ukuran file maksimal 2MB
                </div>
            `;
            event.target.value = '';
            showNotification('error', 'Ukuran file maksimal 2MB', 'File Terlalu Besar');
            return;
        }
        
        // Tampilkan info file
        const fileSize = (file.size / 1024).toFixed(2);
        fileValidation.innerHTML = `
            <div class="alert alert-success mt-2 p-3 file-info">
                <div><i class="fas fa-file-csv me-2"></i><strong>${file.name}</strong></div>
                <small class="text-muted">${fileSize} KB • Siap diupload</small>
            </div>
        `;
        
        showNotification('success', `File "${file.name}" siap diupload`, 'File Valid');
        
    } else {
        fileValidation.innerHTML = '';
    }
}

// Export ke global scope
window.initTambahTes = initTambahTes;
window.showNotification = showNotification;
window.showConfirmModal = showConfirmModal;