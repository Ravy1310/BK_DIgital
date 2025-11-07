//
// File: kelola_Guru.js - COMPLETE FIXED VERSION
//

// ==================== FUNGSI UTAMA ====================
function initializeKelolaGuru() {
    console.log('🔄 Initializing Kelola Guru...');
    
    // Inisialisasi modal
    const modalElement = document.getElementById('modalTambah');
    const formTambah = document.getElementById('formTambah');
    const modalTitle = document.getElementById('modalTitle');

    if (!modalElement || !formTambah) {
        console.error('❌ Required elements not found');
        return;
    }

    try {
        const modalTambah = new bootstrap.Modal(modalElement);
        console.log('✅ Bootstrap Modal initialized');

        // === FUNGSI RESET FORM YANG PROPER ===
        function resetForm() {
            console.log('🧹 Resetting form...');
            
            // Reset semua input field
            document.getElementById('editId').value = '';
            document.getElementById('namaGuru').value = '';
            document.getElementById('teleponGuru').value = '';
            document.getElementById('alamatGuru').value = '';
            document.getElementById('username').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
            document.getElementById('confirmPassword').value = '';
            
            // Reset tampilan
            modalTitle.textContent = "Tambah Data Guru";
            
            // Reset validasi UI
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            const feedback = document.getElementById('passwordFeedback');
            
            if (password) password.classList.remove('password-match', 'password-mismatch');
            if (confirmPassword) confirmPassword.classList.remove('password-match', 'password-mismatch');
            if (feedback) {
                feedback.textContent = '';
                feedback.className = 'password-feedback';
            }
            
            // Reset ke mode tambah
            if (typeof window.setupEditMode === 'function') {
                window.setupEditMode();
            }
            
            console.log('✅ Form reset completed');
        }

        // === INISIALISASI PENCARIAN REAL-TIME ===
        function initializeSearch() {
            const btnCari = document.getElementById('btnCari');
            const searchBox = document.getElementById('searchBox');

            if (searchBox) {
                let searchTimeout;
                
                // Fungsi untuk melakukan pencarian AJAX
                const performSearch = (keyword) => {
                    console.log('🔍 Performing search for:', keyword);
                    
                    // Show loading indicator
                    showTableLoading();
                    
                    // AJAX request untuk mendapatkan data
                    fetch('../../includes/admin_control/KelolaGuru_Controller.php')
                        .then(response => handleFetchResponse(response, 'mencari data'))
                        .then(data => {
                            if (data.status === 'success') {
                                // Filter data berdasarkan keyword
                                const filteredData = filterData(data.data, keyword);
                                updateTableWithData(filteredData);
                            } else {
                                throw new Error(data.message || 'Gagal mengambil data');
                            }
                        })
                        .catch(error => {
                            console.error('❌ Search error:', error);
                            // Tidak perlu hideTableLoading karena akan diganti oleh updateTableWithData
                            alert('Error saat pencarian: ' + error.message);
                        });
                };

                // Event listener untuk input real-time dengan debounce
                searchBox.addEventListener('input', function(e) {
                    const keyword = this.value.trim();
                    
                    // Clear timeout sebelumnya
                    clearTimeout(searchTimeout);
                    
                    // Jika kosong, langsung refresh ke data awal
                    if (keyword === '') {
                        performSearch('');
                        return;
                    }
                    
                    // Set timeout baru (300ms delay)
                    searchTimeout = setTimeout(() => {
                        performSearch(keyword);
                    }, 300);
                });

                // Event listener untuk tombol Enter
                searchBox.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(searchTimeout);
                        performSearch(this.value.trim());
                    }
                });

                // Tombol cari tetap berfungsi
                if (btnCari) {
                    // Clone tombol untuk remove existing listeners
                    btnCari.replaceWith(btnCari.cloneNode(true));
                    const newBtnCari = document.getElementById('btnCari');
                    
                    newBtnCari.addEventListener('click', function(e) {
                        e.preventDefault();
                        clearTimeout(searchTimeout);
                        performSearch(searchBox.value.trim());
                    });
                }

                console.log('✅ Real-time search initialized');
            } else {
                console.warn('⚠️ Search box #searchBox tidak ditemukan');
            }
        }

        // === TAMBAH DATA - Button handler ===
        const btnTambah = document.getElementById('btnTambah');
        if (btnTambah) {
            // Remove any existing click handlers dengan cara yang lebih aman
            btnTambah.replaceWith(btnTambah.cloneNode(true));
            const newBtnTambah = document.getElementById('btnTambah');
            
            newBtnTambah.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('🎯 Tambah Data button clicked');
                
                // Reset form sebelum show modal
                resetForm();
                
                modalTambah.show();
            });
        }

        // === EVENT DELEGATION dengan proper cleanup ===
        const container = document.getElementById('contentArea');
        if (container) {
            // Hapus handler lama jika ada
            if (window.kelolaGuruHandler) {
                container.removeEventListener('click', window.kelolaGuruHandler);
            }
            
            window.kelolaGuruHandler = function(e) {
                // === Handle EDIT ===
                const editBtn = e.target.closest('.edit-btn');
                if (editBtn) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    
                    const id = editBtn.getAttribute('data-id');
                    const username = editBtn.getAttribute('data-username');
                    const email = editBtn.getAttribute('data-email');
                    const row = editBtn.closest('tr');
                    
                    if (!row) return;
                    
                    console.log('✏️ Edit button clicked for ID:', id);
                    console.log('Username from data-attr:', username);
                    console.log('Email from data-attr:', email);
                    
                    // PERBAIKAN: Reset form dulu sebelum isi data baru
                    resetForm();
                    
                    // Ambil data dari tabel
                    const cells = row.cells;
                    
                    // Bersihkan data dari whitespace dan newlines
                    const getCleanText = (cell) => {
                        return cell.textContent.replace(/\s+/g, ' ').trim();
                    };
                    
                    const nama = getCleanText(cells[1]);
                    const telepon = getCleanText(cells[2]);
                    const alamat = getCleanText(cells[3]);
                    
                    console.log('Cleaned data - Nama:', `"${nama}"`);
                    console.log('Cleaned data - Telepon:', `"${telepon}"`);
                    console.log('Cleaned data - Alamat:', `"${alamat}"`);
                    
                    // Isi form dengan data baru
                    document.getElementById('editId').value = id;
                    document.getElementById('namaGuru').value = nama;
                    document.getElementById('teleponGuru').value = telepon;
                    document.getElementById('alamatGuru').value = alamat;
                    document.getElementById('username').value = username || '';
                    document.getElementById('email').value = email || '';
                    
                    modalTitle.textContent = "Edit Data Guru";
                    
                    // Setup edit mode
                    if (typeof window.setupEditMode === 'function') {
                        window.setupEditMode();
                    }
                    
                    // Tampilkan modal
                    modalTambah.show();
                    
                    // Debug final check
                    setTimeout(() => {
                        console.log('Final form values after edit:');
                        console.log('- Nama:', document.getElementById('namaGuru').value);
                        console.log('- Telepon:', document.getElementById('teleponGuru').value);
                        console.log('- Alamat:', document.getElementById('alamatGuru').value);
                        console.log('- Username:', document.getElementById('username').value);
                        console.log('- Email:', document.getElementById('email').value);
                    }, 100);
                    
                    return false;
                }

                // === Handle DELETE ===
                const deleteBtn = e.target.closest('.delete-btn');
                if (deleteBtn) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    
                    const id = deleteBtn.getAttribute('data-id');
                    const row = deleteBtn.closest('tr');
                    const getCleanText = (cell) => cell.textContent.replace(/\s+/g, ' ').trim();
                    const nama = row ? getCleanText(row.cells[1]) : '';
                    
                    if (confirm(`Yakin ingin menghapus data guru "${nama}"?`)) {
                        console.log('🗑️ Delete confirmed for ID:', id);
                        
                        fetch('../../includes/admin_control/KelolaGuru_Controller.php', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_guru=${id}`
                        })
                        .then(response => handleFetchResponse(response, 'menghapus data'))
                        .then(data => {
                            if (data.status === 'success') {
                                alert(data.message);
                                refreshAllData();
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error menghapus data: ' + error.message);
                        });
                    }
                    return false;
                }

                // === Handle STATUS ===
                const statusBtn = e.target.closest('.status-btn');
                if (statusBtn) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    
                    const id = statusBtn.getAttribute('data-id');
                    const statusSekarang = statusBtn.getAttribute('data-status');
                    const row = statusBtn.closest('tr');
                    const getCleanText = (cell) => cell.textContent.replace(/\s+/g, ' ').trim();
                    const nama = row ? getCleanText(row.cells[1]) : '';
                    const statusBaru = statusSekarang === 'Aktif' ? 'Nonaktif' : 'Aktif';
                    
                    if (confirm(`Ubah status akun "${nama}" menjadi ${statusBaru}?`)) {
                        console.log('🔄 Status change for ID:', id);
                        
                        fetch('../../includes/admin_control/KelolaGuru_Controller.php', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_guru=${id}&action=ubah_status`
                        })
                        .then(response => handleFetchResponse(response, 'mengubah status'))
                        .then(data => {
                            if (data.status === 'success') {
                                alert(data.message);
                                refreshAllData();
                            } else {
                                throw new Error(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error mengubah status: ' + error.message);
                        });
                    }
                    return false;
                }
            };
            
            container.addEventListener('click', window.kelolaGuruHandler);
            console.log('✅ Event delegation handler registered');
        }

        // === FORM SUBMISSION ===
        // Clone form untuk remove existing listeners
        const newForm = formTambah.cloneNode(true);
        formTambah.parentNode.replaceChild(newForm, formTambah);
        const currentForm = document.getElementById('formTambah');
        
        currentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('🔵 Form submission started...');
            
            // Validasi form
            const nama = document.getElementById('namaGuru').value.trim();
            const telepon = document.getElementById('teleponGuru').value.trim();
            const alamat = document.getElementById('alamatGuru').value.trim();
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const editId = document.getElementById('editId').value;
            
            if (!nama || !telepon || !alamat || !username || !email) {
                alert('Semua field harus diisi!');
                return;
            }

            // Validasi telepon (10-15 digit angka)
            if (!/^[0-9]{10,15}$/.test(telepon)) {
                alert('Nomor telepon harus 10-15 digit angka');
                return;
            }

            // Validasi email
            if (!isValidEmail(email)) {
                alert('Format email tidak valid');
                return;
            }

            // VALIDASI PASSWORD
            const isEditMode = editId !== '';
            
            if (isEditMode) {
                // Mode edit: jika password diisi, harus valid
                if (password && password.length < 6) {
                    alert('Password baru harus minimal 6 karakter!');
                    return;
                }
                if (password && password !== confirmPassword) {
                    alert('Password dan konfirmasi password tidak cocok!');
                    return;
                }
            } else {
                // Mode tambah: password wajib
                if (!password) {
                    alert('Password wajib diisi untuk guru baru!');
                    return;
                }
                if (password.length < 6) {
                    alert('Password harus minimal 6 karakter!');
                    return;
                }
                if (!confirmPassword) {
                    alert('Harap konfirmasi password Anda!');
                    return;
                }
                if (password !== confirmPassword) {
                    alert('Password dan konfirmasi password tidak cocok!');
                    return;
                }
            }
            
            console.log('💾 Preparing to save data...');
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            submitBtn.disabled = true;
            
            // Prepare data
            const formData = new FormData();
            formData.append('nama_guru', nama);
            formData.append('telepon_guru', telepon);
            formData.append('alamat_guru', alamat);
            formData.append('username', username);
            formData.append('email', email);
            formData.append('password', password);
            
            if (editId) {
                formData.append('id_guru', editId);
            }
            
            console.log('📤 Sending AJAX request...');
            
            // AJAX Request
            fetch('../../includes/admin_control/KelolaGuru_Controller.php', {
                method: 'POST',
                body: formData
            })
            .then(response => handleFetchResponse(response, 'menyimpan data'))
            .then(data => {
                console.log('✅ JSON response:', data);
                
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.status === 'success') {
                    console.log('🎉 Success from server');
                    
                    // Tutup modal
                    modalTambah.hide();
                    
                    // PERBAIKAN: Reset form setelah simpan berhasil
                    setTimeout(() => {
                        resetForm();
                    }, 300);
                    
                    // Show success message
                    alert(data.message);
                    
                    // Refresh data tabel dan statistik tanpa reload halaman
                    setTimeout(() => {
                        refreshAllData();
                    }, 500);
                    
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('❌ Fetch error:', error);
                
                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                alert('Error menyimpan data: ' + error.message);
            });
        });

        // === EVENT LISTENER UNTUK MODAL HIDDEN ===
        modalElement.addEventListener('hidden.bs.modal', function() {
            console.log('📝 Modal closed, resetting form...');
            // Reset form ketika modal ditutup (baik dengan tombol batal atau klik outside)
            resetForm();
        });

        // === TOMBOL BATAL MANUAL ===
        const btnBatal = document.querySelector('button[data-bs-dismiss="modal"]');
        if (btnBatal) {
            btnBatal.addEventListener('click', function() {
                console.log('❌ Cancel button clicked, resetting form...');
                resetForm();
            });
        }

        // === INISIALISASI PENCARIAN ===
        initializeSearch();

        console.log('✅ Kelola Guru initialization completed successfully');

    } catch (error) {
        console.error('❌ Error initializing Kelola Guru:', error);
    }
}

