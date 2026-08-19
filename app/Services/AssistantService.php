<?php
namespace App\Services;

class AssistantService
{
    protected array $faq = [
        'cara upload karya' => 'Untuk upload karya: masuk (login) sebagai siswa/guru, buka menu Karya Siswa, klik tombol "Unggah Karya", isi deskripsi dan pilih file (JPG/PNG/MP4/PDF, maks 20MB). Karya akan tayang setelah disetujui Admin.',
        'cara cek nilai' => 'Untuk cek nilai: klik tombol "Masuk" di navbar, login pakai NIS (siswa) atau email (guru), lalu buka menu Cek Nilai dan Kehadiran.',
        'lupa password' => 'Jika lupa password, silakan hubungi Admin sekolah untuk direset, atau gunakan NIS sebagai password default jika belum pernah diganti.',
        'cara ikut latihan' => 'Untuk mengikuti Latihan: login terlebih dahulu (siswa/guru), buka menu Latihan, pilih materi, lalu sistem akan membuatkan 10 soal secara otomatis.',
    ];

    public function answer(string $question): array
    {
        $normalized = strtolower(trim($question));

        foreach ($this->faq as $keyword => $answer) {
            if (str_contains($normalized, $keyword)) {
                return ['source' => 'faq', 'answer' => $answer];
            }
        }

        return ['source' => 'ai', 'answer' => $this->askGemini($question)];
    }

    protected function askGemini(string $question): string
    {
        $prompt = <<<PROMPT
Kamu adalah "Asisten Portal", asisten virtual ramah untuk "SMAN 1 Turen Math Portal" — website sekolah untuk pembelajaran matematika siswa SMA.

Kamu bisa membantu dua hal:
1. Pertanyaan seputar MATEMATIKA (konsep, rumus, cara mengerjakan soal, penjelasan materi X/XI/XII sesuai Kurikulum Merdeka) — jawab dengan jelas dan mudah dipahami siswa SMA, sertakan contoh singkat jika membantu.
2. Pertanyaan seputar CARA PAKAI WEBSITE ini. Fitur yang tersedia: Materi (PDF per kelas/semester), Pembelajaran Digital (video/simulasi), Profil Guru, Toolkit (kalkulator), Karya Siswa (galeri, wajib login untuk upload/like/komentar), Forum Diskusi (wajib login untuk posting/like/komentar), Bank Soal (soal dari guru & AI), Latihan AI (kuis 10 soal adaptif, wajib login), Cek Nilai & Kehadiran (login pakai NIS untuk siswa, email untuk guru/admin), Jurnal Mengajar & Generator Modul Ajar AI (khusus guru).

Aturan jawaban:
- Bahasa Indonesia, ramah, dan ringkas (maksimal 4-5 kalimat, atau poin singkat jika perlu langkah-langkah).
- Untuk soal matematika, boleh gunakan notasi sederhana (mis. x^2, akar(x), pecahan a/b) karena tampil sebagai teks biasa.
- Jika pertanyaan di luar topik matematika dan di luar website ini (misalnya politik, gosip, hal pribadi), tolak dengan sopan dan arahkan kembali ke topik matematika/website.
- Jangan mengarang fitur yang tidak disebutkan di atas.

Pertanyaan pengguna: "{$question}"

Balas HANYA dengan teks jawaban polos, tanpa format JSON, tanpa markdown heading, tanpa tanda kutip pembuka/penutup.
PROMPT;

        try {
            $gemini = new GeminiService();
            $answer = $gemini->call($prompt);
            return trim($answer, " \t\n\r\0\x0B\"");
        } catch (\Exception $e) {
            \Log::warning('Assistant Gemini error: ' . $e->getMessage());
            return 'Maaf, asisten sedang tidak bisa menjawab saat ini. Coba lagi beberapa saat, atau hubungi Admin.';
        }
    }
}