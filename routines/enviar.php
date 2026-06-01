<?php
include("../config.php");
include("CMySqldbHTML.php");
if (isset($_POST["tablename"])){
    $tblpost = $_POST['tablename'];
    $obj = new CMySqldbHTML($tblpost, $database, $server, $db_user, $db_pass);
    $arrproc = $obj->getcmpsForm();
    $longitud = count($arrproc);
    $arrtrn = array();
    for ($i=0; $i < $longitud; $i++){
        $stin = $arrproc[$i];
        $arrtrn[$i] = $_POST[$stin];
    }
    //puede esperar falta validar formularios y si no pasa volver atras ver cookies
    // si valida 
    
    /*
        * IMPORTANTE:
        * prioridad crear y enviar documentos antes de guardar */
    // :)
    // El objeto DocxConversion.php
    // en la funcion que le estoy creando replace_docx()
    // tiene el codigo investigado para editar y terminar 
    // funcion conId uno enviar reporte visita con form de tabla y todo
    
    // :)
    // y guardar reporte ya esta en la linea siguiente
    $obj->procForm($arrtrn);
}
// Lugar al que nos redirigimos despues de procesar
echo "<script>location.href = '../program.php';</script>";
?>