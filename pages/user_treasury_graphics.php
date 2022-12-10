<!DOCTYPE HTML>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graphique</title>

    <script src="https://code.highcharts.com/highcharts.js"></script>

<style type="text/css">
    .highcharts-figure,
    .highcharts-data-table table {
        width: 100%;
        margin: 1em auto;
        z-index: 1;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }
</style>
</head>

<body>

<?php 
    $array_date = array();
    $array_montant = array();
    array_push($array_date, $date);
    for($i = 0; $i < 14 ; $i++){
        $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        array_push($array_date, $date);
    }
    $array_date = array_reverse($array_date);
    foreach($array_date as $date_array){
        $sql = $cnx->prepare("SELECT 
        COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='+' AND date_traitement <= '$date_array'), 0) - COALESCE((SELECT SUM(montant) FROM Transaction WHERE SIREN=R.SIREN AND sens='-' AND date_traitement <= '$date_array'), 0) AS montant_total
        FROM Commercant AS R NATURAL JOIN Transaction
        WHERE date_traitement <= '$date_array' AND SIREN LIKE '%$SIREN'
        GROUP BY SIREN");
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_OBJ);
        $row = $result->montant_total;
        array_push($array_montant, (int) $row);
    }
    include("graphics/treasury_linear.php");
?>

</body>
</html>