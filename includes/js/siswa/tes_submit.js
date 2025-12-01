// assets/js/tes/tes_submit.js

/**
 * Fungsi untuk handle submit tes dengan AJAX
 */
function submitTesForm() {
    const form = document.getElementById('tesForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Validasi semua soal terjawab
    const totalQuestions = document.querySelectorAll('.question-card').length;
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    
    if (answeredQuestions < totalQuestions) {
        const unanswered = totalQuestions - answeredQuestions;
        showAlert('error', `Masih ada ${unanswered} soal yang belum dijawab. Silakan lengkapi semua soal sebelum mengirim.`);
        
        // Scroll ke soal pertama yang belum terjawab
        scrollToUnanswered();
        return false;
    }
    
    // Tampilkan loading
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
    submitBtn.disabled = true;
    
    // Kumpulkan semua jawaban
    const jawaban = {};
    document.querySelectorAll('input[type="radio"]:checked').forEach(input => {
        const soalId = input.name.replace('jawaban[', '').replace(']', '');
        jawaban[soalId] = input.value;
    });
    
    // Tambahkan jawaban ke form sebagai hidden input
    const jawabanInput = document.createElement('input');
    jawabanInput.type = 'hidden';
    jawabanInput.name = 'jawaban_json';
    jawabanInput.value = JSON.stringify(jawaban);
    form.appendChild(jawabanInput);
    
    // Submit form
    form.submit();
    
    // Timeout untuk mengembalikan tombol jika terlalu lama
    setTimeout(() => {
        if (submitBtn.disabled) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            showAlert('error', 'Gagal mengirim tes. Silakan coba lagi.');
        }
    }, 15000);
    
    return true;
}

/**
 * Fungsi untuk show alert
 */
function showAlert(type, message) {
    // Remove existing alerts
    const existingAlert = document.querySelector('.custom-alert');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    `;
    
    alertDiv.innerHTML = `
        <strong>${type === 'error' ? 'Error!' : 'Success!'}</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

/**
 * Fungsi untuk scroll ke soal yang belum terjawab
 */
function scrollToUnanswered() {
    const questions = document.querySelectorAll('.question-card');
    for (let question of questions) {
        const hasAnswer = question.querySelector('input[type="radio"]:checked');
        if (!hasAnswer) {
            question.style.border = '2px solid #dc3545';
            question.style.boxShadow = '0 0 0 0.2rem rgba(220,53,69,.25)';
            
            setTimeout(() => {
                question.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                question.style.border = '';
                question.style.boxShadow = '';
            }, 3000);
            
            break;
        }
    }
}

/**
 * Fungsi untuk konfirmasi batal
 */
function confirmCancel() {
    const answeredQuestions = document.querySelectorAll('input[type="radio"]:checked').length;
    let message = 'Apakah Anda yakin ingin membatalkan tes?';
    
    if (answeredQuestions > 0) {
        message += `\nAnda sudah menjawab ${answeredQuestions} soal. Semua jawaban akan hilang.`;
    }
    
    if (confirm(message)) {
        window.location.href = 'tesbk.php';
    }
}

// Export fungsi untuk digunakan di file lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        submitTesForm,
        showAlert,
        scrollToUnanswered,
        confirmCancel
    };
}