<?php
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

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

?>

<!-- Page level plugins -->
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap4.min.js"></script>

<?php

// ------------------------------------
// PRESENTO IL FORM DI INSERIMENTO NOTE
// ------------------------------------
if (($_GET['id'] == 'N') && ($_GET['tipo'] == 'edit')) {
?>

<center><h4 class="m-2 font-weight-bold text-success">Annotazione per verifica della competenza territoriale del Socio</h4></center>
<br>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $_GET['id']; ?>&tipo=update" method="POST">
	  <script type="text/javascript" src="js/nicEdit.js"></script> 

	<table border="0" align="center" width="70%" cellspacing="3" cellpadding="3">
		<tr>
			<td align="left" colspan="5">
				<!-- CAG E NOME SOCIO-->
				<h5><b>cag <?php echo $_GET['cag']; ?> - <?php echo $_GET['nominativo']; ?></b></h5>
			</td>
			<td align="right" valign="top">
			    <input class="btn btn-success" type="submit" value="Inserisci">
                <input type="hidden" name="tipo" value="insert">
                <input type="hidden" name="filiale" value="<?php echo $_GET['filiale']; ?>">
                <input type="hidden" name="cag" value="<?php echo $_GET['cag']; ?>">
                <input type="hidden" name="nominativo" value="<?php echo $_GET['nominativo']; ?>">                
			</td>
		</tr>
		
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Descrizione della verfica effettuata</td>
			<td colspan="5">
			<!-- DESCRIZIONE -->
			<!--	<script type="text/javascript">
				//<![CDATA[
						bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
				//]]>
				</script> -->
				<textarea style="background-color:#FAEDD8;font-size:13px;" class="form-control" name="descrizione" cols="100" rows="10"></textarea>
			</td>
		</tr>
		
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Documenti presenti nel documentale</td>
			<td>
				<!-- DOCUMENTALE -->
				<select class="custom-select" style="background-color:#FAEDD8;" name="documentale">
					<option value='NO'>NO</option>
					<option value='SI'>SI</option>
				</select>
			</td>
			<td class="col-form-label col-form-label-sm" align="right">Operatore</td>
			<td>
				<!-- OPERATORE -->
				<input  style="background-color:#FAEDD8;" class="form-control" type="text" name="operatore" size="60" value="<?php echo 'LN00'.$_COOKIE['usr_id']; ?>" placeholder="LNxxxxx">
			</td>			
			<td class="col-form-label col-form-label-sm" align="right">Status Esito</td>
			<td>
				<!-- STATUS ESITO -->
				<select class="custom-select" style="background-color:#FAEDD8;" name="esito">
					<option value='Da verificare'>Da verificare</option>
					<option value='Valido'>Valido</option>
					<option value='Escludere'>Escludere</option>
				</select>
			</td>
		</tr>


</table>

</form>

<?php
}

if (isset($_POST['tipo']) && ($_POST['tipo'] == 'insert')) {

// inserisco il record
$note = mysqli_real_escape_string($connection, htmlspecialchars($_POST['descrizione']));

$insert =
                    'INSERT INTO tab_comuni_soci_note
                    (filiale, cag, nominativo, documentale, status_esito, note, operatore, data_segnalazione, attivo)
                    
					 VALUES
					 ("'.$_POST['filiale'].'","'.$_POST['cag'].'","'.$_POST['nominativo'].'","'.$_POST['documentale'].'","'.$_POST['esito'].'","'.$note.'",
					 "'.strtoupper($_POST['operatore']).'",now(),"")
                    ' ;
                     // echo $insert;
mysqli_query($connection,$insert) or die(mysqli_error($connection));;

echo '<center><br><br><b style="color:green;">Record inserito</b><br><br>';

}


