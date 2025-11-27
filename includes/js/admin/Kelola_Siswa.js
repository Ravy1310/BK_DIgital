// Kelola_Siswa.js
function initKelolaSiswa() {
    console.log('✅ Kelola Siswa initialized');
    
    // Cek elemen penting
    const formTambah = document.getElementById('formTambah');
    const tabelElement = document.getElementById('tabelSiswa');
    
    if (!formTambah || !tabelElement) {
        console.error('❌ Element penting tidak ditemukan');
        return;
    }
    
    const tabel = tabelElement.querySelector('tbody');
    const modalTambahElement = document.getElementById('modalTambah');
    
    if (!modalTambahElement) {
        console.error('❌ Modal tidak ditemukan');
        return;
    }
    
    const modalTambah = new bootstrap.Modal(modalTambahElement);
    const modalTitle = document.getElementById('modalTitle');
    
    if (!modalTitle) {
        console.error('❌ Modal title tidak ditemukan');
        return;
    }

    let isEditMode = false;
    let currentEditId = null;

    // === SETUP IMPORT MODAL ===
    function setupImportModal() {
        const formImport = document.getElementById('formImport');
        const btnDownloadTemplate = document.getElementById('btnDownloadTemplate');
        const fileImport = document.getElementById('fileImport');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInfo = document.getElementById('fileInfo');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = uploadProgress.querySelector('.progress-bar');
        const modalImport = new bootstrap.Modal(document.getElementById('modalImport'));

        if (!formImport || !btnDownloadTemplate) {
            console.error('❌ Element import modal tidak ditemukan');
            return;
        }

        // Download template
        btnDownloadTemplate.addEventListener('click', () => {
            window.open('../../includes/admin_control/download_templateSiswa.php', '_blank');
        });

        // File upload area click
        fileUploadArea.addEventListener('click', () => {
            fileImport.click();
        });

        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileImport.files = files;
                handleFileSelection(files[0]);
            }
        });

        // File change validation
        fileImport.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                handleFileSelection(file);
            }
        });

        function handleFileSelection(file) {
            // Validasi ekstensi
            if (!file.name.toLowerCase().endsWith('.csv')) {
                showAlert('Hanya file CSV yang didukung!', 'danger');
                fileImport.value = '';
                fileInfo.innerHTML = '';
                return;
            }

            // Validasi ukuran (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                showAlert('Ukuran file terlalu besar. Maksimal 2MB!', 'danger');
                fileImport.value = '';
                fileInfo.innerHTML = '';
                return;
            }

            // Tampilkan info file
            fileInfo.innerHTML = `
                <div class="alert alert-success py-2">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>File siap diupload:</strong> ${file.name} (${(file.size / 1024).toFixed(2)} KB)
                </div>
            `;
        }

        // Form submit
        formImport.addEventListener('submit', async (e) => {
            e.preventDefault();

            const file = fileImport.files[0];
            if (!file) {
                showAlert('Pilih file CSV terlebih dahulu!', 'danger');
                return;
            }

            const overwriteData = document.getElementById('overwriteData').checked;

            try {
                // Tampilkan progress bar
                uploadProgress.style.display = 'block';
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';

                // Disable button
                const submitBtn = document.getElementById('btnSubmitImport');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Mengupload...';

                const formData = new FormData();
                formData.append('excel_file', file);
                formData.append('action', 'import');
                formData.append('overwrite', overwriteData ? '1' : '0');

                const xhr = new XMLHttpRequest();

                // Track upload progress
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressBar.textContent = Math.round(percentComplete) + '%';
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status === 200) {
                        try {
                            const result = JSON.parse(xhr.responseText);
                            
                            if (result.success) {
                                let successMessage = `Import berhasil! ${result.imported} data ditambahkan.`;
                                if (result.skipped > 0) {
                                    successMessage += ` ${result.skipped} data dilewati.`;
                                }
                                showAlert(successMessage, 'success');
                                
                                // Tutup modal dan refresh data
                                modalImport.hide();
                                loadDataSiswa();
                                
                                // Reset form
                                formImport.reset();
                                uploadProgress.style.display = 'none';
                                fileInfo.innerHTML = '';
                            } else {
                                throw new Error(result.message || 'Gagal import data');
                            }
                        } catch (parseError) {
                            throw new Error('Response tidak valid dari server');
                        }
                    } else {
                        throw new Error(`HTTP error! status: ${xhr.status}`);
                    }
                });

                xhr.addEventListener('error', () => {
                    throw new Error('Gagal terhubung ke server');
                });

                xhr.open('POST', '../../includes/admin_control/KelolaSiswa_Controller.php');
                xhr.send(formData);

            } catch (error) {
                console.error('Import error:', error);
                showAlert('Error: ' + error.message, 'danger');
                
                // Reset UI
                uploadProgress.style.display = 'none';
                const submitBtn = document.getElementById('btnSubmitImport');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Import Data';
            }
        });

        // Reset form ketika modal ditutup
        document.getElementById('modalImport').addEventListener('hidden.bs.modal', () => {
            formImport.reset();
            uploadProgress.style.display = 'none';
            fileInfo.innerHTML = '';
            const submitBtn = document.getElementById('btnSubmitImport');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-upload me-2"></i>Import Data';
        });
    }

    // === SIMPAN DATA KE DATABASE ===
   // === SIMPAN DATA KE DATABASE ===
