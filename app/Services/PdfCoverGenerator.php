<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;

class PdfCoverGenerator
{
    /**
     * Generate gambar halaman pertama PDF. Return path relatif (disk 'public') atau null kalau gagal/Imagick tidak ada.
     */
    public function generate(string $pdfPath): ?string
    {
        if (!extension_loaded('imagick')) {
            return null;
        }

        try {
            $fullPdfPath = Storage::disk('public')->path($pdfPath);
            $imagick = new \Imagick();
            $imagick->setResolution(150, 150);
            $imagick->readImage($fullPdfPath . '[0]'); // halaman pertama
            $imagick->setImageFormat('jpg');
            $imagick->setImageCompressionQuality(80);

            $coverFilename = 'materials-cover/' . uniqid() . '.jpg';
            $fullCoverPath = Storage::disk('public')->path($coverFilename);

            if (!is_dir(dirname($fullCoverPath))) {
                mkdir(dirname($fullCoverPath), 0755, true);
            }

            $imagick->writeImage($fullCoverPath);
            $imagick->clear();
            $imagick->destroy();

            return $coverFilename;
        } catch (\Exception $e) {
            \Log::warning('Gagal generate cover PDF: ' . $e->getMessage());
            return null;
        }
    }
}