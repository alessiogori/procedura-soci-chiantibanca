<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
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

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

?>

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
  // Call the dataTables jQuery plugin
  $(document).ready(function() {
      $('#dataTable').DataTable( {
            "order": [[ 3, "asc" ]]
    } );

    });</script>      

<?php

if (empty($_GET['filiale'])) 
{$condizionefiliale = "WHERE Filiale <> 999 AND Fascia like 'Fascia ".$_GET['fascia']."%' ";}
else 
{$condizionefiliale = " WHERE Filiale = ".$_GET['filiale']." AND Fascia like 'Fascia ".$_GET['fascia']."%' ";}

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}
 
// ********************************************************
// DATI GENERALI
// ********************************************************
$select = " SELECT  *
      FROM view_fasce_senzarichieste
      ".$condizionefiliale."
      ORDER BY Nominativo";

logquery ($select);
//echo $select;
$querydati = $dbhandle->query($select) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Soci ChiantiBanca per Fascia '.$_GET['fascia'].'</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr class="table-primary">
        <th style="text-align:left; font-size:13px;">Fascia</th>
        <th style="text-align:left; font-size:13px;">IDSOCIO</th>
        <th style="text-align:left; font-size:13px;">NAG</th>
        <th style="text-align:left; font-size:13px;">Nominativo</th>
        <th style="text-align:center; font-size:13px;">Filiale</th>
        <th style="text-align:center; font-size:13px;">Età</th>
        <th style="text-align:center; font-size:13px;">Data Entrata</th>
        <th style="text-align:right; font-size:13px;">Azioni Totali</th>
        <th style="text-align:right; font-size:13px;">Controvalore Euro</th>
        <th style="text-align:right; font-size:13px;">Socio Mutua</th>
    </tr>
    </thead>
    <tbody>
';  

      // Preparo l'esportazione su file
      $myfilectrasf = fopen("tmp/fascia".$_GET['fascia'].".csv", "w");
      $contenutoOutput = "FASCIA;IDSOCIO;NAG;NOMINATIVO;FILIALE;ETA;DATA_ENTRATA;AZIONI;IMPORTO;SOCIOMUTUA\n";

while($datisocio=mysqli_fetch_array($querydati)){ 
    
    $selectProt = " SELECT idsocio, eta, data_entrata
                    FROM sds_soci
                    WHERE nag = ".$datisocio['nag']."
                    ";

    logquery ($selectProt);
    //echo $select;
    $querydatiProt = $dbhandle->query($selectProt) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
    while($datisocioProt=mysqli_fetch_array($querydatiProt)){ 
        $prot = $datisocioProt['idsocio'];
        $eta = $datisocioProt['eta'];
        $dtentrata = $datisocioProt['data_entrata'];
    }
    
			// ********************************************************
			// RICERCA SE IL SOCIO E' ANCHE SOCIO MUTUA
			// ********************************************************
			$select_mutua 	  = "	SELECT * FROM tab_mutua
									WHERE nag = ".$datisocio['nag'];
			logquery ($select_mutua);  
			$querydati_mutua = mysqli_query($connection, $select_mutua);
				if(mysqli_num_rows($querydati_mutua) > 0)
				    while($datisociomutua = mysqli_fetch_array($querydati_mutua))
				    {
				       $esistenzasociomutua = 
					 	"<img src='img/ico_pallino_green.png' >";
            $esistenzasociomutua_output = 'SI';
					   $modulimutua = 'si';
				    }
				else
				{
				    $esistenzasociomutua = '';
            $esistenzasociomutua_output = '';
				    $modulimutua = 'no';
				}

echo "<tr class='table-secondary'>   
        <td style='text-align:left;'>".$datisocio['Fascia']."</td>
        <td style='text-align:left;'>".$prot."</td>
        <td style='text-align:left;'>".$datisocio['nag']."</td>
        <td style='text-align:left;'>
        <a class='text-success' href='sqldati_schedasocio.php?id=".$prot."'>".$datisocio['Nominativo']."</a></td>
        <td style='text-align:left;'>".$datisocio['Filiale']."&nbsp;</td>
        <td style='text-align:left;'>".$eta."&nbsp;</td>
        <td style='text-align:left;'>".$dtentrata."&nbsp;</td>
        <td style='text-align:right;'>".number_format($datisocio['ValAzTotali']/30.33,0,',','.')."&nbsp;</td>
        <td style='text-align:right;'>".number_format(($datisocio['ValAzTotali']),0,',','.')."&nbsp;</td>
        <td style='text-align:center;'>".$esistenzasociomutua."</td>        
        </tr>
          "; 


                  $contenutoOutput .= 
                         $datisocio['Fascia'].";"
                        .$prot.";"
                        .$datisocio['nag'].";"
                        .$datisocio['Nominativo'].";"
                        .$datisocio['Filiale'].";"
                        .$eta.";"
                        .$dtentrata.";"
                        .number_format($datisocio['ValAzTotali']/30.33,0,',','.').";"
                        .number_format($datisocio['ValAzTotali'],0,',','.').";"
                        .$esistenzasociomutua_output."\n";

}

echo '    </tbody>
  </table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';


      fwrite($myfilectrasf, $contenutoOutput);
      fclose($myfilectrasf);


?>

<center>
  <a href="tmp/fascia<?php echo $_GET['fascia']; ?>.csv">
    <i class="fa fa-download fa-1x text-gray"></a><br>
</center>

<center>
  <a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png">
</center>
