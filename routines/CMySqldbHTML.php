<?php

/*
 * Idioma - Language.
 * Español - English.
 *
 * Al usar esta clase se debe construir con datos primero de lo contrario fallara
 * To use this class must construct with the parameters first, from another way will fail
 */

/**
 * Descripcion de CMySqldbHTML - Description of CMySqldbHTML
 * CMySqldbHTML esta diseñado para la interacción con bases de datos mysql phpmyadmin y generar HTML a partir de los datos.
 * CMySqldbHTML is designed for the interaction between data bases mysql phpmyadmin and and create HTML code with the data
 *
 * El constructor requiere los parametros - The constructor parameters are:
 * $tablenm = Nombre de la tabla - Name of the table
 * $db = Nombre de la base de datos - Name of data base
 * $host = Nombre o direccion del host o servidor - Name or adress of the host or server
 * $user = Nombre de usuario para conexion con la base de datos - User name to connect with the database
 * $pass = Conrtaseña para conexion con la base de datos - User password to connect with the database
 * 
 * Hay dos formas de usar la clase - There are two ways for the use of the class:
 *
 * 1.- $tablenm = "": Si entregamos el nombre de la tabla vacio - If set the table name empty
 *      $data   : Almacena los nombres de las bases de datos - Save the names of the databases
 *      $tblsdb : Almacena los nombres de las tablas de la base de datos entregada 
                - Save the names of the tables in the database gived
 *
 * 2.- $tablenm <> "": Entregando el nombre de una tabla existente - Setting the table name created.
 *      $numfilas   : Numero de filas de la tabla - Numbers of rows in the table
 *      $numcols    : Numero de columnas de la tabla - Numbers of columns in the table
 *      $hddata     : Encabezados de la tabla - Table headers
 *      $tpar       : Tipos de campos de la tabla - Types of table fields
 *      $lenar      : Longitud máxima para los campos de la tabla - Maximum length for the table's fields
 *      $bandar     : Banderas de los campos de la tabla - Flags for the table's fields
 *      $data       : Almacena el contenido de la tabla por filas - Save the rows content of the table
 *
 * Metodos:
 *
 * showrCObjeto()   : 
 *      1.- Escribe el codigo HTML para ver la lista de las bases de datos y las tablas de la base de datos asignada
 *        - Write the HTML code to see the list of databases and tables of the assigned database
 *      2.- Escribe el codigo HTML para ver la tabla con sus encabezados
 *        - Write the HTML code to see the table with its headings
 *
 * Solo para caso 2 - Only case two(2).
 * getrowdata($id,$colid)
 *      Devuelve un array con los datos del $id, de encontrarlo en la columna o encabezado $colid
 *      Returns an array with the $id data, if found in the $colid field
 *
 * drawrowdata($id,$colid)
 *      Escrible el codigo HTML en una tabla con los datos del $id encontrado en la columna $colid
 *      Write the HTML code in a table with tha $id data, if found in the $colid field
 *
 * Para formularios - To forms
 * setArcolno($ar)
 *      $arcolno = $ar, que son los campos que no queremos que dibuje en el formulario, 
 *      como timestamp o autoincremto u otro caso.
 *
 * drawForm($nm)
 *      Escribe el codigo HTML de un formulario con los nombres de los campos de la tabla como etiquetas
 *      remplazando '_' con ' ', para darle presentacion, y poder usar dichos nombres como name en los input
 *      del formulario para su posterior captura con el metodo _POST.
 *      El primer input lo hace autococus y crea un tabindex para el form.
 *      Caracteristicas especiales:
 *      $arcolno permite que en el formulario no dibuje determinados campos, como timestamp o autoincremto u otro caso.
 *      $nm es el dato de autorrelleno para el primer campo, en caso de tenerlo lo autorrelena y solo lectura.
 *      De lo contrario entregamos $nm=''.
 *
 *      Write the HTML code of a form with the names of the fields in the table as labels replacing '_' with ' ', to give 
 *      presentation and be able to use these names as name in the input of the form for later capture with the _POST method.
 *      The first input is done by autococus and creates a tabindex for the form.
 *      Special features:
 *      $arcolno allows the form not to draw certain fields, such as timestamp or autoincremto or another case.
 *      $nm is the autofill data for the first field, if you have it, the self-fill and read only.
 *      Otherwise we deliver $nm=''.
 *      
 * getcmpsForm():
 *      Devuelve un array con los nombres de los encabezados - Return an array with header names
 *
 * procForm($arrfrm):
 *      Procesa y graba los datos capturados y recibidos en $arrfrm, usa las variables $tstmp y $ord para identificar y
 *      autorellenar, ya que normalmente son campos que el formulario no dibuja, y deben estar en $arcolno.
 *      Para su uso despues de construir el objeto, damos valor a las variables con setTstmp($ts) y setOrd($o).
 *      It processes and records the data captured and received in $ arrfrm, uses the variables $ tstmp and $ ord to identify
 *      and autofill, since they are normally fields that the form does not draw, and they must be in $ arcolno.
 *      For use after building the object, we give value to the variables with setTstmp ($ ts) and setOrd ($ o)
 *
 * Getters and Setters
 *      getData() return $this->data;
 *      getNumfilas() return $this->numfilas;
 *      getNumcols() return $this->numcols;
 *      getHddata() return $this->hddata;
 *      getTblsdb() return $this->tblsdb;
 *      getTablenm() return $this->tablenm;
 *      setArcolno($ar) $this->arcolno = $ar;
 *      getArcolno() return $this->arcolno;
 *      setTstmp($ts) $this->tstmp = $ts;
 *      setOrd($o) $this->ord = $o;
 *
 * @author Luis Gabriel Hernández - alias or nickname: luisga158
 */
