function initManajemenAccount() {
  console.log("🚀 Inisialisasi: manajemen_account.js");

  let notificationTimer; // Timer untuk notifikasi
  const apiUrl = "../../includes/developer_control/manajemenAkun_control.php";
  console.log("🔗 API URL:", apiUrl);

  // Fungsi getElements dan fetchAndDebug bisa tetap di dalam sini
  function getElements() {
    return {
      tableBody: document.getElementById("dataBody"),
      form: document.getElementById("accountForm"),
      message: document.getElementById("formMessage"),
      // Ambil elemen password untuk pengecekan
      passwordField: document.getElementById("password"),
      konfirmasiField: document.getElementById("konfirmasi")
    };
  }

  // Fungsi untuk menampilkan notifikasi
  // Kita buat fungsi terpisah agar bisa dipakai ulang
  function showNotification(text, type = 'danger') {
    const { message } = getElements();
    
    // Hapus timer lama jika ada
    clearTimeout(notificationTimer);

    // Atur teks dan kelas
    message.textContent = text;
    message.className = (type === 'success') ? "text-success" : "text-danger";
    
    // Tampilkan dengan animasi fade-in
    message.classList.add('show');

    // Atur timer untuk menghilangkan notifikasi setelah 3 detik
    notificationTimer = setTimeout(() => {
      message.classList.remove('show');
      // Hapus teks setelah animasi fade-out selesai
      setTimeout(() => {
          message.textContent = '';
          message.className = ''; 
      }, 500); // 500ms = 0.5s (sesuai durasi transisi CSS)
    }, 3000); // 3000ms = 3 detik
  }


  async function fetchAndDebug(url, options = {}) {
    // ... (Tidak ada perubahan di sini) ...
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
        return { ok: false, text };
      }
    } catch (err) {
      console.error("❌ Fetch error:", err);
      return { ok: false, error: err };
    }
  }

  async function loadAdmins() {
    // ... (Ini adalah versi Anda yang sudah benar dengan 6 kolom) ...
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
      return;
    }
    tableBody.innerHTML = "";
    result.data.forEach((admin, i) => {
      const row = `
        <tr class="fade-in">
          <td>${i + 1}</td>
          <td>${admin.nama}</td>
          <td>${admin.no_telp || '-'}</td>
          <td>${admin.email}</td>
          <td>${admin.username}</td>
          <td>
            <button class="btn btn-danger btn-sm" data-id="${admin.id_admin}">Hapus</button>
          </td>
        </tr>
      `;
      tableBody.insertAdjacentHTML('beforeend', row);
    });
    tableBody.querySelectorAll('button[data-id]').forEach(btn => {
      btn.addEventListener('click', () => {
        deleteAdmin(btn.getAttribute('data-id'));
      });
    });
  }

  async function setupForm() {
    const { form, passwordField, konfirmasiField } = getElements();
    if (!form) return console.warn("⚠️ #accountForm belum tersedia.");

    form.addEventListener('submit', async e => {
      e.preventDefault(); // Selalu cegah submit default

      // --- AWAL LOGIKA KONFIRMASI PASSWORD ---
      const password = passwordField.value;
      const konfirmasi = konfirmasiField.value;

      if (password !== konfirmasi) {
        // Panggil fungsi notifikasi kustom kita
        showNotification("Password dan Konfirmasi Password tidak cocok!", "danger");
        
        // (Opsional) Hapus isi kedua field password
        passwordField.value = "";
        konfirmasiField.value = "";
        
        // (Opsional) Fokuskan kembali ke field password pertama
        passwordField.focus();

        return; // Hentikan eksekusi, jangan kirim ke server
      }
      // --- AKHIR LOGIKA KONFIRMASI PASSWORD ---
      
      console.log("📨 Submit form tambah admin");
      const fd = new FormData(form);
      // for (let [k,v] of fd.entries()) console.log("Form field:", k, v); 

      const r = await fetchAndDebug(apiUrl, { method: 'POST', body: fd });
      
      // Tampilkan notifikasi server
      if (!r.ok) {
        showNotification("Server tidak mengembalikan JSON.", "danger");
        return;
      }

      const result = r.json;
      showNotification(result.message || "Aksi selesai.", result.status);

      if (result.status === "success") {
        form.reset();
        loadAdmins();
      }
    });
  }

  async function deleteAdmin(id_admin) {
    // ... (Tidak ada perubahan di sini) ...
    console.log("🗑 Menghapus admin ID:", id_admin);
    if (!confirm("Yakin ingin menghapus akun admin ini?")) return;
    const r = await fetchAndDebug(apiUrl, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/x-data-url-form-urlencoded' },
      body: `id_admin=${encodeURIComponent(id_admin)}`
    });
    if (!r.ok) {
      alert("Server error — cek console."); // Notif 'alert' standar boleh dipakai di sini
      return;
    }
    alert(r.json.message || "Akun dihapus.");
    loadAdmins();
  }

  // Jalankan fungsi utama
  setupForm();
  loadAdmins();

  // Ekspos fungsi ke console untuk debugging manual
  window._debug_admin = { loadAdmins, deleteAdmin };
}