// ==================== FUNGSI BANTUAN LAINNYA ====================

// Fungsi untuk menampilkan loading indicator
function showTableLoading() {
    const tableBody = findTableBody();
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </td>
            </tr>
        `;
    }
}

// Fungsi untuk menyembunyikan loading indicator
function hideTableLoading() {
    // Loading indicator akan diganti saat updateTableWithData dipanggil
}

// Fungsi untuk mencari table body
function findTableBody() {
    const selectors = [
        'table tbody',
        '.table tbody',
        'table.table tbody',
        '#contentArea table tbody',
        'table#tabelGuru tbody',
        'tbody'
    ];
    
    for (const selector of selectors) {
        const tableBody = document.querySelector(selector);
        if (tableBody) {
            console.log(`✅ Table body found with selector: ${selector}`);
            return tableBody;
        }
    }
    
    console.error('❌ Table body tidak ditemukan dengan selector apapun');
    return null;
}

// Fungsi untuk filter data di client-side
function filterData(data, keyword) {
    if (!keyword) return data;
    
    const lowerKeyword = keyword.toLowerCase();
    return data.filter(guru => 
        (guru.nama && guru.nama.toLowerCase().includes(lowerKeyword)) ||
        (guru.telepon && guru.telepon.includes(lowerKeyword)) ||
        (guru.alamat && guru.alamat.toLowerCase().includes(lowerKeyword)) ||
        (guru.username && guru.username.toLowerCase().includes(lowerKeyword)) ||
        (guru.email && guru.email.toLowerCase().includes(lowerKeyword)) ||
        (guru.status && guru.status.toLowerCase().includes(lowerKeyword))
    );
}

// Fungsi untuk memperbarui tabel dengan data baru
function updateTableWithData(data) {
    const tableBody = findTableBody();
    
    if (!tableBody) {
        console.error('❌ Table body tidak ditemukan');
        return;
    }
    
    // Kosongkan tabel
    tableBody.innerHTML = '';
    
    if (data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="fas fa-search me-2"></i>
                    Tidak ada data yang ditemukan
                </td>
            </tr>
        `;
        return;
    }
    
    // Isi dengan data baru
    data.forEach((guru, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="fw-medium">${index + 1}</td>
            <td class="fw-medium">${escapeHtml(guru.nama || '')}</td>
            <td>${escapeHtml(guru.telepon || '')}</td>
            <td>${escapeHtml(guru.alamat || '')}</td>
            <td>
                <span class="status-badge ${(guru.status || 'Aktif') === 'Aktif' ? 'status-aktif' : 'status-nonaktif'}">
                    ${guru.status || 'Aktif'}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn-action btn-edit edit-btn" 
                            data-id="${guru.id_guru}"
                            data-username="${escapeHtml(guru.username || '')}"
                            data-email="${escapeHtml(guru.email || '')}">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn-action btn-delete delete-btn" 
                            data-id="${guru.id_guru}">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn-action btn-status status-btn"
                            data-id="${guru.id_guru}" 
                            data-status="${guru.status || 'Aktif'}">
                        <i class="bi bi-${(guru.status || 'Aktif') === 'Aktif' ? 'x' : 'check'}-circle"></i>
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(row);
    });
    
    console.log(`✅ Table updated with ${data.length} rows`);
}

