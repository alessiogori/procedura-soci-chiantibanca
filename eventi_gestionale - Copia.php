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

// VERIFICO CHE L'UTENTE CHE ACCEDE ALLA PAGINA SIA DELL'UFFICIO SOCI o UN CAPO AREA
if(in_array($_COOKIE['filiale_id'], array('999','998','995','996','100')))   {


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
  <div class="alert alert-dismissible alert-success"><h3><img src="img/eventi.png" height="50">&nbsp;Eventi</h3>
<small>(F5 per aggiornare la pagina)</small>
  </div>
</div>

<?php

if (empty($_GET['action'])) {



    echo '
    <div class="col-lg-12">
    <div class="card text-white bg-light mb-12">
      <div class="card-header">Informazioni per Festa del Socio</div>
      <div class="card-body text-black" align="justify">';

    echo 'Agli eventi della <b>"Festa del Socio"</b> potranno iscriversi solo i soci delle filiali delle zone di riferimento (N.B. l’invito è nominale, non è prevista la possibilità di iscrivere un accompagnatore).
      Per motivi organizzativi è stato stabilito un numero massimo di partecipanti ad ogni evento: risulta, pertanto, obbligatoria la prenotazione che avverrà esclusivamente compilando il modello di registrazione che verrà inviato per mail/SMS ai soci interessati dall\'evento.
      Nel caso in cui il socio non sia in grado di registrarsi in autonomia, potrà procedere all’iscrizione chiamando il numero verde 800 171212 o recandosi in filiale: spetterà a un collega procedere all’iscrizione dalla sua postazione, utilizzando il medesimo link inviato ai Soci.
      Compilato il modello di registrazione, il socio dovrà confermare l’iscrizione alla mail da lui indicata: quindi, riceverà una seconda mail con invito e QR-Code (da mostrare stampato o in formato digitale) per accedere all’evento.
      Nel caso in cui il socio non fosse in possesso di una mail, sarà possibile indicare la mail di filiale (o del collega che procede alla registrazione) e consegnare al socio la stampa dell’iscrizione.
      Ribadiamo: l’invito è nominale e non potrà essere ceduto a terzi.';

    echo '
      </div>
    </div>
    </div>
<hr>
    ';

    //////////////////////////////////////////////////////////////////
    // LISTA EVENTI IN PROGRAMMA
    //////////////////////////////////////////////////////////////////

    $select = " SELECT  idevento,  tipo_evento, descrizione_evento, data_evento, ora_evento, luogo_evento,
                        note, link, posti_disponibili, posti_residui
                FROM tab_eventi
                WHERE 
                      str_to_date(data_evento,'%d/%m/%Y') >=  str_to_date('".$adesso."','%d/%m/%Y')
                ORDER BY str_to_date(data_evento,'%d/%m/%Y')
              " ;
    $qry = mysqli_query($connection, $select);

    echo '
    <div class="col-lg-6">
    <div class="card text-white bg-success mb-12">
      <div class="card-header">Eventi interni in programma</div>
      <div class="card-body">
      <table class="table table-hover" width="90%"> 
        <tr class="table-secondary">
          <td>ID</td>
          <td>Evento</td>
          <td>Data</td>
          <td>Ora</td>
          <td>Posti Totali</td>
          <td>Posti Residui</td>
        </tr>
    ';

    while($dati = mysqli_fetch_array($qry)){ 

      if    ($dati['posti_residui'] == 0) 
              {$posti_residui = 'Sold-Out';}
      else  {$posti_residui = '<a style="text-decoration:none;color:white;" href="eventi_gestionale.php?action=form&idevento='.$dati['idevento'].'">'.$dati['posti_residui'].'</a>';
            }


      if      ($dati['tipo_evento'] == 'Calcio') {$ic = '<i class="fa fa-futbol"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Basket') {$ic = '<i class="fa fa-basketball-ball"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Pallavolo') {$ic = '<i class="fa fa-volleyball-ball"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Teatro') {$ic = '<i class="fa fa-mask"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Concerto') {$ic = '<i class="fa fa-music"></i>&nbsp;&nbsp;';}
      else    {$ic = '<i class="fa fa-users"></i>&nbsp;&nbsp;';}


      echo '<tr>
              <td align="left">'.$dati['idevento'].'</td>
              <td align="left" title="'.$dati['luogo_evento'].'">'.$ic.$dati['descrizione_evento'].'</td>
              <td align="left">'.$dati['data_evento'].'</td>
              <td align="left">'.$dati['ora_evento'].'</td>
              <td align="left"><a style="text-decoration:none;color:white;" href="eventi_gestionale.php?action=elenco&idevento='.$dati['idevento'].'">'.$dati['posti_disponibili'].'</a></td>
              <td align="left">'.$posti_residui.'</td>
            </tr>
            ';

	   }

    echo '</table>
      </div>
    </div>
    </div>
    ';


    //////////////////////////////////////////////////////////////////
    // LISTA EVENTI TRASCORSI
    //////////////////////////////////////////////////////////////////

    $select = " SELECT  idevento,  tipo_evento, descrizione_evento, data_evento, ora_evento, luogo_evento,
                        note, link, posti_disponibili, posti_residui
                FROM tab_eventi
                WHERE 
                      str_to_date(data_evento,'%d/%m/%Y') <  str_to_date('".$adesso."','%d/%m/%Y')
                ORDER BY str_to_date(data_evento,'%d/%m/%Y')
              " ;
    $qry = mysqli_query($connection, $select);

    echo '
    <div class="col-lg-6">
    <div class="card text-white bg-info mb-12">
      <div class="card-header">Eventi interni trascorsi</div>
      <div class="card-body">
      <table class="table table-hover" width="90%"> 
        <tr class="table-secondary">
          <td>ID</td>
          <td>Evento</td>
          <td>Data</td>
          <td>Ora</td>
          <td>Posti Totali</td>
          <td>Posti Residui</td>
        </tr>
    ';

    while($dati = mysqli_fetch_array($qry)){ 

      if    ($dati['posti_residui'] == 0) {$posti_residui = 'Sold-Out';}
      else  {$posti_residui = $dati['posti_residui'];}

      if      ($dati['tipo_evento'] == 'Calcio') {$ic = '<i class="fa fa-futbol"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Basket') {$ic = '<i class="fa fa-basketball-ball"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Pallavolo') {$ic = '<i class="fa fa-volleyball-ball"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Teatro') {$ic = '<i class="fa fa-mask"></i>&nbsp;&nbsp;';}
      elseif  ($dati['tipo_evento'] == 'Concerto') {$ic = '<i class="fa fa-music"></i>&nbsp;&nbsp;';}
      else    {$ic = '<i class="fa fa-users"></i>&nbsp;&nbsp;';}

      echo '<tr>
              <td align="left">'.$dati['idevento'].'</td>
              <td align="left" title="'.$dati['luogo_evento'].'">'.$ic.$dati['descrizione_evento'].'</td>
              <td align="left">'.$dati['data_evento'].'</td>
              <td align="left">'.$dati['ora_evento'].'</td>
              <td align="left"><a style="text-decoration:none;color:white;" href="eventi_gestionale.php?action=elenco&idevento='.$dati['idevento'].'">'.$dati['posti_disponibili'].'</a></td>
              <td align="left">'.$posti_residui.'</td>
            </tr>
            ';

     }

    echo '</table>
      </div>
    </div>
    </div>
    ';
?>

<!-- chiudo la riga --></div>

  <br><br>
    <i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
        <a href="docs/manuale_eventi.pdf" target="_blank">Manuale<a>
        <br>
  <center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>

<?php 

  } // chiudo if action = lista eventi

// -----------------------------------
// INSERIMENTO NOMINATIVO NELL'EVENTO
// -----------------------------------
elseif ($_GET['action'] == "insert") 

  {

    $select_insert = "
            INSERT INTO TAB_EVENTI_ISCRIZIONI
            (idevento,data_richiesta,utente_inserimento,nag,nominativo,data_nascita,luogo_nascita,email,cellulare,note)
            VALUES 
           (
              '".$_GET['ID']."'
             ,NOW()
             ,'".$_GET['user']."'
             ,'".$_GET['nag']."'
             ,'".$_GET['nominativo']."'
             ,'".$_GET['datanascita']."'
             ,'".$_GET['luogonascita']."'
             ,'".$_GET['email']."'
             ,'".$_GET['cellulare']."'
             ,'".$_GET['note']."'
            )
            ";

      echo $select_insert;                    
      mysqli_query($connection, $select_insert )
                    or die("INSERT --- ".mysqli_error($connection));;

    // AGGIORNAMENTO DEL CONTATTORE IN TAB_EVENTI
    // ------------------------------------------
      $select_residuo = "SELECT posti_residui FROM TAB_EVENTI  WHERE IDEVENTO = ".$_GET['ID']."";
      $query = mysqli_query($connection, $select_residuo);
      while($dati = mysqli_fetch_array($query)){ 
        $valore_residuo = (int)$dati['posti_residui'];
        $residuo = $valore_residuo-1;
        }

      $select_update = "
            UPDATE TAB_EVENTI
            SET
            posti_residui = ".$residuo."
            WHERE IDEVENTO = ".$_GET['ID']."";

      mysqli_query($connection, $select_update )
                    or die("INSERT --- ".mysqli_error($connection));;

    echo '<center>Inserimento effettuato - Posti adesso disponibili per l\'evento '.$residuo.'</center>
      <br><br><br>
      <center><a href="eventi_gestionale.php" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';
  }


// -----------------------------------
// FORM INSERIMENTO NOMINATIVO 
// -----------------------------------
elseif ($_GET['action'] == "form") 

  {

        $select = " SELECT  idevento,  tipo_evento, descrizione_evento, data_evento, ora_evento, luogo_evento,
                        note, link, posti_disponibili, posti_residui
                FROM tab_eventi
                WHERE 
                      idevento = ".$_GET['idevento']."
              " ;
        $qry = mysqli_query($connection, $select);
        while($dati = mysqli_fetch_array($qry)){ 

echo '
<center>
<div class="col-lg-4">
    <div class="card text-white bg-success mb-12">
      <div class="card-header">Inserimento nominativo per evento</div>
      <div class="card-body">

<b>ID '.$dati['idevento'].' - '.$dati['descrizione_evento'].'&nbsp;&nbsp;('.$dati['data_evento'].' - '.$dati['ora_evento'].')<br>
Posti Residui '.$dati['posti_residui'].'</b>
<br><br>

<form action="'.$_SERVER['PHP_SELF'].'" method="GET" onsubmit="return ray.ajax()">

      <input id="action" name="action" type="hidden" class="form-control" value="insert" readonly>
      <input id="ID" name="ID" type="hidden" class="form-control" value="'.$_GET['idevento'].'" readonly>
      <input id="adesso" name="adesso" type="hidden" class="form-control" value="'.$adesso.'" readonly>
      <input id="user" name="user" type="hidden" class="form-control" value="'.trim($_COOKIE['user_nomeCookie']).'" readonly>

  <div class="form-group row">
    <label for="nag" class="col-4 col-form-label">NAG</label> 
    <div class="col-8">
      <input id="nag" name="nag" placeholder="" type="text" class="form-control" >
    </div>
  </div>
  <div class="form-group row">
    <label for="nominativo" class="col-4 col-form-label">Nominativo</label> 
    <div class="col-8">
      <input id="nominativo" name="nominativo" placeholder="Cognome e Nome" type="text" class="form-control" required="required">
    </div>
  </div>
  <div class="form-group row">
    <label for="datanascita" class="col-4 col-form-label">Data di Nascita</label> 
    <div class="col-8">
      <input id="datanascita" name="datanascita" placeholder="gg/mm/aaaa" type="text" class="form-control" required="required">
    </div>
  </div>
  <div class="form-group row">
    <label for="luogonascita" class="col-4 col-form-label">Luogo di Nascita</label> 
    <div class="col-8">
      <input id="luogonascita" name="luogonascita" type="text" class="form-control" required="required">
    </div>
  </div>
  <div class="form-group row">
    <label for="email" class="col-4 col-form-label">Email</label> 
    <div class="col-8">
      <input id="email" name="email" type="text" class="form-control">
    </div>
  </div> 
  <div class="form-group row">
    <label for="cellulare" class="col-4 col-form-label">Cellulare</label> 
    <div class="col-8">
      <input id="cellulare" name="cellulare" type="text" class="form-control">
    </div>
  </div> 
  <div class="form-group row">
    <label for="note" class="col-4 col-form-label">Note</label> 
    <div class="col-8">
      <input id="note" name="note" type="text" class="form-control">
    </div>
  </div> 
  <div class="form-group row">
    <div class="offset-4 col-8">
      <button name="submit" type="submit" class="btn btn-primary">Inserisci prenotazione</button>
    </div>
  </div>
</form>

      </div>
    </div>
    </div>
';

echo '<br><br>
    <i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
        <a href="docs/manuale_eventi.pdf" target="_blank">Manuale<a>
        <br>
  <center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';

    }

   }


// -----------------------------------
// ELENCO ISCRITTI ALL'EVENTO
// -----------------------------------
elseif ($_GET['action'] == "elenco") 

  {


        $select_titolo = " SELECT  
                      idevento,  tipo_evento, descrizione_evento,  data_evento, ora_evento, luogo_evento, note, link, posti_disponibili, posti_residui
                FROM tab_eventi 
                WHERE 
                      idevento = ".$_GET['idevento']."
              " ;
        $qry_titolo = mysqli_query($connection, $select_titolo);
        while($dati_titolo = mysqli_fetch_array($qry_titolo)){ 


      if      ($dati_titolo['tipo_evento'] == 'Calcio') {$ic = '<i class="fa fa-futbol"></i>&nbsp;&nbsp;';}
      elseif  ($dati_titolo['tipo_evento'] == 'Basket') {$ic = '<i class="fa fa-basketball-ball"></i>&nbsp;&nbsp;';}
      elseif  ($dati_titolo['tipo_evento'] == 'Pallavolo') {$ic = '<i class="fa fa-volleyball-ball"></i>&nbsp;&nbsp;';}
      elseif  ($dati_titolo['tipo_evento'] == 'Teatro') {$ic = '<i class="fa fa-mask"></i>&nbsp;&nbsp;';}
      elseif  ($dati_titolo['tipo_evento'] == 'Concerto') {$ic = '<i class="fa fa-music"></i>&nbsp;&nbsp;';}
      else    {$ic = '<i class="fa fa-users"></i>&nbsp;&nbsp;';}

          $titolo = 'ID '.$dati_titolo['idevento'].' - <b>'.$ic.$dati_titolo['descrizione_evento'].'</b><br><small>'.
                    $dati_titolo['luogo_evento'].' - '.
                    $dati_titolo['data_evento'].' - '.
                    $dati_titolo['ora_evento'].' &nbsp;'.
                    '(Posti Disponibili '.$dati_titolo['posti_disponibili'].
                    ' - Posti Residui '.$dati_titolo['posti_residui'].')'.
                    $dati_titolo['link'];
           }

        $select = " SELECT  
                      i.idevento,  tipo_evento, descrizione_evento, NAG, nominativo, data_nascita, luogo_nascita, email,
                      cellulare, data_richiesta, utente_inserimento, i.note
                FROM tab_eventi_iscrizioni as i join tab_eventi as e
                ON i.idevento = e.idevento
                WHERE 
                      i.idevento = ".$_GET['idevento']."
              order by nominativo" ;
        $qry = mysqli_query($connection, $select);

    echo '
    <div class="col-lg-12">
    <div class="card text-white bg-secondary mb-12">
      <div class="card-header">'.$titolo.'</div>
      <div class="card-body">
      <table class="table table-hover" width="90%"> 
        <tr class="table-secondary">
          <td>nr</td>
          <td>NAG</td>
          <td>Nominativo</td>
          <td>Data Nascita</td>
          <td>Luogo Nascita</td>
          <td>Email</td>
          <td>Cellulare</td>
          <td><small>Data Richiesta<br>User Inserimento</small></td>
          <td>Note</td>
        </tr>
    ';

    $counter=1;

    while($dati = mysqli_fetch_array($qry)){ 

      echo '<tr>
              <td align="left">'.$counter.'</td>
              <td align="left">'.$dati['NAG'].'</td>
              <td align="left">'.strtoupper($dati['nominativo']).'</td>
              <td align="left">'.$dati['data_nascita'].'</td>
              <td align="left">'.$dati['luogo_nascita'].'</td>
              <td align="left">'.$dati['email'].'</td>
              <td align="left">'.$dati['cellulare'].'</td>
              <td align="left"><small>'.$dati['data_richiesta'].'<br>'.$dati['utente_inserimento'].'</small></td>
              <td align="left">'.$dati['note'].'</td>
            </tr>
            ';
    $counter++;

     }

    echo '</table>
      </div>
    </div>
    </div>
    ';

    echo '<br><br>
    <i class="fas fa-file-pdf fa-1x text-lightgray-300 col-auto"></i>
        <a href="docs/manuale_eventi.pdf" target="_blank">Manuale<a>
        <br>
  <center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>';


}

  else // finale

  {

    echo 'peeeee';
  }

} // chiudo IF principale dell'autenticazione
?>

</center>
</body>
</html>