formTambah.addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('idSiswa').value;
    const nama = document.getElementById('namaSiswa').value;
    const kelas = document.getElementById('kelasSiswa').value;
    const tahun = document.getElementById('tahunMasuk').value;
    const genderElement = document.querySelector('input[name="gender"]:checked');
    
    if (!genderElement) {
        alert('Jenis kelamin harus dipilih!');
        return;
    }
    
    const gender = genderElement.value;

    // Validasi form
    if (!id || !nama || !kelas || !tahun || !gender) {
        alert('Semua field harus diisi!');
        return;
    }

    // Validasi tahun masuk
    if (!/^\d{4}$/.test(tahun)) {
        alert('Tahun masuk harus 4 digit angka!');
        return;
    }

    try {
        let url = '../../includes/admin_control/KelolaSiswa_Controller.php';
        let body;

        if (isEditMode && currentEditId) {
            // EDIT MODE - Kirim id_siswa_lama
            body = `action=edit&id_siswa_lama=${encodeURIComponent(currentEditId)}&id_siswa=${encodeURIComponent(id)}&nama=${encodeURIComponent(nama)}&kelas=${encodeURIComponent(kelas)}&tahun_masuk=${encodeURIComponent(tahun)}&jenis_kelamin=${encodeURIComponent(gender)}`;
            console.log('📤 Edit request body:', body);
        } else {
            // TAMBAH MODE
            body = `action=tambah&id_siswa=${encodeURIComponent(id)}&nama=${encodeURIComponent(nama)}&kelas=${encodeURIComponent(kelas)}&tahun_masuk=${encodeURIComponent(tahun)}&jenis_kelamin=${encodeURIComponent(gender)}`;
            console.log('📤 Tambah request body:', body);
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: body
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const text = await response.text();
        console.log('📥 Response for save:', text);
        
        let result;
        
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            console.error('JSON Parse Error:', parseError, 'Response:', text);
            throw new Error('Response tidak valid dari server');
        }

        if (result.success) {
            const message = isEditMode ? 'Data siswa berhasil diupdate!' : 'Data siswa berhasil ditambahkan!';
            showAlert(message, 'success');
            
            await loadDataSiswa();
            modalTambah.hide();
            resetForm();
        } else {
            throw new Error(result.message || (isEditMode ? 'Gagal update data' : 'Gagal menambah data'));
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error: ' + error.message, 'danger');
    }
});

    // === RESET FORM ===
    function resetForm() {
        formTambah.reset();
        modalTitle.textContent = "Tambah Data Siswa";
        isEditMode = false;
        currentEditId = null;
        
        // Enable ID field untuk tambah data
        document.getElementById('idSiswa').disabled = false;
    }

    // === SETUP EDIT HANDLER ===
    function setupEditHandler() {
        tabel.addEventListener('click', (e) => {
            if (e.target.closest('.edit-btn')) {
                const button = e.target.closest('.edit-btn');
                const id_siswa = button.getAttribute('data-id');
                
                // Load data siswa untuk diedit
                loadSiswaDataForEdit(id_siswa);
            }
        });
    }

    // === LOAD DATA SISWA UNTUK EDIT ===
    // === LOAD DATA SISWA UNTUK EDIT ===