// ==================== FUNGSI BANTUAN FETCH ====================

// Fungsi untuk menangani respons fetch
function handleFetchResponse(response, action = 'operation') {
    return response.text().then(text => {
        try {
            const data = JSON.parse(text);
            if (!response.ok) {
                throw new Error(data.message || `Server error: ${response.status}`);
            }
            return data;
        } catch (jsonError) {
            console.error(`JSON Parse Error for ${action}:`, jsonError);
            console.error('Raw response:', text);
            throw new Error(`Respons server tidak valid (bukan JSON) untuk ${action}. Status: ${response.status}`);
        }
    });
}

// ==================== FUNGSI STATISTIK REAL-TIME ====================

// Fungsi untuk memperbarui statistik
function updateStatistics(data) {
    console.log('📊 Updating statistics with data:', data);
    
    // Hitung statistik dari data
    const totalGuru = data.length;
    const akunAktif = data.filter(guru => guru.status === 'Aktif').length;
    const akunNonaktif = totalGuru - akunAktif;
    
    // Update elemen statistik dengan animasi
    animateCounter('jumlahGuru', totalGuru);
    animateCounter('akunAktif', akunAktif);
    animateCounter('akunNonaktif', akunNonaktif);
    
    console.log(`📊 Statistics updated: Total=${totalGuru}, Aktif=${akunAktif}, Nonaktif=${akunNonaktif}`);
}

