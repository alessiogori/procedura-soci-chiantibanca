<?php
// PHP Survey, https://www.netartmedia.net/php-survey
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// https://www.netartmedia.net

?><?php
if(!defined('IN_SCRIPT')) die("");

$id=intval($_REQUEST["id"]);

$this->ms_i($id);


?>

<?php

$xml = simplexml_load_file($this->data_file);


?>
 <div class="card">
		<div class="header">
			<h4 class="title"><?php  echo $this->texts["edit_survey"];?></h4>
		</div>
		
		<div class="content">
			
			
		
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["survey_name"];?></label>
							<input type="text" name="survey_name" class="form-control border-input"  value="<?php echo $xml->survey[$id]->name;?>">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["survey_description"];?></label>
							<textarea type="text" id="survey_description" name="survey_description" class="form-control border-input"><?php echo $xml->survey[$id]->description;?></textarea>
						</div>
					</div>
				</div>
				
				
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
						
							<input type="checkbox" id="anonymous" value="1" name="anonymous" <?php if($xml->survey[$id]->anonymous=="1") echo "checked";?>/>
							
							<?php  echo $this->texts["make_anonymous"];?>
							
						</div>
					</div>
				</div>
				

				
			
			
			<div class="clearfix"></div>
			
			<hr/>
		
			<input type="hidden" name="survey_questions" id="survey_questions" value="<?php  echo $xml->survey[$id]->questions;?>"/>	
			<br/>
			<h4 class="title"><?php  echo $this->texts["edit_survey_questions"];?></h4>
			<br/>
			<table class="table">
		
			<tbody id="custom_display">	
			<?php
			$custom_amenities=explode(";;;",stripslashes($xml->survey[$id]->questions));
			$line_counter=0;
			foreach($custom_amenities as $amenity)
			{
				if(trim($amenity)=="") continue;
				$amenity_items=explode("---",$amenity);
				if(sizeof($amenity_items) != 3) continue;
			?>
			  <tr id="line<?php echo $line_counter;?>">
				<td><?php echo $amenity_items[0];?></td>
				<td>
				
					<input value="<?php echo $amenity_items[1];?>" id="survey_question_<?php echo $line_counter;?>" name="survey_question_<?php echo $line_counter;?>" type="text" class="form-control border-input"  placeholder=""/>
				</td>
				<td>
				
					<?php 
					
					if(trim($amenity_items[2])!="")
					{
					?>	
						<textarea id="possible_answers_<?php echo $line_counter;?>" name="possible_answers_<?php echo $line_counter;?>" rows="7" type="text" class="form-control border-input"><?php echo str_replace("@@@","\n",$amenity_items[2]);?></textarea>
						
					<?php
					}					
					?>
				
				
				</td>
				<td align="right" valign="top">
					<a href="javascript:RemoveLine('<?php echo $line_counter;?>','<?php echo $amenity;?>')"><img src="images/closeicon.png"/></a>
				</td>
			  </tr>
			<?php
				$line_counter++;
			}
			?>
			</tbody>
			</table>
			
		
			
		</div>
	</div>

					
	