class CMySqldbHTML {
    //put your code here
    
    private $tablenm;
    private $db;
    private $host;
    private $user;
    private $pass;    
    private $data;
    private $tblsdb;
    private $numfilas;
    private $numcols;
    private $hddata;
    private $tpar = array();
    private $lenar = array();
    private $bandar = array();
    private $rowdt;
    private $arcolno = array();         // columnas exepcion para dibujar form
    private $tstmp;                     // nombre del campo timestamp
    private $ord;                       // nombre del campo ordinal autoincrement
    /* Si $tablename="" carga todas las bases de datos en $data y los nombres de las tablas en $tblsdb
    /* Si tablename devuelve los names de las cols en $hddata, y el contenido de la tabla por columnas en $data
    /* al igual que el numero de filas y columnas en numfilas, y numcols.
    */
    public function __construct($tablenm, $db, $host, $user, $pass) {
        $this->tablenm = $tablenm;
        $this->db = $db;
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $con = mysqli_connect($host, $user, $pass, $db);
        if(!$con){
            echo 'Ha sucedido un error inesperado en la conexión de la base de datos';
            }
        if ($tablenm == ''){
            if(!$result = mysqli_query($con, "show databases;")) die(); //si la conexión cancelar programa
            $rawdata = array(); //creamos un array
            // guardamos en un array todos las nombres de las bases de datos de la consulta
            $i=0;
            while($row = mysqli_fetch_array($result)) {
                $rawdata[$i] = $row['Database'];
                $i++;
            }
            $this->data = $rawdata;
            // guardamos en un array todos los nombres de las tablas de la base de datos entregada
            if(!$result = mysqli_query($con, "show tables;")) die();
            $rawtbl = array();
            $i=0;
            while($row = mysqli_fetch_array($result)) {
                $rawtbl[$i] = $row[0];
                $i++;
            }            
            $this->tblsdb = $rawtbl;
        } else {
            mysqli_select_db($con,$db);
            $query = "SELECT * FROM ".$tablenm;
            $result = mysqli_query($con,"SELECT * FROM ".$tablenm);
            $this->numfilas = $result->num_rows;
            $this->numcols = $result->field_count;
            // toma los encabezados en $infocampo
            $info_campo = $result->fetch_fields();
            $i=0;
            foreach ($info_campo as $valor) {
                $this->hddata[$i] = $valor->name;
                $this->tpar[$i] = $valor->type;
                $this->lenar[$i] = $valor->max_length;
                $this->bandar[$i] = $valor->flags;
                $i++;
            }
            $longitud = count($this->hddata);
            $rawdat = array();
            $j=0;
            $result = mysqli_query($con,"SELECT * FROM ".$tablenm);
            while($row = mysqli_fetch_array($result)) {                
                $rawdat[$j] = $row;
                $j++;
            }
            $this->data = $rawdat;
        }
        $close = mysqli_close($con);
    }

    public function getData() {
        return $this->data;
    }

    public function getNumfilas() {
        return $this->numfilas;
    }

    public function getNumcols() {
        return $this->numcols;
    }
    
    public function getHddata() {
        return $this->hddata;
    }
    
    public function getTblsdb() {
        return $this->tblsdb;
    }
    
    public function getTablenm() {
        return $this->tablenm;
    }
    
    // condicionales para los formularios
    public function setArcolno($ar) {
        $this->arcolno = $ar;
    }
    
    public function getArcolno() {
        return $this->arcolno;
    }
    public function setTstmp($ts){
        $this->tstmp = $ts;
    }
    public function setOrd($o){
        $this->ord = $o;
    }
    
