// tambahtes.js - Simplified and Robust Version

console.log('🔥 tambahtes.js loaded!');

// Main initialization function
function initTambahTes() {
    console.log('🎯 Initializing Tambah Tes System...');
    
    // Multiple attempts to find the form
    let form = findForm();
    
    if (form) {
        console.log('✅ SUCCESS: Form found!', form);
        setupFormHandlers(form);
        setupButtonHandlers();
        setupValidation();
        console.log('🎉 Tambah Tes System fully initialized!');
    } else {
        console.error('❌ FAILED: Form not found after multiple attempts');
        console.log('🔍 Current form elements:', document.querySelectorAll('form'));
        showAlert('System initialization failed. Please refresh the page.', 'danger');
    }
}

// Robust form finding with multiple fallbacks
function findForm() {
    console.log('🔍 Searching for form...');
    
    // Try multiple selectors
    const selectors = [
        '#formTambahTes',
        'form',
        'form[enctype="multipart/form-data"]',
        'form[name="formTambahTes"]'
    ];
    
    for (let selector of selectors) {
        const form = document.querySelector(selector);
        if (form) {
            console.log(`✅ Form found with selector: ${selector}`);
            return form;
        }
    }
    
    // Last resort: find any form in the document
    const allForms = document.querySelectorAll('form');
    if (allForms.length > 0) {
        console.log(`✅ Form found from forms collection: ${allForms[0]}`);
        return allForms[0];
    }
    
    return null;
}

// Setup form event handlers
function setupFormHandlers(form) {
    console.log('🔧 Setting up form handlers...');
    
    // Remove existing listeners to prevent duplicates
    const newForm = form.cloneNode(true);
    form.parentNode.replaceChild(newForm, form);
    
    // Add submit handler
    newForm.addEventListener('submit', function(event) {
        console.log('📝 Form submission intercepted');
        event.preventDefault();
        processFormSubmission(newForm);
    });
    
    console.log('✅ Form handlers setup completed');
}

// Process form submission
function processFormSubmission(form) {
    console.log('🔄 Processing form submission...');
    
    // Get form data
    const formData = new FormData(form);
    const namaTes = formData.get('nama_tes') || '';
    const deskripsiTes = formData.get('deskripsi_tes') || '';
    
    console.log('Form data:', {
        namaTes: namaTes,
        deskripsiTes: deskripsiTes.substring(0, 50) + '...'
    });
    
    // Basic validation
    if (!namaTes.trim()) {
        showAlert('Nama tes harus diisi!', 'warning');
        return;
    }
    
    if (!deskripsiTes.trim()) {
        showAlert('Deskripsi tes harus diisi!', 'warning');
        return;
    }
    
    // File validation if exists
    const fileInput = form.querySelector('input[type="file"]');
    if (fileInput && fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (!file.name.toLowerCase().endsWith('.csv')) {
            showAlert('Hanya file CSV yang diizinkan!', 'warning');
            return;
        }
    }
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading me-2"></span> Menyimpan...';
    submitBtn.disabled = true;
    
    // Send to server
    sendToServer(formData, submitBtn, originalText);
}

// Send data to server
// tambahtes.js - Enhanced Error Handling

// tambahTes.js - Enhanced Debugging Version

