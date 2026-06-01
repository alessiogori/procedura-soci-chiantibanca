<?php
/*
echo'

  <!-- Custom fonts for this template-->
  <link href="css/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  ';
*/

?>
<!--  
*****************************************************************************
INTESTAZIONE 
*****************************************************************************
-->

  <table border="0" align="center" width="98%">
    <tr>
      <td align="left" width="70%"><a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/index.php"><img src="img/logo_chiantibanca.png" height="60"></a></td>
      <td align="left" ><?php echo $userdesc; ?></td>
      <td align="right" style="text-decoration: none;font-size:small;"><b>Ufficio Soci</b><br>
          <i class="fas fa-phone-alt fa-1x text-gray-300 col-auto"></i> 055 8255 318/337

  <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
  <div class="btn-group" role="group">
    <button id="btnGroupDrop1" type="button" title="Chat" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:#222222;"><i class="fas fa-comment-dots fa-1x text-gray-300 col-auto"></i></button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1" style="">

<a class="dropdown-item" style="font-size:12px;color:white;" href="https://teams.microsoft.com/l/chat/0/0?users=duccio.becattini@chiantibanca.it" target="_blank"><i class="fas fa-comment-dots fa-1x text-gray-300 col-auto"></i>Becattini</a>
<a class="dropdown-item" style="font-size:12px;color:white;" href="https://teams.microsoft.com/l/chat/0/0?users=elisabetta.sbaragli@chiantibanca.it" target="_blank"><i class="fas fa-comment-dots fa-1x text-gray-300 col-auto"></i>Sbaragli</a>

    </div>
  </div>
  </div>

  <br>          
          <i class="fas fa-envelope fa-1x text-gray-300 col-auto"></i> soci@chiantibanca.it / soci@pecchiantibanca.it<br>
      </td>
    </tr>
    </table>





<!--  
*****************************************************************************
NAVIGAZIONE
*****************************************************************************
-->

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a style="text-decoration: none;" href="http://10.197.139.22:8080/soci/index.php"><i class="fas fa-home fa-2x text-gray-300 col-auto"></i><span class="navbar-brand mb-0 h1"><b>&nbsp;PORTALE SOCI</b></span></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav bd-navbar-nav flex-row">
      <!--
      <li class="nav-item active">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/index.php">Home <span class="sr-only">(current)</span></a>
      </li>
    -->
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/faq/?qa=questions" target="_blank">FAQ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/documentazione.php">Documentazione</a>
      </li>
<!--  <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/modulistica.php">Modulistica</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/suggest.php">Comunicazioni</a>
      </li> -->
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/filiale_auth.php">&reg;Filiale</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/area_auth.php">&reg;Area</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/direzione_auth.php">&reg;Direzione</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/soci_auth.php">&reg;Soci</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/admin.php">&reg;Admin</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/docs/PortaleSoci.pdf">?</a>
      </li>
    </ul>

  </div>

<!--
PARTE VECCHIA  
  		<form class="form-inline my-2 my-lg-0" action="schedasocio.php" method="POST" onsubmit="return ray.ajax()">
		<input class="form-control mr-sm-2" style="text-decoration: none;font-size:small;background-color: #F2FCFC;" type="text" name="ricerca" id="ricerca" placeholder="Ricerca Soci Banca & Mutua" title="Puoi inserire il cognome/nome - cag - numero Socio - telefono">
		<button class="btn btn-primary my-0 my-sm-0 btn-sm" type="submit"><i class="fas fa-search-plus fa-1x text-gray-300 col-auto"></i></button>
        </form>  
      <form class="form-inline my-2 my-lg-0" action="mutua_listaschedasocio.php" method="POST" onsubmit="return ray.ajax()">
    <input class="form-control mr-sm-2" style="text-decoration: none;font-size:small;background-color: #F2FCF2;" type="text" name="ricerca" id="ricerca" placeholder="Ricerca solo Socio Mutua" title="Puoi inserire il cognome/nome - cag - Tessera Mutua">
    <button class="btn btn-primary my-0 my-sm-0 btn-sm" type="submit"><i class="fas fa-search-plus fa-1x text-gray-300 col-auto"></i></button>
        </form>          

