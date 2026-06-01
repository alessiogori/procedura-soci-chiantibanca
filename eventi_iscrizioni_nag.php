<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2023)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// Includo i dati di connessione
include("config/_config.php");
include("config/_functions.php");

// including FusionCharts PHP wrapper
include("graph/fusioncharts.php"); 

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

$adesso = date("d.m.Y");
$anno = date("Y");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>

<?php
// echo $_SERVER['HTTP_REFERER'];


$adesso = date("d/m/Y");


if (!isset($_GET['datain']) OR empty($_GET['datain']) ) 
      {
            $_GET['datain'] = '01/01/1900';
      }

if (!isset($_GET['dataout']) OR empty($_GET['dataout']) ) 
      {

            $_GET['dataout'] = date("d/m/Y", strtotime("-1 day"));            // 1 giorno indietro perchè SADAS è sempre a ieri sera !!
      }
?>


<center>

<!-- Begin Page Content -->
<div class="container-fluid">

<!-- Content Row 1 -->
<div class="row"> 

<div class="col-lg-12">
  <div class="alert alert-dismissible alert-success"><h3>Elenco Eventi per il Socio <?php echo $_GET['nag'];?></h3></div>
</div>

<?php


        $select = " SELECT  
                      i.idevento, tipo_evento, descrizione_evento, NAG, nominativo, data_nascita, luogo_nascita, email,
                      cellulare, data_richiesta, utente_inserimento, i.note
                FROM tab_eventi_iscrizioni as i join tab_eventi as e
                ON i.idevento = e.idevento
                WHERE 
                      NAG = ".$_GET['nag']."
              " ;
        $qry = mysqli_query($connection, $select);

    echo '
    <div class="col-lg-12">
    <div class="card text-white bg-secondary mb-12">
      <div class="card-body">
      <table class="table table-hover" width="90%"> 
        <tr class="table-secondary">
          <td>ID</td>
          <td>Evento</td>
          <td>NAG</td>
          <td>Nominativo</td>
          <td>Email</td>
          <td>Cellulare</td>
          <td><small>Data Richiesta<br>User Inserimento</small></td>
          <td>Note</td>
        </tr>
    ';

    while($dati = mysqli_fetch_array($qry)){ 


      if      ($dati['tipo_evento'] == 'Calcio') {$ic = '<i class="fa fa-futbol"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Basket') {$ic = '<i class="fa fa-basketball-ball"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Pallavolo') {$ic = '<i class="fa fa-volleyball-ball"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Teatro') {$ic = '<i class="fa fa-mask"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Concerto') {$ic = '<i class="fa fa-music"></i>&nbsp;&nbsp;';}
      else    {$ic = '<i class="fa fa-users"></i>&nbsp;&nbsp;';}


      echo '<tr>
              <td align="left">'.$dati['idevento'].'</td>
              <td align="left">'.$ic.$dati['descrizione_evento'].'</td>
              <td align="left">'.$dati['NAG'].'</td>
              <td align="left">'.$dati['nominativo'].'</td>
              <td align="left">'.$dati['email'].'</td>
              <td align="left">'.$dati['cellulare'].'</td>
              <td align="left"><small>'.$dati['data_richiesta'].'<br>'.$dati['utente_inserimento'].'</small></td>
              <td align="left">'.$dati['note'].'</td>
            </tr>
            ';

     }

    echo '</table>
      </div>
    </div>
    </div>
    ';

    echo '<br><br><br>
  <center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';


?>

</center>
</body>
</html>