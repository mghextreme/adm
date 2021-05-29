<?php
	$title = $_POST['title'];
	$id = $_POST['category'];
	
	if (isset($_POST['subcategory']))
		$subcat = $_POST['subcategory'];
	else $subcat[] = NULL;
	
	if (isset($_POST['subcategoryid']))
		$subid = $_POST['subcategoryid'];
	else $subid = NULL;
	
	$sql = "";
	
	die('Updated!');
?>