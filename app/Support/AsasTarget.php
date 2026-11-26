<?php
namespace App\Support;

class AsasTarget
{
    /**
     * @param array      $recap  hasil GradeCalculator::classRecap
     * @param array      $row    baris siswa pada $recap['rows']
     * @param Collection $prev   semester_number => nilai semester lalu
     */
    public static function compute(array $recap, array $row, $prev): array
    {
        $S = 0;
        $W = 0;
        foreach ($recap['topics'] as $t) {
            $w = (float) $recap['topicWeights'][$t->id];
            $S += ($row['finals'][$t->id] ?? 0) * $w;
            $W += $w;
        }
        $a = (float) $recap['asasWeight'];

        $out = [];
        foreach ($prev as $sem => $target) {
            $target = (int) $target;

            if ($a <= 0) {
                $status = 'no_weight';
                $min = null;
            } elseif ($W <= 0) {
                $status = 'no_data';
                $min = null;
            } else {
                $need = (($target - 0.5) * ($W + $a) - $S) / $a;
                $min = max(0, (int) ceil($need - 1e-9));
                $status = $min > 100 ? 'impossible' : ($min === 0 ? 'safe' : 'ok');
            }

            $out[] = [
                'semester' => (int) $sem,
                'label' => SemesterHelper::label((int) $sem),
                'score' => $target,
                'min_asas' => $min,
                'status' => $status,
            ];
        }

        return $out;
    }
}