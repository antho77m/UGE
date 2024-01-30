<?php

$includegraph = false;
$array_date = array();
$array_montant = array();
array_push($array_date, $date);
for ($i = 0; $i < 14; $i++) {
    $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    array_push($array_date, $date);
}
$array_date = array_reverse($array_date);
foreach ($array_date as $date_array) {
    $sql = $cnx->prepare("SELECT 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+' AND date_traitement <= :date), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-' AND date_traitement <= :date), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE SIREN LIKE :siren
        GROUP BY SIREN");
    $sql->bindParam(':siren', $SIREN);
    $sql->bindParam(':date', $date_array);
    $sql->execute();
    $result = $sql->fetch(PDO::FETCH_OBJ);
    if (!empty($result)) {
        $row = $result->montant_total;
        array_push($array_montant, (int) $row);
        $includegraph = true;
    }
}
if ($includegraph) {
    include("graphics/treasury_linear.php");
}
?>
