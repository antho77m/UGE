<?php
    session_start();
    if (isset($_GET['format']) && isset($_SESSION['tab_unpaids'])) {
        $tab = $_SESSION['tab_unpaids'];
        $format = $_GET['format'];

        if ($format == 'CSV') // si le format demandé est CSV
        {
            header("Content-Type: application/csv"); // récupère le type/format demandé
            header("Content-Disposition: attachment; filename=IMPAYES ".date('d/m/Y').".csv;"); // créer le fichier avec le nom entré dans filename 
            $file = fopen('php://output', 'w');
            fputcsv($file, ["SIREN","Date vente","Date traitement","Numero Carte","Reseau","Numero Dossier","Devise","Montant","Libelle"], ';');
            foreach($tab AS $ligne) { // parcours le tableau des impayés
                fputcsv($file, [$ligne['SIREN'],$ligne['date_vente'],$ligne['date_traitement'],$ligne['num_carte'],$ligne['reseau'],$ligne['num_dos'],"EUR",'-'.$ligne['montant'],$ligne['libelle']], ';'); // ajoute dans le fichier ($file) le tableau case par case
            }
            fputcsv($file, ["EXTRAIT DU ".date('d/m/Y')], ';');
            fclose($file);
        } 
        else if ($format == 'XLS') // si le format demandé est XLS
        {
            $excel = "SIREN\tDate vente\tDate traitement\tNumero Carte\tReseau\tNumero Dossier\tDevise\tMontant\tLibelle\n";
            foreach($tab AS $ligne) { // parcours le tableau des impayés
                $excel .= $ligne['SIREN']."\t".$ligne['date_vente']."\t".$ligne['date_traitement']."\t".$ligne['num_carte']."\t".$ligne['reseau']."\t".$ligne['num_dos']."\tEUR\t-".$ligne['montant']."\t".$ligne['libelle']."\n";
            }
            $excel .= "EXTRAIT DU ".date('d/m/Y');
            header("Content-type: application/application/vnd.ms-excel"); // récupère le type/format demandé
            header("Content-disposition: attachment; filename=IMPAYES ".date('d/m/Y').".xls"); // créer le fichier avec le nom entré dans filename
            print $excel;

        }
        else if ($format == 'PDF') // si le format demandé est PDF
        {
            echo "<table border=\"1\"><tr><th>SIREN</th><th>Date vente</th><th>Date traitement</th><th>Numero Carte</th><th>Reseau</th><th>Numero Dossier</th><th>Devise</th><th>Montant</th><th>Libelle</th></tr>";
            foreach($tab AS $ligne) { // parcours le tableau des impayés
                echo "<tr><td>".$ligne['SIREN']."</td><td>".$ligne['date_vente']."</td><td>".$ligne['date_traitement']."</td><td>".$ligne['num_carte']."</td><td>".$ligne['reseau']."</td><td>".$ligne['num_dos']."</td><td>EUR</td><td>".'-'.$ligne['montant']."</td><td>".$ligne['libelle']."</td></tr>";
            }
            echo "</table>";
            echo "<br>EXTRAIT DU ".date('d/m/Y');
            echo "<script>window.print()</script>"; // ouvre une interface pour imprimer en PDF la page
        }
    }
?>