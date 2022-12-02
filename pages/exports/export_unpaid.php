<?php
    session_start();
    if (isset($_GET['format']) && isset($_SESSION['tab'])) {
        $tab = $_SESSION['tab_unpaids'];
        $format = $_GET['format'];

        if ($format == 'CSV') 
        {
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="impayés.csv";');
            $file = fopen('php://output', 'w');
            fputcsv($file, ["SIREN","Date vente","Date traitement","Numero Carte","Reseau","Numero Dossier","Devise","Montant","Libelle"], ';');
            foreach($tab AS $ligne) {
                fputcsv($file, [$ligne['SIREN'],$ligne['date_vente'],$ligne['date_traitement'],$ligne['num_carte'],$ligne['reseau'],$ligne['num_dos'],"EUR",'-'.$ligne['montant'],$ligne['libelle']], ';');
            }
            fputcsv($file, ["EXTRAIT DU ".date('d/m/Y')], ';');
            fclose($file);
        } 
        else if ($format == 'XLSX') 
        {
            require_once("../extensions/xlsxwriter.class.php");
            $header = array(
                'SIREN'=>'string',
                'Date vente'=>'string',
                'Date traitement'=>'string',
                'Numero Carte'=>'string',
                'Reseau'=>'string',
                'Numero Dossier'=>'string',
                'Devise'=>'string',
                'Montant'=>'string',
                'Libelle'=>'string',
            );
            $writer = new XLSXWriter();
            $writer->writeSheetHeader('Sheet1', $header);
            foreach($tab AS $ligne)
                $writer->writeSheetRow('Sheet1', [$ligne['SIREN'],$ligne['date_vente'],$ligne['date_traitement'],$ligne['num_carte'],$ligne['reseau'],$ligne['num_dos'],"EUR",'-'.$ligne['montant'],$ligne['libelle']]);
            $writer->writeSheetRow('Sheet1', ["EXTRAIT DU ".date('d/m/Y')]);
            $writer->writeToFile('impayés.xlsx');

            header('Content-disposition: attachment; filename=impayés.xlsx');
            header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            ob_clean();
            flush();
            readfile('impayés.xlsx');
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