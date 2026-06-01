<?php

// News Portale Soci

?>

	<div class="container-fluid">
	
<?php
echo '<img src="img/chiantibancanews.png"height="100">';
echo '<h3>News inerenti la compagine sociale</h3>';

    $dir = "news/";
    foreach (scandir($dir) as $f) 
    {
      if ($f !== '.' and $f !== '..')
      {
          echo "<a href='news/".$f."' target='_blank'>".$f."</a> <br>";
      }
    }


?>

	</div>