async function loadSiswaDataForEdit(id_siswa) {
    try {
        console.log('🔄 Loading data siswa untuk edit:', id_siswa);
        
        const url = `../../includes/admin_control/KelolaSiswa_Controller.php?action=get_by_id&id_siswa=${encodeURIComponent(id_siswa)}&t=${Date.now()}`;
        console.log('📡 Request URL:', url);
        
        const response = await fetch(url);
        
        console.log('🔍 Response status:', response.status);
        console.log('🔍 Response ok:', response.ok);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const text = await response.text();
        console.log('🔍 Raw response for edit:', text);
        
        let result;
        try {
            result = JSON.parse(text);
            console.log('✅ Parsed JSON result:', result);
        } catch (parseError) {
            console.error('❌ JSON Parse Error:', parseError);
            console.error('❌ Response content:', text);
            throw new Error('Response tidak valid JSON dari server. Server mengembalikan: ' + text.substring(0, 200));
        }

        if (result.success && result.data) {
            console.log('✅ Data found for edit:', result.data);
            fillEditForm(result.data);
        } else {
            throw new Error(result.message || 'Data siswa tidak ditemukan');
        }
    } catch (error) {
        console.error('Error loading data for edit:', error);
        showAlert('Gagal memuat data siswa: ' + error.message, 'danger');
    }
}

    // === FILL EDIT FORM ===
    function fillEditForm(siswa) {
        // Isi form dengan data yang ada
        document.getElementById('idSiswa').value = siswa.id_siswa || '';
        document.getElementById('namaSiswa').value = siswa.nama || '';
        document.getElementById('kelasSiswa').value = siswa.kelas || '';
        document.getElementById('tahunMasuk').value = siswa.tahun_masuk || '';
        
        // Set jenis kelamin
        const gender = siswa.jenis_kelamin || '';
        document.querySelectorAll('input[name="gender"]').forEach(radio => {
            radio.checked = radio.value === gender;
        });
        
        // Set mode edit
        isEditMode = true;
        currentEditId = siswa.id_siswa;
        
        // Update UI untuk mode edit
        modalTitle.textContent = "Edit Data Siswa";
        
        // Disable ID field saat edit (ID tidak bisa diubah)
        document.getElementById('idSiswa').disabled = true;
        
        // Tampilkan modal
        modalTambah.show();
    }

    // === LOAD DATA SISWA DARI DATABASE ===
  // === LOAD DATA SISWA DARI DATABASE ===
async function loadDataSiswa() {
    try {
        console.log('🔄 Loading data siswa...');
        
        const response = await fetch('../../includes/admin_control/KelolaSiswa_Controller.php?action=get&t=' + Date.now());
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const text = await response.text();
        console.log('🔍 Raw response from server:', text);
        console.log('🔍 Response headers:', response.headers);
        
        // Coba parse JSON
        let result;
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            console.error('❌ JSON Parse Error:', parseError);
            console.error('❌ Response content (first 500 chars):', text.substring(0, 500));
            throw new Error('Response tidak valid JSON dari server. Server mungkin mengembalikan error HTML.');
        }

        if (result.success) {
            console.log('✅ Data loaded:', result.data);
            renderTable(result.data);
            updateStatistics(result.data);
        } else {
            throw new Error(result.message || 'Gagal memuat data');
        }
    } catch (error) {
        console.error('Error loading data:', error);
        showAlert('Gagal memuat data siswa: ' + error.message, 'danger');
        
        // Tampilkan response mentah untuk debugging
        const response = await fetch('../../includes/admin_control/KelolaSiswa_Controller.php?action=get&t=' + Date.now());
        const text = await response.text();
        console.error('📄 Full server response:', text);
    }
}

    // === RENDER TABLE ===
    // === RENDER TABLE ===
