// assets/js/pengajuanJadwal.js

document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Script pengajuanJadwal.js loaded');
    
    initializeEventListeners();
    setMinDate();
});

function initializeEventListeners() {
    // Tombol buka modal pengajuan baru
    const btnAjukan = document.getElementById('btnAjukanJadwal');
    if (btnAjukan) {
        btnAjukan.addEventListener('click', openModal);
    }
    
    // Form submission pengajuan baru
    const form = document.getElementById('formPengajuan');
    if (form) {
        form.addEventListener('submit', handleFormSubmission);
    }
    
    // Form submission jadwalkan ulang
    const formReschedule = document.getElementById('formReschedule');
    if (formReschedule) {
        formReschedule.addEventListener('submit', handleRescheduleSubmission);
    }
    
    // Reset form ketika modal ditutup
    const modal = document.getElementById('modalAjukan');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            resetForm();
        });
    }
}

function setMinDate() {
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.querySelector('input[name="tanggal"]');
    const rescheduleDateInput = document.getElementById('rescheduleTanggal');
    
    if (dateInput) dateInput.min = today;
    if (rescheduleDateInput) rescheduleDateInput.min = today;
}

// ======================
// FUNGSI JADWALKAN ULANG
// ======================

function openRescheduleModal(idJadwal, tanggalLama, jamLama) {
    console.log('🔄 Membuka modal jadwalkan ulang untuk ID:', idJadwal);
    
    // Set data ke form
    document.getElementById('rescheduleIdJadwal').value = idJadwal;
    document.getElementById('rescheduleTanggal').value = tanggalLama;
    document.getElementById('rescheduleJam').value = jamLama;
    
    // Buka modal
    const modalElement = document.getElementById('modalReschedule');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}

