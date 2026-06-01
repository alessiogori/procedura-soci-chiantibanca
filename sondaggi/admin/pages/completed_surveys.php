<?php
// PHP Survey All Rights Reserved
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// https://www.netartmedia.net
?><?php
if(!isset($_REQUEST["survey_id"]))
{
?>

<?php
$this->SetAdminHeader($this->texts["completed_surveys"]);
?>

 <div class="col-md-12">
	<div class="card">
		<div class="header">
			<h4 class="title"><?php echo $this->texts["click_see_completed"];?></h4>
			
		</div>
		<div class="content table-responsive table-full-width">
			<table class="table table-striped">
				<thead>
					<th><?php echo $this->texts["id"];?></th>
					<th><?php echo $this->texts["survey_name"];?></th>
					<th><?php echo $this->texts["total_completed"];?></th>
					
				</thead>
				<tbody>
				
				<?php
				$surveys = simplexml_load_file($this->data_file);
				$i=0;
				foreach ($surveys->survey as $survey)
				{
				?>
					<tr>
						<td><a class="underline-link" href="index.php?page=completed_surveys&survey_id=<?php echo $survey->id;?>"><?php echo $survey->id;?></a></td>
						<td><a class="underline-link" href="index.php?page=completed_surveys&survey_id=<?php echo $survey->id;?>"><?php echo $survey->name;?></a></td>
						<td>
						<?php
						$total_completed=$this->count_survey_files("../".$this->data_folder."/".$survey->id);
						
						if($total_completed>0)
						{
							echo "<a class=\"underline-link\" href=\"index.php?page=completed_surveys&survey_id=".$survey->id."\"><strong>".$total_completed."</strong></a>";
						}
						else
						{
							echo "0";
						}
						?>
						</td>
					</tr>
				<?php
				}
				?>
				
				</tbody>
			</table>

		</div>
	</div>
</div>

<?php
}
else
{
	$id=$_REQUEST["survey_id"];
	$this->check_id($id);
	
	
	
	$xml = simplexml_load_file($this->data_file);
	$nodes = $xml->xpath('//surveys/survey/id[.="'.$id.'"]/parent::*');
	$survey = $nodes[0];
	
	$survey_folder="../".$this->data_folder."/".$id;
	
	
	if(isset($_REQUEST["del"]))
	{
		unlink($survey_folder."/".$_REQUEST["del"].".xml");
	}
	
	echo "<div class=\"col-md-12\"><h3>".$this->texts["survey"].": <strong>".$survey->name."</strong></h3></div><div class=\"clearfix\"></div><br/>";

	if(!file_exists($survey_folder))
	{
		
		echo "<br/><i>".$this->texts["no_results"]."</i>";
	}
	else
	{
	
		if ($handle = opendir($survey_folder)) 
		{
			while (false !== ($file = readdir($handle))) 
			{
				if ('.' === $file || '..' === $file) continue;
				
				$file_name=explode(".",$file,2);
				
				$name_items=explode("_",$file_name[0]);
				
				if(sizeof($name_items)!=2) continue;
				
				$completed_survey = simplexml_load_file($survey_folder."/".$file);
					
				
				?>
				 <div class="col-md-12">
					<div class="card">
						
						<a class="pull-right del-link underline-link" href="index.php?page=completed_surveys&survey_id=<?php echo $_REQUEST["survey_id"];?>&del=<?php echo str_replace(".xml","",$file);?>"><?php echo $this->texts["delete"];?></a>
						
						<div class="header">
							<h5 class="title">
								<?php  echo date("F j, Y, g:i a",$name_items[1]);?> &nbsp; 
							
								<?php 
									if($completed_survey->result->name!="") echo " &nbsp; - &nbsp; ".$completed_survey->result->name;
									if($completed_survey->result->email!="") echo " (".$completed_survey->result->email.") ";
									echo " ".$completed_survey->result->phone;
								?>
							</h5>
							<div class="results-data">
							<?php
							$data_lines = explode("@@@",$completed_survey->result->data);
							
							foreach($data_lines as $data_line)
							{
								if(trim($data_line) == "") continue;
								
								$question_answer=explode("###",$data_line);
								
								if(sizeof($question_answer) != 2) continue;
								
								echo $question_answer[0].": <strong>".$question_answer[1]."</strong><br/>";
							}
							
							?>
							</div>
						</div>
						
						<br/>
					</div>
				</div>	
				
				
				<?php
			
			}
			
			
			
			closedir($handle);
			 
		}
	}
 
}
?>