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
$adesso = date("d/m/Y");

echo '<center><i class="fas fa-desktop fa-2x text-gray-300 col-auto"></i>Inserimento record Monitor Socio</center>';

if (isset($_GET['tipo']) && ($_GET['tipo'] == 'new')) {

?>
	<br>


	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
	  <script type="text/javascript" src="js/nicEdit.js"></script> 

	<table border="0" align="center" width="70%" cellspacing="3" cellpadding="3">
		<tr>
			<td align="left" colspan="3">
				<!-- CAG E NOME SOCIO-->
				<h5><b><?php echo $_GET['cag']; ?> - <?php echo $_GET['nominativo']; ?></b></h5>
			</td>
			<td align="right" valign="top">
			    <input class="btn btn-success" type="submit" value="Inserisci">
                <input type="hidden" name="tipo" value="insert">
                <input type="hidden" name="cag" value="<?php echo $_GET['cag']; ?>">
                <input type="hidden" name="nominativo" value="<?php echo $_GET['nominativo']; ?>">                
			</td>
		</tr>
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Tipologia</td>
			<td>
				<!-- TIPOLOGIA -->
				<select class="custom-select" style="background-color:#FAEDD8;" name="Tipologia" required>
					<option value=""></option> ';
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
                <input  style="background-color:#FAEDD8;" class="form-control" name="dataricezione" type="text" placeholder="gg/mm/aaaa" size="20" value="<?php echo $adesso; ?>" required>
			</td>
		</tr>
		<tr>
			<td class="col-form-label col-form-label-sm" align="right">Forma di ricezione</td>
			<td>
				<!-- FORMA DI RICEZIONE -->
				<select  style="background-color:#FAEDD8;" class="custom-select" name="Forma" required>
					<option value=""></option> ';
					<option value='Mail'>Mail</option>
					<option value='PEC'>PEC</option>
					<option value='Lettera'>Lettera ordinaria</option>
					<option value='Raccomandata'>Raccomandata</option>
					<option value='Telefonata'>Telefonata</option>
				</select>
			</td>
			<td class="col-form-label col-form-label-sm" align="right">A mezzo di</td>
			<td>
			<!-- A MEZZO DI -->
			<input  style="background-color:#FAEDD8;" class="form-control" type="text" name="amezzodi" size="60" value="">
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
			<td class="col-form-label col-form-label-sm" align="right">Info su Esito</td>
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
                <input  style="background-color:#FAEDD8;" class="form-control" name="dataesito" type="text" placeholder="gg/mm/aaaa" size="20" value="" required>
			</td>
			<td class="col-form-label col-form-label-sm" align="right">Status Esito</td>
			<td>
				<!-- STATUS ESITO -->
				<select  style="background-color:#FAEDD8;" class="custom-select" name="Status" required>
					<option value=""></option> ';
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
				<input  style="background-color:#FAEDD8;" class="form-control" type="text" name="segnalato_a" size="60" value="">
			</td>
			<td class="col-form-label col-form-label-sm" align="right">Data segnalazione</td>
			<td>
				<!-- DATA SEGNALAZIONE -->
                <input  style="background-color:#FAEDD8;" class="form-control" name="datasegnalazione" type="text" placeholder="gg/mm/aaaa" size="20" value="">
			</td>
		</tr>	
</table>

</form>


<?php

}

elseif (isset($_POST['tipo']) && ($_POST['tipo'] == 'insert')) {

// Aggiorno il record
$post_descr = mysqli_real_escape_string($connection,htmlspecialchars($_POST[descrizione]));
$post_esito = mysqli_real_escape_string($connection,htmlspecialchars($_POST[esito]));
$post_note  = mysqli_real_escape_string($connection,htmlspecialchars($_POST[note]));

$insert =
                    "INSERT INTO tab_monitor_soci 
                    (cag,tipologia,data_ricezione,forma_ricezione,amezzodi,descrizione,esito,data_esito,status_esito,note,segnalato_a,data_segnalazione,attivo,riservato)
					 VALUES
					 ('".$_POST['cag']."','".$_POST['Tipologia']."','".$_POST['dataricezione']."','".$_POST['Forma']."',
					 '".$_POST['amezzodi']."','".$post_descr."','".$post_esito."','".$_POST['dataesito']."',
					 '".$_POST['Status']."','".$post_note."','".$_POST['segnalato_a']."','".$_POST['datasegnalazione']."',
					 'S','N')
                    " ;
                    
mysqli_query($connection,$insert) or die(mysqli_error($connection));;

echo '<center><br><br><b style="color:green;">Record inserito</b><br><br>';
echo '<a href="monitor_lista.php?cag='.$_POST['cag'].'&nominativo='.$_POST['nominativo'].'" >Torna alla lista</a></center>';

}

?>