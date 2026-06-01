<?php
ini_set('max_execution_time', 0); 
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
//
// Questo script serve a controllare se fossero presenti nel db CESSIONI
// posizioni che in realtà sono già estinte
//
// SEZIONE DA NON MODIFICARE
// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");


    function clean_text($string)
    {
     $string = trim($string);
     $string = stripslashes($string);
     $string = htmlspecialchars($string);
     return $string;
    }

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

?>


<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

<?php		
if (empty($_GET['filiale'])) 

{$condizionefiliale = "WHERE codFil <> 999 ";
 $sintesi = 1;
}
else 
{$condizionefiliale = " WHERE codFil = ".$_GET['filiale']." ";
 $sintesi = 0;
}

// Se fuori zona ESTERO
if ($_GET['fuorizona']=="estero") {
    
    $strQueryP = "  SELECT
                codFil as Filiale, c.cag as Cag, concat(int1Socio,' ',int2Socio) as Nominativo, 
                indirResidLocalita as Localita, CABcomune as CAB, '' as Comune, 
                '' as PresenzaFiliale, '' as Competenza, paese as Paese, prot,
                id, documentale, status_esito, note, operatore, data_segnalazione, attivo,
                CASE status_esito
                    WHEN 'Valido' THEN '1'
                    WHEN 'Escludere' THEN '2'
                    ELSE '3' 
                    END as counter
                FROM tab_comuni_soci as c left join tab_comuni_soci_note as n
                ON c.cag = n.cag
                ".$condizionefiliale."
                AND paese <> '086'
                GROUP BY c.cag
                ORDER BY 1,3   ";
                
    $titolo = 'Soci residenti fuori zona di competenza - ESTERO <img src="img/ico_mondoxx.png" height="30">';
    
    $icostatus_green = '';
    $icostatus_red = '';
    $icostatus_yellow = '';

} else {
// Se fuori zona ITALIA
// AND c.cag in ( 3039791, 18008)
    $strQueryP = "  SELECT
                codFil as Filiale, f.cag as Cag, concat(int1Socio,' ',int2Socio) as Nominativo,
                indirResidLocalita as Localita, CABcomune as CAB, f.comune as Comune, 
                f.paese as Paese, f.prot,
                id, documentale, status_esito, note, operatore, data_segnalazione, attivo,
                CASE status_esito
                    WHEN 'Valido' THEN '1'
                    WHEN 'Escludere' THEN '2'
                    ELSE '3' 
                    END as counter
                FROM tab_comuni_fuorizona as f
                LEFT JOIN tab_comuni_soci_note as n
                ON f.cag = n.cag
                ".$condizionefiliale."
                GROUP BY f.cag 
                ORDER BY 1,3   ";

    $titolo = 'Soci residenti fuori zona di competenza - ITALIA <img src="img/ico_italy.png" height="40">';

}
  //echo $strQueryP;
  echo "
  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
	      	order: [[ 2, 'asc' ]],
          	lengthMenu: [ 50, 10, 25, 50, 75, 100, 500, 1000 ],
          	deferRender: true,
          	buttons: ['csvHtml5']
    } );

		});</script>   
    ";    

$resultP = mysqli_query($connection, $strQueryP);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">'.$titolo.'</h4>
      </div>';
     //  <div align="right"><small>'.$icostatus_green.' &nbsp;&nbsp; '.$icostatus_red.' &nbsp;&nbsp; '.$icostatus_yellow.'</small>&nbsp;&nbsp;</div> 
    echo '
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered"  id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr class="table-primary">
        <th style="text-align:left; font-size:13px;">Filiale</th>
        <th style="text-align:left; font-size:13px;">CAG</th>
        <th style="text-align:left; font-size:13px;">Nominativo</th>
        <th style="text-align:left; font-size:13px;">Localita (paese)</th>
        <th style="text-align:left; font-size:13px;">CAB - Comune</th>
        <th style="text-align:center; font-size:13px;">Motivazione </th>
        <th style="text-align:center; font-size:13px;">Data Segnalaz </th>
    </tr>
    </thead>
    <tbody>
';

$contaValido = 0;
$contaEscludere = 0;
$contaDaVerificare = 0;

// Preparo il file per l'estrazione in CSV
$contenuto = '';
$myfile = fopen("tmp/checkzonecompetenza.csv", "w");
$contenuto .= "Filiale;CAG;Nominativo;ValoreNominale;Localita;CAB-Comune;Motivazione;Note;DataSegnalazione\n";

