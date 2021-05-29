<?php
	include('protect.php');
	if (isset($_GET['menu']))
	{
		$page = $_GET['menu'];

		if(pageAccess($page))
		{
			connectDatabase();
			
			$sql = "SELECT type FROM " . $menu_db . " WHERE link=\"" . $page . "\"";
			$query = mysql_query($sql);
			
			$rows = mysql_fetch_array($query);
			
			switch ($rows['type'])
			{
				case 'registration':
					echo '<div id="registrationInc">';
					if (isset($_GET['submenu']))
					{
						switch ($_GET['submenu'])
						{
							case 'show':
								include('registration/show.php');
								break;
							case 'add':
								include('registration/add.php');
								break;
							case 'remove':
								include('registration/remove.php');
								break;
							case 'ok':
								include('registration/ok.php');
								break;
							case 'edit':
								include('registration/edit.php');
								break;
							case 'order':
								include('registration/order.php');
								break;
						}
					}
					else include('registration/show.php');
					echo '</div>';
					break;
					
				case 'singleregistration':
					echo '<div id="registrationInc">';
					include('registration/single_edit.php');
					echo '</div>';
					break;
					
				case 'logout':
				case 'home':
				case 'page':
				case 'html':
					include("html/" . $page . ".php");
					break;
					
				case 'manager':
					echo '<div id="managerInc">';
					if (isset($_GET['submenu']))
					{
						switch ($_GET['submenu'])
						{
							case 'show':
								include('manager/show.php');
								break;
							case 'edit':
								include('manager/edit.php');
								break;
							case 'config':
								include('manager/config.php');
								break;
							case 'menu':
								include('manager/menu.php');
								break;
							case 'add':
								include('manager/add.php');
								break;
							case 'order':
								include('manager/order.php');
								break;
							case 'files':
								include('manager/files.php');
								break;
						}
					}
					else include('manager/show.php');
					echo '</div>';
					break;
					
				case 'categories':
					include('categories/categories.php');
					break;
					
				case 'banners':
					if (isset($_GET['submenu']))
					{
						switch ($_GET['submenu'])
						{
							case 'show':
								include('banners/show.php');
								break;
							case 'edit':
								include('banners/edit.php');
								break;
						}
					}
					else include('banners/show.php');
					break;
			}
		}
		else include("fail.php");
	}
	else include("html/home.php");
?>