function process_map_buttons(){
	if($("#panel").children().length==0){
		setTimeout(process_map_buttons, 500);
	}else{
		$("#panel")
			.children()
			.first()
			.html("Move Map");
		$("	#panel")
			.children()
			.first()
			.next()
			.html("Add Place");
		$("	#panel")
			.children()
			.first()
			.next()
			.next()
			.html("Draw Line");
		$("	#panel")
			.children()
			.last()
			.html("Draw Area");
	}
}

$(document).ready(
	function(){
		setTimeout(process_map_buttons, 500)	
	}
);	