while ($dati_p = mysqli_fetch_array($resultP)) {
    
        $selectvalorenominale = "SELECT controvalore_azioni FROM sds_sinergiareport_soci WHERE nag = ".$dati_p['Cag'];
        $result_vn = mysqli_query($connection, $selectvalorenominale);
        $dati_vn = mysqli_fetch_array($result_vn);
        $valorenominale = $dati_vn['controvalore_azioni'];

        if ($dati_p['status_esito'] == 'Valido') 
            {   $icostatus = '<img src="img/ico_pallino_green.png" title="Positivo">'; 
                $desc_pulsante = 'Validato';
                $contaValido += $dati_p['counter'];
        }
        if ($dati_p['status_esito'] == 'Escludere') 
            {   $icostatus = '<img src="img/ico_pallino_red.png" title="Negativo">'; 
                $desc_pulsante = 'Da Escludere';
                $contaEscludere += $dati_p['counter'];
        }
        if ($dati_p['status_esito'] == 'Da verificare') 
            {   $icostatus = '<img src="img/ico_pallino_yellow.png" title="In Sospeso">'; 
                $desc_pulsante = 'Da verificare';
                $contaDaVerificare += $dati_p['counter'];
        }
        if ($dati_p['status_esito'] == '') 
            {   $icostatus = '<img src="img/ico_pallino_yellow.png" title="In Sospeso">'; 
                $desc_pulsante = 'Da verificare';
                $contaDaVerificare += $dati_p['counter'];
        }

        if ($dati_p['Paese'] <> '086') 
            {$paese = " (".$dati_p['Paese'].")";
             $fuorizona = 'estero';
            } 
            else 
            {$paese = '';
             $fuorizona = 'italia';
            }

        if ($dati_p['id'] == '') {$id = 'N';}
        else {$id = $dati_p['id']; }
        
   		echo "	<tr class='table-secondary'>
    		            <td align='left'>".$dati_p['Filiale']."</td>
                        <td align='left'>".$dati_p['Cag']."&nbsp;</td>
                        <td><a class='text-success' href='sqldati_schedasocio.php?id=".$dati_p['prot']."'>".$dati_p['Nominativo']."</a></td>
                        <td align='left'>".$dati_p['Localita'].$paese."</td>
						<td align='left'>".$dati_p['CAB']." - ".strtoupper($dati_p['Comune'])."</td>
						<td align='center'>";
		echo '			<a href="check_zonecompetenza_note.php?id='.$id.'&filiale='.$dati_p['Filiale'].'&fuorizona='.$fuorizona.'&cag='.$dati_p['Cag'].'&nominativo='.$dati_p['Nominativo'].'&tipo=edit" title="'.$dati_p['status_esito'].'">';
		echo "			<span class='badge badge-info'>".$icostatus."&nbsp;&nbsp;".$desc_pulsante."&nbsp;&nbsp;</span></a>
				    </td>
				        <td align='right'><small>".$dati_p['data_segnalazione']."</small></td>
					</tr>";

					
        $contenuto .= $dati_p['Filiale'].";".$dati_p['Cag'].";".$dati_p['Nominativo'].";".$valorenominale.";".$dati_p['Localita'].";".$dati_p['CAB']." - ".strtoupper($dati_p['Comune']).";".$dati_p['status_esito'].";".puliscistringa(clean_text($dati_p['note'])).";".$dati_p['data_segnalazione']."\n";
					
}
fwrite($myfile, $contenuto);
fclose($myfile);  

echo '    </tbody>
  </table>';

// Presenta il conteggio complessivo
echo '<div align=center>
        <img src="img/ico_pallino_green.png">&nbsp '.$contaValido.' <i class="fas fa-ellipsis-h fa-1x text-gray-300 col-auto"></i>
        <img src="img/ico_pallino_red.png">&nbsp '.($contaEscludere/2).'  <i class="fas fa-ellipsis-h fa-1x text-gray-300 col-auto"></i>
        <img src="img/ico_pallino_yellow.png" >&nbsp '.($contaDaVerificare/3).'';
        
if ($sintesi == 1) {  
// Presenta il conteggio diviso per Filiale
}

if (empty($_GET['filiale'])) {
echo '<br><a class="btn btn-outline-warning" href="tmp/checkzonecompetenza.csv">Scarica l\'elenco</a>';
}
  
echo '    </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';


?>