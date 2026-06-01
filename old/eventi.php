<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(0);

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

<!-- SetUp fixed image -->
    <style>
        /* Custom CSS for fixed-size image */
        .fixed-image {
            width: 300px; /* Set the fixed width for the div */
            height: 300px; /* Set the fixed height for the div */
            overflow: hidden; /* Hide any content that overflows the div */
        }
        .fixed-image img {
            width: 100%; /* Make the image fill the entire div width */
            height: 100%; /* Make the image fill the entire div height */
            object-fit: cover; /* Scale and crop the image to cover the entire div */
        }
        .watermark {
            position: absolute;
            opacity: 0.5; /* Adjust the opacity as needed */            
        }
    </style>


<!-- Begin Page Content -->
<div class="container-fluid">
    
<!-- Content Row 1 -->
<center>
<div class="row">

<!-- TITOLO PAGINA -->
<div class="col-lg-12">
	<div class="alert alert-dismissible alert-success"><h3><img src="img/eventi.png" height="50">&nbsp;Eventi</h3>
        <i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
        <a href="docs/manuale_eventi.pdf" target="_blank">Manuale<a>
        &nbsp;&nbsp;&nbsp;
        <i class="fas fa-users fa-1x text-lightgray-300 col-auto"></i>
        <a href="eventi_gestionale.php">Gestionale<a>
    
    </div>  
</div>


<div class="col-lg-12">

<div class="card-deck" style="text-align: left;">

<?php
//////////////////////////////////////////////////////////////////
// GIOVANI SOCI
//////////////////////////////////////////////////////////////////   
?>
<div class="col-lg-4">
  <div class="card">
      <div class="card-header bg-warning">GIOVANI SOCI
        <i class="fas fa-hand-peace fa-1x text-white-300 col-auto"></i>
      </div>
    <div class="card-body">
  
                  <a href="stats/situazione.php?f=&filiale=<?php echo $chiaveURL;?>" target="_blank" title="Statistiche Area" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Situazione</h4>
                  </a>

                  <br>

   </div>
    <div class="card-footer">
      <small class="text-muted">per analisi dati</small>
    </div>
  </div>
</div>  

<?php
//////////////////////////////////////////////////////////////////
// SOCI
//////////////////////////////////////////////////////////////////   
?>
<div class="col-lg-8">
  <div class="card">
      <div class="card-header bg-success">SOCI
        <i class="fas fa-hand-peace fa-1x text-white-300 col-auto"></i>
      </div>
    <div class="card-body">
                  
                  <a href="lista_domande.php?filiale=<?php echo $chiaveURL;?>" target="_blank" title="Domande da esaminare" style="text-decoration: none;">
                    <h4 class="card-title" style="color:#FFFFFF;"><i class="fa fa-poll fa-1x text-gray"></i>&nbsp;Domande da esaminare</h4>
                  </a>               

                  <br>
    </div>
    <div class="card-footer">
      <small class="text-muted">elenchi di dettaglio</small>
    </div>
  </div>
</div>

