<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2022)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE
ini_set('max_execution_time', 0); 
// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
//include("css/main.php");
//include("css/menu.php");

echo '<br><br>
	  <style type="text/css">
          @import "css/bootstrap.css";
          @import "css/bootstrap.min.css";
          @import "css/fontawesome-free/css/all.min.css";
        </style> ';

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


if  (empty($_POST['ricerca'])) 
	{

echo "<center>
		<img src='img/cartabccdebit.png'><br><br>
		<fieldset style='width:600px;text-align:left;'>
		<legend><i class='fas fa-search fa-1x text-gray-300 col-auto'></i> <b>Ricerca Carta Debito ICCREA da stampare</legend>";

echo '<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%" >
	<tr>
		<td valign="top" width="75%"><br>
			 <form action="migracarte_lista.php" method="POST">
					<input type="text" class="form-control" name="ricerca" id="ricerca" size="40">
					<small style="color:white;"><i>Puoi inserire il cognome/nome - cag - numero Carta</i></small>
			<br><br>
		</td>
		<td valign="top" align="center" width="25%"><br>
					<button type="submit" class="btn btn-success mb-2">RICERCA</button><br>
			</form>
		</td>
	</tr>	
	</table>';

echo '</fieldset></center>';


}

else

{
?>

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>
  <script src="js/dataTables.buttons.min.js"></script>
  <script src="js/buttons.print.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
	$(document).ready(function() {
	    $('#dataTable').DataTable( {
	    	/*
	    	dom: 'Bfrtip'
          	,buttons: [{
                extend: 'print', text: '<i class="fas fa-file-csv fa-2x btn-primary"></i>', 
                className: 'btn btn-dark',
                messageTop: 'Documento ad uso interno - riservato ChiantiBanca',
                messageBottom: 'Documento ad uso interno - riservato ChiantiBanca',
                footer: 'Documento ad uso interno - riservato ChiantiBanca',
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '9pt' )
                        .prepend(
                            '<img src="http://10.119.192.46:8080/soci/img/logo_chiantibanca.png" style="position:absolute; top:0; left:0;opacity:0.2" />'
                        );
 
                    $(win.document.body).find( 'Table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                }
            },'pageLength']
	    	, */
	    	lengthMenu: [ [ 10, 25, 50, 75, 100, 500, 1000 ], 
	    				  ['10 righe', '25 righe', '50 righe', '75 righe', '100 righe', '500 righe', '1000 righe'] ]
	    	,oLanguage: {"sLengthMenu": "Mostra _MENU_ ", "sSearch": "Cerca nella lista:"}
            ,order: [[ 3, "asc" ]]
          	,deferRender: true
    	} );

	});

	</script>   		

<div id="load" style="display:none;">Loading... Please wait</div>

<?php

$adesso = date('d/m');

$condizionefiliale = "WHERE 
			(( CONCAT(int1Socio,' ',int2Socio) LIKE '%".$_POST['ricerca']."%' OR int1Delegato LIKE '%".$_POST['ricerca']."%' OR telefono LIKE '%".$_POST['ricerca']."%' OR Prot LIKE '%".$_POST['ricerca']."%' OR CAG LIKE '%".$_POST['ricerca']."%' ))";

// ********************************************************
// DATI GENERALI
// ********************************************************
$select = "
        SELECT
            Filiale,
            a.Carta as Carta,
            CAGIntestatario,
            Sesso,
            NaturaRapporto,
            ScopoRapporto,
            Cognome,
            Nome,
            CONCAT(Cognome, ' ',Nome) as Nominativo,
            Indirizzo,
            CAP,
            ComuneLocalita,
            Provincia,
            Nazione,
            DataNascita,
            ComuneNascita,
            ProvinciaNascita,
            NazioneNascita,
            StatoCivile,
            CodiceFiscale,
            TelAbitazione,
            TelCellulare,
            OperatoreCellulare,
            TipoDocumento,
            NumeroDocumento,
            DataRilascio,
            EnteRilascio,
            DataScadenza,
            LuogoRilascio,
            ProvinciaRilascio,
            NazioneRilascio,
            CodiceQualifica,
            CodiceSettore,
            TipoAttivita,
            TitoloStudio,
            Email,
            IBAN,
            RichiestoFlussoICCREA,
            DataFlussoICCREA,
            s.StatoStampa as StatoStampa,
            s.DataStampa as DataStampa
        FROM
            migracarte_archivio as a
        LEFT join
            migracarte_stampa as s
        ON
            a.Carta = s.Carta
        WHERE 
			(( CONCAT(Cognome, ' ',Nome) LIKE '%".$_POST['ricerca']."%' 
				OR a.Carta LIKE '%".$_POST['ricerca']."%' 
				OR CAGIntestatario LIKE '%".$_POST['ricerca']."%' ))
			";

logquery ($select);
//echo $select;
$querydati = mysqli_query($connection, $select);

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Migrazione Carte Debito ICCREA 
      &nbsp;&nbsp;<img src="img/cartabccdebit.png" height="50"></h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
		<thead>
			<tr class="table-primary">
				<th style="text-align:left; font-size:13px;">Filiale</th>
				<th style="text-align:left; font-size:13px;">CAG</th>
				<th style="text-align:left; font-size:13px;">Nominativo</th>
				<th style="text-align:center; font-size:13px;">Data Nascita</th>
				<th style="text-align:center; font-size:13px;">Nr.Carta</th>
				<th style="text-align:center; font-size:13px;">Prodotto</th>
				<th style="text-align:center; font-size:13px;">Richiesta Iccrea</th>
				<th style="text-align:center; font-size:13px;">Contratto<br>Stampato</th>
				<th style="text-align:center; font-size:13px;">Modulo<br>Contratto</th>
			</tr>
		</thead>
		<tbody>
';	

while($daticarta=mysqli_fetch_array($querydati)){ 

			// ********************************************************
			// RICERCA DEL NOME DELLA FILIALE
			// ********************************************************
			$select_filiale	  = "	SELECT * FROM tab_psw
									WHERE filiale = ".$daticarta['Filiale'];
			logquery ($select_filiale);  
			$querydati_filiale = mysqli_query($connection, $select_filiale);
				if(mysqli_num_rows($querydati_filiale) > 0)
				    while($datifiliale = mysqli_fetch_array($querydati_filiale))
				    {
				        $nomefiliale = $datifiliale['desc_filiale'];
				        $luogofiliale = $datifiliale['luogo'];
				    }
				else
					{
					    $nomefiliale = '';
					    $luogofiliale = '';
					}


    // Età del socio
    $eta = ( date("Y") - substr($daticarta['DataNascita'],-4) ); 

	// SOLO PER TEST, poi togliere
	$prodottocarta = 'PRODOTTO';

	if ($daticarta['StatoStampa'] == 'S')
		{
			$coloreriga = 'table-light';
		}
	else
		{
			$coloreriga = 'table-secondary';
		}
	

	echo "	  <tr class='".$coloreriga."'>   
				<td style='text-align:left;' title='".$nomefiliale."'>".$daticarta['Filiale']."</td>
				<td style='text-align:left;'>".$daticarta['CAGIntestatario']."</td>
				<td style='text-align:left;'>".$daticarta['Nominativo']."</td>
				<td style='text-align:center;'>".$daticarta['DataNascita']."</td>
				<td style='text-align:center;'>".$daticarta['Carta']."</td>
				<td style='text-align:center;'>".$prodottocarta."</td>
				<td style='text-align:center;'>".$daticarta['RichiestoFlussoICCREA']."
					<br><small>".$daticarta['DataFlussoICCREA']."</small></td>
				<td style='text-align:center;'>".$daticarta['StatoStampa']."
					<br><small>".$daticarta['DataStampa']."</small></td>
		";

if ($daticarta['RichiestoFlussoICCREA'] == 'S'){
	echo '	<td style="text-align:center; font-size:11px; text-decoration: none; color:white;"><a href="modulistica/_testdmx.php?modello=DMX&cag='.$daticarta['CAGIntestatario'].'&socio='.urlencode(stripslashes($daticarta['Nominativo'])).'&luogo='.$luogofiliale.'" target="_blank"><span style="color: white;"><i class="fas fa-file-signature fa-2x col-auto" title="Contratto Carta Debito Iccrea precompilata '.$daticarta['Nominativo'].'"></i></span></a></td>';
} else { 
    echo '	<td style="text-align:center; font-size:11px; text-decoration: none;"></td>';
}    
		  
	echo '  </tr>'; 


}


echo '		</tbody>
	</table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';

}

    echo "";

echo '<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>';

?>


