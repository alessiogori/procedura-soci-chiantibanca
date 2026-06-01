<?php
// PHP Survey, https://www.netartmedia.net/php-survey
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// https://www.netartmedia.net
?><div class="container min-height-400">
			
	<?php
	$surveys = simplexml_load_file($this->data_file);
	$survey_counter=0;
	foreach ($surveys->survey as $survey)
	{
	?>
		<div class="block-wrap">
			<h3><a class="custom-color" href="<?php echo $this->survey_link($survey->id,$survey->name);?>"><?php echo $survey->name;?></a></h3>
			<p><?php echo $survey->description;?></p>
			<a class="btn btn-default btn-md custom-back-color" href="<?php echo $this->survey_link($survey->id,$survey->name);?>"><?php echo $this->texts["start_survey"];?></a>
		</div>
	<?php
		$survey_counter++;
	}
	
	if($survey_counter==0)
	{
		echo "<br/><h2>".$this->texts["no_surveys_found"]."</h2>";
	}
	?>

</div>

<?php
$this->Title($this->texts["surveys"]);
$this->MetaDescription("");
?>