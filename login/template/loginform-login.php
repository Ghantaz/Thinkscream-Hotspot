<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta http-equiv="CACHE-CONTROL" CONTENT="NO-CACHE">	
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<title>Minimal Form Interface</title>
		<meta name="description" content="Minimal Form Interface: Simplistic, single input view form" />
		<meta name="keywords" content="form, minimal, interface, single input, big form, responsive form, transition" />
		<meta name="author" content="Codrops" />
		<link rel="shortcut icon" href="../favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/normalize.css" />
		<!-- <link rel="stylesheet" type="text/css" href="css/demo.css" /> -->
		<link rel="stylesheet" type="text/css" href="css/component.css" />
		<script src="js/modernizr.custom.js"></script>
		<script src="js/jquery.min.js"></script>

<script>
	$(document).ready(function(){
		$("#theForm").submit(function(e){
			e.preventDefault();
    	//$("button").click(function(){
        var posting = $.post("smsotp.php",
        {
          pass: $("#q1").val(),
          challenge: $("#q3").val()
	//$( ".final-message" ).html( data );
	//alert("Data: " + data + "\nStatus: " + status);
        });
        posting.done(function(data){
		var rec = $(data);
		$("#response").html(data);
	    });
    });
});
</script>		

	</head>


	<body>
		<div>
			<!-- Top Navigation -->
<!--			<header class="codrops-header">
				<h1>Meru WiFi Hotspot</span></h1>	
			</header> -->
			<section style="background: #eeefef">

<!-- <img src="images/admin-portal.png" width="98%"> -->
				<form id="theForm" class="simform" autocomplete="off">
					<div>


								<span><label for="q1">Please enter the password displayed on screen:</label></span>

								<input id="q1" name="q1" type="text"/>
						 <input type="hidden" id="q3" name="q3" value="<?php echo $challenge; ?>"/> 
						 
						<button type="submit">Send</button>
					</div><!-- /simform-inner -->
					<div id="response"></div>
					<span class="final-message"></span>
				</form><!-- /simform -->			
			</section>	
		</div><!-- /container -->
	<!--	<script src="js/classie.js"></script> 
		<script src="js/stepsForm.js"></script> -->
<!--		<script>
			var theForm = document.getElementById( 'theForm' );

			new stepsForm( theForm, {
				onSubmit : function( form ) {
					// hide form
					//classie.addClass( theForm.querySelector( '.simform-inner' ), 'hide' );

					/*
					form.submit()
					or
					AJAX request (maybe show loading indicator while we don't have an answer..)
					*/

					// let's just simulate something...
					var messageEl = theForm.querySelector( '.final-message' );
					messageEl.innerHTML = 'You are now officially connected to the Interwebz!';
					//classie.addClass( messageEl, 'show' );
				}
			} );
		</script> -->
	</body>
</html>
