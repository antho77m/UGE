<?php
    session_start();
    if (isset($_GET['format']) && isset($_GET['date']) && isset($_SESSION['tab_treasury'])) {
        $tab = $_SESSION['tab_treasury'];
        $format = $_GET['format'];
        $date = $_GET['date'];

        if ($format == 'CSV') 
        {
            header("Content-Type: application/csv");
            header("Content-Disposition: attachment; filename=IMPAYES ".date('d/m/Y').".csv;");
            $file = fopen('php://output', 'w');
            fputcsv($file, ["SIREN","Raison sociale","Nombre de transactions","Montant total"], ';');
            foreach($tab AS $solde) {
                foreach($solde as $ligne) {
                    fputcsv($file, [$ligne->SIREN,$ligne->Raison_sociale,$ligne->nbT,$ligne->montant_total], ';');
                }
            }
            fputcsv($file, ["Solde de compte à la date: $date"]);
            fputcsv($file, ["EXTRAIT DU ".date('d/m/Y')], ';');
            fclose($file);
        } 
        else if ($format == 'XLS') 
        {
            $excel = "SIREN\tRaison sociale\tNombre de transactions\tMontant total\n";
            foreach($tab AS $solde) {
                foreach($solde as $ligne) {
                    $excel .= $ligne->SIREN."\t".$ligne->Raison_sociale."\t".$ligne->nbT."\t".$ligne->montant_total."\n";
                }
            }
            $excel .= "Solde de compte à la date: $date\n";
            $excel .= "EXTRAIT DU ".date('d/m/Y');
            header("Content-type: application/application/vnd.ms-excel");
            header("Content-disposition: attachment; filename=IMPAYES ".date('d/m/Y').".xls");
            print $excel;

        }
        else if ($format == 'PDF') {
            echo "<table border=\"1\"><tr><th>SIREN</th><th>Raison sociale</th><th>Nombre de transactions</th><th>Montant total</th></tr>";
            foreach($tab AS $solde) {
                foreach($solde as $ligne) {
                    echo "<tr><td>$ligne->SIREN</td><td>$ligne->Raison_sociale</td><td>$ligne->nbT</td><td>$ligne->montant_total</td></tr>";
                }
            }
            echo "</table>";
            echo "<br>Solde de compte à la date: $date";
            echo "<br>EXTRAIT DU ".date('d/m/Y');
            echo "<script>window.print()</script>";
        }
    }
?>