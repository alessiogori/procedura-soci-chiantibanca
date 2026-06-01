<?php
// PHP Survey, https://www.netartmedia.net/php-survey
// A software product of NetArt Media, All Rights Reserved
// Find out more about our products and services on:
// https://www.netartmedia.net

?><?php
if(!defined('IN_SCRIPT')) die("");
?>

<?php
$show_add_form=true;

$this->SetAdminHeader($this->texts["new_survey"]);

if(isset($_REQUEST["proceed_save"]))
{
	
	$listings = simplexml_load_file($this->data_file);

	$listing = $listings->addChild('survey');
	
	$arrChars = array("A","F","B","C","O","Q","W","E","R","T","Z","X","C","V","N");
							
	$random_code = $arrChars[rand(0,(sizeof($arrChars)-1))]."".rand(1000,9999)
	.$arrChars[rand(0,(sizeof($arrChars)-1))].rand(1000,9999);

	$listing->addChild('id', $random_code);
	$listing->addChild('name', stripslashes($_POST["survey_name"]));
	$listing->addChild('description', stripslashes($_POST["survey_description"]));
	$listing->addChild('questions', stripslashes($_POST["survey_questions"]));
	
	if(isset($_POST["anonymous"])&&$_POST["anonymous"]=="1")
	{
		$listing->addChild("anonymous", "1");
	}
	else
	{
		$listing->addChild("anonymous", "0");	
	}
	
	$listings->asXML($this->data_file); 
	
	if(!file_exists("../data/".$random_code))
	{
		if(!mkdir("../data/".$random_code))
		{
			echo "The creation of the folder in which the results from this survey will be saved - data/".$random_code." failed! Please give write permissions to the /data folder";
		}
	}
	
	?>
	<h3><?php echo $this->texts["new_added_success"];?></h3>
	<br/>
	<a href="index.php?page=create_survey" class="underline-link"><?php echo $this->texts["add_another"];?></a>
	<?php echo $this->texts["or_message"];?>
	<a href="index.php?page=surveys" class="underline-link"><?php echo $this->texts["manage_listings"];?></a>
	<br/>
	<br/>
	<br/>
	<?php
	$show_add_form=false;
}	