async function sendToServer(formData, submitBtn, originalText) {
    try {
        console.log('📤 Sending data to server...');
        console.log('FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}:`, value);
        }

        // TEST 1: Coba dengan controller sederhana dulu
        const testResponse = await fetch('../../includes/admin_control/TambahTes_controller.php', {
            method: 'POST',
            body: formData
        });
        
        const testText = await testResponse.text();
        console.log('🧪 Test Controller Response:', testText);
        
        try {
            const testResult = JSON.parse(testText);
            console.log('✅ Test Controller JSON parsed successfully:', testResult);
        } catch (testError) {
            console.error('❌ Test Controller JSON parse failed:', testError);
            console.log('Raw test response:', testText);
        }

        // TEST 2: Coba controller utama dengan timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);
        
        console.log('🎯 Calling main controller...');
        const response = await fetch('../../includes/admin_control/TambahTes_controller.php', {
            method: 'POST',
            body: formData,
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        console.log('📥 Main Response - Status:', response.status, response.statusText);
        console.log('📥 Main Response - Headers:', Object.fromEntries(response.headers.entries()));
        
        // Get raw response text
        const responseText = await response.text();
        console.log('📄 Raw Response (first 1000 chars):', responseText.substring(0, 1000));
        
        // Check if it's HTML error
        if (responseText.includes('<b>') || responseText.includes('<br') || responseText.trim().startsWith('<!DOCTYPE') || responseText.includes('<?php')) {
            console.error('🚨 HTML ERROR DETECTED!');
            throw new Error(`Server returned PHP/HTML error:\n${responseText.substring(0, 500)}`);
        }
        
        let result;
        try {
            result = JSON.parse(responseText);
            console.log('✅ JSON parsed successfully:', result);
        } catch (parseError) {
            console.error('❌ JSON Parse Error Details:', {
                error: parseError.message,
                responseLength: responseText.length,
                firstChars: responseText.substring(0, 100),
                lastChars: responseText.substring(responseText.length - 100)
            });
            
            // Try to extract error message from HTML
            const errorMatch = responseText.match(/<b>(.*?)<\/b>|Fatal error:(.*?)<br|Parse error:(.*?)<br/);
            const extractedError = errorMatch ? errorMatch[1] || errorMatch[2] || errorMatch[3] : 'Unknown PHP error';
            
            throw new Error(`PHP Error: ${extractedError || 'Check server configuration'}`);
        }
        
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (result.success) {
            showAlert('Tes berhasil ditambahkan!', 'success');
            setTimeout(() => loadContent('kelolaTes.php'), 2000);
        } else {
            showAlert('Error: ' + result.message, 'danger');
        }
        
    } catch (error) {
        console.error('❌ NETWORK/SERVER ERROR:', error);
        
        // Restore button state
        if (submitBtn) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
        
        showAlert(`Server Error: ${error.message}`, 'danger');
    }
}
// Setup button handlers
function setupButtonHandlers() {
    console.log('🔧 Setting up button handlers...');
    
    // Download template button
    const downloadBtn = document.getElementById('downloadTemplateBtn') || 
                       document.querySelector('.btn-csv') ||
                       document.querySelector('button[onclick*="download"]');
    
    if (downloadBtn) {
        downloadBtn.onclick = function(e) {
            e.preventDefault();
            downloadTemplate();
        };
        console.log('✅ Download button handler attached');
    }
    
    // Cancel button
    const cancelBtn = document.getElementById('cancelBtn') || 
                     document.querySelector('.btn-merah') ||
                     document.querySelector('button[onclick*="batal"]') ||
                     document.querySelector('button[onclick*="cancel"]');
    
    if (cancelBtn) {
        cancelBtn.onclick = function(e) {
            e.preventDefault();
            loadContent('kelolaTes.php');
        };
        console.log('✅ Cancel button handler attached');
    }
}

// Setup validation
function setupValidation() {
    console.log('🔧 Setting up validation...');
    
    const namaInput = document.querySelector('[name="nama_tes"]');
    const deskripsiInput = document.querySelector('[name="deskripsi_tes"]');
    
    if (namaInput) {
        namaInput.addEventListener('blur', function() {
            if (!this.value.trim()) {
                showFieldError(this, 'Nama tes harus diisi');
            } else {
                clearFieldError(this);
            }
        });
    }
    
    if (deskripsiInput) {
        deskripsiInput.addEventListener('blur', function() {
            if (!this.value.trim()) {
                showFieldError(this, 'Deskripsi tes harus diisi');
            } else {
                clearFieldError(this);
            }
        });
    }
}

// Field error handling
function showFieldError(input, message) {
    clearFieldError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-danger small mt-1';
    errorDiv.textContent = message;
    
    input.parentNode.appendChild(errorDiv);
    input.classList.add('is-invalid');
}

function clearFieldError(input) {
    const existingError = input.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    input.classList.remove('is-invalid');
}

// File upload handling
async function handleFileUpload(event) {
    const file = event.target.files[0];
    const fileInput = event.target;
    
    if (!file) return;
    
    try {
        // Tampilkan loading
        showAlert('Memproses file...', 'info');
        
        // Auto-convert Excel ke CSV jika perlu
        const processedFile = await convertExcelToCSV(file);
        
        // Validasi CSV
        const validation = await validateCSVBeforeUpload(processedFile);
        
        // Update file input dengan file yang sudah diproses
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(processedFile);
        fileInput.files = dataTransfer.files;
        
        showFileInfo(processedFile);
        showAlert('File berhasil diproses dan siap diupload!', 'success');
        
    } catch (error) {
        console.error('File processing error:', error);
        fileInput.value = '';
        showAlert(`Error: ${error}`, 'warning');
    }
}

// Download template
function downloadTemplate() {
    console.log('📥 Downloading template...');
    window.location.href = '../../includes/admin_control/download_template_soal.php';
}

// Alert system
function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const icons = {
        success: '✓',
        danger: '✗',
        warning: '⚠',
        info: 'ℹ'
    };
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <div class="d-flex align-items-center">
            <span class="me-2 fs-5">${icons[type] || '•'}</span>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close btn-sm" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Load content function
function loadContent(file) {
    if (typeof window.parent.loadContent === 'function') {
        window.parent.loadContent(file);
    } else if (typeof window.loadContent === 'function') {
        window.loadContent(file);
    } else {
        window.location.href = file;
    }
}
function convertExcelToCSV(file) {
    return new Promise((resolve, reject) => {
        // Jika file sudah CSV, langsung resolve
        if (file.name.toLowerCase().endsWith('.csv')) {
            resolve(file);
            return;
        }

        // Jika file Excel (.xls, .xlsx), baca dan convert
        const reader = new FileReader();
        
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                
                // Ambil sheet pertama
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                
                // Convert ke CSV
                const csv = XLSX.utils.sheet_to_csv(firstSheet);
                
                // Buat file CSV baru
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const csvFile = new File([blob], file.name.replace(/\.xlsx?$/, '.csv'), { 
                    type: 'text/csv;charset=utf-8;' 
                });
                
                resolve(csvFile);
            } catch (error) {
                reject('Gagal mengkonversi file Excel: ' + error.message);
            }
        };
        
        reader.onerror = () => reject('Gagal membaca file');
        reader.readAsArrayBuffer(file);
    });
}

// Initialize when ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - Starting initialization...');
    setTimeout(initTambahTes, 100);
});

// Fallback initialization
if (document.readyState === 'interactive' || document.readyState === 'complete') {
    console.log('⚡ Document already ready - Initializing now...');
    setTimeout(initTambahTes, 100);
}

// Export functions to global scope
window.initTambahTes = initTambahTes;
window.downloadTemplate = downloadTemplate;
window.loadContent = loadContent;
window.handleFileUpload = handleFileUpload;

console.log('✅ tambahtes.js initialization complete!');