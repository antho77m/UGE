<?php
    session_start();
    if (isset($_GET['format']) && isset($_SESSION['tab'])) {
        $tab = $_SESSION['tab_unpaids'];
        $format = $_GET['format'];

        if ($format == 'CSV') 
        {
            header("Content-Type: application/csv");
            header("Content-Disposition: attachment; filename=IMPAYES ".date('d/m/Y').".csv;");
            $file = fopen('php://output', 'w');
            fputcsv($file, ["SIREN","Date vente","Date traitement","Numero Carte","Reseau","Numero Dossier","Devise","Montant","Libelle"], ';');
            foreach($tab AS $ligne) {
                fputcsv($file, [$ligne['SIREN'],$ligne['date_vente'],$ligne['date_traitement'],$ligne['num_carte'],$ligne['reseau'],$ligne['num_dos'],"EUR",'-'.$ligne['montant'],$ligne['libelle']], ';');
            }
            fputcsv($file, ["EXTRAIT DU ".date('d/m/Y')], ';');
            fclose($file);
        } 
        else if ($format == 'XLS') 
        {
            $excel = "SIREN\tDate vente\tDate traitement\tNumero Carte\tReseau\tNumero Dossier\tDevise\tMontant\tLibelle\n";
            foreach($tab AS $ligne) {
                $excel .= $ligne['SIREN']."\t".$ligne['date_vente']."\t".$ligne['date_traitement']."\t".$ligne['num_carte']."\t".$ligne['reseau']."\t".$ligne['num_dos']."\tEUR\t-".$ligne['montant']."\t".$ligne['libelle']."\n";
            }
            $excel .= "EXTRAIT DU ".date('d/m/Y');
            header("Content-type: application/application/vnd.ms-excel");
            header("Content-disposition: attachment; filename=IMPAYES ".date('d/m/Y').".xls");
            print $excel;

        }
        else if ($format == 'PDF') {
            echo "<table border=\"1\"><tr><th>SIREN</th><th>Date vente</th><th>Date traitement</th><th>Numero Carte</th><th>Reseau</th><th>Numero Dossier</th><th>Devise</th><th>Montant</th><th>Libelle</th></tr>";
            foreach($tab AS $ligne) {
                echo "<tr><td>".$ligne['SIREN']."</td><td>".$ligne['date_vente']."</td><td>".$ligne['date_traitement']."</td><td>".$ligne['num_carte']."</td><td>".$ligne['reseau']."</td><td>".$ligne['num_dos']."</td><td>EUR</td><td>".'-'.$ligne['montant']."</td><td>".$ligne['libelle']."</td></tr>";
            }
            echo "</table>";
            echo "<br>EXTRAIT DU ".date('d/m/Y');
            echo "<script>window.print()</script>";
        }
    }
?>