if($show_add_form)
{
?>
 <div class="card">
		<div class="header">
			<h4 class="title"><?php  echo $this->texts["create_new_survey"];?></h4>
		</div>
		<div class="content">
			<form action="index.php" method="post">
			<input type="hidden" name="page" value="create_survey"/>
			<input type="hidden" name="proceed_save" value="1"/>
		
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["survey_name"];?></label>
							<input type="text" name="survey_name" class="form-control border-input"  value="">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label><?php  echo $this->texts["survey_description"];?></label>
							<textarea type="text" id="survey_description" name="survey_description" class="form-control border-input"></textarea>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
						
							<input type="checkbox" id="anonymous" name="anonymous"/>
							
							<?php  echo $this->texts["make_anonymous"];?>
							
						</div>
					</div>
				</div>
				

				
			
			
			<div class="clearfix"></div>
			
			<hr/>
		
			<input type="hidden" name="survey_questions" id="survey_questions"/>	
			<br/>
			<h4 class="title"><?php  echo $this->texts["add_survey_questions"];?></h4>
	
			<table class="table">
		
			<tbody id="custom_display">	
			
			</tbody>
			</table>
			
		
			<script>
			
			var line_counter=0;
			
			function AddQuestion()
			{
			
				
				var survey_question = document.getElementById("survey_question").value;
				
				if(survey_question=="")
				{
					alert("<?php echo $this->texts["enter_survey_question"];?>");
					document.getElementById("survey_question").focus();
					return;
				}
				
				var selected_field_type = document.getElementById("selected_type").value;
				var possible_answers = document.getElementById("possible_answers").value;
				
				
				possible_answers=possible_answers.replace(/@@@/gm, " / ");
				
				var preview_html = "";
				
				preview_html=
				"<tr id=\"line"+line_counter+"\" class=\"line-custom\">";
				
				preview_html+="<td>"+selected_field_type+"</td>";
				preview_html+="<td>"+survey_question+"</td>";
				preview_html+="<td>"+possible_answers+"</td>";
				preview_html+="<td align=\"right\"><a href=\"javascript:RemoveLine('"+line_counter+"','"+survey_question+"')\"><img src=\"images/closeicon.png\"/></a></td>";
				
				preview_html+="</tr>";
				
				document.getElementById("custom_display").innerHTML+=preview_html;
				
				line_counter++;
				document.getElementById("survey_questions").value+=selected_field_type+"---"+survey_question+"---"+possible_answers.replace(/(?:\r\n|\r|\n)/g, '@@@')+";;;";
				
				document.getElementById("survey_question").value="";
			}
			
			function RemoveLine(x,y)
			{
				document.getElementById("line"+x).style.display="none";
				document.getElementById("survey_questions").value=document.getElementById("survey_questions").value.replace(y+";;;", "");
			}
			</script>
			
			<div class="row">
			
				<div class="col-md-2">
					<div class="form-group">
						<input type="hidden" id="selected_type" name="selected_type" value="Text"/>
					
						<label><?php echo $this->texts["field_type"];?></label>
						<div class="clearfix"></div>
						<div class="btn-group">
						
							<a style="padding-left:8px !important;padding-right:8px !important" class="form-control border-input dropdown-toggle" data-toggle="dropdown" href="#">
							
							<img src="images/form_textbox.gif"> <?php echo $this->texts["textbox"];?> <span class="caret"></span></a>
							
							<ul class="dropdown-menu">
								<li><a href="javascript:void(0);"><img src="images/form_combo.gif"/><?php echo $this->texts["dropdown"];?></a></li>
								<li><a href="javascript:void(0);"><img src="images/form_textarea.gif"/><?php echo $this->texts["textarea"];?></a></li>
								<li><a href="javascript:void(0);"><img src="images/form_check.gif"/><?php echo $this->texts["checkbox"];?></a></li>
								<li><a href="javascript:void(0);"><img src="images/form_radio.gif"/><?php echo $this->texts["radiobutton"];?></a></li>
							</ul>
						</div>
						
						<script>
						$(".dropdown-menu li a").click(function () {
							var selText = $(this).text().trim();
							
							document.getElementById("selected_type").value=selText;
							
							if(selText=="Drop down"||selText=="Checkbox"||selText=="Radio button")
							{
								document.getElementById("answers_group").style.display="block";
							}
							else
							{
								document.getElementById("answers_group").style.display="none";	
							}
							
							var imgSource = $(this).find('img').attr('src');
							var img = '<img src="' + imgSource + '"/>';        
							$(this).parents('.btn-group').find('.dropdown-toggle').html(img + ' ' + selText + ' <span class="caret"></span>');
						});
						
						</script>
			
			
			
						
					</div>
				</div>
				
				
				
				<div class="col-md-5">
					<div class="form-group">
						<label><?php echo $this->texts["question"];?></label>
						<input id="survey_question" name="survey_question" type="text" class="form-control border-input"  placeholder=""/>
					</div>
				</div>
				
				
				<div class="col-md-3">
					<div class="form-group no-display" id="answers_group">
						<label><?php echo $this->texts["possible_answers"];?></label>
						<textarea id="possible_answers" name="possible_answers" rows="7" type="text" class="form-control border-input"></textarea>
					</div>
				</div>
				
				<div class="col-md-1">
					<div class="form-group">
						<br/>
						<a class="btn btn-default btn-wd" href="javascript:AddQuestion()"><?php echo $this->texts["add"];?></a>
					</div>
				</div>
			
			</div>
			
				
			<div class="clearfix"></div>
			<br/>
			<br/>
			<hr/>
			<br/>
			
			<button type="submit" class="btn btn-primary btn-fill btn-wd"><?php echo $this->texts["save_survey"];?></button>
		
			<div class="clearfix"></div>
			<br/>
			</form>
		</div>
	</div>
<?php
}
?>

					
	