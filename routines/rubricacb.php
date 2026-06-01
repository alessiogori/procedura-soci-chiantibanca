<?php
function puliscistringa($stringa) 
{ 
$stringa = str_replace("à", "a", $stringa); 
$stringa = str_replace("è", "e", $stringa); 
$stringa = str_replace("à", "a", $stringa); 
$stringa = str_replace("ì", "i", $stringa); 
$stringa = str_replace("ù", "u", $stringa);
$stringa = str_replace("ò", "o", $stringa);
$stringa = str_replace("grave", "", $stringa);
$stringa = ereg_replace("[^A-Za-z0-9 ]", "", $stringa ); 
return $stringa; 
}
// $nomefile = puliscistringa($nomefile);

echo '<html>
        <head>
        <script type="text/javascript" src="../js/fusioncharts/fusioncharts.js"></script>
        <script type="text/javascript" src="../js/fusioncharts/themes/fusioncharts.theme.candy.js"></script>
        </head>
        <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
        </style> 

        <body><br><br>
        ';

$dbhandle = new mysqli("127.0.0.1", "u23jh0rfn", "jCTcQVM5Q5UhsFXb", "rubrica");
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}


    // Preparo il file per l'estrazione in CSV
    $contenutofile = '';
    $select_file = "SELECT matr, cognome, nome, ufficio, cell, tel_voip, tel
					FROM lista
					WHERE matr <> ''
    				ORDER BY cognome, nome
                    " ;
    //$select_file;
    $qry_file = $dbhandle->query($select_file) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");  
    
    /*
    // Per visualizzare i dati come array
    while($row = $qry_file->fetch_array(MYSQLI_NUM)){
    	print_r($row);
    }
    */
    
    $myfile = fopen("../tmp/rubricacb.csv", "w");
    $contenutofile .= "Matricola;Cognome;Nome;Ufficio;Cellulare;TelefonoVoip;Telefono\n";
    while($cnt_file = mysqli_fetch_array($qry_file)){ 
        $contenutofile .= 
			$cnt_file['matr'].";".
			ucfirst(puliscistringa($cnt_file['cognome'])).";".
			ucfirst(puliscistringa($cnt_file['nome'])).";".
			$cnt_file['ufficio'].";".
			$cnt_file['cell'].";".	
			$cnt_file['tel_voip'].";".
			$cnt_file['tel']."\n";
    }
    fwrite($myfile, $contenutofile);
    fclose($myfile);

    echo '<br><center><a class="btn btn-outline-warning" href="../tmp/rubricacb.csv">Scarica la Rubrica Telefonica ChiantiBanca</a>';


// closing database connection      
$dbhandle->close();	
?>