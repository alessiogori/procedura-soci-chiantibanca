<?php
	include('conn.php');
	
	$tipo_evento=$_POST['tipo_evento'];
	$descrizione_evento=$_POST['descrizione_evento'];
	$data_evento=$_POST['data_evento'];
	$ora_evento=$_POST['ora_evento'];
	$luogo_evento=$_POST['luogo_evento'];
	$note=$_POST['note'];
	$link=$_POST['link'];
	$posti_disponibili=$_POST['posti_disponibili'];
	$posti_residui=$_POST['posti_residui'];
		
	mysqli_query($conn,"insert into tab_eventi 
		(tipo_evento,descrizione_evento,data_evento,ora_evento,luogo_evento,note,link,posti_disponibili,posti_residui) 
		values 
		('$tipo_evento','$descrizione_evento','$data_evento','$ora_evento','$luogo_evento','$note','$link','$posti_disponibili','$posti_residui')");
	header('location:index.php');
	
?>