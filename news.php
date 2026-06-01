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

if (isset($_POST['action']) && ($_POST['action'] == 'update')) {

// insert record
//echo $_POST[newspost];
$post = mysqli_real_escape_string($connection,htmlspecialchars($_POST[newspost]));
mysqli_query($connection,"UPDATE tab_news set
							datainsert = now(),
							newscategoria = '".$_POST['newscategoria']."',
							newstitolo = '".$_POST['newstitolo']."',
							newspost = '".$post."'"
							);

header("location: index.php");
}

else
	
{
// Interrogo la tabella del database
$query = mysqli_query($connection, 
					'SELECT *
					 FROM 	tab_news');

while($news=mysqli_fetch_array($query)){ 

?>
	<br>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

	<div id="sample" align="center">
	  <script type="text/javascript" src="js/nicEdit.js"></script> 
	  <script type="text/javascript">
	//<![CDATA[
			bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
	  //]]>
	  </script>
	  
		<table border="0" align="center" width="700">
			<tr>
			<td align="left">Titolo </td>
			<td align="left"><input type="text" name="newstitolo" size="30" value="<?php echo $news['newstitolo']; ?>"></td>
			<td align="left">Categoria </td>
			<td align="left"><input type="text" name="newscategoria" size="30" value="Mutua"></td>
			</tr>

			<tr>
			<td colspan="4"><br>
				<textarea name="newspost" cols="100" rows="5">
				<?php echo $news['newspost']; ?>
				</textarea>
			</td>
			</tr>
			
	  
			<tr>
			<td colspan="4" align="center">
				<input type="submit" value="Aggiorna">
				<input type="hidden" name="action" value="update">
			</td>
			</tr>
			
		</table>
	
	</form>
		
	<br />

	</div>

<?php
}
}
?>