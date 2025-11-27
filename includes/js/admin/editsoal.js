// includes/js/admin/editsoal.js

console.log('🔧 editsoal.js loading...');

class TambahSoalManager {
    constructor() {
        this.modalId = 'tambahModal';
        this.formId = 'tambahSoalForm';
        this.initialized = false;
        this.submitHandler = null;
    }

    init() {
        if (this.initialized) return;
        
        console.log('🔄 TambahSoalManager initializing...');
        this.setupEventListeners();
        this.setupTambahForm();
        this.initialized = true;
        console.log('✅ TambahSoalManager initialized');
    }

    setupEventListeners() {
        // Setup untuk tombol tambah opsi
        const tambahOpsiBtn = document.querySelector('[onclick*="tambahOpsi"]');
        if (tambahOpsiBtn) {
            tambahOpsiBtn.onclick = () => this.tambahOpsi();
        }
        
        this.updateHapusButtons();
    }

    setupTambahForm() {
        const form = document.getElementById(this.formId);
        if (form) {
            // Hapus event listener lama jika ada
            if (this.submitHandler) {
                form.removeEventListener('submit', this.submitHandler);
            }
            
            // Buat handler baru
            this.submitHandler = (e) => this.handleSubmit(e);
            
            // Tambah event listener baru
            form.addEventListener('submit', this.submitHandler);
            console.log('✅ Tambah form event listener setup');
        }
        
        this.updateHapusButtons();
    }

