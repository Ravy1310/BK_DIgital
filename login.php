<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login BK Digital - SMA AL-ISLAM KRIAN</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">

    <style>
        /* Warna kustom sesuai gambar */
        :root {
            --primary-blue: #004d9c; /* Warna biru gelap */
            --off-white: #f5f5f5; /* Warna latar belakang kanan, diubah menjadi 6 digit */
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5 !important; /* ubah ke putih */
            overflow: hidden; /* Mencegah scroll saat ada elemen absolut */
        }
        /* pastikan container mengikuti background baru */
        .container-fluid, .right-panel {
            background-color: #f5f5f5 !important;
        }
/* ANIMASI MASUK DARI KIRI + GRADASI BERGERAK */
.left-panel {
    background: linear-gradient( to top right, #002E6E, #0045B0, #0059D4) !important;
    background-size: 200% 200%;
    animation: gradientSlideIn 1.8s ease forwards, gradientMove 10s ease infinite;
    transform: translateX(-100%);
    opacity: 0;
}

/* Saat panel masuk dari kiri */
@keyframes gradientSlideIn {
    0% {
        transform: translateX(-100%);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Gerakan lembut gradasi */
@keyframes gradientMove {
    0% {
        background-position: 0% 100%;
    }
    50% {
        background-position: 100% 0%;
    }
    100% {
        background-position: 0% 100%;
    }
}

/* Semua elemen dalam left-panel muncul dari kiri */
.left-panel * {
    opacity: 0;
    transform: translateX(-40px);
    animation: slideInLeft 1.2s ease forwards;
}

/* Efek masuk dari kiri dengan fade */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-40px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Urutan kemunculan */
.left-panel header {
    animation-delay: 0.8s;
}
.left-panel main h2 {
    animation-delay: 1.1s;
}
.left-panel main h6 {
    animation-delay: 1.3s;
}

/* Panel kanan muncul perlahan dari kanan bawah */
.right-panel {
    opacity: 0;
    transform: translate(50px, 30px);
    animation: fadeInRight 1.2s ease-out forwards;
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translate(50px, 30px);
    }
    to {
        opacity: 1;
        transform: translate(0, 0);
    }
}

/* Elemen-elemen di dalam form muncul bertahap (smooth delay) */
.right-panel form > * {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.8s ease forwards;
}

.right-panel form > *:nth-child(1) { animation-delay: 0.3s; }
.right-panel form > *:nth-child(2) { animation-delay: 0.5s; }
.right-panel form > *:nth-child(3) { animation-delay: 0.7s; }
.right-panel form > *:nth-child(4) { animation-delay: 0.9s; }
.right-panel form > *:nth-child(5) { animation-delay: 1.1s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

        /* Kurva pemisah di kanan (HANYA UNTUK DESKTOP) */
        @media (min-width: 768px) {
            .left-panel {
                /* Menggunakan kelas shapedividers_com-652 */
                height: 100%;
                /* NILAI BARU: 50% */
                width: 50%; /* Menyesuaikan lebar di desktop, digeser lebih ke kanan */
            }
            .right-panel {
                /* NILAI BARU: 50% */
                width: 50%; /* Menyesuaikan lebar di desktop */
            }
        }

        /* Override Bootstrap: Rounded pills for inputs and buttons */
        .form-control.rounded-pill,
        .input-group-text.rounded-start-pill,
        .input-group-text.rounded-end-pill,
        .btn.rounded-pill {
             border-radius: 50rem !important;
        }

        .input-group-text {
            /* background-color: white !important; diubah menjadi #ffffff */
            background-color: #fff !important; 
            color: #4a4a4a;
            border-color: #fff;
            padding-right: 0.8rem;
        }

        .form-control {
            height: 50px;
        }

        .btn-primary {
            background-color: var(--primary-blue) !important;
            border-color: var(--primary-blue) !important;
            font-size: 1.25rem;
            height: 55px;

            /* smooth transition for hover effect */
            transition: background-color .15s ease, border-color .15s ease, transform .08s ease, box-shadow .15s ease;
        }

        /* hover / focus state - ubah warna saat pointer berada di tombol */
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #003a7a !important; /* warna lebih gelap saat hover */
            border-color: #003a7a !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 77, 156, 0.18);
        }

        .text-header-logo {
            font-size: 0.85rem;
        }

       
        /* --- IMPLEMENTASI KODE SHAPE DIVIDER BARU (.shapedividers_com-652) --- */
        .shapedividers_com-652{
            overflow: hidden;
            position: relative;
           
        }

        .shapedividers_com-652::before{
            content: '';
            font-family: 'shape divider from ShapeDividers.com';
            position: absolute;
            bottom: -1px;
            left: -1px;
            right: -1px;
            top: -1px;
            z-index: 3;
            pointer-events: none;
            background-repeat: no-repeat;
            background-size: 142px 197%;
            background-position: 100% 38%;
            background-image: url('data:image/svg+xml;charset=utf8, <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2.17 35.28" preserveAspectRatio="none"><path d="M1.67 0c-.55 3.07.41 9.27 0 16.14-.4 6.88-.58 13.75.1 19.14h.4V0z" fill="%23ffffff"/><path d="M1.16 0c-.8 3.17.4 7.29.56 10.04C1.89 12.8.25 19.3.42 22.71c.16 3.43.84 4.65.86 7.05.03 2.4-.88 5.52-.88 5.52h1.77V0z" opacity=".5" fill="%23ffffff"/><path d="M.31 0c.84 2.56.3 7.68.43 11.79.12 4.1.61 6.86.28 9.58-.33 2.73-1.18 5.61-1 8.61.19 3 .82 4.73.84 5.3h1.2V0z" opacity=".5" fill="%23ffffff"/></svg>');
            filter: drop-shadow(10px 0px 18px rgba(0,0,0, 1));
        }

        @media (min-width:768px){
            .shapedividers_com-652::before{
                background-size: 178px 197%;
                background-position: 100% 38%;    
            }  
        }
        
        @media (min-width:1025px){
            .shapedividers_com-652::before{ 
                bottom: -0.1vw;
                left: -0.1vw;
                right: -0.1vw;
                top: -0.1vw; 
                background-size: 215px 225%;
                background-position: 100% 50%;   
                /* Perhatikan: fill diubah menjadi %23f5f5f5 */
                background-image: url('data:image/svg+xml;charset=utf8, <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2.17 35.28" preserveAspectRatio="none"><path d="M1.16 0c-.8 3.17.4 7.29.56 10.04C1.89 12.8.25 19.3.42 22.71c.16 3.43.84 4.65.86 7.05.03 2.4-.88 5.52-.88 5.52h1.77V0z" fill="%23f5f5f5"/></svg>'); 
            }
        }
        /* --- AKHIR KODE SHAPE DIVIDER BARU --- */

    </style>
</head>

<body class="bg-light">

    <div class="container-fluid vh-100 p-0">
        <div class="row g-0 h-100">

        <div class="col-12 col-md-5 left-panel shapedividers_com-652 d-flex flex-column justify-content-start align-items-start text-white p-0 position-relative">

    <header class="position-absolute top-0 start-0 d-flex align-items-center m-3">
        <div class="me-2 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm"
             style="width: 60px; height: 60px; overflow: hidden;">
            <img src="assets/image/logo sekolah.png" alt="Logo Sekolah" width="75" height="49">
        </div>
        <div>
            <p class="mb-0 small fw-semibold text-header-logo">SMA</p>
            <h1 class="h6 fw-bold mb-0 text-header-logo">AL-ISLAM KRIAN</h1>
        </div>
    </header>

    <main class="flex-grow-1 d-flex flex-column justify-content-center align-items-start w-100 mx-5 px-2" style="max-width: 400px;">
        <h2 class="fw-bolder mb-1" style="line-height:1.2; font-size: 2.5rem;">Selamat Datang</h2>
        <h6 class="fw-medium mb-3" style="line-height:1.2; font-size: 1.5rem;">di BK Digital</h6>
    </main>

</div>


            <div class="col-12 col-md-7 right-panel d-flex align-items-center justify-content-center p-4 p-md-5">
                <form id="loginForm" class="w-100" style="max-width:520px; margin-left: auto; margin-right: 2rem;">
                    
                    <h1 class="display-6 fw-bolder text-center text-md-start mb-5" style="color:#212529; margin-left: 190px;">Login</h1>

                    <div id="statusMessage" class="alert d-none text-center rounded-3 mb-4"></div>
                    <div class="mb-4">
                        <label for="username" class="form-label small fw-semibold text-muted mb-1">Username</label>
                        <div class="input-group shadow-sm rounded-end-pill border-start-0">
                            <span class="input-group-text "><i class="fas fa-user text-dark"></i></span>
                            <input type="text" id="username" name="username" class="form-control rounded-end-pill border-start-0" placeholder="username" required>
                        </div>
                    </div>

                    <div class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-1">
    <label for="password" class="form-label small fw-semibold text-muted mb-0  ">Password</label>
    <a href="#" class="small text-decoration-none" style="color:var(--primary-blue);" onclick="handleForgotPassword(event)">Lupa password?</a>
  </div>
  <div class="input-group shadow-sm rounded-end-pill border-start-0">
    <span class="input-group-text"><i class="fas fa-key text-dark"></i></span>
    <input type="password" id="password" name="password" class="form-control border-start-0" placeholder="password" required>
    <button type="button" class="input-group-text bg-white   border-start-0" id="togglePassword" style="cursor: pointer;">
      <i class="fa-solid fa-eye-slash text-dark"></i>
    </button>
  </div>
</div>


                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow-lg">Login</button>
                    
                </form>
            </div>

        </div>
    </div>

  <script>
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
  // const pesan = document.getElementById("pesan"); // Variabel 'pesan' TIDAK ADA di HTML, dihilangkan

  const username = usernameInput.value.trim();
  const password = passwordInput.value.trim();

  if (!username || !password) {
    // Menggunakan showStatus sebagai pengganti 'pesan'
    showStatus("Harap isi semua kolom Username dan Password.", "error"); 
    return;
  }

  try {
    console.log("🚀 Mengirim data ke server...");
    console.log("👤 Username:", username);
    // console.log("🔒 Password:", password); // Sebaiknya tidak mencetak password ke console

    const response = await fetch("includes/login_controller.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
    });

    const responseText = await response.text();
    console.log("🧾 Respon mentah dari server:", responseText);

    let result;
    try {
      result = JSON.parse(responseText);
      console.log("✅ Respon JSON dari server:", result);
    } catch (err) {
      console.error("❌ Gagal parse JSON:", err);
      // Menggunakan showStatus sebagai pengganti 'pesan'
      showStatus("Server tidak mengirim data JSON yang valid.", "error"); 
      // kosongkan field walau gagal parse
      usernameInput.value = "";
      passwordInput.value = "";
      return;
    }

    if (result.status === "success") {
      // Menggunakan showStatus sebagai pengganti 'pesan'
      showStatus(result.message, "success"); 

      localStorage.setItem("username", result.user);
      localStorage.setItem("role", result.role);

      // Kosongkan input
      usernameInput.value = "";
      passwordInput.value = "";

      // Redirect sesuai role
      if (result.role === "superadmin") {
        window.location.href = "pages/Developer/manajemen_account.php";
        console.log("Redirect ke halaman:", result.role, "=>", "pages/Developer/manajemen_account.php");

      } else if (result.role === "admin") {
        window.location.href = "dashboard_admin.php";
      } else {
        window.location.href = "dashboard_user.php";
      }

    } else {
      // Menggunakan showStatus sebagai pengganti 'pesan'
      showStatus(result.message, "error"); 

      // Kosongkan field meski login gagal
      usernameInput.value = "";
      passwordInput.value = "";
    }

  } catch (error) {
    
    // Perbaikan: Ganti pesan agar lebih umum saat error koneksi
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
  passwordField.setAttribute('type', type); // Menghapus typo 'w' yang error
  this.querySelector('i').classList.toggle('fa-eye');
  this.querySelector('i').classList.toggle('fa-eye-slash');
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
     ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
</body></html>