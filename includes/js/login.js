document.addEventListener('DOMContentLoaded', () => {
const loginForm = document.getElementById('loginForm');
const statusMessage = document.getElementById('statusMessage');

function showStatus(msg, type) {
  statusMessage.textContent = msg;
  statusMessage.className = 'alert text-center rounded-3 mb-4';
  if (type === 'success') statusMessage.classList.add('alert-success', 'alert-dismissible', 'fade', 'show');
  else if (type === 'error') statusMessage.classList.add('alert-danger', 'alert-dismissible', 'fade', 'show');
  else statusMessage.classList.add('alert-warning', 'alert-dismissible', 'fade', 'show');

  statusMessage.classList.remove('d-none');
  
  // Perbaikan: Hapus pesan status setelah 5 detik
  setTimeout(() => {
    statusMessage.classList.add('d-none');
    statusMessage.className = 'alert d-none text-center rounded-3 mb-4'; // Reset class
  }, 5000); 
}

// === Fungsi login ===
loginForm.addEventListener("submit", async function(e) {
  e.preventDefault();

  const usernameInput = document.getElementById("username");
  const passwordInput = document.getElementById("password");

  const username = usernameInput.value.trim();
  const password = passwordInput.value.trim();

  if (!username || !password) {
    showStatus("Harap isi semua kolom Username dan Password.", "error"); 
    return;
  }

  try {
   
    const response = await fetch("includes/login_controller.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
    });

    const responseText = await response.text();
  
    let result;
    try {
      result = JSON.parse(responseText);
    } catch (err) {
      showStatus("Server tidak mengirim data JSON yang valid.", "error"); 
      usernameInput.value = "";
      passwordInput.value = "";
      return;
    }

    if (result.status === "success") {
      // HAPUS LOCALSTORAGE KARENA SUDAH PAKAI SESSION
      // localStorage.removeItem("username");
      // localStorage.removeItem("role");

      // Kosongkan input
      usernameInput.value = "";
      passwordInput.value = "";

      // Redirect sesuai role
      if (result.role === "superadmin") {
        window.location.href = "pages/Developer/sideMenu_dev.php";
      } else if (result.role === "admin") {
        window.location.href = "pages/admin/sideMenu_admin.php";
      } else {
        window.location.href = "dashboard_user.php";
      }

    } else {
      showStatus(result.message, "error"); 
      usernameInput.value = "";
      passwordInput.value = "";
    }

  } catch (error) {
    showStatus("Terjadi kesalahan saat koneksi ke server.", "error"); 
    usernameInput.value = "";
    passwordInput.value = "";
  }
});

// Fungsi lupa password
function handleForgotPassword(e) {
  e.preventDefault();
  showStatus('Fungsi lupa password belum diimplementasikan.', 'info');
}

// Tombol lihat/sembunyi password
const togglePassword = document.getElementById('togglePassword');
const passwordField = document.getElementById('password');

togglePassword.addEventListener('click', function() {
  const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
  passwordField.setAttribute('type', type);
  this.querySelector('i').classList.toggle('fa-eye');
  this.querySelector('i').classList.toggle('fa-eye-slash');
});
});