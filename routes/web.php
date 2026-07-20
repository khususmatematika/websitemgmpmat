<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Public\TeacherPublicController;
use App\Http\Controllers\Public\MaterialPublicController;
use App\Http\Controllers\Public\ToolkitPublicController;
use App\Http\Controllers\Public\StudentWorkController;
use App\Http\Controllers\Public\ForumController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GuruAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Guru\GuruDashboardController;
use App\Http\Controllers\Guru\GuruProfileController;
use App\Http\Controllers\Guru\GuruMaterialController;
use App\Http\Controllers\Guru\GuruDigitalLessonController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTeacherController;
use App\Http\Controllers\Admin\AdminMaterialController;
use App\Http\Controllers\Admin\AdminDigitalLessonController;
use App\Http\Controllers\Admin\AdminToolkitController;
use App\Http\Controllers\Admin\AdminStudentWorkController;
use App\Http\Controllers\Admin\AdminReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\BankSoalController;
use App\Http\Controllers\Public\LatihanController;
use App\Http\Controllers\Guru\GuruBankSoalController;
use App\Http\Controllers\Admin\AdminBankSoalController;
use App\Http\Controllers\Admin\AdminMaterialTopicController;
use App\Http\Controllers\Guru\GuruJournalController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminClassController;
use App\Http\Controllers\Admin\AdminLetterheadController;
use App\Http\Controllers\Guru\GuruModuleGeneratorController;

// ================= BERANDA =================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ================= LOGIN GABUNGAN (Guru & Admin otomatis dikenali) =================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================= PUBLIK: Profil Guru =================
Route::get('/profil-guru', [TeacherPublicController::class, 'index'])->name('teachers.public');

// ================= PUBLIK: Materi, Pembelajaran Digital, Toolkit =================
Route::get('/materi', [MaterialPublicController::class, 'index'])->name('materials.public');
Route::get('/materi/{material}/preview', [MaterialPublicController::class, 'preview'])->name('materials.preview');
Route::get('/pembelajaran-digital', [MaterialPublicController::class, 'digitalLessons'])->name('digital-lessons.public');
Route::get('/toolkit', [ToolkitPublicController::class, 'index'])->name('toolkits.public');

// ================= PUBLIK: Karya Siswa =================
Route::get('/karya-siswa', [StudentWorkController::class, 'index'])->name('student-works.public');
Route::get('/karya-siswa/upload', [StudentWorkController::class, 'create'])->name('student-works.create');
Route::post('/karya-siswa', [StudentWorkController::class, 'store'])->name('student-works.store');
Route::post('/karya-siswa/{studentWork}/like', [StudentWorkController::class, 'like'])->name('student-works.like');
Route::post('/karya-siswa/{studentWork}/comment', [StudentWorkController::class, 'comment'])->name('student-works.comment');
Route::post('/karya-siswa/{studentWork}/report', [StudentWorkController::class, 'report'])->name('student-works.report');
Route::post('/karya-siswa/comment/{comment}/report', [StudentWorkController::class, 'reportComment'])->name('student-work-comments.report');

// ================= PUBLIK: Forum Diskusi =================
Route::get('/forum', [ForumController::class, 'index'])->name('forum.public');
Route::get('/forum/buat', [ForumController::class, 'create'])->name('forum.create');
Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
Route::post('/forum/posts/{post}/like', [ForumController::class, 'likePost'])->name('forum.posts.like');
Route::post('/forum/comments/{comment}/like', [ForumController::class, 'likeComment'])->name('forum.comments.like');
Route::post('/forum/posts/{post}/comment', [ForumController::class, 'comment'])->name('forum.posts.comment');
Route::post('/forum/posts/{post}/report', [ForumController::class, 'reportPost'])->name('forum.posts.report');
Route::post('/forum/comments/{comment}/report', [ForumController::class, 'reportComment'])->name('forum.comments.report');

// ================= PUBLIK: Bank Soal & Latihan =================
Route::get('/bank-soal', [BankSoalController::class, 'index'])->name('bank-soal.public');
Route::post('/bank-soal/generate', [BankSoalController::class, 'generate'])->name('bank-soal.generate');

Route::get('/latihan', [LatihanController::class, 'create'])->name('latihan.create');
Route::post('/latihan/mulai', [LatihanController::class, 'start'])->name('latihan.start');
Route::get('/latihan/{quizSession}', [LatihanController::class, 'show'])->name('latihan.show');
Route::post('/latihan/{quizSession}/selesai', [LatihanController::class, 'finish'])->name('latihan.finish');
Route::get('/leaderboard', [LatihanController::class, 'leaderboard'])->name('leaderboard.public');

