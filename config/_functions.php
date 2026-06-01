<?php

function Pulisci($dato) {
    
    $dato_pulito = str_replace('"','',$dato);
    
    return $dato_pulito;
    
}

function DATE_TO_MYSQL($data) {
    
    ereg ("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})", $data, $vars);
    $data_mysql = $vars[3].'-'.$vars[2].'-'.$vars[1];
    
    return $data_mysql;
    
} 

function Navigazione($id_table) {
/* ----------------------------------------------------------------------------------
	Navigazione: TORNA SU + COPIA TABLE 
   ----------------------------------------------------------------------------------*/
echo '		
		<script type="text/javascript">
			function selectElementContents(el) {
				var body = document.body, range, sel;
				if (body.createTextRange) {
					range = body.createTextRange();
					range.moveToElementText(el);
					range.select();
				} else if (document.createRange && window.getSelection) {
					range = document.createRange();
					range.selectNodeContents(el);
					sel = window.getSelection();
					sel.removeAllRanges();
					sel.addRange(range);
				}
			}
		</script>';

echo '	<a href="#partenza"><img src="img/su.png"></a>
		&nbsp;
		<a onclick="selectElementContents( document.getElementById(\''.$id_table.'\') );" title="Premi e poi fai CTRL+C per copiare la tabella">
		<img src="img/copia.png"></a>
		&nbsp;
		';

}

function copia($id_table) {
/* ----------------------------------------------------------------------------------
	Navigazione: TORNA SU + COPIA TABLE 
   ----------------------------------------------------------------------------------*/
echo '		
		<script type="text/javascript">
			function selectElementContents(el) {
				var body = document.body, range, sel;
				if (body.createTextRange) {
					range = body.createTextRange();
					range.moveToElementText(el);
					range.select();
				} else if (document.createRange && window.getSelection) {
					range = document.createRange();
					range.selectNodeContents(el);
					sel = window.getSelection();
					sel.removeAllRanges();
					sel.addRange(range);
				}
			}
		</script>';

echo '	<a onclick="selectElementContents( document.getElementById(\''.$id_table.'\') );" title="Premi e poi fai CTRL+C per copiare la tabella">
		<img src="img/copia.png"></a>
		&nbsp;
		';

}

function get_browser_name($user_agent)
/* ----------------------------------------------------------------------------------
	Ritorna il nome del browser utilizzato
   ----------------------------------------------------------------------------------*/
{
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
    
    return 'Other';
}


