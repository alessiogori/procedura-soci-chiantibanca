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

{$condizionefiliale = "WHERE FILIALE_CAPOFILA <> 999 ";
 $sintesi = 1;
}
else 
{$condizionefiliale = " WHERE FILIALE_CAPOFILA = ".$_GET['filiale']." ";
 $sintesi = 0;
}

// Se fuori zona ESTERO
if ($_GET['fuorizona']=="estero") {
    
    $strQueryP = "  SELECT
                    FILIALE_CAPOFILA as Filiale, 
                    c.nag as Cag, 
                    concat(INTESTAZIONE_A, ' ', INTESTAZIONE_B) as Nominativo, 
                    concat(DESCR_COM_RES, ' - ', LOCALITA_RES) as Localita, '' as CAB, '' as Comune, 
                    '' as PresenzaFiliale, '' as Competenza, LOCALITA_RES as Paese, data_entrata as DataEntrata,
                    IDSOCIO as prot, id, documentale, status_esito, note, operatore, data_segnalazione, attivo,
                    CASE status_esito
                            WHEN 'Valido' THEN '1'
                            WHEN 'Escludere' THEN '2'
                            ELSE '3' 
                            END as counter
                    FROM sds_soci as c left join tab_comuni_soci_note as n
                    ON c.nag = n.cag
                    ".$condizionefiliale."
                    AND PROVINCIA_RES = 'SE'
                    AND SOCIO_ISTITUTO = '1'
                    GROUP BY c.nag
                    ORDER BY 1,3   ";
                  
    $titolo = 'Soci residenti fuori zona di competenza - ESTERO <img src="img/ico_mondoxx.png" height="30">';
    
    $icostatus_green = '';
    $icostatus_red = '';
    $icostatus_yellow = '';

} else {
// Se fuori zona ITALIA
// AND c.cag in ( 3039791, 18008)
    $strQueryP = "  SELECT
                    FILIALE_CAPOFILA as Filiale, 
                    c.nag as Cag, 
                    concat(INTESTAZIONE_A, ' ', INTESTAZIONE_B) as Nominativo, 
                    concat(LOCALITA_RES, ' ', DESCR_COM_RES, ' (', PROVINCIA_RES,')') as Localita, '' as CAB, '' as Comune, 
                    '' as PresenzaFiliale, '' as Competenza, LOCALITA_RES as Paese,  data_entrata as DataEntrata,
                    IDSOCIO as prot, id, documentale, status_esito, note, operatore, data_segnalazione, attivo,
                    CASE status_esito
                            WHEN 'Valido' THEN '1'
                            WHEN 'Escludere' THEN '2'
                            ELSE '3' 
                            END as counter
                    FROM sds_soci as c left join tab_comuni_soci_note as n
                    ON c.nag = n.cag
                    ".$condizionefiliale."
                    AND PA_3 IN (998,999)
                    AND PROVINCIA_RES <> 'SE'
                    AND SOCIO_ISTITUTO = '1'
                    GROUP BY c.nag
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
        <th style="text-align:center; font-size:13px;">Data Entrata</th>
        <th style="text-align:left; font-size:13px;">Localita (paese)</th>
        <th style="text-align:center; font-size:13px;">Motivazione</th>
        <th style="text-align:center; font-size:13px;">Data Segnalaz</th>
        <th style="text-align:center; font-size:13px;">Oper Segnalaz</th>
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

    // Controllo se sono presenti motivazioni inserite in fase di domanda di ammissione
      $select_motiv =   "
                        SELECT count(*) as qta
                        FROM
                        tab_motivazioni 
                        WHERE
                        nag = ".$dati_p['Cag']."
                        "; 

      $result_motiv = mysqli_query($connection, $select_motiv);

      while ($dati_motiv = mysqli_fetch_array($result_motiv)) {
        
        if ($dati_motiv['qta'] > 0) 
        {
             // VISUALIZZA MOTIVAZIONE
            $ico_motivazione = '
            <a href="motivazioni.php?start=IN&filiale='.$dati_p['Filiale'].'&nag='.$dati_p['Cag'].'&data_domanda=&nome='.$dati_p['Nominativo'].'" target="_blank">
            <i class="fas fa-sticky-note" style="color:lightgreen;" title="Motivazione ammissione PRESENTE"></i>
            </a>
            ';
        }
        else
        {
            // FORM DI INSERIMENTO MOTIVAZIONE
            $ico_motivazione = '
            <i class="fas fa-sticky-note" style="color:gray;" title="Motivazione ammissione non presente"></i>
            ';
        }
      }

    // Controllo se nella tabella SOCI_DOMANDE sono presenti indicazioni sulla zona di competenza
        $selectnotedomanda = "  SELECT  PROF_COMUNE, PROF_VIA, PROF_PRESSO, PROF_PROV,
                                        IMM_COMUNE, IMM_VIA, IMM_PRESSO, IMM_PROV
                                FROM sds_soci_domande
                                WHERE CTIPOESITO <> 'DR' AND nag = ".$dati_p['Cag'];

        $result_nd = mysqli_query($connection, $selectnotedomanda);
        $dati_nd = mysqli_fetch_array($result_nd) ;

        if (trim($dati_nd['PROF_COMUNE']) != '')
        {
          $noteDomandaProfessione = '<br><small>Da Domanda di Amissione: Dipendente presso '.$dati_nd['PROF_PRESSO'].
                                    ' '.$dati_nd['PROF_COMUNE'].'</small>'; 
          $noteDomandaImmobili = '';

        }

        elseif (trim($dati_nd['IMM_COMUNE']) != '')
        {
          $noteDomandaImmobili = '<br><small>Da Domanda di Amissione: Immobile in '.$dati_nd['IMM_COMUNE'].
                                    ' '.$dati_nd['IMM_VIA'].'</small>';
          $noteDomandaProfessione = '';
        }
        else
            {
              $noteDomandaProfessione = '';
              $noteDomandaImmobili = '';
            }
    
        $selectvalorenominale = "SELECT valore_azioni 
                                 FROM sds_soci LEFT JOIN sds_soci_certificati
                                 ON sds_soci.IDSOCIO = sds_soci_certificati.IDSOCIO
                                 WHERE nag = ".$dati_p['Cag'];
                                 
        $result_vn = mysqli_query($connection, $selectvalorenominale);
        $dati_vn = mysqli_fetch_array($result_vn);
        $valorenominale = $dati_vn['valore_azioni'];

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
            {$paese = '';
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
                        <td>".$ico_motivazione."&nbsp;&nbsp;<a class='text-success' href='sqldati_schedasocio.php?id=".$dati_p['prot']."'>".$dati_p['Nominativo']."</a>".$noteDomandaProfessione.$noteDomandaImmobili."
                            </td>
                        <td align='center'>".$dati_p['DataEntrata'].$paese."</td>
                        <td align='left'>".$dati_p['Localita'].$paese."</td>
						<td align='center'>";
		echo '			<a href="check_zonecompetenza_note.php?id='.$id.'&filiale='.$dati_p['Filiale'].'&fuorizona='.$fuorizona.'&cag='.$dati_p['Cag'].'&nominativo='.$dati_p['Nominativo'].'&tipo=edit" title="'.$dati_p['status_esito'].'">';
		echo "			<span class='badge badge-info'>".$icostatus."&nbsp;&nbsp;".$desc_pulsante."&nbsp;&nbsp;</span></a>
				    </td>
				        <td align='center'><small>".$dati_p['data_segnalazione']."</small></td>
                        <td align='center'>".$dati_p['operatore']."</td>
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