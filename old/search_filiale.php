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

if(!empty($_POST["keyword2"])) {
$query ="   SELECT CAG, int1Socio, dataNasc, Stato, prot 
            FROM tab_soci_as37 
            WHERE int1Socio like '" . $_POST["keyword2"] . "%' 
            ORDER BY int1Socio 
            LIMIT 0,6";
$result = $db_handle->runQuery($query);

if(!empty($result)) {
?>

    <ul id="country-list">
    
<?php
foreach($result as $country) {
    $a03 = " (A03.".$country["prot"].")";
?>
    <li onClick="selectCountry('<?php echo $country["int1Socio"].$a03; ?>');">
        <?php echo $country["CAG"]; ?>
        <?php echo $country["int1Socio"]; ?>
        <?php echo $country["dataNasc"]; ?>
        <?php echo '<B style="color:red;">'.$country["Stato"].'</B>'; ?>
    </li>
<?php } ?>
    </ul>

<?php } }
?>

