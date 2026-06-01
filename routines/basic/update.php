<?php
	include('conn.php');

if ( !isset($_GET['idreg']) )
	{

	$id=$_GET['id'];
	
	$tipo_evento=$_POST['tipo_evento'];
	$descrizione_evento=$_POST['descrizione_evento'];
	$data_evento=$_POST['data_evento'];
	$ora_evento=$_POST['ora_evento'];
	$luogo_evento=$_POST['luogo_evento'];
	$note=$_POST['note'];
	$link=$_POST['link'];
	$posti_disponibili=$_POST['posti_disponibili'];
	$posti_residui=$_POST['posti_residui'];
	
		mysqli_query($conn,"update tab_eventi set 
		tipo_evento='".$tipo_evento."'
		,descrizione_evento='".$descrizione_evento."'
		,data_evento='".$data_evento."'
		,ora_evento='".$ora_evento."'
		,luogo_evento='".$luogo_evento."'
		,note='".$note."'
		,link='".$link."'
		,posti_disponibili='".$posti_disponibili."'
		,posti_residui='".$posti_residui."'
		where idevento='".$id."'");

	header('location:index.php');

}

else

{

	$idreg=$_GET['idreg'];
	
	$idevento=$_POST['idevento'];
	$data_richiesta=$_POST['data_richiesta'];
	$utente_inserimento=$_POST['utente_inserimento'];
	$nag=$_POST['nag'];
	$nominativo=$_POST['nominativo'];
	$data_nascita=$_POST['data_nascita'];
	$luogo_nascita=$_POST['luogo_nascita'];
	$email=$_POST['email'];
	$cellulare=$_POST['cellulare'];
	$note=$_POST['note'];
	
		mysqli_query($conn,"update tab_eventi_iscrizioni set 
		idevento='".$idevento."'
		,data_richiesta='".$data_richiesta."'
		,utente_inserimento='".$utente_inserimento."'
		,nag='".$nag."'
		,nominativo='".$nominativo."'
		,data_nascita='".$data_nascita."'
		,luogo_nascita='".$luogo_nascita."'
		,email='".$email."'
		,cellulare='".$cellulare."'
		,note='".$note."'
		where idregistrazione='".$idreg."'");

	header('location:index.php');
}

?>