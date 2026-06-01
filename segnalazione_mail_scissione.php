<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2023)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);


require_once('config/_functions.php');   //logquery ($selectdati); 
include("config/_config.php");
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);
// Connessione all'ODBC SADAS
$connect = odbc_connect('SADAS', NULL, NULL) or die ('0');
// Head e CSS
include("css/main.php");
include("css/menu.php");

$usr_id = $_COOKIE['usr_id'];
$usr_mail = $_COOKIE['usr_mail'];

if (isset($_COOKIE['filiale_id'])) 
	{ $filiale_id = $_COOKIE['filiale_id'];   }
else
	{ $filiale_id = 1000 ; }

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
		$user_mansione 	= ucwords(strtolower($dati_user->DESCR_MANSIONE_WPROF));
	}


// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
echo '<center>
        <h2><i class="fas fa-cut fa-1x text-gray-300 col-auto"></i>RICHIESTA SCISSIONE CERTIFICATO AZIONARIO</h2>';
/*        
echo '  <style type="text/css">
          @import "../css/bootstrap.css";
          @import "../css/bootstrap.min.css";
          @import "../css/fontawesome-free/css/all.min.css";
        </style> ';
*/      
if 	(!isset($_POST['inviamail'])) {
?>
        <h3>Integra le informazioni necessarie per completare il modulo</h3>

        
<style>
//.frmSearch {width:610px; border: 1px solid #a8d4b1;background-color: #c6f7d0;margin: 2px 0px;padding:40px;border-radius:4px;}
#country-list{float:left;list-style:none;margin-top:-3px;padding:0;width:480px;position: absolute;}
#country-list li{padding: 10px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid;color:black;text-align:left;}
#country-list li:hover{background:#ece3d2;cursor: pointer;color:black;text-align:left;}
#search-box{width:500px;padding: 10px;border: #a8d4b1 1px solid;border-radius:4px;background-color: #c6f7d0;}

@font-face {
    font-family: 'product_sansregular';
    src: url('product_sans_regular-webfont.woff2') format('woff2'),
         url('product_sans_regular-webfont.woff') format('woff');
    font-weight: normal;
    font-style: normal;

}

</style>
<script src="js/jquery.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$("#search-box").keyup(function(){
		$.ajax({
		type: "POST",
		url: "mail_search.php?segnalazione=vincolo",
		data:'keyword='+$(this).val(),
		beforeSend: function(){
			$("#search-box").css("background","#FFF url(img/LoaderIcon.gif) no-repeat 165px");
		},
		success: function(data){
			$("#suggesstion-box").show();
			$("#suggesstion-box").html(data);
			$("#search-box").css("background","#FFF");
		}
		});
	});
});

function selectCountry(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
}
</script>
<br><br>
<form action="segnalazione_mail_scissione.php" method="POST" onsubmit="return ray.ajax()">
<table border="0" align="center">
    <tr>
        <td align="left" valign="top">
        	<label><small>La ricerca può avvenire per NAG, NOME, NUMERO CONTO</small>
            <div class="form-group">
            <input type="text" class="form-control" id="search-box" name="search-box" placeholder="Socio con certificato da scindere" required>
            <div id="suggesstion-box"></div>
            </div>
        	</label>
        </td>
    </tr>    
    <tr>
        <td>
            <div class="form-group">
              <input type="text" class="form-control" id="note" name="note" placeholder="Indicare qui come scindere il Certificato" >
            </div>
        </td>
    </tr>
    <tr >
        <td align="center"><br>
            <div class="form-group">
                <button type="submit" class="btn btn-danger mb-2"><i class="fas fa-upload fa-2x text-lightgray-300 col-auto"></i>INVIA RICHIESTA A UFFICIO SOCI</button>
            </div>
        </td>
    </tr>
    
</table>

 <input type="hidden" name="inviamail" value="si">
 <input type="hidden" name="dipendente" value="<?php echo $user_nome." - ".$user; ?>">
  
</form>

<?php
	}
elseif 
    ($_POST['inviamail'] == "si")
	{
        global $debug;
		//No email sabato o domenica
		if(date('w')=="0" || date('w')=="6") return;
		
		//Parm email
		//ini_set('SMTP', 'smtp.bccsi.bcc.it'); 
		ini_set('SMTP', 'smtp.bccsi.bcc.it'); 
		ini_set('smtp_port', 25); 
		ini_set('sendmail_from','noreply@chiantibanca.it');
		
		//Parm others
		$mail_cc = "";  
		$mail_dest = "soci@chiantibanca.it"; 
		$nome_mittente = $user_nome." da Portale Soci"; 
		$mail_mittente = $usr_mail ;
		
		$mail_oggetto = "Portale Soci - Segnalazione SCISSIONE CERTIFICATO di ".$_POST['search-box'];
		
		$mail_corpo = "<html><body><br>";
		$mail_corpo .= "<style type='text/css'>  body{font-family:'Courier New',Courier,monospace;font-size:11pt;} </style>";
    		
		$mail_corpo .= "Si richiede di scindere il certificato del socio<br> <b style='color:brown;'>".$_POST['search-box']."</b> <br>";
		$mail_corpo .= "nel seguente modo:<br> <b style='color:brown;'>".$_POST['note']." </b><br>";

		$mail_corpo .= "<hr>";

		$mail_corpo .= "Segnalato da <br>";
		$mail_corpo .= $_POST['dipendente']."<br>";

		$mail_corpo .= "- - - - - - - - - ";
		$mail_corpo .= "<br>&#128231;&nbsp;<a href=\"mailto:".$mail_mittente."?subject=Scissione Certificato avvenuta - ".$_POST['search-box']."&body=Fatto, ciao.\">Invia mail di eseguito</a>";

		$mail_corpo .= "</body></html>\r\n";
		
		$mail_headers = "From: " . $nome_mittente . " <" . $mail_mittente . ">\r\n";
		$mail_headers .= "Reply-To: " . $mail_mittente . "\r\n";
		$mail_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
		$mail_headers .= "MIME-Version: 1.0\r\n";
		$mail_headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$mail_headers .= "Content-Transfer-Encoding: base64";
		
		if(!empty($mail_cc)) $mail_headers .= "CC: ".$mail_cc."\r\n";
		
		$mail_oggetto_encoded = '=?UTF-8?B?' . base64_encode($mail_oggetto) . '?=';
		$mail_corpo_encoded = base64_encode($mail_corpo);

		if (mail($mail_dest, $mail_oggetto_encoded, $mail_corpo_encoded, $mail_headers)) {
		  if($debug) echo "Messaggio inviato a " . $mail_dest . "<br /> Puoi chiudere questa finestra.\r\n";
		} else {
		  if($debug) echo "Errore. Nessun messaggio inviato. <br> \r\n";
		}
		
	echo '<br><br><br>
	<center>Messaggio inviato</center>';

    	}
    	


?>

                