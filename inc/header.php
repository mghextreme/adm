<?php
	session_start();
	include('protect.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Área administrativa | E2</title>
		<link rel="shortcut icon" href="imgs/icon.ico">
		<link href="css/style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
		
		<!-- Fancybox Start -->
		<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
		<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
		<!-- Fancybox End -->
		
		<!-- Validation Engine Start -->
		<script type="text/javascript" src="js/validator/jquery.validationEngine-pt.js"></script>
		<script type="text/javascript" src="js/validator/jquery.validationEngine.js"></script>
		<script type="text/javascript" src="js/validator/finalized.js"></script>
		
		<link href="js/validator/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
		<!-- Validation Engine End -->
		
		<script type="text/javascript">
			$(document).ready(function() {
				$("a.fancybox").fancybox({
					'overlayShow'			: true,
					'overlayOpacity'		: 0.75,
					'transitionIn'			: 'fade',
					'transitionOut'			: 'fade',
					'titleShow'				: false,
					'titlePosition'			: 'outside',
					'width'					: '90%',
					'height'				: '90%',
					'showCloseButton'		: true,
					'showNavArrows'			: false,
					'enableEscapeButton'	: true,
					'type'					: 'iframe',
					'centerOnScroll'		: true
				});
				$("a.fancyboxAlbum").fancybox({
					'overlayShow'			: true,
					'overlayOpacity'		: 0.75,
					'transitionIn'			: 'fade',
					'transitionOut'			: 'fade',
					'titleShow'				: false,
					'titlePosition'			: 'outside',
					'width'					: 900,
					'height'				: '90%',
					'showCloseButton'		: true,
					'showNavArrows'			: false,
					'enableEscapeButton'	: true,
					'type'					: 'iframe',
					'centerOnScroll'		: true
				});
			});
		</script>
	</head>