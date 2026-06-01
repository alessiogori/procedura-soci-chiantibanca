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
          	"order": [[ 3, "asc" ]],
          	"lengthMenu": [ 10, 25, 50, 75, 100, 500, 1000 ],
          	"deferRender": true
    } );

		});</script>   
<?php

if (empty($_GET['filiale'])) 
{$condizionefiliale = "WHERE FIL_ANAGRAFICA <> 999 AND eta = ".$_GET['eta']." ";}
else 
{$condizionefiliale = " WHERE FIL_ANAGRAFICA in (".$_GET['filiale'].") AND eta = ".$_GET['eta']." ";}

if ($_GET['rapporti'] == 'si') 
{$condizionefiliale2 = " AND rapporti = 'SI' ";
 $titolo = ' (con rapporti) ';    
}
else 
{$condizionefiliale2 = " AND rapporti = 'NO' ";
 $titolo = ' (senza rapporti) ';
}

if ($_GET['socio'] == 'si') 
{$condizionefiliale3 = " AND SOCIOBANCA = 'SI' ";
 $titolo = ' (già Socio Banca) ';    
}
else 
{$condizionefiliale3 = " AND SOCIOBANCA = 'NO' ";
 $titolo = ' (non Socio Banca) ';
}

$dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
 
if ($dbhandle -> connect_error) {
    exit("There was an error with your connection: ".$dbhandle -> connect_error);
}
 
// ********************************************************
// DATI GENERALI
// ********************************************************

$select = " SELECT *
      FROM sds_soci_under35
      ".$condizionefiliale."
      ".$condizionefiliale2."
      ".$condizionefiliale3."
      GROUP BY NAG
      ORDER BY INTESTAZIONE_A, INTESTAZIONE_B";      
      
logquery ($select);
 //echo $select;
$querydati = $dbhandle->query($select) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

echo '
<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h4 class="m-2 font-weight-bold text-success">Giovani ChiantiBanca per età = '.$_GET['eta'].' anni '.$titolo.'</h4>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr class="table-primary">
        <th style="text-align:left; font-size:13px;">Età</th>
        <th style="text-align:left; font-size:13px;">NAG</th>
        <th style="text-align:left; font-size:13px;">Nominativo</th>
        <th style="text-align:left; font-size:13px;">Filiale</th>
        <th style="text-align:left; font-size:13px;">Rapporto</th>
        <th style="text-align:center; font-size:13px;">Socio Banca</th>
        <th style="text-align:center; font-size:13px;">Socio Mutua</th>
    </tr>
    </thead>
    <tbody>
';  

while($dati_u30=mysqli_fetch_array($querydati)){ 
    
    $selectProt = " SELECT IDSOCIO
                    FROM sds_soci
                    WHERE nag = ".$dati_u30['NAG']."
                    ";

    logquery ($selectProt);
    // echo $select;
    $querydatiProt = $dbhandle->query($selectProt) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
    if(mysqli_num_rows($querydatiProt) > 0)
      while($datisocioProt=mysqli_fetch_array($querydatiProt)){ 
          $idsocio = $datisocioProt['IDSOCIO'];
          
            // Pallino colorato per avvalorare lo status
        	if ($dati_u30['SOCIOBANCA'] == 'NO') 
        		{$pallino = '<img src="img/ico_pallino_red.png" title="ESTINTO">';} 
        	else {$pallino = '<img src="img/ico_pallino_green.png" title="In essere">';} 
        

          $esistenzasociobanca = 
            "<a href='sqldati_schedasocio.php?id=".$idsocio."' target='_blank' style='color:yellow;'>".$pallino."</a>";
      }
    else
            $esistenzasociobanca = ''; 


			// ********************************************************
			// RICERCA SE IL SOCIO E' ANCHE SOCIO MUTUA
			// ********************************************************
			$select_mutua 	  = "	SELECT * FROM tab_mutua
									WHERE nag = ".$dati_u30['NAG'];
			logquery ($select_mutua);  
			$querydati_mutua = mysqli_query($connection, $select_mutua);
				if(mysqli_num_rows($querydati_mutua) > 0)
				    while($datisociomutua = mysqli_fetch_array($querydati_mutua))
				    {
             // $esistenzasociomutua = "<a href='mutua_schedasocio.php?cag=".$datisociomutua['cag']."' title='Apri Scheda Socio Mutua'><img src='img/ico_pallino_green.png' ></a>";
			       $esistenzasociomutua = "<img src='img/ico_pallino_green.png' >";
					   $modulimutua = 'si';
				    }
				else
				{
				    $esistenzasociomutua = '';
				    $modulimutua = 'no';
				}

			
echo "<tr class='table-secondary'>   
        <td style='text-align:left;'>".$dati_u30['ETA']."</td>
        <td style='text-align:left;'>".$dati_u30['NAG']."</td>
        <td style='text-align:left;'>".$dati_u30['INTESTAZIONE_A']." ".$dati_u30['INTESTAZIONE_B']."</td>
        <td style='text-align:left;'>".$dati_u30['FIL_ANAGRAFICA']."&nbsp;</td>
        <td style='text-align:left;'>".$dati_u30['RAPPORTI']."&nbsp;</td>
        <td style='text-align:center;'>".$esistenzasociobanca."</td>        
        <td style='text-align:center;'>".$esistenzasociomutua."</td>        
        </tr>
          "; 
}

echo '    </tbody>
  </table>
      </div>
    </div>
  </div>

</div>
<!-- /.container-fluid -->';



?>

<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