async function handleRescheduleSubmission(e) {
    e.preventDefault();
    console.log('📝 Form jadwalkan ulang disubmit');
    
    const form = e.target;
    const submitBtn = document.getElementById('btnReschedule');
    const originalText = submitBtn.innerHTML;
    
    try {
        // Validasi form
        const validationError = validateRescheduleForm(form);
        if (validationError) {
            showAlert('error', validationError);
            return;
        }
        
        // Konfirmasi
        const isConfirmed = await showRescheduleConfirmation(form);
        if (!isConfirmed) {
            console.log('❌ Pengguna membatalkan jadwalkan ulang');
            return;
        }
        
        // Kirim data
        await submitRescheduleData(form, submitBtn, originalText);
        
    } catch (error) {
        console.error('❌ Error dalam handleRescheduleSubmission:', error);
        showAlert('error', 'Terjadi kesalahan: ' + error.message);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

function validateRescheduleForm(form) {
    const tanggal = form.tanggal.value;
    const jam = form.jam.value;
    
    if (!tanggal) return 'Tanggal baru harus diisi';
    if (!jam) return 'Jam baru harus dipilih';
    
    // Validasi tanggal
    const today = new Date().toISOString().split('T')[0];
    if (tanggal < today) {
        return 'Tanggal tidak boleh kurang dari hari ini';
    }
    
    // Validasi hari Minggu
    const selectedDate = new Date(tanggal);
    if (selectedDate.getDay() === 0) {
        return 'Tidak bisa memilih hari Minggu';
    }
    
    return null;
}

function showRescheduleConfirmation(form) {
    return new Promise((resolve) => {
        const tanggal = form.tanggal.value;
        const jam = form.jam.value;
        
        const message = `
Apakah Anda yakin ingin mengatur ulang jadwal konseling?

📅 Tanggal Baru: ${tanggal}
⏰ Jam Baru: ${jam} WIB

Jadwal akan kembali menunggu konfirmasi dari guru BK.
        `.trim();
        
        resolve(confirm(message));
    });
}

async function submitRescheduleData(form, submitBtn, originalText) {
    try {
        // Loading state
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';
        submitBtn.disabled = true;
        
        // Prepare form data
        const formData = new FormData(form);
        
        console.log('🚀 Mengirim data jadwalkan ulang:', {
            id_jadwal: formData.get('id_jadwal'),
            tanggal: formData.get('tanggal'),
            jam: formData.get('jam'),
            action: formData.get('action')
        });
        
        // Kirim request
        const response = await fetch('controller/pengajuan_konseling.php', {
            method: 'POST',
            body: formData
        });
        
        // Cek status response
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Parse response
        const result = await response.json();
        console.log('📨 Response jadwalkan ulang:', result);
        
        if (result.success) {
            showAlert('success', result.message);
            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalReschedule'));
            modal.hide();
            // Refresh halaman setelah 2 detik
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(result.message);
        }
        
    } catch (error) {
        console.error('❌ Error submitRescheduleData:', error);
        throw error;
    } finally {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// ======================
// FUNGSI PENGAJUAN BARU
// ======================

function openModal() {
    console.log('🔄 Membuka modal pengajuan baru');
    
    const modalElement = document.getElementById('modalAjukan');
    if (!modalElement) {
        console.error('❌ Modal element tidak ditemukan');
        showAlert('error', 'Modal tidak dapat dibuka');
        return;
    }
    
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('✅ Modal berhasil dibuka');
    } catch (error) {
        console.error('❌ Error membuka modal:', error);
        showAlert('error', 'Gagal membuka form pengajuan');
    }
}

async function handleFormSubmission(e) {
    e.preventDefault();
    console.log('📝 Form pengajuan baru disubmit');
    
    const form = e.target;
    const submitBtn = document.getElementById('btnSubmit');
    const originalText = submitBtn.innerHTML;
    
    try {
        // Validasi form
        const validationError = validateForm(form);
        if (validationError) {
            showAlert('error', validationError);
            return;
        }
        
        // Konfirmasi pengajuan
        const isConfirmed = await showConfirmation(form);
        if (!isConfirmed) {
            console.log('❌ Pengguna membatalkan pengajuan');
            return;
        }
        
        // Kirim data
        await submitFormData(form, submitBtn, originalText);
        
    } catch (error) {
        console.error('❌ Error dalam handleFormSubmission:', error);
        showAlert('error', 'Terjadi kesalahan: ' + error.message);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

function validateForm(form) {
    const tanggal = form.tanggal.value;
    const jam = form.jam.value;
    const topik = form.topik.value;
    const id_guru = form.id_guru.value;
    
    if (!tanggal) return 'Tanggal harus diisi';
    if (!jam) return 'Jam harus dipilih';
    if (!topik) return 'Topik harus dipilih';
    if (!id_guru) return 'Guru BK harus dipilih';
    
    // Validasi tanggal
    const today = new Date().toISOString().split('T')[0];
    if (tanggal < today) {
        return 'Tanggal tidak boleh kurang dari hari ini';
    }
    
    // Validasi hari Minggu
    const selectedDate = new Date(tanggal);
    if (selectedDate.getDay() === 0) {
        return 'Tidak bisa memilih hari Minggu';
    }
    
    return null;
}

function showConfirmation(form) {
    return new Promise((resolve) => {
        const tanggal = form.tanggal.value;
        const jam = form.jam.value;
        const topik = form.topik.value;
        const guruName = form.id_guru.options[form.id_guru.selectedIndex].text;
        
        const message = `
Apakah Anda yakin ingin mengajukan jadwal konseling?

📅 Tanggal: ${tanggal}
⏰ Jam: ${jam} WIB
📚 Topik: ${topik}
👨‍🏫 Guru BK: ${guruName}

Pastikan data sudah benar sebelum mengirim.
        `.trim();
        
        resolve(confirm(message));
    });
}

async function submitFormData(form, submitBtn, originalText) {
    try {
        // Loading state
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';
        submitBtn.disabled = true;
        
        // Prepare form data
        const formData = new FormData(form);
        
        console.log('🚀 Mengirim data pengajuan baru:', {
            tanggal: formData.get('tanggal'),
            jam: formData.get('jam'),
            topik: formData.get('topik'),
            id_guru: formData.get('id_guru'),
            id_siswa: formData.get('id_siswa')
        });
        
        // Kirim request
        const response = await fetch('../../includes/siswa_control/pengajuanKonseling_Controller.php', {
            method: 'POST',
            body: formData
        });
        
        // Cek status response
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Parse response
        const result = await response.json();
        console.log('📨 Response pengajuan baru:', result);
        
        if (result.success) {
            showAlert('success', result.message);
            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalAjukan'));
            modal.hide();
            // Refresh halaman setelah 2 detik
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(result.message);
        }
        
    } catch (error) {
        console.error('❌ Error submitFormData:', error);
        throw error;
    } finally {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

function resetForm() {
    const form = document.getElementById('formPengajuan');
    if (form) {
        form.reset();
        setMinDate();
    }
}

function showAlert(type, message) {
    // Hapus alert sebelumnya
    const existingAlert = document.querySelector('.alert.position-fixed');
    if (existingAlert) {
        existingAlert.remove();
    }

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
    `;
    alertDiv.innerHTML = `
        <i class="bi ${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove setelah 5 detik
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}