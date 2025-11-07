
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
:root {
    --primary-blue: #004d9c;
    --off-white: #f5f5f5;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--off-white);
    overflow: hidden;
}

/* --- LEFT PANEL ANIMASI --- */
.left-panel {
    background: linear-gradient(to top right, #002E6E, #0045B0, #0059D4) !important;
    background-size: 200% 200%;
    animation: gradientSlideIn 1.8s ease forwards, gradientMove 10s ease infinite;
    transform: translateX(-100%);
    opacity: 0;
}
.left-panel * {
    opacity: 0;
    transform: translateX(-40px);
    animation: slideInLeft 1.2s ease forwards;
}
@keyframes gradientSlideIn {
    0% { transform: translateX(-100%); opacity: 0; }
    100% { transform: translateX(0); opacity: 1; }
}
@keyframes gradientMove {
    0% { background-position: 0% 100%; }
    50% { background-position: 100% 0%; }
    100% { background-position: 0% 100%; }
}
@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-40px); }
    to { opacity: 1; transform: translateX(0); }
}
.left-panel header { animation-delay: 0.8s; }
.left-panel main h2 { animation-delay: 1.1s; }
.left-panel main h6 { animation-delay: 1.3s; }

/* --- RIGHT PANEL ANIMASI --- */
.right-panel {
    opacity: 0;
    transform: translate(50px, 30px);
    animation: fadeInRight 1.2s ease-out forwards;
    background-color: #f5f5f5 !important;
}
@keyframes fadeInRight {
    from { opacity: 0; transform: translate(50px, 30px); }
    to { opacity: 1; transform: translate(0,0); }
}
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
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* --- SHAPE DIVIDER --- */
.shapedividers_com-652 {
    overflow: hidden;
    position: relative;
}
.shapedividers_com-652::before {
    content: '';
    position: absolute;
    inset: -0.1vw;
    z-index: 3;
    pointer-events: none;
    background-repeat: no-repeat;
    background-size: 215px 225%;
    background-position: 100% 50%;
    background-image: url('data:image/svg+xml;charset=utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2.17 35.28" preserveAspectRatio="none"><path d="M1.16 0c-.8 3.17.4 7.29.56 10.04C1.89 12.8.25 19.3.42 22.71c.16 3.43.84 4.65.86 7.05.03 2.4-.88 5.52-.88 5.52h1.77V0z" fill="%23f5f5f5"/></svg>');
}

/* --- BUTTON WARNA --- */
.btn-primary {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
    transition: all .15s ease;
}
.btn-primary:hover,
.btn-primary:focus {
    background-color: #003a7a;
    border-color: #003a7a;
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(0, 77, 156, 0.18);
}

/* --- RESPONSIVE SHAPE DIVIDER --- */
@media (max-width: 767.98px) {
    .shapedividers_com-652::before { content: none !important; }
}
</style>

</head>

<body>
<div class="container-fluid vh-100 p-0">
  <div class="row g-0 h-100">

    <!-- LEFT PANEL -->
    <div class="col-12 col-md-5 left-panel shapedividers_com-652 d-flex flex-column justify-content-start align-items-start text-white p-0 position-relative">
      <header class="position-absolute top-0 start-0 d-flex align-items-center m-3">
        <div class="me-2 d-flex justify-content-center align-items-center bg-white rounded-circle shadow-sm" style="width: 60px; height: 60px; overflow: hidden;">
          <img src="assets/image/logo sekolah.png" alt="Logo Sekolah" class="img-fluid">
        </div>
        <div>
          <p class="mb-0 small fw-semibold text-header-logo">SMA</p>
          <h1 class="h6 fw-bold mb-0 text-header-logo">AL-ISLAM KRIAN</h1>
        </div>
      </header>

     <main class="flex-grow-1 d-flex flex-column justify-content-center align-items-start w-100 mx-4 px-2" style="max-width: 400px;">
    <h2 class="fw-bolder mb-1 fs-3 fs-md-4 fs-lg-2 text-start text-md-start">Selamat Datang</h2>
    <h6 class="fw-medium mb-3 fs-6 fs-md-5 fs-lg-4 text-start text-md-start">di BK Digital</h6>
</main>

    </div>

    <!-- RIGHT PANEL -->
    <div class="col-12 col-md-7 right-panel d-flex align-items-center justify-content-center p-4 p-md-5">
      <form id="loginForm" class="w-100 mx-auto" style="max-width:520px;">
        <h1 class="display-6 fw-bolder mb-5 text-center login-title">Login</h1>

        <div id="statusMessage" class="alert d-none text-center rounded-3 mb-4"></div>

        <!-- Username -->
        <div class="mb-4">
          <label for="username" class="form-label small fw-semibold text-muted mb-1">Username</label>
          <div class="input-group shadow-sm rounded-pill">
            <span class="input-group-text"><i class="fas fa-user text-dark"></i></span>
            <input type="text" id="username" name="username" class="form-control border-0" placeholder="username" required>
          </div>
        </div>

        <!-- Password -->
        <div class="mb-5">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label small fw-semibold text-muted mb-0">Password</label>
            <a href="#" class="small text-decoration-none text-primary" onclick="handleForgotPassword(event)">Lupa password?</a>
          </div>
          <div class="input-group shadow-sm rounded-pill">
            <span class="input-group-text"><i class="fas fa-key text-dark"></i></span>
            <input type="password" id="password" name="password" class="form-control border-0" placeholder="password" required>
            <button type="button" class="input-group-text bg-white" id="togglePassword" style="cursor:pointer;">
              <i class="fa-solid fa-eye-slash text-dark"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow-lg">Login</button>
      </form>
    </div>

  </div>
</div>

  <script src="includes/js/login.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
     ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
</body></html>