// Fungsi untuk animasi counter
function animateCounter(elementId, targetValue) {
    const element = document.getElementById(elementId);
    if (!element) {
        console.warn(`⚠️ Element #${elementId} tidak ditemukan`);
        return;
    }
    
    const currentValue = parseInt(element.textContent) || 0;
    
    if (currentValue === targetValue) return;
    
    // Animasi sederhana
    let start = currentValue;
    const duration = 500;
    const startTime = performance.now();
    
    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const current = Math.floor(start + (targetValue - start) * easeOutQuart);
        
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = targetValue;
        }
    }
    
    requestAnimationFrame(updateCounter);
}

// Fungsi untuk refresh semua data (tabel + statistik)
function refreshAllData() {
    const searchBox = document.getElementById('searchBox');
    const currentSearch = searchBox ? searchBox.value.trim() : '';
    
    console.log('🔄 Refreshing all data...');
    
    // Show loading indicator
    showTableLoading();
    
    fetch('../../includes/admin_control/KelolaGuru_Controller.php')
        .then(response => handleFetchResponse(response, 'refresh all data'))
        .then(data => {
            if (data.status === 'success') {
                // Update tabel
                const filteredData = filterData(data.data, currentSearch);
                updateTableWithData(filteredData);
                
                // Update statistik
                updateStatistics(data.data);
            } else {
                throw new Error(data.message || 'Gagal refresh data');
            }
        })
        .catch(error => {
            console.error('❌ Refresh error:', error);
            alert('Error refresh data: ' + error.message);
        });
}

// Fungsi helper untuk validasi email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Fungsi helper untuk escape HTML (mencegah XSS)
function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return unsafe
        .toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// ==================== EXPORT FUNGSI ====================

// Export fungsi ke window
window.initKelolaGuru = initializeKelolaGuru;
window.handleFetchResponse = handleFetchResponse;
window.refreshAllData = refreshAllData;
window.updateStatistics = updateStatistics;
window.filterData = filterData;
window.updateTableWithData = updateTableWithData;
window.showTableLoading = showTableLoading;
window.hideTableLoading = hideTableLoading;
window.findTableBody = findTableBody;
window.isValidEmail = isValidEmail;
window.escapeHtml = escapeHtml;