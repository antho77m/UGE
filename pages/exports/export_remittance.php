<?php
    session_start();
    if (isset($_GET['format']) && isset($_GET['detail']) && isset($_SESSION['tab_remises']) && isset($_SESSION['tab_remises_detailles'])) {
        $tab1 = $_SESSION['tab_remises'];
        $tab2 = $_SESSION['tab_remises_detailles'];
        $format = $_GET['format'];
        $detailled = $_GET['detail'];

        if ($detailled == 1) { // si detailled = 1, soit on demande le fichier des remises détaillées 
            $filename = "REMISES DETAILLES";
        } else {
            $filename = "REMISES";
        }

        if ($format == 'CSV') // si le format demandé est CSV
        {
            header("Content-Type: application/csv"); // récupère le type/format demandé
            header("Content-Disposition: attachment; filename=$filename ".date('d/m/Y').".csv;"); // créer le fichier avec le nom entré dans filename
            $file = fopen('php://output', 'w');
            if ($detailled == 1) // si on veut un fichier avec détail des remises
            {
                foreach($tab1 AS $ligne) { // parcours le tableau des remises
                    fputcsv($file, ["LISTE DES TRANSACTIONS DE LA REMISE DE L'ENTREPRISE ".$ligne[1].", No DE SIREN ".$ligne[0]." LE ".$ligne[3]], ';');
                    fputcsv($file, ["SIREN", "Date vente", "Numero Carte", "Reseau", "Numero Autorisation", "Devise", "Montant", "Sens"], ';');
                    foreach($tab2 AS $remises) { // parcours les remises
                        foreach($remises AS $remise) { // parcours le détail des transactions de chaque remise
                            if ($remise['SIREN'] == $ligne[0] && $ligne[3] == $remise['date_traitement']) { // si le SIREN et la date de traitement correspond à la remise ($remise)
                                fputcsv($file, [$remise['SIREN'], $remise['date_vente'], $remise['num_carte'], $remise['reseau'], $remise['num_autorisation'], "EUR", $remise['montant'], $remise['sens']], ';'); // ajoute dans le fichier ($file) le tableau case par case
                            }
                        }
                    }
                    fputcsv($file, [""], ';'); // génère un espace entre deux lignes
                }
            } 
            else 
            {
                fputcsv($file, ["SIREN","Raison Sociale","Numero Remise","Date traitement","Nombre de transactions","Devise","Montant Total"], ';');
                foreach($tab1 AS $ligne) { // parcours le tableau des remises
                    fputcsv($file, $ligne, ';'); // ajoute dans le fichier ($file) le tableau ($ligne) case par case
                }
            }
            fputcsv($file, ["EXTRAIT DU ".date('d/m/Y')], ';');
            fclose($file);
        } 
        else if ($format == 'XLS') // si le format demandé est XLS 
        {
            if ($detailled == 1) // si on veut un fichier avec détail des remises 
            {
                $excel = "";
                foreach($tab1 AS $ligne) { // parcours le tableau des remises
                    $excel .= "LISTE DES TRANSACTIONS DE LA REMISE DE L'ENTREPRISE ".$ligne[1].", No DE SIREN ".$ligne[0]." LE ".$ligne[3]."\n";
                    $excel .= "SIREN\tDate vente\tNumero Carte\tReseau\tNumero Autorisation\tDevise\tMontant\tSens\n";
                    foreach($tab2 AS $remises) { // parcours les remises
                        foreach($remises AS $remise) { // parcours le détail des transactions de chaque remise
                            if ($remise['SIREN'] == $ligne[0] && $ligne[3] == $remise['date_traitement']) { // si le SIREN et la date de traitement correspond à la remise ($remise)
                                $excel .= $remise['SIREN']."\t".$remise['date_vente']."\t".$remise['num_carte']."\t".$remise['reseau']."\t".$remise['num_autorisation']."\tEUR\t".$remise['montant']."\t".$remise['sens']."\n";
                            }
                        }
                    }
                }
            } 
            else 
            {
                $excel = "SIREN\tRaison Sociale\tNumero Remise\tDate traitement\tNombre de transactions\tDevise\tMontant total\n";
                foreach($tab1 AS $ligne) { // parcours les lignes du tableau des remises
                    $excel .= $ligne[0]."\t".$ligne[1]."\t".$ligne[2]."\t".$ligne[3]."\t".$ligne[4]."\t".$ligne[5]."\t".$ligne[6]."\n";
                }
            }
            $excel .= "EXTRAIT DU ".date('d/m/Y');
            header("Content-type: application/application/vnd.ms-excel"); // récupère le type/format demandé
            header("Content-disposition: attachment; filename=$filename ".date('d/m/Y').".xls"); // créer le fichier avec le nom entré dans filename
            print $excel;
        }
        else if ($format == 'PDF') // si le format demandé est PDF
        {
            if ($detailled == 1) // si on veut un fichier avec détail des remises
            {
                foreach($tab1 AS $ligne) { // parcours le tableau des remises
                    echo "LISTE DES TRANSACTIONS DE LA REMISE DE L'ENTREPRISE ".$ligne[1].", No DE SIREN ".$ligne[0]." LE ".$ligne[3]."<br>";
                    echo "<table border=\"1\"><tr><th>SIREN</th><th>Date vente</th><th>Numero Carte</th><th>Reseau</th><th>Numero Autorisation</th><th>Devise</th><th>Montant</th><th>Sens</th></tr>";
                    foreach($tab2 AS $remises) { // parcours les remises
                        foreach($remises AS $remise) { // parcours le détail des transactions de chaque remise
                            if ($remise['SIREN'] == $ligne[0] && $ligne[3] == $remise['date_traitement']) { // si le SIREN et la date de traitement correspond à la remise ($remise)
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
                foreach($tab1 AS $ligne) { // parcours le tableau des remises
                    echo "<tr><td>".$ligne[0]."</td><td>".$ligne[1]."</td><td>".$ligne[2]."</td><td>".$ligne[3]."</td><td>".$ligne[4]."</td><td>".$ligne[5]."</td><td>".$ligne[6]."</td></tr>";
                }
            }
            echo "</table>";
            echo "<br>EXTRAIT DU ".date('d/m/Y');
            echo "<script>window.print()</script>"; // ouvre une interface pour imprimer en PDF la page
        }
    }
?>