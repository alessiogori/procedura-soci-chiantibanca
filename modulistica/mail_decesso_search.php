<?php
// *****************************************************************************
// Portale ChiantiBanca - Soci
// Sviluppo e realizzazione: Alessio Fedi (2020)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

// require_once('_functions.php');   //logquery ($selectdati); 
// include("../config/_config.php");
// $connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************

class DBController {
	private $host = "localhost";
	private $user = "3qa25raa3f";
	private $password = "8ynDHEuDkMhM63dy";
	private $database = "soci";
	private $conn;
	
	function __construct() {
		$this->conn = $this->connectDB();
	}
	
	function connectDB() {
		$conn = mysqli_connect($this->host,$this->user,$this->password,$this->database);
		return $conn;
	}
	
	function runQuery($query) {
		$result = mysqli_query($this->conn,$query);
		while($row=mysqli_fetch_assoc($result)) {
			$resultset[] = $row;
		}		
		if(!empty($resultset))
			return $resultset;
	}
	
	function numRows($query) {
		$result  = mysqli_query($this->conn,$query);
		$rowcount = mysqli_num_rows($result);
		return $rowcount;	
	}
}

$db_handle = new DBController();

if(!empty($_POST["keyword"])) {
$query ="   SELECT NAG, CONCAT(INTESTAZIONE_A,' ',INTESTAZIONE_B) as NOMINATIVO, DATA_NASCITA, 
			case 
				WHEN STATO_NAG = 1 THEN 'ATTIVO'
				WHEN STATO_NAG = 2 THEN 'EX CLIENTE'
				END as STATO,
			IDSOCIO, DATA_DECESSO
            FROM sds_soci 
            WHERE CONCAT(INTESTAZIONE_A,' ',INTESTAZIONE_B) like '" . $_POST["keyword"] . "%' 
            ORDER BY INTESTAZIONE_A 
            LIMIT 0,6";
$result = $db_handle->runQuery($query);

if(!empty($result)) {
?>

    <ul id="country-list">
    
<?php
foreach($result as $country) {
    $a03 = " (".$country["IDSOCIO"].")";

if ($country["STATO"] == 'ATTIVO') {$colore = 'green';} else {$colore = 'red';}

if ($country["DATA_DECESSO"] <= 0) {$deceduto = '';} else {$deceduto = '<B style="color:red;">Deceduto '.$country["DATA_DECESSO"].'</B>'; }

?>

    <li onClick="selectCountry('<?php echo $country["NOMINATIVO"].$a03; ?>');">
        <?php echo $country["NAG"]; ?>
        <?php echo $country["NOMINATIVO"]; ?>
        <?php echo $country["DATA_NASCITA"]; ?>
        <?php echo '<B style="color:'.$colore.';">'.$country["STATO"].'</B>'; ?>
        <?php echo $deceduto; ?>
    </li>
<?php } ?>
    </ul>

<?php } }
?>