<?php
/*

// Watermark SoldOut
$soldout = '<img class="watermark" src="img/soldout.png" alt="Watermark" width="300">';
             
// EVENTO 01 - GIOVANI SOCI
// ------------------------
$evento_01_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/22/dlXcodvLfXfqbTZl4jL7uIHB3EE9mMuKZNMlRTrE.jpg">';
$evento_01_link_page = '<a href="https://eventi.chiantibanca.it/evento/13" target="_blank">';

// EVENTO 02 - Villa Mocale
// ------------------------
$evento_02_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/15/t6EBpx1hK4SkK1Nq9wZZQg9jhF1txttYdvKfieVt.jpg">';
$evento_02_link_page = '<a href="https://eventi.chiantibanca.it/evento/1" target="_blank">';

// EVENTO 03 - Piccolo Castello
// ----------------------------
$evento_03_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/16/oW2pGGqyskSXpiQDAU69Dsx4E8uyExTJvecpymfE.jpg">';
$evento_03_link_page = '<a href="https://eventi.chiantibanca.it/evento/2" target="_blank">';

// EVENTO 04 - Piccolo Castello
// ----------------------------
$evento_04_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/17/3h9rLrE3UGIHzzmIwDSBTXEbMF7ZGksDd9GC11LY.jpg">';
$evento_04_link_page = '<a href="https://eventi.chiantibanca.it/evento/7" target="_blank">';

// EVENTO 05 - Tenuta Artimino
// ----------------------------
$evento_05_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/18/lp8uLpRECZTsxxkOueTCAGVX0Lj3EJhvWljjavLx.jpg">';
$evento_05_link_page = '<a href="https://eventi.chiantibanca.it/evento/8" target="_blank">';

// EVENTO 06 - Forte Belvedere
// ----------------------------
$evento_06_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/23/yDhqABlnLiEXWZfSOAJhjzZHdDTNYOKdPj3UI0JX.jpg">';
$evento_06_link_page = '<a href="https://eventi.chiantibanca.it/evento/9" target="_blank">';

// EVENTO 07 - Arsenali
// ----------------------------
$evento_07_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/20/d2KrJEpj3hWDeQI9Q2m1ZJ0Dd5Hu0H94PhySUM2q.jpg">';
$evento_07_link_page = '<a href="https://eventi.chiantibanca.it/evento/10" target="_blank">';

// EVENTO 08 - Hotel 500
// ----------------------------
$evento_08_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/21/S41LdpFrwgnHcPffm07TE71p0gU8KS6u3gqWomSi.jpg">';
$evento_08_link_page = '<a href="https://eventi.chiantibanca.it/evento/11" target="_blank">';

// EVENTO 09 - Villa Cappugi
// ----------------------------
$evento_09_link_img = '<img class="fixed-image" src="https://eventi.chiantibanca.it/mediagallery/13/lPkSSnRpAvYY721d0Y4XZDuLzvZLZlnoLIPstRQW.jpg">';
$evento_09_link_page = '<a href="https://eventi.chiantibanca.it/evento/12" target="_blank">';

?>

<div class="col-lg-12" style="background-color:#222222;">
<div class="card-deck" style="text-align: left;" style="background-color:#222222;"> 

  <div class="card">
      <div class="card-header" style="background-color:#222222;">GIOVANI SOCI
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<table width="80%" border="0">
    <tr>
        <td>
            <?php
                    echo $evento_01_link_page;  echo $soldout;
                    echo $evento_01_link_img.'</a>';
            ?>
        </td>
        <td>&nbsp;&nbsp;</td>
        <td>
            <?php
                    echo '';
            ?>            
        </td>
    </tr>
</table>

    </div>
  </div>

  <!-- chiudo la riga --></div>
<div class="card-deck" style="text-align: left;">

  <div class="card">
      <div class="card-header" style="background-color:#222222;">Zona CHIANTI
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<?php
        // echo $evento_02_link_page; // echo $soldout;
        // echo $evento_02_link_img.'</a>';
?>
    </div>
  </div>

  <div class="card">
      <div class="card-header" style="background-color:#222222;">Zona FIRENZE
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<?php
        // echo $evento_06_link_page; // echo $soldout;
        // echo $evento_06_link_img.'</a>';
?>
    </div>
  </div>

  <div class="card">
      <div class="card-header" style="background-color:#222222;">Zona SIENA
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<?php
        // echo $evento_03_link_page; // echo $soldout;
        // echo $evento_03_link_img.'</a>';
?>
    </div>
  </div>

  <div class="card">
      <div class="card-header" style="background-color:#222222;">Zona VAL D'ELSA
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<?php
        // echo $evento_04_link_page; // echo $soldout;
        // echo $evento_04_link_img.'</a>';
?>
    </div>
  </div>

  <!-- chiudo la riga --></div>
<div class="card-deck" style="text-align: left;">

  <div class="card">
      <div class="card-header" style="background-color:#222222;">Zona CAMPI
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<?php
        // echo $evento_08_link_page; // echo $soldout;
        // echo $evento_08_link_img.'</a>';
?>
    </div>
  </div>

  <div class="card">
      <div class="card-header" style="background-color:#222222;">Zona PRATO
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<?php
        // echo $evento_05_link_page; // echo $soldout;
        // echo $evento_05_link_img.'</a>';
?>
    </div>
  </div>

  <div class="card">
      <div class="card-header" style="background-color:#222222;">Zona PISTOIA
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<?php
        // echo $evento_09_link_page; // echo $soldout;
        // echo $evento_09_link_img.'</a>';
?>
    </div>
  </div>

  <div class="card">
      <div class="card-header" style="background-color:#222222;">Zona TIRRENO
        <i class="fas fa-hand-peace fa-1x text-gray-300 col-auto"></i>
      </div>
    <div class="card-body" style="background-color:#222222;">

<?php
        // echo $evento_07_link_page; // echo $soldout;
        // echo $evento_07_link_img.'</a>';
?>
    </div>
  </div>


<!-- chiudo la riga --></div>
<!-- FINE ULTIMO DIV --></div>


</div> <!-- chiusura row -->

</div> <!-- /.container-fluid -->

	<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>
*/
?>

<!-- Chiudo DIV blocco --></div>

<!-- chiudo la riga --></div>
<!-- FINE ULTIMO DIV --></div>


<br><br><center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
