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

if ($_GET['fascia'] == 1) 
    { $param = ' AND AnniAnzianitaSocio <= 10 '; } 
elseif ($_GET['fascia'] == 2) 
    { $param = ' AND AnniAnzianitaSocio between 10 and 20 '; } 
elseif ($_GET['fascia'] == 3) 
    { $param = ' AND AnniAnzianitaSocio between 20 and 30 '; } 
elseif ($_GET['fascia'] == 4) 
    { $param = ' AND AnniAnzianitaSocio between 30 and 40 '; } 
elseif ($_GET['fascia'] == 5) 
    { $param = ' AND AnniAnzianitaSocio between 40 and 50 '; } 
elseif ($_GET['fascia'] == 6) 
    { $param = ' AND AnniAnzianitaSocio > 50 '; } 
else
    { $param = ' '; } 

// Griglia per fasce
if ($_GET['start'] == 0)
{

    if (empty($_GET['filiale'])) 
    {$condizionefiliale = "WHERE Filiale <> 999 ".$param." ";}
    else 
    {$condizionefiliale = " WHERE Filiale = ".$_GET['filiale']." ".$$param." ";}

    $dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
     
    if ($dbhandle -> connect_error) {
        exit("There was an error with your connection: ".$dbhandle -> connect_error);
    }
     
    // ********************************************************
    // DATI GENERALI
    // ********************************************************
    $select = " SELECT  
                view_fasce_anzianitasocio.NAG,
                view_fasce_anzianitasocio.Nominativo,
                view_fasce_anzianitasocio.DataEntrata,
                view_fasce_anzianitasocio.AnniAnzianitaSocio,
                view_fasce_anzianitasocio.NumeroAzioni,
                view_fasce_anzianitasocio.Importo,
                view_fasce_anzianitasocio.DataNascita,
                view_fasce_anzianitasocio.Eta,
                view_fasce_anzianitasocio.Filiale,
                view_fasce_anzianitasocio.NomeFiliale,
                view_fasce_anzianitasocio.Area,             
                case 
                    when AnniAnzianitaSocio <= 10          then 'Fascia 1 - fino a 10 anni'
                    when AnniAnzianitaSocio between 10 and 20 then 'Fascia 2 - fino a 20 anni'
                    when AnniAnzianitaSocio between 20 and 30 then 'Fascia 3 - fino a 30 anni'
                    when AnniAnzianitaSocio between 30 and 40 then 'Fascia 4 - fino a 40 anni'
                    when AnniAnzianitaSocio between 40 and 50 then 'Fascia 5 - fino a 50 anni'
                    when AnniAnzianitaSocio > 50           then 'Fascia 6 - oltre 50 anni'
                else '' end as Fascia
          FROM view_fasce_anzianitasocio
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
          <h4 class="m-2 font-weight-bold text-success">Soci ChiantiBanca '.$_GET['fascia'].'</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr class="table-primary">
            <th style="text-align:left; font-size:13px;">Fascia</th>
            <th style="text-align:left; font-size:13px;">NAG</th>
            <th style="text-align:left; font-size:13px;">Nominativo</th>
            <th style="text-align:left; font-size:13px;">Anz.Socio</th>
            <th style="text-align:left; font-size:13px;">Età</th>        
            <th style="text-align:center; font-size:13px;">Filiale</th>
            <th style="text-align:left; font-size:13px;">Nome Filiale</th>
            <th style="text-align:right; font-size:13px;">Azioni Totali</th>
            <th style="text-align:right; font-size:13px;">Controvalore Euro</th>
            <th style="text-align:right; font-size:13px;">Socio Mutua</th>        
          </tr>
        </thead>
        <tbody>
    ';  

    while($datisocio=mysqli_fetch_array($querydati)){ 
        
        $selectProt = " SELECT idsocio 
                        FROM sds_soci
                        WHERE NAG = ".$datisocio['NAG']."
                        ";

        logquery ($selectProt);
        //echo $select;
        $querydatiProt = $dbhandle->query($selectProt) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
        while($datisocioProt=mysqli_fetch_array($querydatiProt)){ 
            $idsocio = $datisocioProt['idsocio'];
        }

    			// ********************************************************
    			// RICERCA SE IL SOCIO E' ANCHE SOCIO MUTUA
    			// ********************************************************
    			$select_mutua 	  = "	SELECT * FROM tab_mutua
    									WHERE nag = ".$datisocio['NAG'];
    			logquery ($select_mutua);  
    			$querydati_mutua = mysqli_query($connection, $select_mutua);
    				if(mysqli_num_rows($querydati_mutua) > 0)
    				    while($datisociomutua = mysqli_fetch_array($querydati_mutua))
    				    {
    				       $esistenzasociomutua = 
    					 	"<img src='img/ico_pallino_green.png' >";
    					   $modulimutua = 'si';
    				    }
    				else
    				{
    				    $esistenzasociomutua = '';
    				    $modulimutua = 'no';
    				}
    				    
    echo "<tr class='table-secondary'>   
            <td style='text-align:left;'>".$datisocio['Fascia']."</td>
            <td style='text-align:left;'>".$datisocio['NAG']."</td>
            <td style='text-align:left;'>
            <a class='text-success' href='sqldati_schedasocio.php?id=".$idsocio."'>".$datisocio['Nominativo']."</a></td>
            <td style='text-align:left;'>".$datisocio['AnniAnzianitaSocio']."&nbsp;-&nbsp;<small>".$datisocio['DataEntrata']."</small></td>
            <td style='text-align:left;'>".$datisocio['Eta']."&nbsp;</td>
            <td style='text-align:left;'>".$datisocio['Filiale']."&nbsp;</td>        
            <td style='text-align:left;'>".$datisocio['NomeFiliale']."&nbsp;</td>
            <td style='text-align:right;'>".number_format($datisocio['NumeroAzioni'],0,',','.')."&nbsp;</td>
            <td style='text-align:right;'>".number_format(($datisocio['Importo']),0,',','.')."&nbsp;</td>
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
}


// -----------------------------------------------------------------------------------------
// Griglia per fasce
if ($_GET['start'] == 1)
{

    if ($_GET['nextyear'] == 'si')
      { $param2 = ' AND AnniAnzianitaSocioNextYear ='.$_GET['anzianita'] ; 
        $titolo_param2 = ' il prossimo anno';} 
    else
      { $param2 = ' AND AnniAnzianitaSocio ='.$_GET['anzianita'] ; 
        $titolo_param2 = ' ';} 

    if (empty($_GET['filiale'])) 
    {$condizionefiliale_anz = "WHERE Filiale <> 999 ".$param2." ";}
    else 
    {$condizionefiliale_anz = " WHERE Filiale = ".$_GET['filiale']." ".$param2." ";}

    $dbhandle = new mysqli($host, $db_user, $db_psw, $db_name);
     
    // ********************************************************
    // DETTAGLIO SOCI PER ANZIANITA'
    // ********************************************************
    $select_ANZ = " SELECT  *
          FROM view_fasce_anzianitasocio
          ".$condizionefiliale_anz."
          ORDER BY Nominativo";

    logquery ($select_ANZ);
    //echo $select_ANZ;
    $querydati_ANZ = $dbhandle->query($select_ANZ) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");

    echo '
    <!-- Begin Page Content -->
    <div class="container-fluid">

      <!-- DataTales Example -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h4 class="m-2 font-weight-bold text-success">Soci ChiantiBanca con anzianità '.$_GET['anzianita'].' anni '.$titolo_param2.'</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr class="table-primary">
            <th style="text-align:left; font-size:13px;">NAG</th>
            <th style="text-align:left; font-size:13px;">Nominativo</th>
            <th style="text-align:left; font-size:13px;">Anz.Socio</th>
            <th style="text-align:left; font-size:13px;">Età</th>        
            <th style="text-align:center; font-size:13px;">Filiale</th>
            <th style="text-align:left; font-size:13px;">Nome Filiale</th>
            <th style="text-align:right; font-size:13px;">Azioni Totali</th>
            <th style="text-align:right; font-size:13px;">Controvalore Euro</th>
            <th style="text-align:right; font-size:13px;">Socio Mutua</th>        
          </tr>
        </thead>
        <tbody>
    ';  

    while($datisocio_ANZ=mysqli_fetch_array($querydati_ANZ)){ 
        
        $selectProt_ANZ = " SELECT idsocio 
                        FROM sds_soci
                        WHERE nag = ".$datisocio_ANZ['NAG']."
                        ";

        logquery ($selectProt_ANZ);
        //echo $select;
        $querydatiProt_ANZ = $dbhandle->query($selectProt_ANZ) or exit("Error code ({$dbhandle->errno}): {$dbhandle->error}");
        while($datisocioPro_ANZ=mysqli_fetch_array($querydatiProt_ANZ)){ 
            $prot_ANZ = $datisocioPro_ANZ['idsocio'];
        }

          // ********************************************************
          // RICERCA SE IL SOCIO E' ANCHE SOCIO MUTUA
          // ********************************************************
          $select_mutua_ANZ     = " SELECT * FROM tab_mutua
                      WHERE nag = ".$datisocio_ANZ['NAG'];
          logquery ($select_mutua_ANZ);  
          $querydati_mutua_ANZ = mysqli_query($connection, $select_mutua_ANZ);
            if(mysqli_num_rows($querydati_mutua_ANZ) > 0)
                while($datisociomutua_ANZ = mysqli_fetch_array($querydati_mutua_ANZ))
                {
                   $esistenzasociomutua_ANZ = 
                "<img src='img/ico_pallino_green.png' >";
                 $modulimutua_ANZ = 'si';
                }
            else
            {
                $esistenzasociomutua_ANZ = '';
                $modulimutua_ANZ = 'no';
            }
                
    echo "<tr class='table-secondary'>   
            <td style='text-align:left;'>".$datisocio_ANZ['NAG']."</td>
            <td style='text-align:left;'>
            <a class='text-success' href='sqldati_schedasocio.php?id=".$prot_ANZ."'>".$datisocio_ANZ['Nominativo']."</a></td>
            <td style='text-align:left;'>".$datisocio_ANZ['AnniAnzianitaSocio']."&nbsp; - &nbsp;<small>".$datisocio_ANZ['DataEntrata']."</small></td>
            <td style='text-align:left;'>".$datisocio_ANZ['Eta']."&nbsp;</td>
            <td style='text-align:left;'>".$datisocio_ANZ['Filiale']."&nbsp;</td>        
            <td style='text-align:left;'>".$datisocio_ANZ['NomeFiliale']."&nbsp;</td>
            <td style='text-align:right;'>".number_format($datisocio_ANZ['NumeroAzioni'],0,',','.')."&nbsp;</td>
            <td style='text-align:right;'>".number_format(($datisocio_ANZ['Importo']),0,',','.')."&nbsp;</td>
            <td style='text-align:center;'>".$esistenzasociomutua_ANZ."</td>          
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
}

?>

<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png"></center>
