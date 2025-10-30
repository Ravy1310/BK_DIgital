function initManajemenAccount() {
  console.log("🚀 Inisialisasi: manajemen_akun.js");

  let notificationTimer;
  const apiUrl = "../../includes/developer_control/manajemenAkun_control.php";
  let adminDataStore = {};
  let allAdmins = []; // Menyimpan semua data admin untuk pencarian
  let currentDisplayedAdmins = []; // Menyimpan data yang sedang ditampilkan
  
  console.log("🔗 API URL:", apiUrl);

  // Fungsi untuk mengambil semua elemen DOM yang diperlukan
  function getElements() {
    return {
      tableBody: document.getElementById("dataBody"),
      form: document.getElementById("accountForm"),
      message: document.getElementById("formMessage"),
      passwordField: document.getElementById("password"),
      konfirmasiField: document.getElementById("konfirmasi"),
      searchInput: document.getElementById("cari"),
      searchButton: document.getElementById("searchButton"),
      
      // Modal elements
      editModal: document.getElementById('editAdminModal'),
      editForm: document.getElementById('editAccountForm'),
      editIdAdmin: document.getElementById('edit_id_admin'),
      editNama: document.getElementById('edit_nama'),
      editNoTelp: document.getElementById('edit_no_telp'),
      editEmail: document.getElementById('edit_email'),
      editUsername: document.getElementById('edit_username'),
      editPassword: document.getElementById('edit_password'),
      editKonfirmasi: document.getElementById('edit_konfirmasi'),
      editFormMessage: document.getElementById('editFormMessage'),
      updateAdminBtn: document.getElementById('updateAdminBtn'),
      ButtonBatal: document.getElementById('ButtonBatal'),
      BatalUpdate: document.getElementById('BatalUpdate')
    };
  }

  // Fungsi untuk menampilkan notifikasi
  function showNotification(text, type = 'danger', isModal = false) {
    const messageElement = isModal ? getElements().editFormMessage : getElements().message;
    if (!messageElement) return;
    
    clearTimeout(notificationTimer);

    messageElement.textContent = text;
    messageElement.className = (type === 'success') ? "text-success" : "text-danger";
    messageElement.classList.add('show');

    notificationTimer = setTimeout(() => {
      messageElement.classList.remove('show');
      setTimeout(() => {
        messageElement.textContent = '';
        messageElement.className = ''; 
      }, 500); 
    }, 3000); 
  }

  // Fungsi wrapper untuk fetch dengan logging
  async function fetchAndDebug(url, options = {}) {
    console.log("➡️ Fetch:", url, options);
    try {
      const res = await fetch(url, options);
      console.log("⬅️ Status:", res.status, res.statusText);
      const text = await res.text();
      console.log("📦 Response (200 chars):", text.slice(0, 200));
      
      try {
        const json = JSON.parse(text);
        console.log("✅ Parsed JSON:", json);
        return { ok: true, json };
      } catch (err) {
        console.error("❌ Gagal parse JSON:", err);
        console.error("Teks Respons Penuh:", text);
        return { ok: false, text };
      }
    } catch (err) {
      console.error("❌ Fetch error:", err);
      return { ok: false, error: err };
    }
  }

  // Fungsi untuk menutup modal
  function closeModal() {
    const modalElement = document.getElementById('editAdminModal');
    if (!modalElement) return;
    
    // Bootstrap 5
    if (window.bootstrap && bootstrap.Modal) {
      try {
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
          modalInstance.hide();
          return;
        }
      } catch (error) {
        console.error('Bootstrap Modal error:', error);
      }
    }
    
    // jQuery
    if (window.jQuery && $.fn.modal) {
      try {
        $('#editAdminModal').modal('hide');
        return;
      } catch (error) {
        console.error('jQuery Modal error:', error);
      }
    }
    
    // Vanilla JS fallback
    modalElement.style.display = 'none';
    modalElement.classList.remove('show');
    modalElement.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
      backdrop.remove();
    }
  }

  // Fungsi untuk membuka modal edit
  function openEditModal(id_admin) {
    console.log("✏️ Membuka modal edit admin ID:", id_admin);
    const admin = adminDataStore[id_admin];
    if (!admin) {
        console.error("Data admin tidak ditemukan di store!");
        showNotification("Data admin tidak ditemukan.", "danger");
        return;
    }
    
    const { 
        editIdAdmin, 
        editNama, 
        editNoTelp, 
        editEmail, 
        editUsername, 
        editPassword, 
        editKonfirmasi,
        editFormMessage 
    } = getElements();
    
    // Isi form modal dengan data admin
    editIdAdmin.value = admin.id_admin;
    editNama.value = admin.nama;
    editNoTelp.value = admin.no_telp || '';
    editEmail.value = admin.email;
    editUsername.value = admin.username;
    editPassword.value = '';
    editKonfirmasi.value = '';
    editFormMessage.textContent = '';
    
    // Tampilkan modal
    showModal();
  }

  // Fungsi untuk menampilkan modal
  function showModal() {
    const modalElement = document.getElementById('editAdminModal');
    if (!modalElement) {
        console.error('Modal element tidak ditemukan!');
        return;
    }
    
    // Method 1: Bootstrap 5
    if (window.bootstrap && bootstrap.Modal) {
        try {
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalElement);
            }
            modalInstance.show();
            return;
        } catch (error) {
            console.error('Bootstrap Modal error:', error);
        }
    }
    
    // Method 2: jQuery
    if (window.jQuery && $.fn.modal) {
        try {
            $('#editAdminModal').modal('show');
            return;
        } catch (error) {
            console.error('jQuery Modal error:', error);
        }
    }
    
    // Method 3: Vanilla JS fallback
    console.warn('Menggunakan fallback vanilla JS untuk modal');
    modalElement.style.display = 'block';
    modalElement.classList.add('show');
    modalElement.setAttribute('aria-hidden', 'false');
    modalElement.setAttribute('aria-modal', 'true');
    modalElement.setAttribute('role', 'dialog');
    document.body.classList.add('modal-open');
    
    // Tambahkan backdrop
    let backdrop = document.querySelector('.modal-backdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
    }
    
    // Handle click backdrop untuk close
    backdrop.onclick = function() {
        closeModal();
    };
  }

  // Fungsi untuk memuat data admin ke tabel
  async function loadAdmins() {
    const { tableBody } = getElements();
    if (!tableBody) return console.warn("⚠️ #dataBody belum tersedia.");
    
    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Memuat...</td></tr>`; 
    
    const r = await fetchAndDebug(apiUrl);
    
    if (!r.ok) {
      tableBody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Gagal memuat data (cek console)</td></tr>`; 
      return;
    }
    
    const result = r.json;
    if (result.status !== "success" || !result.data?.length) {
      tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">${result.message || 'Belum ada data admin'}</td></tr>`;
      allAdmins = [];
      currentDisplayedAdmins = [];
      return;
    }
    
    // Simpan semua data admin
    allAdmins = result.data;
    currentDisplayedAdmins = [...allAdmins]; // Salin semua data untuk ditampilkan awal
    
    renderTable(currentDisplayedAdmins);
  }

  // Fungsi untuk merender tabel dengan data yang diberikan
  function renderTable(admins) {
    const { tableBody } = getElements();
    if (!tableBody) return;
    
    tableBody.innerHTML = "";
    adminDataStore = {}; 
    
    if (admins.length === 0) {
      tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Tidak ada data yang cocok</td></tr>`;
      return;
    }
    
    admins.forEach((admin, i) => {
      adminDataStore[admin.id_admin] = admin; 
      
      const row = `
        <tr class="fade-in">
          <td>${i + 1}</td>
          <td>${admin.nama}</td>
          <td>${admin.no_telp || '-'}</td>
          <td>${admin.email}</td>
          <td>${admin.username}</td>
          <td>
            <button class="btn btn-light btn-sm btn-edit me-1" data-id="${admin.id_admin}" title="Edit">
              <svg class="feather feather-edit" 
                   fill="none" 
                   height="15" 
                   stroke="#0050BC" 
                   stroke-linecap="round" 
                   stroke-linejoin="round" 
                   stroke-width="2" 
                   viewBox="0 0 24 24" 
                   width="15" 
                   xmlns="http://www.w3.org/2000/svg">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
              </svg>
            </button>
            <button class="btn btn-light btn-sm btn-delete" data-id="${admin.id_admin}" title="Hapus">
              <svg style="enable-background:new 0 0 24 24;" 
                   height="15" 
                   width="15" 
                   version="1.1" 
                   viewBox="0 0 24 24" 
                   xml:space="preserve" 
                   xmlns="http://www.w3.org/2000/svg" 
                   xmlns:xlink="http://www.w3.org/1999/xlink"
                   fill="#C60000">
                <g id="info"/>
                <g id="icons">
                  <g id="delete">
                    <path d="M18.9,8H5.1c-0.6,0-1.1,0.5-1,1.1l1.6,13.1c0.1,1,1,1.7,2,1.7h8.5c1,0,1.9-0.7,2-1.7l1.6-13.1C19.9,8.5,19.5,8,18.9,8z"/>
                    <path d="M20,2h-5l0,0c0-1.1-0.9-2-2-2h-2C9.9,0,9,0.9,9,2l0,0H4C2.9,2,2,2.9,2,4v1c0,0.6,0.4,1,1,1h18c0.6,0,1-0.4,1-1V4     C22,2.9,21.1,2,20,2z"/>
                  </g>
                </g>
              </svg>
            </button>
          </td>
        </tr>
      `;

      tableBody.insertAdjacentHTML('beforeend', row);
    });
    
    // Pasang listener ke tombol-tombol baru
    tableBody.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', () => {
        openEditModal(btn.getAttribute('data-id')); 
      });
    });
    
    tableBody.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', () => {
        deleteAdmin(btn.getAttribute('data-id')); 
      });
    });
  }

  // Fungsi untuk melakukan pencarian
  function searchAdmins(keyword) {
    if (!keyword.trim()) {
      // Jika pencarian kosong, tampilkan semua data
      currentDisplayedAdmins = [...allAdmins];
    } else {
      // Filter data berdasarkan keyword
      const searchTerm = keyword.toLowerCase().trim();
      currentDisplayedAdmins = allAdmins.filter(admin => 
        admin.nama.toLowerCase().includes(searchTerm) ||
        (admin.no_telp && admin.no_telp.includes(searchTerm)) ||
        admin.email.toLowerCase().includes(searchTerm) ||
        admin.username.toLowerCase().includes(searchTerm)
      );
    }
    
    renderTable(currentDisplayedAdmins);
  }

  // Fungsi untuk mendaftarkan event listener
  async function setupEventListeners() {
    const { 
      form, 
      updateAdminBtn, 
      ButtonBatal, 
      BatalUpdate, 
      searchInput, 
      searchButton 
    } = getElements();
    
    if (!form) return console.warn("⚠️ #accountForm belum tersedia.");

    // Listener untuk form tambah admin
    form.addEventListener('submit', async e => {
      e.preventDefault(); 
      await submitForm(false); // false = mode tambah
    });

    // Listener untuk tombol update di modal
    if (updateAdminBtn) {
      updateAdminBtn.addEventListener('click', async () => {
        await submitForm(true); // true = mode edit
      });
    }

    // Listener untuk tombol silang (X) di modal
    if (ButtonBatal) {
      ButtonBatal.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal();
      });
    }

    // Listener untuk tombol "Batal" di modal
    if (BatalUpdate) {
      BatalUpdate.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal();
      });
    }

    // Listener untuk pencarian - tombol enter
    if (searchInput) {
      searchInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
          searchAdmins(searchInput.value);
        } else {
          // Real-time search (opsional, bisa diaktifkan jika diinginkan)
          // searchAdmins(searchInput.value);
        }
      });
      
      // Real-time search dengan debounce
      let searchTimeout;
      searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          searchAdmins(e.target.value);
        }, 300); // Delay 300ms setelah user berhenti mengetik
      });
    }

    // Listener untuk tombol search
    if (searchButton) {
      searchButton.addEventListener('click', () => {
        searchAdmins(searchInput.value);
      });
    }

    // Listener untuk klik di luar modal (backdrop)
    const modalElement = document.getElementById('editAdminModal');
    if (modalElement) {
      modalElement.addEventListener('click', (e) => {
        if (e.target === modalElement) {
          closeModal();
        }
      });
    }

    // Listener untuk tombol ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeModal();
      }
    });
  }

  // Fungsi untuk submit form (baik tambah maupun edit)
  async function submitForm(isEdit) {
    const { 
      form, 
      editForm, 
      passwordField, 
      konfirmasiField, 
      editPassword, 
      editKonfirmasi,
      editModal 
    } = getElements();

    const currentForm = isEdit ? editForm : form;
    const currentPassword = isEdit ? editPassword : passwordField;
    const currentKonfirmasi = isEdit ? editKonfirmasi : konfirmasiField;

    const password = currentPassword.value;
    const konfirmasi = currentKonfirmasi.value;

    // Validasi Password
    if (password !== konfirmasi) {
      showNotification("Password dan Konfirmasi Password tidak cocok!", "danger", isEdit);
      currentPassword.value = "";
      currentKonfirmasi.value = "";
      currentPassword.focus();
      return; 
    }
    
    // Jika mode tambah dan password kosong
    if (!isEdit && password === "") {
      showNotification("Password wajib diisi untuk akun baru!", "danger", false);
      currentPassword.focus();
      return;
    }

    console.log(`📨 Submit form ${isEdit ? 'update' : 'tambah'} admin`);
    const fd = new FormData(currentForm);

    const r = await fetchAndDebug(apiUrl, { method: 'POST', body: fd });
    
    if (!r.ok) {
      showNotification("Server tidak mengembalikan JSON. Cek console.", "danger", isEdit);
      return;
    }

    const result = r.json;
    showNotification(result.message || "Aksi selesai.", result.status, isEdit);

    if (result.status === "success") {
      if (isEdit) {
        // Tutup modal jika sukses edit
        closeModal();
      } else {
        // Reset form jika sukses tambah
        form.reset();
      }
      loadAdmins(); // Muat ulang data tabel
    }
  }

  // Fungsi untuk menghapus admin
  async function deleteAdmin(id_admin) {
    console.log("🗑 Menghapus admin ID:", id_admin);
    if (!confirm("Yakin ingin menghapus akun admin ini?")) return;
    
    const r = await fetchAndDebug(apiUrl, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
      body: `id_admin=${encodeURIComponent(id_admin)}`
    });
    
    if (!r.ok) {
      showNotification("Server error — cek console.", "danger"); 
      return;
    }
    
    const result = r.json;
    showNotification(result.message || "Akun dihapus.", result.status || "success");
    
    // Muat ulang tabel jika sukses
    if (result.status === "success" || typeof result.status === 'undefined') {
       loadAdmins();
    }
  }

  // --- Jalankan Fungsi Utama ---
  setupEventListeners();
  loadAdmins();

  // Ekspos fungsi ke global (opsional, untuk debugging)
  window._debug_admin = { 
    loadAdmins, 
    deleteAdmin, 
    openEditModal, 
    closeModal, 
    searchAdmins,
    getAllAdmins: () => allAdmins,
    getDisplayedAdmins: () => currentDisplayedAdmins
  };
}