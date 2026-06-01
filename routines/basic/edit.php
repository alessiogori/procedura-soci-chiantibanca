<?php
	include('conn.php');

if ( !isset($_GET['idreg']) )
	{
	
	$id=$_GET['id'];
	$query=mysqli_query($conn,"select * from tab_eventi where idevento='$id'");
	$row=mysqli_fetch_array($query);
	$action = 'update.php?id='.$id;

	$form = '
		<label>Tipo Evento:</label>
			<input type="text" value="'.$row['tipo_evento'].'" name="tipo_evento">
		<br>
		<label>Descrizione Evento:</label>
			<input type="text" value="'.$row['descrizione_evento'].'" name="descrizione_evento">
		<br>
		<label>Data Evento:</label>
			<input type="text" value="'.$row['data_evento'].'" name="data_evento">
		<br>
		<label>Ora Evento:</label>
			<input type="text" value="'.$row['ora_evento'].'" name="ora_evento">
		<br>
		<label>Luogo Evento:</label>
			<input type="text" value="'.$row['luogo_evento'].'" name="luogo_evento">
		<br>
		<label>Note:</label>
			<input type="text" value="'.$row['note'].'" name="note">
		<br>
		<label>Link:</label>
			<input type="text" value="'.$row['link'].'" name="link">
		<br>
		<label>Posti Disponibili:</label>
			<input type="text" value="'.$row['posti_disponibili'].'" name="posti_disponibili">
		<br>
		<label>Posti Residui:</label>
			<input type="text" value="'.$row['posti_residui'].'" name="posti_residui">
		<br>
		<input type="submit" name="submit">
		<a href="index.php">Back</a>
	';

	}

	else

	{
	
	$idreg=$_GET['idreg'];
	$query=mysqli_query($conn,"select * from tab_eventi_iscrizioni where idregistrazione='$idreg'");
	$row=mysqli_fetch_array($query);
	$action = 'update.php?idreg='.$idreg;

	$form = '
		<label>ID Evento:</label>
			<input type="text" value="'.$row['idevento'].'" name="idevento">
		<br>
		<label>Data Richiesta:</label>
			<input type="text" value="'.$row['data_richiesta'].'" name="data_richiesta">
		<br>
		<label>Utente inserimento:</label>
			<input type="text" value="'.$row['utente_inserimento'].'" name="utente_inserimento">
		<br>
		<label>Nag:</label>
			<input type="text" value="'.$row['nag'].'" name="nag">
		<br>
		<label>Nominativo:</label>
			<input type="text" value="'.$row['nominativo'].'" name="nominativo">
		<br>
		<label>Data Nascita:</label>
			<input type="text" value="'.$row['data_nascita'].'" name="data_nascita">
		<br>
		<label>Luogo Nascita:</label>
			<input type="text" value="'.$row['luogo_nascita'].'" name="luogo_nascita">
		<br>
		<label>Email:</label>
			<input type="text" value="'.$row['email'].'" name="email">
		<br>
		<label>Cellulare:</label>
			<input type="text" value="'.$row['cellulare'].'" name="cellulare">
		<br>
		<label>Note:</label>
			<input type="text" value="'.$row['note'].'" name="note">
		<br>
		<input type="submit" name="submit">
		<a href="index.php">Back</a>
	';

	}


?>
<!DOCTYPE html>
<html>
<head>
<title>Basic MySQLi Commands</title>
</head>
<body style="font-size: 16px;">
	<h3>Edit Evento</h3>
	<form method="POST" action="<?php echo $action; ?>">

		<?php echo $form; ?>

	</form>
</body>
</html>