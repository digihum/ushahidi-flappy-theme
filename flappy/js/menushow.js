$(document).ready(
	function(){
		$("#menushow")
			.on("click", function(){
					if($("#mainmenu").is(":visible")){
						$("#mainmenu")
							.slideUp("100");
					}else{
						$("#mainmenu")
							.slideDown("100");
					}
				}
			);
		$("#how-to-report-menu")
			.on("click", function(){
					if($("#how-to-report-box").is(":visible")){
						$("#how-to-report-box")
							.slideUp("100");
					}else{
						$("#how-to-report-box")
							.slideDown("100");
					}
				}
			);
	}
);	