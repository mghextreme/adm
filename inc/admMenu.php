<?php include('protect.php'); ?>
<div id="sideMenu">
	<ul id="sideMenuList">
		<?php
			connectDatabase();
			
			$sql = "SELECT `name`, `type`, `link` FROM `" . $menu_db . "` ORDER BY `order` ASC";
			
			$query = mysql_query($sql);
			
			while ($rows = mysql_fetch_array($query, MYSQL_ASSOC))
			{
				if (pageAccess($rows['link']))
				{
					$href = 'index.php';
					$link = '';
					switch ($rows['type'])
					{
						case 'home':
							$link = 'home';
							break;
						case 'logout':
						case 'page':
						case 'html':
						case 'singleregistration':
						case 'registration':
						case 'manager':
						case 'categories':
						case 'banners':
							$link = $rows['link'];
							$href .= '?menu=' . $link;
							break;
						default:
							break;
					}
					
					$active = '';
					if (isset($_GET['menu']))
					{
						if ($_GET['menu'] == $link)
							$active = ' class="active"';
					}
					else if ($rows['name'] == "Home")
						$active = ' class="active"';
					
					echo "<li><a href=\"{$href}\"{$active}>{$rows['name']}</a></li>";
				}
			}
			
		?>
	</ul>
</div>