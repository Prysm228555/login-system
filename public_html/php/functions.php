<?php
/**
 * calculate the average of the bts mock exam
 *
 * @param array $notes grade chart
 * @param int|null $round rounding of average
 * @return float average of the bts mock exam
 */
function btsBlanc(array $notes, ?int $round = null): float{
    $somme = 0;
    $totalCoef = 0;
    foreach($notes as $note){
        if ($note['note'] != null){
            $totalCoef += $note['coef'];
            $somme += $note['note'] * $note['coef'];
        }
    }
    if ($round != null){
        return round($somme / $totalCoef, $round);
    }
    return $somme / $totalCoef;
}
?>