function select_filiale()
{
	echo '
		<style type="text/css">
		body {background:#eee;}
		.i2Style{
		font:bold 18px Tahoma, Geneva, sans-serif;
		font-style:normal;
		color:#ffffff;
		background:#dbae15;
		border:0px solid #ffffff;
		text-shadow:0px 0px 0px #222222;
		box-shadow:2px 2px 5px #000000;
		-moz-box-shadow:2px 2px 5px #000000;
		-webkit-box-shadow:2px 2px 5px #000000;
		border-radius:1px 1px 1px 1px;
		-moz-border-radius:1px 1px 1px 1px;
		-webkit-border-radius:1px 1px 1px 1px;
		width:300px;
		padding:5px 44px;
		cursor:pointer;
		margin:0 auto;
		}
		.i2Style:active{
		cursor:pointer;
		position:relative;
		top:2px;
		}

		</style>
		';

	// La parte commentata va inserita nel file di origine

	// <form method="post">
	echo '
			<select name="filiale">
			<option value="" selected>Scegli la Filiale...</option>
			<option value="000">00 Fontebecci</option> 
			<option value="001">01 Colle</option> 
			<option value="002">02 Siena 1 Montanini</option>
			<option value="003">03 Poggibonsi</option> 
			<option value="004">04 Castelnuovo</option> 
			<option value="005">05 Siena 2 Porta Pispini</option>
			<option value="009">09 Steccaia</option> 
			<option value="020">20 San Casciano</option> 
			<option value="021">21 Montespertoli</option> 
			<option value="022">22 Sambuca</option> 
			<option value="023">23 Scandicci Charta</option>
			<option value="024">24 Tavarnelle</option> 
			<option value="025">25 Mercatale</option> 
			<option value="026">26 Firenze 1 Sansovino</option>
			<option value="030">30 Firenze 2 Europa</option>
			<option value="032">32 Firenze 3 Forlanini</option>
			<option value="033">33 Firenze 4 Savonarola</option>
			<option value="035">35 Firenze 5 Campo di Marte</option>
			<option value="036">36 Empoli</option> 
			<option value="040">40 Campi Bisenzio</option> 
			<option value="041">41 Calenzano</option> 
			<option value="042">42 Firenze 6 Belfiore</option>
			<option value="043">43 Prato Ferrucci</option>
			<option value="044">44 Sesto Fiorentino</option> 
			<option value="050">50 San Miniato</option> 
			<option value="051">51 Montalcino</option> 
			<option value="052">52 Pieve al Toppo</option> 
			<option value="053">53 Pisa</option> 
			<option value="054">54 San Giuliano</option> 
			<option value="055">55 Livorno</option> 
			<option value="056">56 Arezzo</option>
			<option value="060">60 Chiazzano</option> 
			<option value="061">61 Pistoia Guerrazzi</option>
			<option value="062">62 Montale</option>
			<option value="063">63 Pistoia Centro</option>
			<option value="064">64 Montemurlo</option> 
			<option value="066">66 La Colonna</option>
			<option value="067">67 Prato Via Galilei</option> 
			<option value="070">70 Carmignano</option>
			<option value="071">71 Poggio a Caiano</option>
			<option value="073">73 Tobbiana</option>
			<option value="075">75 Prato Valentini</option>
			</select>
		';
	// <input type='submit' name='update' value='Cartasi per Filiale' formAction='csi.php' ​src="button-arrow.gif" width="18" height="18" alt="">
	// <input type='submit' name='delete' value='SIB per Filiale' formAction='sib.php'​ src="button-arrow.gif" width="18" height="18" alt="">
	// </form>
}


// TraceLog query
// Traccia tutte le interrogazioni effettuate sul portale
function logquery ($testo_query)
{

$dbname = 'soci';
$dbuser = '3qa25raa3f';
$dbpass = '8ynDHEuDkMhM63dy';
$dbhost = 'localhost';
$connect = mysqli_connect($dbhost, $dbuser, $dbpass) or die("Unable to Connect to '$dbhost'");
mysqli_select_db($connect, $dbname) or die("Could not open the db '$dbname'");

	$ip_provenienza = $_SERVER['REMOTE_ADDR'];
	$data_query = date('YmdHis');
	$nomefile = basename($_SERVER['PHP_SELF']);


	$update_log="INSERT INTO `tab_log`(`id`, `ip`, `data_query`, `testo_query`, `nomefile`) VALUES
				 (null, '".$ip_provenienza."', '".$data_query."', '".$testo_query."', '".$nomefile."')";
//echo $update_log;
mysqli_query($connect, $update_log);
}


function puliscistringa($stringa) 
{ 
$stringa = str_replace("à", "a", $stringa); 
$stringa = str_replace("è", "e", $stringa); 
$stringa = str_replace("é", "e", $stringa); 
$stringa = str_replace("ì", "i", $stringa); 
$stringa = str_replace("ò", "o", $stringa); 
$stringa = str_replace("ù", "u", $stringa); 
$stringa = preg_replace("[^A-Za-z0-9 ]", "", $stringa ); 
return $stringa; 
}

?>

<?php

function diff_date_ingiorni($data1,$data2){
// La data deve essere nel formato 'Y-m-d' 

/*
$data_1 = 	substr($data1,6,4).'-'.
            substr($data1,3,2).'-'.
            substr($data1,0,2);
$data_2 = date("Y-m-d");
*/

$firstDate  = new DateTime($data1);
$secondDate = new DateTime($data2);
$intvl = $firstDate->diff($secondDate);

// Giorni di differenza tra OGGI e Data Delibera
// Se superiori a 60 rigettare domanda di ammissione
// echo $intvl->y . " year, " . $intvl->m." months and ".$intvl->d." day"; 
$diffgiorni = $intvl->days ;
return ($diffgiorni);

}
?>