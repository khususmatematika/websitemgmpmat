<?php
namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public array $errors = [];
    public int $successCount = 0;

    public function collection($rows)
    {
        foreach ($rows as $i => $row) {
            $rowNum = $i + 2; // +2 karena heading row + index dari 0

            $nis = trim((string) ($row['nis'] ?? ''));
            $name = trim((string) ($row['nama'] ?? ''));
            $kelasReguler = trim((string) ($row['kelas_reguler'] ?? ''));
            $kelasPilihan = trim((string) ($row['kelas_pilihan'] ?? ''));

            if (empty($name)) {
                $this->errors[] = "Baris {$rowNum}: Nama wajib diisi, dilewati.";
                continue;
            }

            $student = Student::updateOrCreate(
                $nis ? ['nis' => $nis] : ['name' => $name],
                ['name' => $name, 'nis' => $nis ?: null]
            );

            $classIds = [];

            if ($kelasReguler) {
                $class = SchoolClass::where('name', $kelasReguler)->first();
                if ($class) {
                    $classIds[] = $class->id;
                } else {
                    $this->errors[] = "Baris {$rowNum}: Kelas reguler '{$kelasReguler}' tidak ditemukan.";
                }
            }

            if ($kelasPilihan) {
                $class = SchoolClass::where('name', $kelasPilihan)->first();
                if ($class) {
                    $classIds[] = $class->id;
                } else {
                    $this->errors[] = "Baris {$rowNum}: Kelas pilihan '{$kelasPilihan}' tidak ditemukan.";
                }
            }

            if (!empty($classIds)) {
                $student->classes()->syncWithoutDetaching($classIds);
            }

            $this->successCount++;
        }
    }
}