// ================= GURU =================
Route::prefix('guru')->name('guru.')->group(function () {
    Route::get('/login', [GuruAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [GuruAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [GuruAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:guru')->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profil', [GuruProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profil', [GuruProfileController::class, 'update'])->name('profile.update');

        Route::resource('materi', GuruMaterialController::class)->except(['show']);
        Route::resource('pembelajaran-digital', GuruDigitalLessonController::class)->except(['show']);
        Route::resource('bank-soal', GuruBankSoalController::class)->except(['show']);

        Route::get('/jurnal', [GuruJournalController::class, 'index'])->name('jurnal.index');
        Route::get('/jurnal/cetak', [GuruJournalController::class, 'printPdf'])->name('jurnal.print');
        Route::get('/jurnal/isi/{teacherClass}', [GuruJournalController::class, 'create'])->name('jurnal.create');
        Route::post('/jurnal/isi/{teacherClass}', [GuruJournalController::class, 'store'])->name('jurnal.store');
        Route::get('/jurnal/{teachingJournal}/edit', [GuruJournalController::class, 'edit'])->name('jurnal.edit');
        Route::put('/jurnal/{teachingJournal}', [GuruJournalController::class, 'update'])->name('jurnal.update');

        Route::get('/kehadiran', [GuruJournalController::class, 'attendance'])->name('kehadiran.index');

        Route::get('/modul-ajar', [GuruModuleGeneratorController::class, 'index'])->name('modul-ajar.index');
        Route::get('/modul-ajar/buat', [GuruModuleGeneratorController::class, 'create'])->name('modul-ajar.create');
        Route::post('/modul-ajar', [GuruModuleGeneratorController::class, 'store'])->name('modul-ajar.store');
        Route::get('/modul-ajar/{aiGeneratedModule}', [GuruModuleGeneratorController::class, 'show'])->name('modul-ajar.show');
        Route::post('/modul-ajar/{aiGeneratedModule}/generate-step', [GuruModuleGeneratorController::class, 'generateStep'])->name('modul-ajar.generate-step');
        Route::get('/modul-ajar/{aiGeneratedModule}/cetak', [GuruModuleGeneratorController::class, 'printPdf'])->name('modul-ajar.print');
        Route::delete('/modul-ajar/{aiGeneratedModule}', [GuruModuleGeneratorController::class, 'destroy'])->name('modul-ajar.destroy');
    });
});

// ================= ADMIN =================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('guru', AdminTeacherController::class)->except(['show']);
        Route::post('/guru/{guru}/reset-password', [AdminTeacherController::class, 'resetPassword'])->name('guru.reset-password');

        Route::resource('siswa', AdminStudentController::class)->except(['show']);
        Route::get('/siswa-import/template', [AdminStudentController::class, 'downloadTemplate'])->name('siswa.import.template');
        Route::post('/siswa-import', [AdminStudentController::class, 'import'])->name('siswa.import');

        Route::resource('kelas', AdminClassController::class)->except(['show']);

        Route::resource('materi', AdminMaterialController::class)->except(['show']);

        Route::get('/topik-materi', [AdminMaterialTopicController::class, 'index'])->name('topik-materi.index');
        Route::post('/topik-materi', [AdminMaterialTopicController::class, 'store'])->name('topik-materi.store');
        Route::delete('/topik-materi/{topik_materi}', [AdminMaterialTopicController::class, 'destroy'])->name('topik-materi.destroy');

        Route::resource('pembelajaran-digital', AdminDigitalLessonController::class)->except(['show']);
        Route::resource('toolkit', AdminToolkitController::class)->except(['show']);

        Route::get('/karya-siswa', [AdminStudentWorkController::class, 'index'])->name('karya-siswa.index');
        Route::post('/karya-siswa/{studentWork}/approve', [AdminStudentWorkController::class, 'approve'])->name('karya-siswa.approve');
        Route::post('/karya-siswa/{studentWork}/reject', [AdminStudentWorkController::class, 'reject'])->name('karya-siswa.reject');
        Route::delete('/karya-siswa/{studentWork}', [AdminStudentWorkController::class, 'destroy'])->name('karya-siswa.destroy');

        Route::get('/laporan', [AdminReportController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/{report}/resolve', [AdminReportController::class, 'resolve'])->name('laporan.resolve');

        Route::resource('bank-soal', AdminBankSoalController::class)->except(['show']);

        Route::get('/kop-surat', [AdminLetterheadController::class, 'edit'])->name('letterhead.edit');
        Route::put('/kop-surat', [AdminLetterheadController::class, 'update'])->name('letterhead.update');
    });
});