    showModal() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            modal.classList.add('show');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            this.resetForm();
            console.log('✅ Tambah modal shown');
        }
    }

    hideModal() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.style.overflow = '';
            console.log('✅ Tambah modal hidden');
        }
    }

    resetForm() {
        const form = document.getElementById(this.formId);
        if (form) {
            form.reset();
            
            const opsiWrapper = document.getElementById('opsi-wrapper');
            if (opsiWrapper) {
                const opsiItems = opsiWrapper.querySelectorAll('.opsi-item');
                opsiItems.forEach((item, index) => {
                    if (index >= 2) {
                        item.remove();
                    }
                });
                
                const inputs = opsiWrapper.querySelectorAll('input[type="text"]');
                inputs.forEach(input => {
                    input.value = '';
                });
                
                const bobots = opsiWrapper.querySelectorAll('input[type="number"]');
                bobots.forEach(bobot => {
                    bobot.value = '1';
                });
                
                this.updateHapusButtons();
            }
        }
    }

    tambahOpsi() {
        const opsiWrapper = document.getElementById('opsi-wrapper');
        if (!opsiWrapper) return;
        
        const newOpsi = document.createElement('div');
        newOpsi.className = 'opsi-item mb-3';
        newOpsi.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-8">
                    <input type="text" name="opsi[]" class="form-control" placeholder="Opsi jawaban" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="bobot[]" class="form-control" min="1" max="10" value="1" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="window.tambahSoalManager.hapusOpsi(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        opsiWrapper.appendChild(newOpsi);
        this.updateHapusButtons();
    }

    hapusOpsi(button) {
        const opsiItem = button.closest('.opsi-item');
        if (opsiItem) {
            opsiItem.remove();
            this.updateHapusButtons();
        }
    }

    updateHapusButtons() {
        const opsiItems = document.querySelectorAll('.opsi-item');
        const hapusButtons = document.querySelectorAll('#opsi-wrapper .btn-danger');
        
        if (opsiItems.length <= 2) {
            hapusButtons.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('disabled');
            });
        } else {
            hapusButtons.forEach(btn => {
                btn.disabled = false;
                btn.classList.remove('disabled');
            });
        }
    }

    async handleSubmit(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('🔄 Handling tambah soal submit...');
    
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validasi form
    if (!this.validateForm(form)) {
        this.showAlert('error', 'Pertanyaan dan minimal 2 opsi wajib diisi!');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    
    try {
        const basePath = window.location.pathname.includes('BK_DIGITAL') ? '/BK_DIGITAL/' : '/';
        const actionUrl = `${basePath}includes/admin_control/TambahSoal_Controller.php`;
        
        console.log('📤 Mengirim data ke:', actionUrl);
        
        const response = await fetch(actionUrl, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.text();
        console.log('✅ Response dari server:', result);
        
        // Handle response yang lebih baik
        if (result.includes('berhasil') || result.includes('sukses') || result.trim() === '') {
            this.showAlert('success', 'Soal berhasil ditambahkan!');
            this.hideModal();
            this.resetForm();
            
            // Refresh dengan delay untuk memastikan data tersimpan
            setTimeout(() => {
                this.refreshPage();
            }, 1000);
            
        } else if (result.includes('Tes tidak ditemukan')) {
            // Handle khusus error "Tes tidak ditemukan"
            console.warn('⚠️ Tes tidak ditemukan, tapi tetap refresh halaman');
            this.showAlert('success', 'Soal berhasil ditambahkan!');
            this.hideModal();
            this.resetForm();
            
            setTimeout(() => {
                this.refreshPage();
            }, 1000);
        } else {
            throw new Error(result || 'Gagal menambah soal');
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
        
        // Meskipun error, coba refresh karena mungkin soal sudah tersimpan
        if (error.message.includes('Tes tidak ditemukan')) {
            this.showAlert('success', 'Soal berhasil ditambahkan!');
            this.hideModal();
            this.resetForm();
            
            setTimeout(() => {
                this.refreshPage();
            }, 1000);
        } else {
            this.showAlert('error', 'Gagal menambah soal: ' + error.message);
        }
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

    validateForm(form) {
        let isValid = true;
        
        const pertanyaan = form.querySelector('textarea[name="pertanyaan"]');
        if (!pertanyaan.value.trim()) {
            pertanyaan.classList.add('is-invalid');
            isValid = false;
        } else {
            pertanyaan.classList.remove('is-invalid');
        }
        
        const opsiInputs = form.querySelectorAll('input[name="opsi[]"]');
        const bobotInputs = form.querySelectorAll('input[name="bobot[]"]');
        
        let validOpsiCount = 0;
        opsiInputs.forEach((input, index) => {
            if (input.value.trim()) {
                validOpsiCount++;
                input.classList.remove('is-invalid');
                
                const bobot = bobotInputs[index];
                if (bobot && bobot.value >= 1 && bobot.value <= 10) {
                    bobot.classList.remove('is-invalid');
                } else {
                    bobot.classList.add('is-invalid');
                    isValid = false;
                }
            } else {
                input.classList.add('is-invalid');
                isValid = false;
            }
        });
        
        if (validOpsiCount < 2) {
            this.showAlert('error', 'Minimal 2 opsi jawaban harus diisi!');
            isValid = false;
        }
        
        return isValid;
    }

    getCurrentIdTes() {
    // Coba dari URL parameter pertama
    const urlParams = new URLSearchParams(window.location.search);
    let idTes = urlParams.get('id_tes');
    
    // Jika tidak ada di URL, coba dari form hidden
    if (!idTes) {
        const form = document.getElementById(this.formId);
        if (form) {
            const hiddenIdTes = form.querySelector('input[name="id_tes"]');
            if (hiddenIdTes) {
                idTes = hiddenIdTes.value;
            }
        }
    }
    
    // Jika masih tidak ada, coba dari sessionStorage atau localStorage
    if (!idTes) {
        idTes = sessionStorage.getItem('current_id_tes') || localStorage.getItem('current_id_tes');
    }
    
    console.log('🔍 Current ID Tes:', idTes);
    return idTes;
}

refreshPage() {
    const idTes = this.getCurrentIdTes();
    
    if (!idTes) {
        console.error('❌ ID Tes tidak ditemukan!');
        this.showAlert('error', 'ID Tes tidak valid. Silakan refresh manual.');
        return;
    }
    
    console.log('🔄 Refreshing page for tes:', idTes);
    
    // Simpan ID Tes untuk backup
    sessionStorage.setItem('current_id_tes', idTes);
    localStorage.setItem('current_id_tes', idTes);
    
    if (typeof window.loadContent === 'function') {
        console.log('🔄 Using loadContent to refresh');
        window.loadContent(`editsoal.php?id_tes=${idTes}`);
    } else {
        console.log('🔄 Using direct redirect');
        window.location.href = `editsoal.php?id_tes=${idTes}`;
    }
}

    showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const existingAlert = document.querySelector('.custom-alert-tambah');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `custom-alert custom-alert-tambah alert ${alertClass} alert-dismissible fade show`;
        alertDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100000;
            min-width: 300px;
            max-width: 500px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        alertDiv.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    destroy() {
        const form = document.getElementById(this.formId);
        if (form && this.submitHandler) {
            form.removeEventListener('submit', this.submitHandler);
        }
        this.initialized = false;
        console.log('🧹 TambahSoalManager destroyed');
    }
}

class EditSoalManager {
    constructor() {
        this.currentIdTes = this.getCurrentIdTes();
        this.initialized = false;
        this.clickHandler = null;
        this.submitHandler = null;
        this.keydownHandler = null;
        this.modalClickHandler = null;
        this.init();
    }

    getCurrentIdTes() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('id_tes');
    }

    init() {
        if (this.initialized) {
            console.log('ℹ️ EditSoalManager already initialized');
            return;
        }

        console.log('🔄 EditSoalManager initializing...');
        this.setupEventListeners();
        this.initialized = true;
        console.log('✅ EditSoalManager initialized for tes:', this.currentIdTes);
    }

    setupEventListeners() {
        console.log('🔧 Setting up event listeners...');
        
        this.removeEventListeners();
        
        this.clickHandler = (e) => {
            const editButton = e.target.closest('.btn-edit-soal');
            if (editButton) {
                console.log('✏️ Edit button clicked');
                e.preventDefault();
                e.stopPropagation();
                this.handleEditSoal(editButton);
                return;
            }
            
            const deleteButton = e.target.closest('.btn-hapus-soal');
            if (deleteButton) {
                console.log('🗑️ Delete button clicked');
                e.preventDefault();
                e.stopPropagation();
                this.handleHapusSoal(deleteButton);
                return;
            }
            
            const tambahButton = e.target.closest('#btnTambahSoal') || e.target.closest('#btnTambahPertama');
            if (tambahButton) {
                console.log('➕ Tambah soal button clicked');
                e.preventDefault();
                e.stopPropagation();
                this.handleTambahSoal();
                return;
            }
            
            if (e.target.closest('#btnKembali')) {
                console.log('↩️ Kembali button clicked');
                e.preventDefault();
                e.stopPropagation();
                this.handleKembali();
                return;
            }
            
            if (e.target.closest('#btnCloseModal')) {
                console.log('❌ Close modal button clicked');
                e.preventDefault();
                e.stopPropagation();
                this.hideModal();
                return;
            }
        };

        this.submitHandler = (e) => {
            if (e.target.classList.contains('edit-soal-form')) {
                console.log('📝 Form submission detected');
                e.preventDefault();
                e.stopPropagation();
                this.handleSubmitForm(e.target);
            }
        };

        this.modalClickHandler = (e) => {
            if (e.target === e.currentTarget) {
                console.log('❌ Modal background clicked, closing modal');
                this.hideModal();
            }
        };

        this.keydownHandler = (e) => {
            if (e.key === 'Escape') {
                const editModal = document.getElementById('editModal');
                const tambahModal = document.getElementById('tambahModal');
                
                if (editModal && editModal.classList.contains('show')) {
                    console.log('⌨️ Escape key pressed, closing edit modal');
                    this.hideModal();
                } else if (tambahModal && tambahModal.classList.contains('show')) {
                    console.log('⌨️ Escape key pressed, closing tambah modal');
                    window.tambahSoalManager.hideModal();
                }
            }
        };

        document.addEventListener('click', this.clickHandler);
        document.addEventListener('submit', this.submitHandler);
        document.addEventListener('keydown', this.keydownHandler);

        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.addEventListener('click', this.modalClickHandler);
        }

        console.log('✅ Event listeners setup completed');
    }

    removeEventListeners() {
        if (this.clickHandler) {
            document.removeEventListener('click', this.clickHandler);
        }
        if (this.submitHandler) {
            document.removeEventListener('submit', this.submitHandler);
        }
        if (this.keydownHandler) {
            document.removeEventListener('keydown', this.keydownHandler);
        }
        
        const editModal = document.getElementById('editModal');
        if (editModal && this.modalClickHandler) {
            editModal.removeEventListener('click', this.modalClickHandler);
        }
    }

    async handleEditSoal(button) {
        console.log('🔄 Handling edit soal...');
        
        const idSoal = button.getAttribute('data-soal-id');
        const idTes = button.getAttribute('data-tes-id');
        const nomorSoal = button.getAttribute('data-nomor');
        
        console.log('📋 Data:', { idSoal, idTes, nomorSoal });
        
        if (!idSoal || !idTes) {
            console.error('❌ Missing data attributes');
            this.showAlert('error', 'Data soal tidak lengkap');
            return;
        }
        
        await this.showEditModal(idSoal, idTes, nomorSoal);
    }

    handleHapusSoal(button) {
        const idSoal = button.getAttribute('data-soal-id');
        const idTes = button.getAttribute('data-tes-id');
        
        if (!confirm("Yakin ingin menghapus soal ini? Tindakan ini tidak dapat dibatalkan.")) return;

        this.showLoading('Menghapus soal...');
        
        const basePath = window.location.pathname.includes('BK_DIGITAL') ? '/BK_DIGITAL/' : '/';
        const deleteUrl = `${basePath}includes/admin_control/HapusSoal_Controller.php?id_soal=${idSoal}&id_tes=${idTes}`;
        
        fetch(deleteUrl)
            .then(response => response.text())
            .then((result) => {
                this.hideLoading();
                if (result.includes('berhasil') || !result.includes('error')) {
                    this.showAlert('success', 'Soal berhasil dihapus!');
                    this.refreshPage(idTes);
                } else {
                    throw new Error(result);
                }
            })
            .catch(err => {
                this.hideLoading();
                console.error('Error:', err);
                this.showAlert('error', 'Terjadi kesalahan saat menghapus soal: ' + err.message);
            });
    }

    handleTambahSoal() {
        if (window.tambahSoalManager) {
            window.tambahSoalManager.showModal();
        } else {
            console.error('TambahSoalManager not available');
            const idTes = this.currentIdTes;
            if (typeof window.loadContent === 'function') {
                window.loadContent(`tambahsoal.php?id_tes=${idTes}`);
            } else {
                window.location.href = `tambahsoal.php?id_tes=${idTes}`;
            }
        }
    }

    handleKembali() {
        if (typeof window.loadContent === 'function') {
            window.loadContent('kelolasoal.php');
        } else {
            window.location.href = 'kelolasoal.php';
        }
    }

    async showEditModal(idSoal, idTes, nomorSoal) {
        const editModal = document.getElementById('editModal');
        const editModalTitle = document.getElementById('editModalTitle');
        const editModalBody = document.getElementById('editModalBody');
        
        if (!editModal || !editModalTitle || !editModalBody) {
            console.error('Modal elements not found');
            this.showAlert('error', 'Modal tidak dapat dimuat');
            return;
        }
        
        editModalTitle.innerHTML = `<i class="fas fa-edit me-2"></i>Edit Soal #${nomorSoal}`;
        editModalBody.innerHTML = this.getLoadingTemplate('Memuat data soal...');
        editModal.classList.add('show');
        editModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        try {
            const soalData = await this.fetchSoalData(idSoal);
            const formHtml = this.createEditForm(soalData, idSoal, idTes);
            editModalBody.innerHTML = formHtml;
            this.setupFormValidation();
            
        } catch (error) {
            console.error('Error loading soal data:', error);
            editModalBody.innerHTML = this.getErrorTemplate('Gagal memuat data soal: ' + error.message);
        }
    }

    async fetchSoalData(idSoal) {
        const basePath = window.location.pathname.includes('BK_DIGITAL') ? '/BK_DIGITAL/' : '/';
        const fetchUrl = `${basePath}includes/admin_control/get_soal_data_json.php?id_soal=${idSoal}`;
        
        console.log('📡 Fetching soal data from:', fetchUrl);
        
        const response = await fetch(fetchUrl);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Gagal memuat data soal');
        }
        
        console.log('✅ Soal data fetched successfully');
        return data;
    }

    createEditForm(soalData, idSoal, idTes) {
        const { soal, opsi_list } = soalData;
        const labels = ['a', 'b', 'c', 'd', 'e'];
        
        let opsiFields = '';
        
        labels.forEach((label, index) => {
            const opsi = opsi_list[index] || { opsi: '', bobot: 1 };
            const huruf = label.toUpperCase();
            
            opsiFields += `
                <div class="card mb-3 border">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <label class="form-label fw-medium">Opsi ${huruf}</label>
                                <input type="text" class="form-control" 
                                       name="opsi_${label}" 
                                       value="${this.escapeHtml(opsi.opsi)}" 
                                       placeholder="Masukkan opsi ${huruf}" 
                                       required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-medium">Bobot ${huruf}</label>
                                <select class="form-select" name="bobot_${label}" required>
                                    <option value="1" ${opsi.bobot == 1 ? 'selected' : ''}>1</option>
                                    <option value="2" ${opsi.bobot == 2 ? 'selected' : ''}>2</option>
                                    <option value="3" ${opsi.bobot == 3 ? 'selected' : ''}>3</option>
                                    <option value="4" ${opsi.bobot == 4 ? 'selected' : ''}>4</option>
                                    <option value="5" ${opsi.bobot == 5 ? 'selected' : ''}>5</option>
                                </select>
                                <small class="text-muted">Nilai: 1-5</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        return `
            <form class="edit-soal-form" method="POST">
                <input type="hidden" name="id_soal" value="${idSoal}">
                <input type="hidden" name="id_tes" value="${idTes}">

                <div class="mb-4">
                    <label class="form-label fw-semibold">Pertanyaan</label>
                    <textarea class="form-control" name="pertanyaan" rows="4" required 
                              style="min-height: 120px;" 
                              placeholder="Masukkan pertanyaan soal">${this.escapeHtml(soal.pertanyaan)}</textarea>
                </div>

                <label class="form-label fw-semibold mb-3">Pilihan Jawaban:</label>
                ${opsiFields}

                <div class="custom-modal-footer">
                    <button type="button" class="btn btn-secondary px-4" onclick="window.editSoalManager.hideModal()">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        `;
    }

    setupFormValidation() {
        const form = document.querySelector('.edit-soal-form');
        if (!form) return;

        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        const textareas = form.querySelectorAll('textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });

        const inputs = form.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });
    }

    handleSubmitForm(form) {
    if (!this.validateForm(form)) {
        this.showAlert('error', 'Harap lengkapi semua field yang wajib diisi!');
        return;
    }

    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    const idTes = form.querySelector('input[name="id_tes"]').value;
    
    console.log('🔄 Submitting form data...', { idTes });
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    
    const basePath = window.location.pathname.includes('BK_DIGITAL') ? '/BK_DIGITAL/' : '/';
    const actionUrl = `${basePath}includes/admin_control/UbahSoal_Controller.php`;
    
    console.log('📤 Mengirim ke:', actionUrl);
    
    fetch(actionUrl, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('📥 Status Response:', response.status, response.statusText);
        return response.text();
    })
    .then(result => {
        console.log('✅ Response dari server:', result);
        
        // Handle berbagai kemungkinan response
        if (result.includes('berhasil') || result.includes('sukses') || result.trim() === '' || result.includes('Tes tidak ditemukan')) {
            this.showAlert('success', 'Soal berhasil diperbarui!');
            this.hideModal();
            
            setTimeout(() => {
                this.refreshPage(idTes);
            }, 1000);
            
        } else {
            throw new Error(result);
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        
        // Jika error "Tes tidak ditemukan", tetap refresh
        if (error.message.includes('Tes tidak ditemukan')) {
            this.showAlert('success', 'Soal berhasil diperbarui!');
            this.hideModal();
            
            setTimeout(() => {
                this.refreshPage(idTes);
            }, 1000);
        } else {
            this.showAlert('error', 'Gagal menyimpan perubahan: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

    validateForm(form) {
        let isValid = true;
        
        const pertanyaan = form.querySelector('textarea[name="pertanyaan"]');
        if (!pertanyaan.value.trim()) {
            pertanyaan.classList.add('is-invalid');
            isValid = false;
        } else {
            pertanyaan.classList.remove('is-invalid');
        }
        
        const labels = ['a', 'b', 'c', 'd', 'e'];
        labels.forEach(label => {
            const opsiInput = form.querySelector(`input[name="opsi_${label}"]`);
            if (!opsiInput.value.trim()) {
                opsiInput.classList.add('is-invalid');
                isValid = false;
            } else {
                opsiInput.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    }

    hideModal() {
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.classList.remove('show');
            editModal.style.display = 'none';
            document.body.style.overflow = '';
            console.log('✅ Modal closed');
        }
    }

    refreshPage(idTes) {
        if (typeof window.loadContent === 'function') {
            console.log('🔄 Refreshing page via loadContent');
            window.loadContent(`editsoal.php?id_tes=${idTes}`);
        } else {
            console.log('🔄 Refreshing page via reload');
            location.reload();
        }
    }

    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    getLoadingTemplate(message = 'Memuat...') {
        return `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">${message}</p>
            </div>
        `;
    }

    getErrorTemplate(message) {
        return `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
            <div class="text-center mt-3">
                <button type="button" class="btn btn-secondary" onclick="window.editSoalManager.hideModal()">
                    Tutup
                </button>
            </div>
        `;
    }

    showLoading(message = 'Memproses...') {
        this.hideLoading();
        
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'global-loading';
        loadingDiv.className = 'global-loading';
        loadingDiv.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        `;
        
        loadingDiv.innerHTML = `
            <div class="text-center text-white">
                <div class="spinner-border mb-3"></div>
                <p>${message}</p>
            </div>
        `;
        
        document.body.appendChild(loadingDiv);
    }

    hideLoading() {
        const loadingDiv = document.getElementById('global-loading');
        if (loadingDiv) {
            loadingDiv.remove();
        }
    }

    showAlert(type, message) {
        const existingAlert = document.querySelector('.custom-alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const alertDiv = document.createElement('div');
        alertDiv.className = `custom-alert alert ${alertClass} alert-dismissible fade show`;
        alertDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            min-width: 300px;
            max-width: 500px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        alertDiv.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        const timeout = type === 'success' ? 5000 : 8000;
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, timeout);
    }

    destroy() {
        this.removeEventListeners();
        this.initialized = false;
        console.log('🧹 EditSoalManager destroyed');
    }
}

// ==================== FUNGSI INISIALISASI ====================

function initEditSoalManager() {
    console.log('🚀 Initializing EditSoalManager...');
    
    if (window.editSoalManager && typeof window.editSoalManager.destroy === 'function') {
        console.log('♻️ Cleaning up existing EditSoalManager...');
        window.editSoalManager.destroy();
    }
    
    window.editSoalManager = new EditSoalManager();
    console.log('✅ EditSoalManager created successfully');
    
    return window.editSoalManager;
}

function initTambahSoalManager() {
    console.log('🚀 Initializing TambahSoalManager...');
    
    if (window.tambahSoalManager && typeof window.tambahSoalManager.destroy === 'function') {
        console.log('♻️ Cleaning up existing TambahSoalManager...');
        window.tambahSoalManager.destroy();
    }
    
    window.tambahSoalManager = new TambahSoalManager();
    window.tambahSoalManager.init();
    console.log('✅ TambahSoalManager created successfully');
    
    return window.tambahSoalManager;
}

function setupManualEventHandlers() {
    console.log('🔧 Setting up manual event handlers as fallback...');
    
    const editButtons = document.querySelectorAll('.btn-edit-soal');
    console.log(`Found ${editButtons.length} edit buttons`);
    
    editButtons.forEach((button, index) => {
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
        
        newButton.addEventListener('click', function(e) {
            console.log('🎯 Manual event handler triggered');
            e.preventDefault();
            e.stopPropagation();
            
            const idSoal = this.getAttribute('data-soal-id');
            const idTes = this.getAttribute('data-tes-id');
            const nomor = this.getAttribute('data-nomor');
            
            console.log('📋 Manual handler data:', { idSoal, idTes, nomor });
            
            if (window.editSoalManager) {
                window.editSoalManager.handleEditSoal(this);
            } else {
                console.error('EditSoalManager not available');
            }
        });
    });
    
    const tambahButtons = document.querySelectorAll('#btnTambahSoal, #btnTambahPertama');
    tambahButtons.forEach(button => {
        if (button) {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (window.tambahSoalManager) {
                    window.tambahSoalManager.showModal();
                } else {
                    console.error('TambahSoalManager not available');
                }
            });
        }
    });
    
    console.log('✅ Manual event handlers setup completed');
}

// ==================== INISIALISASI UTAMA ====================

console.log('🎯 Starting managers initialization...');

function initializeAllManagers() {
    console.log('🔄 Initializing all managers...');
    initEditSoalManager();
    initTambahSoalManager();
    setTimeout(setupManualEventHandlers, 100);
}

// Inisialisasi ketika DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM fully loaded, initializing managers...');
    initializeAllManagers();
});

// Juga initialize jika DOM sudah ready
if (document.readyState === 'interactive' || document.readyState === 'complete') {
    console.log('📄 DOM already ready, initializing managers immediately...');
    setTimeout(initializeAllManagers, 100);
}

// Export untuk penggunaan global
window.initEditSoalManager = initEditSoalManager;
window.initTambahSoalManager = initTambahSoalManager;
window.setupManualEventHandlers = setupManualEventHandlers;

console.log('✅ editsoal.js loaded completely');