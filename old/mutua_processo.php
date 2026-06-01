<?php
// *****************************************************************************
// Portale ChiantiMutua
// Sviluppo e realizzazione: Alessio Fedi (2019)
// *****************************************************************************
// SEZIONE DA NON MODIFICARE

// Nascondo gli errori
error_reporting(E_ALL ^ E_DEPRECATED);

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
	<div class="alert alert-dismissible alert-success"><h3>Il mondo ChiantiMutua</h3>
    </div>  
</div>

</div>
</center>

<!-- Content Row 2 -->
<div class="row">

<table border="0" align="center" width="100%">
    <tr>
        
        <td valign="top" width="33%">
            <div class="col-lg-12">
              <div class="card border-success mb-6">
                <div class="card-header">
                    <img src="img/ico_1.png" width="40" align="absmiddle">
                    <font size="5"> &nbsp;  <a href="#1" style="text-decoration: none;color:white;">Nuovo Portale BCC MS</a> </font>
                </div>
              </div>
            </div>
        </td>
        
        <td valign="top" width="33%">
            <div class="col-lg-12">
              <div class="card border-success mb-6">
                <div class="card-header">
                    <img src="img/ico_2.png" width="40" align="absmiddle">
                    <font size="5"> &nbsp; <a href="#2" style="text-decoration: none;color:white;">Iniziativa 50%</a> </font>
                </div>
              </div>
            </div>
        </td>
        
        <td valign="top" width="33%">
            <div class="col-lg-12">
              <div class="card border-success mb-6">
                <div class="card-header">
                    <img src="img/ico_3.png" width="40" align="absmiddle">
                    <font size="5"> &nbsp; <a href="#3" style="text-decoration: none;color:white;">Processo di lavoro</a> </font>
                </div>
              </div>
            </div>
        </td>        
        
    </tr>
</table>



</div>
<br>
<!-- Content Row 3 -->
<div class="row">
    
<!-- 1 -->
<a name="1"></a>
<div class="col-lg-12">
  <div class="card border-success mb-6">
    <div class="card-header"> <b>Nuovo Portale BCC MS</b> </font>
    </div>
    <div class="card-body">

<p class="m-0 text-justify">              
L’innovativo portale BCC MS è un progetto di ChiantiMutua che ha lo scopo principale di garantire ai propri associati l’accesso ai servizi sanitari convenzionati in modo immediato, efficace e conveniente.<br>
<font style="font-weight:bold;color:lime;">Immediato</font>, perché una volta ricercata la prestazione sul portale, questa può essere subito prenotata dal socio ( in un prossimo futuro anche pagata).<br>
<font style="font-weight:bold;color:lime;">Efficace</font>, perché il portale fa vedere le prestazioni richieste disponibili presso le strutture sanitarie ordinate per data di disponibilità e vicinanza della struttura.<br>
<font style="font-weight:bold;color:lime;">Conveniente</font>, perché attraverso l’integrazione con il gestionale della Mutua, il portale propone al socio la prestazione disponibile ad un costo già diminuito sia dello sconto riservato ai soci Mutua, che del rimborso previsto dai Regolamenti, che sarà pagato alla struttura sanitaria direttamente da ChiantiMutua. In tal modo il socio otterrà subito il sussidio, con il grande vantaggio di non dover presentare la domanda per ottenere il rimborso dalla Mutua.<br>
Il portale dunque è il canale di accesso alle prestazioni sanitarie che offre i maggiori vantaggi agli associati, ma anche il principale strumento per ridurre l’attività burocratica della Mutua (ed anche un po’ per la Filiale) che non deve processare a mano le domande di rimborso e i relativi bonifici.<br>
Naturalmente sono molto utili allo scopo anche le nuove procedure on line per la richiesta di rimborso e per la domanda di ammissione a Socio.<br>
  </p>    
  </div>
  </div>
</div>

</div>
<div class="row">
        <p>&nbsp;</p>

<!-- 2 -->
<a name="2"></a>
<div class="col-lg-12">
  <div class="card border-success mb-6">
    <div class="card-header"> <b>Iniziativa 50%</b> </font>
    </div>
    <div class="card-body">

<p class="m-0 text-justify"> 
Il nuovo Portale BCC MS (BCC Mutuality Service) riservato in esclusiva ai Soci Mutua sarà utilizzato anche da tutte le Mutue toscane legate alle BCC e anche da quelle di tutta Italia, è elemento strategico che potrà dare un concreto vantaggio al sistema delle Banche di Credito Cooperativo e per questo ChiantiMutua e ChiantiBanca intendono promuoverlo al massimo indirizzando gli associati a registrarsi nell’Area Riservata di www.chiantimutua.it dalla quale si accede al portale e ad altre utili informazioni.<br> 
Naturalmente l’inserimento sul portale delle strutture sanitarie convenzionate necessita di tempo e sarà pertanto graduale. La maggiorazione dei rimborsi prevista fino al 28.02.2021 per la prenotazione tramite il portale è un elemento che dovrebbe facilitare l’inserimento di nuove strutture sanitarie nel portale, specialmente quelle più importanti. L’iniziativa del 50% prevede comunque di ottenere dei rimborsi (seppure con % minori) anche per le prestazioni effettuate direttamente presso le strutture sanitarie senza passare dal portale.<br> 
Qui di seguito si riporta il riepilogo del processo che interessa i vari soggetti (Cliente banca, Filiale, Mutua) con riferimento alle domande di ammissione a socio Mutua, sia fatte in Filiale che direttamente on line, con i particolari casi relativi all’Operazione a Premi “Giovani Soci Banca” e al contestuale accesso alle prestazioni sanitarie tramite il portale, che necessitano una delibera di ammissione alla Mutua immediata.<br> 
Per questi ultimi casi, dovendo riscontrare subito i dati effettivamente riportati sulla domanda di adesione sottoscritta dal socio (in Filiale o on line), si procederà a dotare tutte le fotocopiatrici / stampanti di rete delle Filiali di un destinatario “Contratti Mutua” sulla  funzione SCANNER, in modo che la Filiale, non appena ha fatto firmare la domanda cartacea al cliente o la riceverà da chi l’avrà inserita on line, la passerà semplicemente nello scanner prima di inoltrarla per corriere all’Ufficio Mutua della Direzione Generale.<br> 
</p>
  </div>
  </div>
</div>

</div>
<div class="row">
        <p>&nbsp;</p>
    
<!-- 3 -->
<a name="3"></a>
<div class="col-lg-12">
  <div class="card border-success mb-6">
    <div class="card-header"> <b>Processo di lavoro</b> </font>
    </div>
    <div class="card-body">

<p class="m-0 text-justify">  
<i>“L’adesione a CHIANTIMUTUA in qualità di socio ordinario è riservata alle persone fisiche che sono Soci o clienti di CHIANTIBANCA CREDITO COOPERATIVO S.c.."</i>	(Regolamento Generale ChiantiMutua)	
<center><img src="img/mutua_processo_ammissione.png" ></center>
  </p>    
  </div>
  </div>
</div>

<!-- chiudo la riga --></div>
<!-- FINE ULTIMO DIV --></div>


<br><br><center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="img/frecciasx.png"></center>