    public function showrCObjeto() {
        $con = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
        if(!$con){
            echo 'Ha sucedido un error inesperado en la conexión de la base de datos';
            }
        if ($this->tablenm == ''){
            echo '<select name="listdblcl">';    
            $i=0;
            foreach ($this->data as &$valor){
                echo '<option value="'.$i.'">'.$valor.'</option>';
            $i++;
            }
            echo "</select><br />";
            echo '<select name="listdblcl">';    
            $i=0;
            foreach ($this->tblsdb as &$valor){
                echo '<option value="'.$i.'">'.$valor.'</option>';
            $i++;
            }
            echo "</select><br />";
        } else {
            if ($this->numfilas > 0){
                echo "<table>
                <tr>";
                // datos encabezado
                $longitud = count($this->hddata);
                for ($i=0; $i < $longitud; $i++){
                    echo '<th>'.$this->hddata[$i].'</th>';
                }
                $rawr = $this->data;
                echo "</tr>";
                // datos de tabla
                for ($j=0; $j < $this->numfilas; $j++){
                    $rawd = $rawr[$j];
                    echo "<tr>";
                    for ($i=0; $i < $longitud; $i++){
                        echo "<td>" .utf8_encode($rawd[$i]). "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
                echo "<br />";
            }
        }
        $close = mysqli_close($con);
    }
    
    public function getrowdata($id,$colid){
        $longitud = count($this->hddata);
        $arrtrn = array();
        $rawr = $this->data;
        for ($j=0; $j < $this->numfilas; $j++){
            $rawd = $rawr[$j];
            if ($rawd[$colid] == $id){
                $j = $this->numfilas-1;
                for ($i=0; $i < $longitud; $i++){
                    $arrtrn[$i] = $rawd[$i];
                }
            }
        }
        return $arrtrn;
    }
    
    public function drawrowdata($id,$colid){
        echo "<table>
                <tr>";
        $longitud = count($this->hddata);
        for ($i=0; $i < $longitud; $i++){
            echo '<th>'.$this->hddata[$i].'</th>';
        }
        $rawr = $this->data;
        echo "</tr>";
        for ($j=0; $j < $this->numfilas; $j++){
            $rawd = $rawr[$j];
            if ($rawd[$colid] == $id){
                $j = $this->numfilas;
                echo "<tr>";
                for ($i=0; $i < $longitud; $i++){
                    echo "<td>" .utf8_encode($rawd[$i]). "</td>";
                }
                echo "</tr>";
            }
        }
        echo "<table>";
    }
        
    public function drawForm($nm){
        echo '<form class="form" action="enviar.php" method="POST">';
        echo '<fieldset class="pure-group">';
        $longitud = count($this->hddata);
        $contin = 0;
        for ($i=0; $i < $longitud; $i++){
            if ((!(is_array($this->arcolno))) or (!(in_array($this->hddata[$i], $this->arcolno)))) {
                echo '<h5>'.str_replace("_"," ",$this->hddata[$i]).'</h5>';
                // condicionales para los formularios
                if ((!($nm=="")) && ($contin==0)){
                    echo '<input type="text" size="auto" name="'.$this->hddata[$i].'" value="'.utf8_encode($nm).'" readonly="readonly" />';
                    $i++;
                    $contin++;
                    echo '<h5>'.str_replace("_"," ",$this->hddata[$i]).'</h5>';
                }
                if ($contin==0){
                    echo '<input type="text" size="auto" name="'.' ', '', $this->hddata[$i].'" autofocus />';
                    $contin++;
                } else { 
                    echo '<input type="text" size="auto" name="'.' ', '', $this->hddata[$i].'" tabindex="'.$contin.'" />';
                    $contin++;
                }
            }
        }
        echo '</fieldset>';
        echo '<input type="text" size="auto" name="tablename" value="'.$this->tablenm.'" hidden />';
        echo '<input id="btnobjdbfrm" type="submit" value=" Enviar " tabindex="'.$contin.'"  />';
        echo '</form>';
    }
    
    public function getcmpsForm(){
        $arrtrn = array();
        $longitud = count($this->hddata);
        $icont = 0;
        for ($i=0; $i < $longitud; $i++){
            if (!(in_array($this->hddata[$i], $this->arcolno))){
                $arrtrn[$icont] = $this->hddata[$i];
                $icont++;
            }
        }
        return $arrtrn;
    }
    
    // funcion para grabar los datos del formulario
    public function procForm($arrfrm){
        $sql = "INSERT INTO ".$this->tablenm." (";
        $longitud = count($this->hddata);
        $longno = count($this->arcolno);
        for ($i=0; $i < $longitud; $i++){
            $sql = $sql.$this->hddata[$i];
            if ($i < ($longitud-1)) {
                $sql = $sql.", ";
            } else {
                $sql = $sql.") VALUES (";
            }
        }
        $longitud = count($this->hddata);
        $conti = 0;
        for ($i=0; $i < $longitud; $i++){            
            if (!(in_array($this->hddata[$i], $this->arcolno))){
                $sql = $sql."'".utf8_decode($arrfrm[$conti]);
                $conti++;
            } else {
                if ($this->hddata[$i] == $this->tstmp){
                    $fecha = date_create();
                    $tmstmp = date_format($fecha, 'Y-m-d H:i:s');
                    $sql = $sql."'".$tmstmp;
                } elseif ($this->hddata[$i] == $this->ord) {
                    $sql = $sql."'".($this->numfilas+1);
                }                
            }
            if ($i < ($longitud-1)) {
                $sql = $sql."', ";
            } else {
                $sql = $sql."')";
            }
        }
        echo $sql;
        $con = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
        if (!$con) {die("Connection failed: " . mysqli_connect_error());}
        mysqli_select_db($con, $this->db);
        $result = mysqli_query($con, "SELECT * FROM ".$this->tablenm);
        if (mysqli_query($con, $sql)) {
            echo "<script>alert('Registro agregado exitosamente.');</script>";
        } else {
            echo "<script>alert('no record.');</script>";      
        }
        mysqli_close($con);
    }
}
?>