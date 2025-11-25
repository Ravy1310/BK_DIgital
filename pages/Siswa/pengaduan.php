<?php
session_start();

$verified_id = $_GET['idsiswa'] ?? null;
$from_anon   = isset($_GET['anonim']) ? (int)$_GET['anonim'] : 0;

// Validasi id_siswa jika teridentifikasi
if(!$from_anon && empty($verified_id)){
    echo "<div class='alert alert-danger'>Teridentifikasi tapi ID siswa tidak valid.</div>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buat Pengaduan | BK Digital</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body{
        font-family:'Poppins',sans-serif;
        background:url('../../assets/image/background.jpg') center/cover no-repeat;
        height:100vh;
        margin:0;
        display:flex;
        align-items:center;
        justify-content:center;
        padding:20px;
    }
    .form-container{
        width:100%;
        max-width:680px;
        background:white;
        padding:30px;
        border-radius:12px;
        box-shadow:0 8px 18px rgba(0,0,0,0.1);
        animation:fade .4s ease-out forwards;
        opacity:0; transform:translateY(-8px);
    }
    @keyframes fade{ to{opacity:1; transform:translateY(0);} }

    .float-popup{
        position:fixed;
        left:20px;
        top:120px;
        padding:12px 18px;
        border-radius:10px;
        display:flex;
        align-items:center;
        gap:10px;
        opacity:0;
        transform:translateX(-10px);
        transition:.35s;
        z-index:9999;
    }
    .float-popup.show{ opacity:1; transform:translateX(0); }
    .float-popup.success{ background:#d1e7dd; color:#0f5132; }
    .float-popup.error{ background:#f8d7da; color:#842029; }
    .float-popup .icon{
        width:28px; height:28px;
        display:flex; justify-content:center; align-items:center;
        background:rgba(255,255,255,0.4);
        border-radius:6px;
    }
</style>
</head>
<body>

<?php
$popup = null;
if(isset($_SESSION['success'])){
    $popup=['type'=>'success','text'=>$_SESSION['success']];
    unset($_SESSION['success']);
}elseif(isset($_SESSION['error'])){
    $popup=['type'=>'error','text'=>$_SESSION['error']];
    unset($_SESSION['error']);
}
?>

<?php if(!empty($popup)): ?>
<div id="floatPopup" class="float-popup <?= $popup['type'] ?>">
    <div class="icon">
        <?= $popup['type']=='success' ? "<i class='bi bi-check-lg'></i>" : "<i class='bi bi-x-lg'></i>" ?>
    </div>
    <div><?= htmlspecialchars($popup['text']) ?></div>
</div>
<?php endif; ?>

<div class="form-container">
    <h4 class="mb-3 fw-bold text-primary">Buat Pengaduan</h4>

    <form id="formPengaduan" method="POST" action="../../includes/pengaduan_controller.php">

        <!-- Hidden input -->
        <input type="hidden" name="id_siswa" value="<?= $from_anon ? 0 : $verified_id ?>">
        <input type="hidden" id="anonimInput" name="anonim" value="<?= $from_anon ?>">

        <div class="mb-3">
            <label class="form-label">Jenis Laporan</label>
            <select id="jenisAduan" name="jenis_laporan" class="form-select" required>
                <option disabled selected>Pilih jenis laporan</option>
                <option value="Anonim" <?= $from_anon?'selected':'' ?>>Anonim</option>
                <option value="Teridentifikasi" <?= !$from_anon?'selected':'' ?>>Teridentifikasi</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Jenis Kejadian</label>
            <select name="jenis_kejadian" class="form-select" required>
                <option disabled selected>Pilih jenis kejadian</option>
                <option value="Bully">Bully</option>
                <option value="Kekerasan Fisik">Kekerasan Fisik</option>
                <option value="Kekerasan Verbal">Kekerasan Verbal</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Penjelasan</label>
            <textarea name="penjelasan" class="form-control" rows="5" required></textarea>
        </div>

        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" onclick="location.href='dashboard_siswa.php'">Batal</button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#konfirmasiModal">Kirim</button>
        </div>

    </form>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="konfirmasiModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pengiriman</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Apakah semua data sudah benar?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button id="confirmSendBtn" class="btn btn-primary">Ya, Kirim</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Popup otomatis
    const fp = document.getElementById("floatPopup");
    if(fp){ setTimeout(()=> fp.classList.add("show"), 100); setTimeout(()=> fp.classList.remove("show"), 4000); }

    // Update hidden input anonim saat pilih jenis laporan
    document.getElementById("jenisAduan").addEventListener("change", e => {
        document.getElementById("anonimInput").value = e.target.value === "Anonim" ? 1 : 0;
    });

    // Submit form setelah konfirmasi
    document.getElementById("confirmSendBtn").onclick = ()=> {
        const f = document.getElementById("formPengaduan");
        if(!f.checkValidity()){
            showPopup("error","Semua field wajib diisi!");
            return;
        }
        f.submit();
    };

    // Fungsi popup error/success dinamis
    function showPopup(type,text){
        const el=document.createElement("div");
        el.className="float-popup "+type+" show";
        el.innerHTML="<div class='icon'><i class='bi "+
            (type=="error"?"bi-x-lg":"bi-check-lg") +
            "'></i></div><div>"+text+"</div>";
        document.body.appendChild(el);
        setTimeout(()=>el.remove(),3500);
    }
</script>

</body>
</html>