// --------------------------------------
// PRESENTO IL FORM DI AGGIORNAMENTO NOTE
// --------------------------------------
if (($_GET['id'] <> 'N') && ($_GET['tipo'] == 'edit')) {
    
// Preparo l'EDIT del record
$query = mysqli_query($connection, 
					"SELECT *
					 FROM 	tab_comuni_soci_note
					 WHERE  id = ".$_GET['id'] );

while($monitor=mysqli_fetch_array($query)){ 

?>
	<br>


	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $_GET['id']; ?>&tipo=update" method="POST">
	  <script type="text/javascript" src="js/nicEdit.js"></script> 

	<table border="0" align="center" width="70%" cellspacing="3" cellpadding="3">
		<tr>
			<td align="left" colspan="5">
				<!-- CAG E NOME SOCIO-->
				<h5><b style="color:#C1D6E8;">cag <?php echo $monitor['cag']; ?> - <?php echo $_GET['nominativo']; ?></b></h5>
			</td>
			<td align="right" valign="top">
			    <small>Aggiornato il <?php echo $monitor['data_segnalazione']; ?></small>
			    <input class="btn btn-warning" type="submit" value="Aggiorna">
                <input type="hidden" name="tipo" value="update">
                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                <input type="hidden" name="cag" value="<?php echo $monitor['cag']; ?>">
                <input type="hidden" name="nominativo" value="<?php echo $_GET['nominativo']; ?>">                
			</td>
		</tr>
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Descrizione della verfica effettuata</td>
			<td colspan="5">
			<!-- DESCRIZIONE -->
			<!--	<script type="text/javascript">
				//<![CDATA[
						bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
				//]]>
				</script> -->
				<textarea style="background-color:#FAEDD8;font-size:13px;" class="form-control" name="descrizione" cols="100" rows="10"><?php echo $monitor['note']; ?></textarea>
			</td>
		</tr>
		
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Documenti presenti nel documentale</td>
			<td>
				<!-- DOCUMENTALE -->
				<select class="custom-select" style="background-color:#FAEDD8;" name="documentale">
					<option value="<?php echo $monitor['documentale']; ?>"><?php echo $monitor['documentale']; ?></option> ';
					<option value='SI'>SI</option>
					<option value='NO'>NO</option>
				</select>
			</td>
			<td class="col-form-label col-form-label-sm" align="right">Operatore</td>
			<td>
				<!-- OPERATORE -->
				<input  style="background-color:#FAEDD8;" class="form-control" type="text" name="operatore" size="60" value="<?php echo $monitor['operatore']; ?>" placeholder="LNxxxx">
			</td>			
			<td class="col-form-label col-form-label-sm" align="right">Status Esito</td>
			<td>
				<!-- STATUS ESITO -->
				<select class="custom-select" style="background-color:#FAEDD8;" name="esito">
					<option value="<?php echo $monitor['status_esito']; ?>"><?php echo $monitor['status_esito']; ?></option> ';
					<option value='Valido'>Valido</option>
					<option value='Da verificare'>Da verificare</option>
					<option value='Escludere'>Escludere</option>
				</select>
			</td>
		</tr>

</table>

</form>
<br><br>
<center>
	Inserire la nota + indicare la presenza o meno di pezze d'appoggio sul documentale + indicare la propria matricola + cambiare lo stato indicando quello opportuno
</center>	

<?php
}

}

if (isset($_POST['tipo']) && ($_POST['tipo'] == 'update')) {
    
// Aggiorno il record
$note = mysqli_real_escape_string($connection, htmlspecialchars($_POST['descrizione']));

mysqli_query($connection,
                    "UPDATE tab_comuni_soci_note 
					 SET 
					 documentale    = '".$_POST['documentale']."',
					 status_esito   = '".$_POST['esito']."',
					 note           = '".$note."',
					 operatore      = '".strtoupper($_POST['operatore'])."',
					 data_segnalazione = now()
					 WHERE  
					 id = ".$_POST['id'] );
//var_dump($_POST);
echo '<center><br><br><b style="color:green;">Record aggiornato</b><br><br>';

}

?>