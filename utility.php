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
include("config/_functions.php");

// Mi connetto al database
$connection = mysqli_connect($host, $db_user, $db_psw, $db_name);

// Head e CSS
include("css/main.php");
include("css/menu.php");

// FINE SEZIONE DA NON MODIFICARE
// *****************************************************************************
?>

<!-- Begin Page Content -->
<div class="container-fluid">
    
<!-- Content Row 1 -->
<center>
<div class="row">

<!-- TITOLO PAGINA -->
<div class="col-lg-12">
	<div class="alert alert-dismissible alert-success"><h3>Utility</h3>
    </div>  
</div>

</div> <!-- /. Fine Content Row 1 -->
</center>

<?php
if ($_POST['calcoloeta'] == "si") 
{   $data1 = date_create($_POST['dt2']);
    $data2 = date_create($_POST['dt1']);
    $interval = date_diff($data2, $data1);
    
  echo  'Data di nascita >> '.$_POST['dt1'].'<br>
         Data del contratto >> '.$_POST['dt2'].'<br><br>
         <h4>Età Calcolata = '.$interval->format('%y anni %m mesi %d giorni').'</h4>
         
        <br><br><br>
	    <center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>
        ';}

else {
?>

<!-- Content Row 1 -->
<div class="row">	

            <div class="col-lg-4">
              <div class="card border-success mb-6">
                <div class="card-header">Addendum ULTERIORI QUOTE<i class="fas fa-print fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">

                  <form action="modulistica/SO52_addendum_ulterioriquote.php" method="GET" onsubmit="return ray.ajax()">
                        <table border="0" width="90%" align="center">
                            <tr>
                                <td>
                                   NAG Nuovo Socio 
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="nag" id="nag" size=12>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                   Nr.Ulteriori Azioni richieste
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="numaz" id="numaz" size=12>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                   Filiale / Conto 
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="filcc" id="filcc" size=12 placeholder="FIL/CONTO">
                                </td>
                            </tr>
                                <td colspan="2" align="right">
                                    <button type="submit" class="btn btn-success mb-2"><i class="fas fa-print fa-1x text-lightgray-300 col-auto">&nbsp;&nbsp;Stampa</i></button><br>
                                    <input type="hidden" class="form-control" name="print" id="print" value="si">
                                </td>
                            </tr>
                        </table>
                    </form>
      
               </div>
              </div>
            </div>            


<!--            
            <div class="col-lg-3">
              <div class="card border-success mb-6">
                <div class="card-header">UNDER 35<i class="fas fa-birthday-cake fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">

                <h4 class="card-title" style="color:#FFFFFF;">Calcolo età</h4>
                
                <form action="utility.php" method="POST" onsubmit="return ray.ajax()">
                    <table border="0" style="border-color:red;" width="90%" align="center">
                        <tr>
                            <td>
                               Data Nascita 
                            </td>
                            <td>
                                Data Contratto
                            </td>
                            <td rowspan="2" valign="bottom">
                                <button type="submit" class="btn btn-success mb-2"><i class="fas fa-baby-carriage fa-1x text-lightgray-300 col-auto">&nbsp;&nbsp;Calcola</i></button><br>
            <input type="hidden" class="form-control" name="calcoloeta" id="calcoloeta" value="si">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="form-control form-control-sm" name="dt1" id="datanascita" placeholder="aaaa/mm/gg" size=12>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm" name="dt2" id="datacontratto" placeholder="aaaa/mm/gg" size=12>
                            </td>
                        </tr>
                    </table>
                </form>
                
             </div>
              </div>
            </div>
-->
            <div class="col-lg-4">
              <div class="card border-success mb-6">
                <div class="card-header">Addendum RATEIZZAZIONE<i class="fas fa-print fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">

                  <form action="modulistica/SO51_addendum_rateizzazione.php" method="GET" onsubmit="return ray.ajax()">
                        <table border="0" width="90%" align="center">
                            <tr>
                                <td valign="top">
                                   Tipo Domanda a Socio  
                                </td>
                                <td>
                                    <input type="radio" class="form-check-input" name="scelta" id="U30" value="U30" > Under 35 <small>(3x
                                    <input type="text" style="background-color:white;color:black;" name="numaz" id="numaz" size="2" value="10">
                                    )</small><br>
                                    <input type="radio" class="form-check-input" name="scelta" id="CM" value="CM" > ChiantiMutua <small>(8x2)</small><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                   NAG Nuovo Socio 
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="nag" id="nag" size=12>
                                </td>
                            </tr>
                                <td colspan="2" align="right">
                                    <button type="submit" class="btn btn-success mb-2"><i class="fas fa-print fa-1x text-lightgray-300 col-auto">&nbsp;&nbsp;Stampa</i></button><br>
                                    <input type="hidden" class="form-control" name="print" id="print" value="si">
                                </td>
                            </tr>
                        </table>
                    </form>
      
                    <small>Selezionare la tipologia di rateizzazione (attenzione anche al numero delle rate)
                    </small>
               </div>
              </div>
            </div>


            <div class="col-lg-4">
              <div class="card border-success mb-6">
                <div class="card-header">Addendum DONAZIONE UNDER 35<i class="fas fa-print fa-1x text-lightgray-300 col-auto"></i>
                </div>
                <div class="card-body">

                  <form action="modulistica/SO50_addendum_donazione.php" method="GET" onsubmit="return ray.ajax()">
                        <table border="0" width="90%" align="center">
                            <tr>
                                <td>
                                   NAG Donante 
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="nagD" id="nagD" size=12>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                   NAG Nuovo Socio 
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="nag" id="nag" size=12>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                   Nr.Azioni possedute 
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="numaztotali" id="numaztotali" size=12>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                   Nr.Azioni donate 
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" name="numaz" id="numaz" size=12>
                                </td>
                            </tr>
                                <td colspan="2" align="right">
                                    <button type="submit" class="btn btn-success mb-2"><i class="fas fa-print fa-1x text-lightgray-300 col-auto">&nbsp;&nbsp;Stampa</i></button><br>
                                    <input type="hidden" class="form-control" name="print" id="print" value="si">
                                </td>
                            </tr>
                        </table>
                    </form>
      
               </div>
              </div>
            </div>


</div> <!-- /. Fine content Row 3 -->


</div> <!-- /. Fine Begin Page Content -->

</div>
<!-- /.container-fluid -->



	<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></a></center><br><br>


<?php
} // chiudo ELSE
?>