<?php
/*
// -------------------------------------
// Prendo i valori dell'UTENTE e FILIALE
// -------------------------------------

	Primo passaggio _AUTH.PHP

*/

// Ufficio Processi
// 20240419 #mz Stop accesso anonimo.
if (!isset($_COOKIE['usr_id'])){
	header('Location: https://chiantibanca.worktogether.it/login.asp?ReturnUrl=https%3A%2F%2Fchiantibanca.worktogether.it%2Fviews%2Fjump.asp%3Ftype%3DApp%26AppId%3D14');
	exit;
}

	// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');

$usr_id = $_COOKIE['usr_id'];
$usr_mail = $_COOKIE['usr_mail'];

if (isset($_COOKIE['filiale_id'])) 
	{ $filiale_id = $_COOKIE['filiale_id'];   }
else
	{ $filiale_id = 1000 ; }
 
//$filiale_id = $_COOKIE['filiale_id']; 

	// --------------------------
	// IDENTIFICAZIONE UTENTE
	// --------------------------
	$select_user = "
					SELECT *
					FROM TAB_UTENTI
					WHERE TAB_UTENTI.COD_USE_NUMERICO = ".$usr_id."";
	$result_user = odbc_exec($connect, $select_user);
	while ($dati_user = odbc_fetch_object($result_user)) {
		$user 			= 'LN00'.$usr_id;
		$user_nag 		= $dati_user->NAG;
		$user_nome 		= $dati_user->NOME_UTENTE;
		$user_mansione 	= $dati_user->DESCR_MANSIONE_WPROF;
		setcookie('user_nomeCookie',$user_nome); 
	}

	// ----------------------------
	// VISUALIZZAZIONE DATI UTENTE
	// ----------------------------

	    if ($filiale_id == 999) {
	    	// ADMIN SOCI
	      $userdesc = '
	      		<table border="0">
	      		<tr>
	      			<td align="left"><i class="fas fa-user-cog fa-2x text-300 col-auto" style="color:orange;"></i></td>
	      			<td align="left">
	      				<small style="color:orange;">'.$user.'</small><br>
	      				<span style="color:orange;">'.$user_nome.'</span><br>
	      				<small style="color:orange;">'.ucwords(strtolower($user_mansione)).' ['.$filiale_id.']</small>
	      			</td>
	      		</tr>
	      		</table>
	      		 ';
	    }
	    elseif ($filiale_id == 998) {
	    	// SEGRETERIA PRESIDENZA E DIREZIONE
	      $userdesc = '
	      		<table border="0">
	      		<tr>
	      			<td align="left"><i class="fas fa-user-circle fa-2x text-300 col-auto" style="color:yellow;"></i></td>
	      			<td align="left">
	      				<small style="color:yellow;">'.$user.'</small><br>
	      				<span style="color:yellow;">'.$user_nome.'</span><br>
	      				<small style="color:yellow;">'.ucwords(strtolower($user_mansione)).' ['.$filiale_id.']</small>
	      			</td>
	      		</tr>
	      		</table>
	      		 ';	    	
	    }
	    elseif ($filiale_id == 997) {
	    	// LEGALE
	      $userdesc = '
	      		<table border="0">
	      		<tr>
	      			<td align="left"><i class="fas fa-user-circle fa-2x text-300 col-auto" style="color:yellow;"></i></td>
	      			<td align="left">
	      				<small style="color:yellow;">'.$user.'</small><br>
	      				<span style="color:yellow;">'.$user_nome.'</span><br>
	      				<small style="color:yellow;">'.ucwords(strtolower($user_mansione)).' ['.$filiale_id.']</small>
	      			</td>
	      		</tr>
	      		</table>
	      		 ';	  	    	
	    }
	    elseif ($filiale_id == 996) {
	    	// CONTROLLO DI GESTIONE
	      $userdesc = '
	      		<table border="0">
	      		<tr>
	      			<td align="left"><i class="fas fa-user-circle fa-2x text-300 col-auto" style="color:yellow;"></i></td>
	      			<td align="left">
	      				<small style="color:yellow;">'.$user.'</small><br>
	      				<span style="color:yellow;">'.$user_nome.'</span><br>
	      				<small style="color:yellow;">'.ucwords(strtolower($user_mansione)).' ['.$filiale_id.']</small>
	      			</td>
	      		</tr>
	      		</table>
	      		 ';	  	    	
	    }
	    elseif ($filiale_id == 995) {
	    	// AREE
	      $userdesc = '
	      		<table border="0">
	      		<tr>
	      			<td align="left"><i class="fas fa-user-circle fa-2x text-300 col-auto" style="color:yellow;"></i></td>
	      			<td align="left">
	      				<small style="color:yellow;">'.$user.'</small><br>
	      				<span style="color:yellow;">'.$user_nome.'</span><br>
	      				<small style="color:yellow;">'.ucwords(strtolower($user_mansione)).' ['.$filiale_id.']</small>
	      			</td>
	      		</tr>
	      		</table>
	      		 ';	  	    	
	    }		    
	    else  {
	    	// TUTTI GLI ALTRI
	      $userdesc = '
	      		<table border="0">
	      		<tr>
	      			<td align="left"><i class="fas fa-user-circle fa-2x text-300 col-auto" style="color:lightgreen;"></i></td>
	      			<td align="left">
	      				<small style="color:lightgreen;">'.$user.'</small><br>
	      				<span style="color:lightgreen;">'.$user_nome.'</span><br>
	      				<small style="color:lightgreen;">'.ucwords(strtolower($user_mansione)).' ['.$filiale_id.']</small>
	      			</td>
	      		</tr>
	      		</table>
	      		 ';		    	
	    }

