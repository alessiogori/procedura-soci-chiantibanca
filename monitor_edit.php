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

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

echo '<center><i class="fas fa-desktop fa-2x text-gray-300 col-auto"></i>Edit Monitor Socio</center>';

if (isset($_GET['tipo']) && ($_GET['tipo'] == 'edit')) {

// Preparo l'EDIT del record
$query = mysqli_query($connection, 
					"SELECT *
					 FROM 	tab_monitor_soci
					 WHERE  id = ".$_GET['id'] );

while($monitor=mysqli_fetch_array($query)){ 

?>
	<br>


	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	  <script type="text/javascript" src="js/nicEdit.js"></script> 

	<table border="0" align="center" width="70%" cellspacing="3" cellpadding="3">
		<tr>
			<td align="left" colspan="3">
				<!-- CAG E NOME SOCIO-->
				<h5><b style="color:#C1D6E8;"><?php echo $monitor['cag']; ?> - <?php echo $_GET['nominativo']; ?></b></h5>
			</td>
			<td align="right" valign="top">
			    <input class="btn btn-warning" type="submit" value="Aggiorna">
                <input type="hidden" name="tipo" value="update">
                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                <input type="hidden" name="cag" value="<?php echo $monitor['cag']; ?>">
                <input type="hidden" name="nominativo" value="<?php echo $_GET['nominativo']; ?>">                
			</td>
		</tr>
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Tipologia</td>
			<td>
				<!-- TIPOLOGIA -->
				<select class="custom-select" style="background-color:#FAEDD8;" name="Tipologia">
					<option value="<?php echo $monitor['tipologia']; ?>"><?php echo $monitor['tipologia']; ?></option> ';
					<option value='Contestazione'>Contestazione</option>
					<option value='Sollecito'>Sollecito</option>
					<option value='Reclamo'>Reclamo</option>
					<option value='Info'>Richiesta informazioni</option>
					<option value='Altro'>Altro</option>
				</select>
			</td>
			<td class="col-form-label col-form-label-sm" align="right">Data di<br>ricezione</td>
			<td>
				<!-- DATA RICEZIONE -->
                <input  style="background-color:#FAEDD8;" class="form-control" name="dataricezione" type="text" placeholder="gg/mm/aaaa" size="20" value="<?php echo $monitor['data_ricezione']; ?>">
			</td>
		</tr>
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Forma di ricezione</td>
			<td>
				<!-- FORMA DI RICEZIONE -->
				<select  style="background-color:#FAEDD8;" class="custom-select" name="Forma">
					<option value="<?php echo $monitor['forma_ricezione']; ?>"><?php echo $monitor['forma_ricezione']; ?></option> ';
					<option value='Mail'>Mail</option>
					<option value='PEC'>PEC</option>
					<option value='Lettera'>Lettera ordinaria</option>
					<option value='Raccomandata'>Raccomandata</option>
					<option value='Telefonata'>Telefonata</option>
				</select>
			</td>
			<td class="col-form-label col-form-label-sm" align="right">Ricevuto da</td>
			<td>
			<!-- A MEZZO DI -->
			<input  style="background-color:#FAEDD8;" class="form-control" type="text" name="amezzodi" size="60" value="<?php echo $monitor['amezzodi']; ?>">
			</td>
		</tr>
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Descrizione</td>
			<td colspan="3">
				<textarea style="background-color:#FAEDD8;font-size:13px;" class="form-control" name="descrizione" cols="100" rows="10">
				<?php echo trim($monitor['descrizione']); ?>
				</textarea>
			</td>
		</tr>	
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Esito</td>
			<td colspan="3">
				<textarea style="background-color:#FAEDD8;font-size:13px;" class="form-control" name="esito" cols="100" rows="10">
				<?php echo trim($monitor['esito']); ?>
				</textarea>
			</td>
		</tr>	
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Data esito</td>
			<td>
				<!-- DATA ESITO -->
                <input  style="background-color:#FAEDD8;" class="form-control" name="dataesito" type="text" placeholder="gg/mm/aaaa" size="20" value="<?php echo $monitor['data_esito']; ?>">
			</td>
			<td class="col-form-label col-form-label-sm" align="right">Status Esito</td>
			<td>
				<!-- STATUS ESITO -->
				<select  style="background-color:#FAEDD8;" class="custom-select" name="Status">
					<option value="<?php echo $monitor['status_esito']; ?>"><?php echo $monitor['status_esito']; ?></option> ';
					<option value='ChiusoPositivo'>Chiuso Positivo</option>
					<option value='ChiusoNegativo'>Chiuso Negativo</option>
					<option value='InSospeso'>In Sospeso</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Note</td>
			<td colspan="3">
				<!-- NOTE -->
				<!-- <script type="text/javascript">
				//<![CDATA[
						bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
				//]]>
				</script> -->
				<textarea style="background-color:#FAEDD8;font-size:13px;" class="form-control" name="note" cols="100" rows="8">
				<?php echo $monitor['note']; ?>
				</textarea>
			</td>
		</tr>
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Segnalato a</td>
			<td>
				<!-- SEGNALATO A -->
				<input  style="background-color:#FAEDD8;" class="form-control" type="text" name="segnalato_a" size="60" value="<?php echo $monitor['segnalato_a']; ?>">
			</td>
			<td class="col-form-label col-form-label-sm" align="right">Data segnalazione</td>
			<td>
				<!-- DATA SEGNALAZIONE -->
                <input  style="background-color:#FAEDD8;" class="form-control" name="datasegnalazione" type="text" placeholder="gg/mm/aaaa" size="20" value="<?php echo $monitor['data_segnalazione']; ?>">
			</td>
		</tr>	
</table>

</form>


<?php
}
}

elseif (isset($_GET['tipo']) && ($_GET['tipo'] == 'off')) {

// Disattivo il record
mysqli_query($connection, 
					"UPDATE tab_monitor_soci
					 SET attivo = 'N'
					 WHERE  id = ".$_GET['id'] );

echo '<center><br><br><b style="color:red;">Record disattivato</b><br><br>';
echo '<a href="monitor_lista.php?cag='.$_GET['cag'].'&nominativo='.$_GET['nominativo'].'" >Torna alla lista</a></center>';

}

//elseif (isset($_POST['tipo']) && ($_POST['tipo'] == 'update')) {
elseif ( ($_POST['tipo'] == 'update')) {

//var_dump($_POST);
// Aggiorno il record

$post_descr = mysqli_real_escape_string($connection,htmlspecialchars($_POST[descrizione]));
$post_esito = mysqli_real_escape_string($connection,htmlspecialchars($_POST[esito]));
$post_note  = mysqli_real_escape_string($connection,htmlspecialchars($_POST[note]));

$update =
                    "UPDATE tab_monitor_soci 
					 SET 
					 tipologia      = '".$_POST['Tipologia']."',
					 data_ricezione = '".$_POST['dataricezione']."',
					 forma_ricezione = '".$_POST['Forma']."',
					 amezzodi       = '".$_POST['amezzodi']."',
					 descrizione    = '".$post_descr."',
					 esito          = '".$post_esito."',
					 data_esito     = '".$_POST['dataesito']."',
					 status_esito   = '".$_POST['Status']."',
					 note           = '".$post_note."',
					 segnalato_a    = '".$_POST['segnalato_a']."',
					 data_segnalazione = '".$_POST['datasegnalazione']."'
					 WHERE  
					 id = ".$_POST['id'] ;

mysqli_query($connection,$update) or die(mysqli_error($connection));;

echo '<center><br><br><b style="color:green;">Record aggiornato</b><br><br>';
echo '<a href="monitor_lista.php?cag='.$_POST['cag'].'&nominativo='.$_POST['nominativo'].'" >Torna alla lista</a></center>';

}

elseif ($_GET['tipo'] == 'update')
{
var_dump($_GET);
}

?>