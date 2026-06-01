<?php

if ( !isset($_GET['idreg']) )
	{

	$id=$_GET['id'];
	include('conn.php');
	mysqli_query($conn,"delete from tab_eventi where idevento='$id'");
	header('location:index.php');


}

else

{

	$idreg=$_GET['idreg'];

	include('conn.php');
	mysqli_query($conn,"delete from tab_eventi_iscrizioni where idregistrazione='$idreg'");

	header('location:index.php');
 
 }

?>