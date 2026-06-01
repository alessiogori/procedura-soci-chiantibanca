<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);


 require_once('config/_functions.php');   //logquery ($selectdati); 
 include("config/_config.php");
 $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
// Head e CSS
// include("../css/main.php");
// include("../css/menu.php");

    function clean_text($string)
    {
     $string = trim($string);
     $string = stripslashes($string);
     $string = htmlspecialchars($string);
     return $string;
    }

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************


if 
    ($_GET['action'] == "")
	{


	if ($_GET['start'] == "IN") 
		{ $tipologia = 'IN';
		  $titolo = 'NUOVO INGRESSO'; 
		  $tendina = '  <div class="form-group">
					      <label for="motivazione" class="form-label mt-4"><span style="color:lightgreen;">Motivazione sintetica INGRESSO</span></label><br>
					      <select class="form-select" id="motivazione" name="motivazione">
					        <option value="Vantaggi economici su Rapporti">Vantaggi economici su Rapporti</option>
					        <option value="Vantaggi economici ChiantiMutua">Vantaggi economici ChiantiMutua</option>
                            <option value="Altro">Altro (indicare nelle Note)</option>
					      </select>
					    </div>';
		}
	elseif ($_GET['start'] == "OUT") 
		{ $tipologia = 'OUT';
		  $titolo = 'USCITA'; 
		  $tendina = '  <div class="form-group">
					      <label for="motivazione" class="form-label mt-4">Motivazione sintetica USCITA</label><br>
					      <select class="form-select" id="motivazione" name="motivazione">
					        <option value="Cambio Banca">Cambio Banca</option>
					        <option value="Cessione azioni">Cessione azioni</option>
					        <option value="Deceduto">Deceduto</option>
					        <option value="Sofferenza">Sofferenza</option>
					      </select>
					    </div>';
		}

echo '<center><br>
        <h2>SEGNALAZIONE MOTIVAZIONE '.$titolo.' SOCIO</h2>';
        
echo '  <style type="text/css">
          @import "css/bootstrap.css";
          @import "css/bootstrap.min.css";
          @import "css/fontawesome-free/css/all.min.css";
        </style> ';
        
?>

<br><br>
<form action="motivazioni_form.php" method="GET" onsubmit="return ray.ajax()">
<table border="0" align="center" width="30%">
    <tr>
        <td align="left" valign="top">
            <div class="form-group" >
            <span style="color:lightgreen;">Aspirante Socio </span><b><h5><?php echo $_GET['nome']; ?></h5></b>
            </div>
        </td>
    </tr>    
    <tr>
        <td align="left" valign="top">
            <div class="form-group">
            <span style="color:lightgreen;">NAG:</span> <?php echo $_GET['nag']; ?> - <span style="color:lightgreen;">Filiale:</span> <?php echo $_GET['filiale']; ?>
            </div>
        </td>
    </tr>    
    <tr>
        <td align="left" valign="top">
            <div class="form-group">
           <?php echo $tendina; ?>
            </div>
        </td>
    </tr>  
    <tr>
        <td>
            <div class="form-group">
              <label for="operatore" class="form-label mt-4"><span style="color:lightgreen;">Dipendente segnalatore</span></label>
				<input type="text" class="form-control" id="operatore" name="operatore" placeholder="Cognome e Nome" required>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="form-group">
              <textarea class="form-control" id="note" name="note" rows="3" placeholder="Indicare qui eventuali note"></textarea>
            </div>
        </td>
    </tr>
    <tr >
        <td align="center">
            <div class="form-group">
                <button type="submit" class="btn btn-danger mb-2"><i class="fas fa-upload fa-2x text-lightgray-300 col-auto"></i>INSERISCI</button>
            </div>
        </td>
    </tr>
    
</table>

 <input type="hidden" name="nag" value="<?php echo $_GET['nag']; ?>">
 <input type="hidden" name="nome" value="<?php echo $_GET['nome']; ?>">
 <input type="hidden" name="tipologia" value="<?php echo $tipologia; ?>">
 <input type="hidden" name="filiale" value="<?php echo $_GET['filiale']; ?>">
 <input type="hidden" name="data_domanda" value="<?php echo $_GET['data_domanda']; ?>">
 <input type="hidden" name="action" value="insert">
                            
</form>

<?php

	}

elseif 

    ($_GET['action'] == "insert")

	{

// inserisco il record
$note = mysqli_real_escape_string($connection,htmlspecialchars($_GET['note']));

$insert =
                    'INSERT INTO tab_motivazioni
                    (nag, nominativo, tipologia, motivazione, note, filiale, operatore, data_segnalazione, attivo, data_domanda)
                    
					 VALUES
					 ("'.$_GET['nag'].'","'.$_GET['nome'].'","'.$_GET['tipologia'].'","'.$_GET['motivazione'].'","'.$note.'","'.$_GET['filiale'].'","'.strtoupper($_GET['operatore']).'",now(),"S","'.$_GET['data_domanda'].'")
                    ' ;
                     // echo $insert;
mysqli_query($connection,$insert) or die(mysqli_error($connection));;

echo '<center><br><br><b style="color:green;">Record inserito</b><br><br>
		Chiudi pure questa finestra';
/*
echo '<center><br><br><b style="color:green;">Record inserito</b><br><br>
        <a href="#" onClick=”javascript:self.close()”>Clicca qui per chiudere questa finestra</a>';
*/
   	}
    	


?>

                