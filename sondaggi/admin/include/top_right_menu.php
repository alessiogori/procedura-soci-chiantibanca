<?php
// PHP Survey All Rights Reserved
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// https://www.netartmedia.net

?><?php
if(!isset($_COOKIE["AuthUser"])) 
{
?>
	
	<a target="_blank" href="https://www.netartmedia.net/en_Contact.html" class="top-right-link"><img src="images/contact.png"/>
	<?php echo $this->texts["have_questions"];?>
	</a>
<?php
}
else
{
?>

	<li class="dropdown">
	  <a href="#">
			
			<p class="notification" id="top_notification">
			
			</p>
			
	  </a>
	  
</li>
<li>
	<a href="../index.php" target="_blank">
		<i class="xti-settings"></i>
		<p><?php echo $this->texts["open_main_site"];?></p>
	</a>
</li>
<li>
	<a href="index.php?page=settings">
		<i class="ti-settings"></i>
		<p><?php echo $this->texts["settings"];?></p>
	</a>
</li>
<li>
	<a href="logout.php">
		<i class="ti-shift-right"></i>
		<p><?php echo $this->texts["logout"];?></p>
	</a>
</li>


<?php
}
?>
