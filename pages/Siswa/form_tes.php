<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soal Tes Bakat Minat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #e9f0fa;
            padding-top: 40px;
        }
        .title-box {
            background: #003893;
            color: white;
            padding: 12px;
            border-radius: 8px;
        }
        .question-card {
            background: #f7f7f7;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .question-box {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
            margin-bottom: 15px;
        }
        .question-box:last-child {
            border-bottom: none;
        }
        .scale-number {
            font-size: 13px;
            color: #444;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- Judul -->
    <div class="text-center mb-4">
        <div class="title-box">
            <h4 class="m-0">Soal Tes Bakat Minat</h4>
        </div>
    </div>

    <!-- Card Soal -->
    <div class="question-card">

        <!-- Soal 1 -->
        <div class="question-box">
            <p>Saya merasa senang saat belajar pelajaran baru.</p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="scale-number">1</span>
                <input type="radio" name="q1">
                <input type="radio" name="q1">
                <input type="radio" name="q1">
                <input type="radio" name="q1">
                <input type="radio" name="q1">
                <span class="scale-number">5</span>
            </div>
        </div>

        <!-- Soal 2 -->
        <div class="question-box">
            <p>Saya memperhatikan dengan antusias saat guru/dosen menjelaskan materi.</p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="scale-number">1</span>
                <input type="radio" name="q2">
                <input type="radio" name="q2">
                <input type="radio" name="q2">
                <input type="radio" name="q2">
                <input type="radio" name="q2">
                <span class="scale-number">5</span>
            </div>
        </div>

        <!-- Soal 3 -->
        <div class="question-box">
            <p>Saya menikmati belajar walaupun tidak ada ujian atau tugas.</p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="scale-number">1</span>
                <input type="radio" name="q3">
                <input type="radio" name="q3">
                <input type="radio" name="q3">
                <input type="radio" name="q3">
                <input type="radio" name="q3">
                <span class="scale-number">5</span>
            </div>
        </div>

        <!-- Soal 4 -->
        <div class="question-box">
            <p>Saya merasa waktu belajar adalah waktu yang menyenangkan.</p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="scale-number">1</span>
                <input type="radio" name="q4">
                <input type="radio" name="q4">
                <input type="radio" name="q4">
                <input type="radio" name="q4">
                <input type="radio" name="q4">
                <span class="scale-number">5</span>
            </div>
        </div>

        <!-- Soal 5 -->
        <div class="question-box">
            <p>Saya belajar karena ingin mencapai cita-cita saya.</p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="scale-number">1</span>
                <input type="radio" name="q5">
                <input type="radio" name="q5">
                <input type="radio" name="q5">
                <input type="radio" name="q5">
                <input type="radio" name="q5">
                <span class="scale-number">5</span>
            </div>
        </div>

        <!-- Soal 6 -->
        <div class="question-box">
            <p>Jika nilai saya rendah, saya berusaha memperbaikinya.</p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="scale-number">1</span>
                <input type="radio" name="q6">
                <input type="radio" name="q6">
                <input type="radio" name="q6">
                <input type="radio" name="q6">
                <input type="radio" name="q6">
                <span class="scale-number">5</span>
            </div>
        </div>

    </div>

    <!-- Tombol -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn btn-primary px-4">Submit</button>
        <button class="btn btn-link text-danger">Clear Form</button>
    </div>

</div>

</body>
</html>
