// includes/js/admin/tambahTes.js - VERSI SANGAT SEDERHANA

let isSubmitting = false;

function initTambahTes() {
    console.log('✅ Tambah Tes initialized');
    
    const form = document.getElementById('formTambahTes');
    const downloadBtn = document.getElementById('downloadTemplateBtn');
    

    if (form) form.addEventListener('submit', handleFormSubmit);
    if (downloadBtn) downloadBtn.addEventListener('click', downloadTemplate);
   
}

async function handleFormSubmit(event) {
    event.preventDefault();
    
    if (isSubmitting) return;
    
    // Validasi sederhana
    const namaTes = document.getElementById('namaTesInput');
    const fileInput = document.getElementById('csvFileInput');
    
    let isValid = true;
    let errors = [];
    
    if (!namaTes || !namaTes.value.trim()) {
        errors.push('Nama tes wajib diisi');
        if (namaTes) namaTes.classList.add('is-invalid');
        isValid = false;
    }
    
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        errors.push('File CSV wajib diisi');
        if (fileInput) fileInput.classList.add('is-invalid');
        isValid = false;
    } else {
        const file = fileInput.files[0];
        if (!file.name.toLowerCase().endsWith('.csv')) {
            errors.push('File harus berformat CSV');
            isValid = false;
        } else if (file.size > 2 * 1024 * 1024) {
            errors.push('Ukuran file maksimal 2MB');
            isValid = false;
        }
    }
    
    if (!isValid) {
        showAlert('❌ ' + errors.join('\n'), 'error');
        return;
    }
    
    // Submit form
    isSubmitting = true;
    setSubmitButtonState(true);
    
    try {
        const formData = new FormData(event.target);
        const response = await fetch('../../includes/admin_control/proses_tambah_tes.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            showAlert('✅ ' + result.message, 'success');
            setTimeout(() => {
                if (typeof loadContent === 'function') loadContent('kelolaTes.php');
            }, 1500);
        } else {
            throw new Error(result.message);
        }
        
    } catch (error) {
        showAlert('❌ ' + error.message, 'error');
    } finally {
        isSubmitting = false;
        setSubmitButtonState(false);
    }
}

function setSubmitButtonState(loading) {
    const submitBtn = document.getElementById('submitBtn');
    if (!submitBtn) return;
    
    submitBtn.disabled = loading;
    if (loading) {
        submitBtn.innerHTML = '<span class="loading me-2"></span> Menyimpan...';
    } else {
        submitBtn.innerHTML = 'Simpan';
    }
}

function downloadTemplate() {
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = '../../includes/admin_control/download_template_soal.php';
    document.body.appendChild(iframe);
    setTimeout(() => document.body.removeChild(iframe), 2000);
    showAlert('Template berhasil diunduh!', 'success');
}


function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} custom-alert alert-dismissible fade show">
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    setTimeout(() => {
        const alert = document.querySelector('.custom-alert');
        if (alert) alert.remove();
    }, 5000);
}

window.initTambahTes = initTambahTes;
window.downloadTemplate = downloadTemplate;