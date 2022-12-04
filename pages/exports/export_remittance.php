<?php
    session_start();
    if (isset($_GET['format']) && isset($_GET['detail']) && isset($_SESSION['tab_remises']) && isset($_SESSION['tab_remises_detailles'])) {
        $tab1 = $_SESSION['tab_remises'];
        $tab2 = $_SESSION['tab_remises_detailles'];
        $format = $_GET['format'];
        $detailled = $_GET['detail'];

        if ($detailled == 1) {
            $filename = "REMISES DETAILLES";
        } else {
            $filename = "REMISES";
        }

        if ($format == 'CSV') 
        {
            header("Content-Type: application/csv");
            header("Content-Disposition: attachment; filename=$filename ".date('d/m/Y').".csv;");
            $file = fopen('php://output', 'w');
            if ($detailled == 1) 
            {
                foreach($tab1 AS $ligne) {
                    fputcsv($file, ["LISTE DES TRANSACTIONS DE LA REMISE DE L'ENTREPRISE ".$ligne[1].", No DE SIREN ".$ligne[0]." LE ".$ligne[3]], ';');
                    fputcsv($file, ["SIREN", "Date vente", "Numero Carte", "Reseau", "Numero Autorisation", "Devise", "Montant", "Sens"], ';');
                    foreach($tab2 AS $remises) {
                        foreach($remises AS $remise) {
                            if ($remise['SIREN'] == $ligne[0] && $ligne[3] == $remise['date_traitement']) {
                                fputcsv($file, [$remise['SIREN'], $remise['date_vente'], $remise['num_carte'], $remise['reseau'], $remise['num_autorisation'], "EUR", $remise['montant'], $remise['sens']], ';');
                            }
                        }
                    }
                    fputcsv($file, [""], ';');
                }
            } 
            else 
            {
                fputcsv($file, ["SIREN","Raison Sociale","Numero Remise","Date traitement","Nombre de transactions","Devise","Montant Total"], ';');
                foreach($tab1 AS $ligne) {
                    fputcsv($file, $ligne, ';');
                }
            }
            fputcsv($file, ["EXTRAIT DU ".date('d/m/Y')], ';');
            fclose($file);
        } 
        else if ($format == 'XLS') 
        {
            if ($detailled == 1) 
            {
                $excel = "";
                foreach($tab1 AS $ligne) {
                    $excel .= "LISTE DES TRANSACTIONS DE LA REMISE DE L'ENTREPRISE ".$ligne[1].", No DE SIREN ".$ligne[0]." LE ".$ligne[3]."\n";
                    $excel .= "SIREN\tDate vente\tNumero Carte\tReseau\tNumero Autorisation\tDevise\tMontant\tSens\n";
                    foreach($tab2 AS $remises) {
                        foreach($remises AS $remise) {
                            if ($remise['SIREN'] == $ligne[0] && $ligne[3] == $remise['date_traitement']) {
                                $excel .= $remise['SIREN']."\t".$remise['date_vente']."\t".$remise['num_carte']."\t".$remise['reseau']."\t".$remise['num_autorisation']."\tEUR\t".$remise['montant']."\t".$remise['sens']."\n";
                            }
                        }
                    }
                }
            } 
            else 
            {
                $excel = "SIREN\tRaison Sociale\tNumero Remise\tDate traitement\tNombre de transactions\tDevise\tMontant total\n";
                foreach($tab1 AS $ligne) {
                    $excel .= $ligne[0]."\t".$ligne[1]."\t".$ligne[2]."\t".$ligne[3]."\t".$ligne[4]."\t".$ligne[5]."\t".$ligne[6]."\n";
                }
            }
            $excel .= "EXTRAIT DU ".date('d/m/Y');
            header("Content-type: application/application/vnd.ms-excel");
            header("Content-disposition: attachment; filename=$filename ".date('d/m/Y').".xls");
            print $excel;
        }
        else if ($format == 'PDF') 
        {
            if ($detailled == 1) 
            {
                foreach($tab1 AS $ligne) {
                    echo "LISTE DES TRANSACTIONS DE LA REMISE DE L'ENTREPRISE ".$ligne[1].", No DE SIREN ".$ligne[0]." LE ".$ligne[3]."<br>";
                    echo "<table border=\"1\"><tr><th>SIREN</th><th>Date vente</th><th>Numero Carte</th><th>Reseau</th><th>Numero Autorisation</th><th>Devise</th><th>Montant</th><th>Sens</th></tr>";
                    foreach($tab2 AS $remises) {
                        foreach($remises AS $remise) {
                            if ($remise['SIREN'] == $ligne[0] && $ligne[3] == $remise['date_traitement']) {
                                echo "<tr><td>".$remise['SIREN']."</td><td>".$remise['date_vente']."</td><td>".$remise['num_carte']."</td><td>".$remise['reseau']."</td><td>".$remise['num_autorisation']."</td><td>EUR</td><td>".$remise['montant']."</td><td>".$remise['sens']."</td>";
                            }
                        }
                    }
                    echo "</table><br>";
                }
            }
            else 
            {
                echo "<table border=\"1\"><tr><th>SIREN</th><th>Raison Sociale</th><th>Numero Remise</th><th>Date traitement</th><th>Nombre de transactions</th><th>Devise</th><th>Montant Total</th></tr>";
                foreach($tab1 AS $ligne) {
                    echo "<tr><td>".$ligne[0]."</td><td>".$ligne[1]."</td><td>".$ligne[2]."</td><td>".$ligne[3]."</td><td>".$ligne[4]."</td><td>".$ligne[5]."</td><td>".$ligne[6]."</td></tr>";
                }
            }
            echo "</table>";
            echo "<br>EXTRAIT DU ".date('d/m/Y');
            echo "<script>window.print()</script>";
        }
    }
?>