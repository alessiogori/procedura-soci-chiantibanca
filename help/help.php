<?php

// URL = /soci/help/help.php?nome=XXXX

$nomefile = $_GET['nome'].'.wmv';

?>

<span style="font-family:verdana;">

<!-- TITOLO PAGINA -->
<center>
    <h3>Aiuto on line per <?php echo $nomefile; ?></h3>
	<small>Doppio click per allargare il video a pieno schermo</small>
	
	<br><br>
    
                    <object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" width="700"
                            height="360" codebase="http://www.microsoft.com/Windows/MediaPlayer/">
                       <param name="Filename" value="/soci/help/<?php echo $nomefile; ?>">
                       <param name="AutoStart" value="true">
                       <param name="ShowControls" value="true">
                       <param name="BufferingTime" value="2">
                       <param name="ShowStatusBar" value="true">
                       <param name="AutoSize" value="true">
                       <param name="InvokeURLs" value="false">
                       <embed src="/soci/help/<?php echo $nomefile; ?>"
                              type="application/x-mplayer2" autostart="1" enabled="1" showstatusbar="1"
                              showdisplay="1" showcontrols="1" 
                              pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" 
                              CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,0,0,0" width="700" height="484"></embed>
                    </object>

</center>
	<br><br><br>
	<center><a href="javascript:history.back();" title="Torna alla ricerca"><img src="../img/frecciasx.png"></a></center><br><br>
	
	
	</span>