function renderTable(data) {
    if (!tabel) {
        console.error('Tabel element tidak ditemukan');
        return;
    }
    
    // Clear existing content
    tabel.innerHTML = '';
    
    if (!data || data.length === 0) {
        const emptyRow = tabel.insertRow();
        emptyRow.innerHTML = `
            <td colspan="6" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                Tidak ada data siswa
            </td>
        `;
        return;
    }
    
    // Render rows
    data.forEach(siswa => {
        const row = tabel.insertRow();
        row.innerHTML = `
            <td>${siswa.id_siswa || ''}</td>
            <td>${siswa.nama || ''}</td>
            <td>${siswa.kelas || ''}</td>
            <td>${siswa.tahun_masuk || ''}</td>
            <td>${siswa.jenis_kelamin || ''}</td>
            <td>
                <button class="btn btn-link p-0 text-primary edit-btn" data-id="${siswa.id_siswa}" title="Edit">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-link p-0 text-danger delete-btn" data-id="${siswa.id_siswa}" title="Hapus">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
    });

    // Setup event handlers - hanya sekali
    setupEventHandlers();
}

// === SETUP EVENT HANDLERS ===
function setupEventHandlers() {
    // Reset flag
    window.deleteInProgress = false;
    
    // Setup handlers
    setupEditHandler();
    setupDeleteHandler();
    
    console.log('✅ Event handlers setup completed');
}
    // === SETUP DELETE HANDLER ===
    // === SETUP DELETE HANDLER ===
function setupDeleteHandler() {
    // Gunakan event delegation dengan sekali binding
    if (!tabel._deleteHandlerAttached) {
        tabel.addEventListener('click', handleDeleteClick);
        tabel._deleteHandlerAttached = true; // Flag untuk prevent multiple binding
        console.log('✅ Delete handler attached');
    }
}

function handleDeleteClick(e) {
    if (e.target.closest('.delete-btn')) {
        e.preventDefault();
        e.stopPropagation(); // Stop event bubbling
        
        const button = e.target.closest('.delete-btn');
        const id_siswa = button.getAttribute('data-id');
        const nama = button.closest('tr').querySelector('td:nth-child(2)').textContent;
        
        console.log('🗑️ Delete button clicked for:', id_siswa, nama);
        
        // Cek jika sudah ada confirm dialog yang aktif
        if (window.deleteInProgress) {
            console.log('⚠️ Delete already in progress, ignoring click');
            return;
        }
        
        deleteSiswa(id_siswa, nama, button);
    }
}

// === DELETE SISWA ===
async function deleteSiswa(id_siswa, nama, button) {
    // Set flag untuk prevent multiple clicks
    window.deleteInProgress = true;
    
    // Show confirmation - hanya sekali
    if (!confirm(`Yakin ingin menghapus data siswa: ${nama} (${id_siswa})?`)) {
        window.deleteInProgress = false;
        return;
    }

    try {
        // Show loading state pada button
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        button.disabled = true;
        
        console.log('🔄 Deleting siswa:', id_siswa);
        
        const response = await fetch('../../includes/admin_control/KelolaSiswa_Controller.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=hapus&id_siswa=${encodeURIComponent(id_siswa)}`
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const text = await response.text();
        let result;
        
        try {
            result = JSON.parse(text);
        } catch (parseError) {
            throw new Error('Response tidak valid dari server');
        }

        if (result.success) {
            showAlert('Data siswa berhasil dihapus!', 'success');
            await loadDataSiswa(); // Refresh data
        } else {
            throw new Error(result.message || 'Gagal menghapus data');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error: ' + error.message, 'danger');
    } finally {
        // Reset states
        window.deleteInProgress = false;
        
        // Restore button state (jika masih ada di DOM)
        if (button && button.parentElement) {
            button.innerHTML = '<i class="bi bi-trash"></i>';
            button.disabled = false;
        }
    }
}
    // === UPDATE STATISTICS ===
    function updateStatistics(data) {
        if (!data) return;
        
        const totalSiswa = data.length;
        const totalLaki = data.filter(s => s.jenis_kelamin === 'Laki-Laki').length;
        const totalPerempuan = data.filter(s => s.jenis_kelamin === 'Perempuan').length;

        const cards = document.querySelectorAll('.card-info');
        
        if (cards.length >= 3) {
            const updateCard = (index, value) => {
                const element = cards[index].querySelector('h4');
                if (element) element.textContent = value;
            };
            
            updateCard(0, totalSiswa);
            updateCard(1, totalLaki);
            updateCard(2, totalPerempuan);
        }
    }

    // === FITUR CARI ===
    const btnCari = document.getElementById('btnCari');
    const searchBox = document.getElementById('searchBox');

    if (btnCari && searchBox) {
        btnCari.addEventListener('click', performSearch);
        
        searchBox.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') performSearch();
        });

        searchBox.addEventListener('input', (e) => {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(performSearch, 500);
        });
    }

    function performSearch() {
        const keyword = searchBox.value.trim().toLowerCase();
        const rows = tabel.querySelectorAll('tr');
        let foundCount = 0;
        
        rows.forEach(row => {
            if (row.cells.length < 6) return;
            
            const id = row.cells[0].textContent.toLowerCase();
            const nama = row.cells[1].textContent.toLowerCase();
            const kelas = row.cells[2].textContent.toLowerCase();
            
            const match = id.includes(keyword) || nama.includes(keyword) || kelas.includes(keyword) || keyword === '';
            row.style.display = match ? '' : 'none';
            if (match) foundCount++;
        });

        if (keyword !== '') {
            showSearchInfo(`Ditemukan ${foundCount} data`);
        } else {
            hideSearchInfo();
        }
    }

    function showSearchInfo(message) {
        let info = document.getElementById('searchInfo');
        if (!info) {
            info = document.createElement('div');
            info.id = 'searchInfo';
            info.className = 'text-muted small mt-2';
            const tableContainer = document.querySelector('.table-container');
            const table = tableContainer.querySelector('table');
            table.parentNode.insertBefore(info, table.nextSibling);
        }
        info.textContent = message;
    }

    function hideSearchInfo() {
        const info = document.getElementById('searchInfo');
        if (info) info.remove();
    }

    // === ALERT FUNCTION ===
    function showAlert(message, type) {
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) existingAlert.remove();

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi ${getAlertIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const tableContainer = document.querySelector('.table-container');
        if (tableContainer) {
            tableContainer.insertBefore(alertDiv, tableContainer.firstChild);
            
            if (type === 'success' || type === 'info') {
                setTimeout(() => alertDiv.remove(), 5000);
            }
        }
    }

    function getAlertIcon(type) {
        const icons = {
            'success': 'bi-check-circle',
            'danger': 'bi-exclamation-circle',
            'warning': 'bi-exclamation-triangle',
            'info': 'bi-info-circle'
        };
        return icons[type] || 'bi-info-circle';
    }

    // === RESET FORM WHEN MODAL CLOSED ===
    modalTambahElement.addEventListener('hidden.bs.modal', () => {
        resetForm();
    });

    // === INITIALIZE ===
    console.log('🚀 Starting Kelola Siswa...');
    setupImportModal();
    loadDataSiswa();
}

// Export ke global scope
window.initKelolaSiswa = initKelolaSiswa;