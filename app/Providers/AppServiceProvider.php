<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\AcademicYear;
use App\Models\TeachingJournal;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Periode jurnal ditentukan dari tanggal jurnal, jadi jurnal tanggal lampau tetap masuk periode yang benar
        TeachingJournal::saving(function ($journal) {
            if (!$journal->academic_year_id || $journal->isDirty('journal_date')) {
                $journal->academic_year_id = AcademicYear::forDate($journal->journal_date)?->id;
            }
        });
    }
}