/*
else
{
	    echo '<center>
	    		<h1>Utente non connesso sulla Intranet aziendale</h1>
	    		Passare dal <a style="color:orange;" href="https://chiantibanca.worktogether.it/">portale Intranet con autenticazione</a>
	    	  </center>';
}
*/


echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">    
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<link rel="shortcut icon" href="favicon.ico">
<title>Portale Soci</title>

<!--    
*****************************************************************************
Funzione di ricerca con timing
*****************************************************************************
-->
<script type="text/javascript">

	  $(document).ready( function() {

	  var typewatch = (function(){
	  var timer = 0;
	  return function(callback, ms){
		  clearTimeout (timer);
		  timer = setTimeout(callback, ms);
		}  
	  })();

	  $("#camporicerca").keyup(function () {
		  typewatch(function () {
		  // executed only 800 ms after the last keyup event.
		  
		  loaddata();

		}, 800);
	  });

	  }); 
	
	function loaddata()
	{
	 var name=document.getElementById( "username" );
		

		 if((name.value != "") && (name.value.length >=5 ))
		 {
		  $.ajax({
		  type: \'post\',
		  url: \'pos_home_ricerca.php\',
		  data: {
		  user_name:name.value
		  },
		  success: function (response) {
		   // We get the element having id of display_info and put the response inside it
		   $( \'#display_info\' ).html(response);
		  }
		  });
		
	}else{
	  $( \'#display_info\' ).html("<b style=\'color:red;\'>Per favore inserisci qualcosa da cercare... (minimo 5 caratteri)");
	 }
	}
</script>

<!--    
*****************************************************************************
Funzione di loading di attesa
*****************************************************************************
-->
<script type="text/javascript">
    var ray={
    ajax:function(st)
        {
            this.show(\'load\');
        },
    show:function(el)
        {
            this.getID(el).style.display=\'\';
        },
    getID:function(el)
        {
            return document.getElementById(el);
        }
    }
</script>

 <!--    
*****************************************************************************
Funzione di cambio background
*****************************************************************************
-->               
<script>
    function bgChange(bg) {
        document.body.style.background = bg;
    }
</script>

 <!--    
*****************************************************************************
Javascript
*****************************************************************************
-->               
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/kickstart.js"></script> 
<script type="text/javascript" src="js/sorttable.js"></script> 
<script type="text/javascript" src="js/intro.js"></script> 
<script type="text/javascript" src="js/sb-admin-2.min.js"></script>
<!-- Bootstrap core JavaScript-->
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
<!-- Core plugin JavaScript-->
<script type="text/javascript" src="js/jquery.easing.min.js"></script>
<script type="text/javascript" src="js/fusioncharts/fusioncharts.js"></script>
<script type="text/javascript" src="js/fusioncharts/themes/fusioncharts.theme.candy.js"></script>
<!-- Datepicker core JavaScript-->
<script type="text/javascript" src="js/datepicker.min.js"></script>

 <!--    
*****************************************************************************
CSS
*****************************************************************************
-->              
<style type="text/css">
    @import "css/main.css"; 
    @import "css/introjs.css"; 
    @import "css/pricing.css";
    @import "css/fontawesome-free/css/all.min.css";
    @import "css/sb-admin-2.min.css";  
    @import "css/bootstrap.css";
    @import "css/bootstrap.min.css";
    @import "../css/datepicker.css";
    @import "../css/datepicker.min.css";    
    
    /* @import "css/menu.css"; */
    /* @import "css/style.css"; */
    /* @import "css/kickstart-buttons.css"; */
    /* @import "css/kickstart-forms.css"; */

</style> 

 <!--    
*****************************************************************************
POPUP
*****************************************************************************
-->              
<script>
	$(document).ready(function(){
		$("#myModal").modal(\'show\');
	});
</script>


</head>
<body width="90%">    

';

?>