<?php

echo '    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">    
				<head>
                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                <meta http-equiv="pragma" content="no-cache">
                <meta http-equiv="cache-control" content="no-cache">
				<link rel="shortcut icon" href="favicon.ico">
                <title>Portale ChiantiMutua</title>
				
				<script type="text/javascript" src="js/jquery.js"></script>
				<script type="text/javascript" src="js/kickstart.js"></script>
                <script type="text/javascript" src="js/sorttable.js"></script> 
                <script type="text/javascript" src="js/intro.js"></script> 

				<!-- Funzione di ricerca con timing -->
                <script type="text/javascript">

    				  $(document).ready( function() {

    				  var typewatch = (function(){
    				  var timer = 0;
    				  return function(callback, ms){
    					  clearTimeout (timer);
    					  timer = setTimeout(callback, ms);
    					}  
    				  })();

    				  $("#camporicerca").keyup(function () {
    					  typewatch(function () {
    					  // executed only 800 ms after the last keyup event.
    					  
    					  loaddata();

    					}, 800);
    				  });

    				  }); 
    				
    				function loaddata()
    				{
    				 var name=document.getElementById( "username" );
    					

    					 if((name.value != "") && (name.value.length >=5 ))
    					 {
    					  $.ajax({
    					  type: \'post\',
    					  url: \'pos_home_ricerca.php\',
    					  data: {
    					  user_name:name.value
    					  },
    					  success: function (response) {
    					   // We get the element having id of display_info and put the response inside it
    					   $( \'#display_info\' ).html(response);
    					  }
    					  });
    					
    				}else{
    				  $( \'#display_info\' ).html("<b style=\'color:red;\'>Per favore inserisci qualcosa da cercare... (minimo 5 caratteri)");
    				 }
    				}
				</script>

                <!-- Funzione di loading di attesa -->
                <script type="text/javascript">
                    var ray={
                    ajax:function(st)
                        {
                            this.show(\'load\');
                        },
                    show:function(el)
                        {
                            this.getID(el).style.display=\'\';
                        },
                    getID:function(el)
                        {
                            return document.getElementById(el);
                        }
                    }
                </script>

                <script>
                <!-- Funzione di cambio background -->
                    function bgChange(bg) {
                        document.body.style.background = bg;
                    }
                </script>

                <!-- --------- INIZIO CSS STYLE ---------- -->
                <style type="text/css">
                    @import "css/main.css";
                    @import "css/menu.css";
                    @import "css/style.css";
                    @import "css/introjs.css";
                    <!-- @import "css/kickstart-buttons.css"; -->
                    <!-- @import "css/kickstart-forms.css"; -->
                    @import "css/fontawesome-free/css/all.min.css";
                    @import "css/sb-admin-2.min.css";
                    @import "css/bootstrap.css"; 
                    @import "css/DT_bootstrap.css";
                </style> 

                <!-- --------- FINE CSS STYLE ---------- -->

				</head>

<script src="js/bootstrap.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8" language="javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8" language="javascript" src="js/DT_bootstrap.js"></script>

                <body>    

';

?>