<?php
include('../configuration.php');
if (!empty($_FILES))
{
	foreach ($_FILES['file']['tmp_name'] as $key => $tempFile)
	{
		//$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_GET['folder'] . '/';
		$targetPath = $absolute_path . 'uploads//';
		$newFileName = strtolower(date(y) . date(m) . date(d) . date(H) . date(i) . date(s) .  "_" . $_FILES['file']['name'][$key]);
		$oldValues = array(' ','&','+','?','=','á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç');
		$newValues = array('-','-','-','-','-','a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c');
		$newFileName = str_replace($oldValues, $newValues, $newFileName);
		//$newFileName = $_GET['name'].'_'.(($_GET['location'] != '')?$_GET['location'].'_':'').$_FILES['file']['name'];
		$targetFile =  str_replace('//', '/', $targetPath) . $newFileName;
		
		// Uncomment the following line if you want to make the directory if it doesn't exist
		mkdir(str_replace('//','/',$targetPath), 777, true);
		
		move_uploaded_file($tempFile, $targetFile);

		$extension = strtolower(substr($_FILES['file']['name'][$key], strripos($_FILES['file']['name'][$key], '.') + 1));
		include("../functions.php");
		connectDatabase();
		$sql = "INSERT INTO `temp`(`name`, `link`, `extension`) VALUES ('" . $_FILES['file']['name'][$key] . "', 'uploads/" . $newFileName . "', '" . $extension . "')";
		mysql_query($sql);
	}
}

if ($newFileName)
	echo $newFileName;
else
	echo '1';
?>