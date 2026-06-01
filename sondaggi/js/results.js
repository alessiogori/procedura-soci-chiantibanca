var current_view="list-view";
var listing_item = [];
function change_view(new_view)
{
	if(current_view==new_view)
	{
		return false;
	}
	else 
	{
		if(listing_item.length==0)
		{
			$(".search-result").each(function() 
			{
				listing_item.push({
					link: $(this).find(".result-details-link").attr("href"),
					image: $(this).find(".img-res").attr("src"),
					title: $(this).find(".search-result-title").html(),
					description: $(this).find(".description").html(),
					price: $(this).find(".listing-price").html(),
					featured: $(this).find(".is_r_featured").html()
					
				});
			});
		}
		var view_html = "";
		if(new_view == "grid-view") 
		{
			current_view="grid-view";
			document.cookie="result=grid-view";
			$("#list-view").removeClass("current");
			$("#grid-view").addClass("current");
				
			$(listing_item).each(function(k, data) 
			{
				
				view_html +='<div class="col-md-4 col-sm-6 col-xs-12">';
				view_html +='<div class="grid-result">';
				view_html +='<div class="my-wrap">';
				view_html +='<a href="'+data["link"]+'" class="btn-block">';
				view_html +='<img alt="'+data["title"]+'" src="'+data["image"]+'" />';
				view_html +='<div class="entry-overlay">';
				view_html +=data["title"];
				if(data["price"].trim()!="")
				view_html +="<br/>"+data["price"];
				view_html +='</div></a>';
				view_html +='</div>';
				view_html +='</div>';
				if(data["featured"]==".")
				{
					view_html +='<img src="images/v_sign.png" class="tick-featured abs-16"/>';
				}
			view_html +='</div>';
			});
			
			
			
		}
		else 
		{
			current_view="list-view";
		
			document.cookie="result=list-view";
			$("#grid-view").removeClass("current");
			$("#list-view").addClass("current");
			
			$(listing_item).each(function(k, data) 
			{
				view_html +='<div class="panel panel-default search-result">';
				view_html +='<div class="panel-heading '+(data["featured"]=="."?'featured-heading':'')+'">';
				view_html +='<h3 class="panel-title">';
				
				view_html +='<a href="'+data["link"]+'" class="search-result-title '+(data["featured"]=="."?'featured-r-title':'')+'">'+data["title"]+'</a>';
				view_html +='</h3>';
				if(data["featured"]==".")
				{
					view_html +='<img src="images/v_sign.png" class="tick-featured"/>';
				}
				
				view_html +='</div>';
				view_html +='<div class="panel-body">';
				view_html +='<div class="row">';
				view_html +='<div class="col-sm-5 col-xs-12">';
				view_html +='<a href="'+data["link"]+'" class="btn-block result-details-link"><img class="img-responsive img-res" src="'+data["image"]+'" /></a>';
				view_html +='</div>';
				view_html +='<div class="col-sm-7 col-xs-12">';
				view_html +='<div class="details">';
				view_html +='<div class="amenities">';
				view_html +='</div>';
				view_html +='<p class="description">';
				view_html +=data["description"];
				view_html +='<br/><br/>'+data["price"]+'';
				
				view_html +='</p>';
				view_html +='</div>';
				view_html +='</div>';
				view_html +='</div>';
				view_html +='<div class="row">';
				view_html +='<div class="col-xs-6">';
						
				view_html +='</div>';
				view_html +='<div class="col-xs-6">';
				view_html +='<div class="text-right">';
				view_html +='<a href="'+data["link"]+'" class="btn btn-primary">Details</a>';
				view_html +='</div>';
				view_html +='</div>';
				view_html +='</div>';
				view_html +='</div>';
				view_html +='</div>';
			});
		}
		
		$(".results-container").hide();
		$(".results-container").html("");
		$(".results-container").html(view_html);
		$(".results-container").fadeIn("fast");
		
		
		if(current_view=="grid-view")
		{
			$(".grid-result").on("mouseenter mouseleave", "a", function(e) 
			{
			
				if (e.type === "mouseenter") {
					$(this).children(".entry-overlay").fadeIn(10);
				}
				else {
					$(this).children(".entry-overlay").fadeOut(10);
				}
			});
			}
		}

}

function r_cookie(cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) 
	  {
	  var c = ca[i].trim();
	  if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	  }
	return "";
}
$(document).ready(function() {
	
	
	
	
	
	
	
	$(".view-select-tabs a").click(function() {
		new_view = $(this).attr("id");
		change_view(new_view);
		return false;	
	});
	r_type=r_cookie("result");
	
	
	if(r_type=="grid-view")
	{
		change_view("grid-view");
	}
	
});

