<?php
// PHP Survey
// https://www.netartmedia.net/php-survey
// Copyright (c) All Rights Reserved NetArt Media
// Find out more about our products and services on:
// https://www.netartmedia.net

?><?php
if(!defined('IN_SCRIPT')) die("");

$this->SetAdminHeader($this->texts["my_surveys"]);

if(isset($_POST["proceed_delete"])&&trim($_POST["proceed_delete"])!="")
{
	if(isset($_POST["delete_surveys"])&&sizeof($_POST["delete_surveys"])>0)
	{
		$delete_surveys=$_POST["delete_surveys"];
		$xml = simplexml_load_file($this->data_file);

		$i=-1;
		$str = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
		<surveys>";
		foreach($xml->children() as $child)
		{
			$i++;
			  if(in_array($i, $delete_surveys)) 
			  {
				
				continue;
				
			  }
			  else
			  {
					$str = $str.$child->asXML();
			  }
		}
		$str = $str."
		</surveys>";
		
		
		$xml->asXML("../data/surveys_".time().".xml");
	
		$fh = fopen($this->data_file, 'w') or die("Error: Can't update the data  file");
		fwrite($fh, $str);
		fclose($fh);
	}
}
?>
<script>

function ValidateSubmit(form)
{
	if(confirm("<?php echo $this->texts["sure_to_delete"];?>"))
	{
		return true;
	}
	else
	{
		return false;
	}
}
</script>

<div class="card col-lg-12">

	<br/>
	

	
	<div class="clearfix"></div>
	<form class="no-margin" action="index.php" method="post" onsubmit="return ValidateSubmit(this)">
	<input type="hidden" name="proceed_delete" value="1"/>
	<input type="hidden" name="page" value="surveys"/>
	
	<div class="header">
		<h4 class="title"><?php  echo $this->texts["your_current_listings"];?></h4>
	</div>
		
	<br/>
	<div class="table-responsive table-wrap">
		<table class="table table-striped">
		  <thead>
			<tr>
			   <th width="80"><?php echo $this->texts["delete"];?></th>
			 
			  <th width="80"><?php echo $this->texts["edit"];?></th>
			  <th width="310"><?php echo $this->texts["survey_name"];?></th>
			  
			  <!--<th><?php echo $this->texts["survey_description"];?></th>-->
			  <th><?php echo $this->texts["total_questions"];?></th>
			  <th width="280"><?php echo $this->texts["embed"];?></th>
			 
			</tr>
		  </thead>
      <tbody>
	  <?php
	    $surveys = simplexml_load_file($this->data_file);
		$i=0;
		foreach ($surveys->survey as $survey)
		{
			?>
			<tr>
				<td><input type="checkbox" value="<?php echo $i;?>" name="delete_surveys[]"/></td>
				
				<td><a href="index.php?page=edit&id=<?php echo $i;?>"><img src="images/edit-icon.gif"/></a></td>
				
				<td>
					<strong><a class="underline-link" href="index.php?page=edit&id=<?php echo $i;?>"><?php echo $survey->name;?></a></strong>
					<br/>
					<i style="font-size:11px"><?php echo $this->text_words($survey->description,30);?></i>
				
				</td>
				
				
				<td><?php echo substr_count($survey->questions, ';;;');?></td>

				<td>
					<a href="../<?php echo $this->survey_link($survey->id,$survey->name);?>" class="underline-link" target="_blank"><?php echo $this->texts["survey_link_open"];?></a>
					
					
					<br/>
					<textarea style="margin-top:15px;width:100%;height:60px;font-size:11px">&lt;iframe src="<?php echo (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER["HTTP_HOST"].str_replace("admin/index.php?page=surveys",$this->survey_link($survey->id,$survey->name),$_SERVER["REQUEST_URI"]);?>"&gt;&lt;/iframe&gt;</textarea>
					
				
				</td>
				
			</tr>
			<?php
			$i++;
		}
	  
	  ?>
     
      </tbody>
    </table>
  </div>
  <br/>
  <input type="submit" class="btn btn-default pull-right" value=" <?php echo $this->texts["delete"];?> "/>
  
  </form>
  <div class="clearfix"></div>
  <br/>
  
  


</div>	

<script>

function LoadPage(x)
{
	document.location.href="index.php?page="+x;
}

function OverDB(element, x)
{
	element.className = "db-wrap back-color-"+x;
}

function OutDB(element)
{
	element.className = "db-wrap";
}

$("#a1").mouseover(function(){
  $("#ul1").addClass("open").removeClass("closed")
})
</script>
