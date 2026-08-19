<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * Generate soal pilihan ganda matematika.
     * Return array of: ['question' => ..., 'options' => ['A'=>..,'B'=>..,'C'=>..,'D'=>..], 'correct_answer' => 'A', 'explanation' => ...]
     */
    public function generateQuestions(string $jenjangLabel, string $topic, int $count = 10): array
    {
        $prompt = <<<PROMPT
Buatkan {$count} soal pilihan ganda matematika SMA untuk {$jenjangLabel}, topik "{$topic}".
Soal harus kontekstual, bervariasi tingkat kesulitan, dan sesuai Kurikulum Merdeka.

Balas HANYA dengan JSON array valid tanpa teks lain, tanpa markdown code block, dengan format persis:
[
  {
    "question": "teks soal di sini",
    "options": {"A": "pilihan A", "B": "pilihan B", "C": "pilihan C", "D": "pilihan D"},
    "correct_answer": "A",
    "explanation": "penjelasan singkat kenapa jawaban itu benar"
  }
]

Pastikan tepat {$count} soal, correct_answer selalu salah satu dari "A","B","C","D".
PROMPT;

        $raw = $this->call($prompt);
        $questions = json_decode($raw, true);

        if (!is_array($questions) || empty($questions)) {
            throw new \Exception('Gagal memproses hasil dari Gemini API. Coba lagi.');
        }

        return $questions;
    }

    /**
     * Generate SATU batch Modul Ajar (dipanggil berulang oleh controller, bukan sekaligus semua).
     * Return: ['meeting_range' => '1-3', 'content' => '...', 'topic_summary' => '...']
     */
    public function generateModuleBatch(array $formData, int $startMeeting, int $endMeeting, string $topicMapSoFar): array
    {
        $prompt = $this->buildModulePrompt($formData, $startMeeting, $endMeeting, $topicMapSoFar);
        $raw = $this->call($prompt);
        $parsed = json_decode($raw, true);

        if (!is_array($parsed) || empty($parsed['content'])) {
            throw new \Exception("Gagal memproses hasil AI pada bagian pertemuan {$startMeeting}-{$endMeeting}. Coba lagi.");
        }

        return [
            'meeting_range' => "{$startMeeting}-{$endMeeting}",
            'content' => $parsed['content'],
            'topic_summary' => $parsed['topic_summary'] ?? '',
        ];
    }

    protected function buildModulePrompt(array $d, int $start, int $end, string $topicMapSoFar): string
    {
        $refNote = !empty($d['reference_note'])
            ? "Guru melampirkan file referensi tambahan bernama \"{$d['reference_note']}\" sebagai konteks pendukung."
            : '';

        return <<<PROMPT
Buatkan bagian Modul Ajar Kurikulum Merdeka mata pelajaran {$d['mapel']} untuk pertemuan ke-{$start} sampai ke-{$end} dari total {$d['meetings_count']} pertemuan.

Data:
- Sekolah: {$d['school_name']}
- Tahun Ajaran: {$d['academic_year']}, Semester {$d['semester']}
- Fase: {$d['fase']}, Kelas: {$d['kelas']}
- Materi: {$d['materi']}
- Alokasi Waktu per Pertemuan: {$d['duration_per_meeting']} menit
- Model Pembelajaran: {$d['learning_model']}
- Integrasi: {$d['integration']}
- Capaian Pembelajaran: {$d['learning_outcomes']}
{$refNote}

Ringkasan subtopik pertemuan sebelumnya (jaga konsistensi narasi, jangan mengulang materi yang sama): {$topicMapSoFar}

Balas HANYA dengan JSON valid tanpa markdown code block, format persis:
{
  "content": "isi lengkap modul ajar untuk pertemuan ini dalam format HTML sederhana (gunakan tag <h3>, <p>, <ul><li> saja), mencakup: Tujuan Pembelajaran, Langkah Kegiatan (Pendahuluan/Inti/Penutup) per pertemuan, Asesmen, dan LKPD singkat jika relevan.",
  "topic_summary": "ringkasan 1-2 kalimat subtopik yang dibahas di pertemuan ini, untuk menjaga konteks batch berikutnya"
}
PROMPT;
    }

    public function call(string $prompt, int $maxRetries = 3): string
    {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model');

        if (empty($apiKey)) {
            throw new \Exception('GEMINI_API_KEY belum diatur di file .env');
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $response = Http::timeout(60)->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'responseMimeType' => 'application/json',
                ],
            ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');
                // Bersihkan kalau model tetap membungkus dengan ```json ... ```
                $text = preg_replace('/^```json\s*|\s*```$/m', '', trim($text));
                return $text;
            }

            if ($response->status() === 429) {
                Log::warning('Gemini API rate limited, retry ke-' . $attempt);
                sleep(2 ** $attempt); // exponential backoff: 2s, 4s, 8s
                continue;
            }

            Log::error('Gemini API error: HTTP ' . $response->status() . ' - ' . $response->body());
            throw new \Exception('Terjadi kesalahan saat menghubungi Gemini API. Coba lagi nanti.');
        }

        throw new \Exception('Gemini API sedang sibuk (rate limit). Coba lagi beberapa saat lagi.');
    }
}