-->
      <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/segnalazione_mail_decesso.php" title="Segnalazione Decesso Socio"><i class="fas fa-cross fa-1x text-gray-300 col-auto"></i></a>

      <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/segnalazione_mail_vincolo.php" title="Richiesta eliminazione Vincolo Conto"><i class="fas fa-check-double fa-1x text-gray-300 col-auto"></i></a>

      <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/segnalazione_mail_scissione.php" title="Richiesta Scissione Certificati"><i class="fas fa-cut fa-1x text-gray-300 col-auto"></i></a>

      <a class="nav-link" style="text-decoration: none;" href="http://10.197.139.22:8080/soci/bday.php?key=<?php echo $filiale_id;?>" title="Compleanno Soci"><i class="fas fa-birthday-cake fa-1x text-gray-300 col-auto"></i></a>

        <form class="input col-md-2" action="schedasocio.php" method="POST" onsubmit="return ray.ajax()">
            <input class="form-control py-1" type="text" placeholder="Ricerca Soci Banca"  id="NAG_ricerca" name="ricerca" title="Puoi inserire il cognome/nome - cag - numero Socio - telefono" style="text-decoration: none;font-size:small;background-color: #F2FCFC;">
            <img src="img/ico_search.png" height="20" style="position: absolute; top: 6px; right: 25px;">
        </form>


    <!-- SCRIPT AUTOCOMPLETE -->
    <!--
    <script type="text/javascript">

          var countries = new Bloodhound({
            datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.name); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            limit: 10,
            prefetch: {
              url: 'NAG_ricerca.json',
              filter: function(list) {
                return $.map(list, function(country) { return { name: country }; });
              }
            }
          });
   
          countries.initialize();
           
          $('#NAG_ricerca').typeahead(null, {
            name: 'ricerca',
            displayKey: 'name',
            source: countries.ttAdapter()
          });


    </script>
    -->
    <!-- /SCRIPT AUTOCOMPLETE -->

<!--
        <form class="input col-md-2" action="mutua_listaschedasocio.php" method="POST" onsubmit="return ray.ajax()">
            <input class="form-control py-1" type="text" placeholder="Ricerca solo Mutua"  id="ricerca" name="ricerca" title="Puoi inserire il cognome/nome - cag - Tessera Mutua" style="text-decoration: none;font-size:small;background-color: #F2FCF2;">
            <img src="img/ico_search.png" height="20" style="position: absolute; top: 6px; right: 25px;">
        </form>
-->        
<!--
          <form class="input-group col-md-2" action="schedasocio.php" method="POST" onsubmit="return ray.ajax()">
            <input class="form-control py-1" type="text" placeholder="Ricerca Banca & Mutua"  id="ricerca" name="ricerca" title="Puoi inserire il cognome/nome - cag - numero Socio - telefono" style="text-decoration: none;font-size:small;background-color: #F2FCFC;">
            <span class="input-group-append">
              <button class="btn btn-outline-secondary" type="button" style="text-decoration: none;font-size:small;background-color: #F2FCFC;">
                    <i class="fa fa-search fa-1x text-blue-300" style="display:inline-block;"></i>
                </button> 
            </span>
        </form>  
        
          <form class="input-group col-md-2" action="mutua_listaschedasocio.php" method="POST" onsubmit="return ray.ajax()">
            <input class="form-control py-1" type="text" placeholder="Ricerca solo Mutua"  id="ricerca" name="ricerca" title="Puoi inserire il cognome/nome - cag - Tessera Mutua" style="text-decoration: none;font-size:small;background-color: #F2FCF2;">
            <span class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" style="text-decoration: none;font-size:small;background-color: #F2FCF2;">
                    <i class="fa fa-search fa-1x text-blue-300" ></i>
                </button>
            </span>
        </form>          
-->
        
  <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
  <div class="btn-group" role="group">
    <button id="btnGroupDrop1" type="button" title="Altri Portali" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="btnGroupDrop1" style="">
      <a class="dropdown-item" style="font-size:12px;color:white;" href="https://chiantibanca.worktogether.it/" target="_blank">Intranet ChiantiBanca</a>
      <a class="dropdown-item" style="font-size:12px;color:white;" href="http://10.197.139.22:8080//FORMAZIONE_sicra/index.php" target="_blank">Formazione Sicra</a>
      <a class="dropdown-item" style="font-size:12px;color:white;" href="http://ftbcc-stratos.soar.bcc.it/" target="_blank">Formazione FTBCC</a>
      <a class="dropdown-item" style="font-size:12px;color:white;" href="https://chiantibanca.worktogether.it/views/jump.asp?type=App&AppId=10" target="_blank">Postalizzazione</a>
     <!-- <a class="dropdown-item" style="font-size:12px;color:white;" href="http://10.197.139.22:8080/mutua/index.php">Mutua</a> -->
    </div>
  </div>
</div>


</nav>

<!--<img src="img/tmp.jpg" class="img-fluid" alt="Responsive image">-->

<br>

