<?php
	function connectDatabase ()
	{
		include ("configuration.php");
		$conn = mysql_connect($db_host, $db_user, $db_pass);
		
		if (!$conn)
			die ('Não foi possível conectar: ' . mysql_error());

		$sel = mysql_select_db($db_name, $conn);
		
		if (!$sel)
			die ('Banco inexistente: ' . mysql_error());

		mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
	}
	
	function GetTime()
	{
		$time = NULL;
		$mkt = mktime(date('H') - 5, date('i'), date('s'), date('m'), date('d'), date('Y'));

		$time['year'] = date('Y', $mkt);
		$time['month'] = date('m', $mkt);
		$time['day'] = date('d', $mkt);
		$time['hours'] = date('H', $mkt);
		$time['minutes'] = date('i', $mkt);
		$time['seconds'] = date('s', $mkt);
		$time['weekday'] = date('D', $mkt);
		$time['sql'] = date('Y-m-d H:i:s', $mkt);
		$time['mkt'] = $mkt;

		return $time;
	}
	
	function pageAccess($page)
	{
		include ("configuration.php");
		
		$sql = "SELECT `isadmin` FROM `" . $users_db . "` WHERE `login`='" . $_SESSION['usere2'] . "'";
		$rows = mysql_fetch_array(mysql_query($sql));
		
		$adminUser = $rows[0] > 0 ? true : false;
		
		$adminPage = false;
		$pageExists = true;
		if ($page != 'logout' && $page != 'home')
		{
			$sql2 = "SELECT `isadmin`, `type` FROM `" . $menu_db . "` WHERE `link`='" . $page . "'";
			$query2 = mysql_query($sql2);

			$rows2Count = mysql_num_rows($query2);
			$rows2 = mysql_fetch_assoc($query2);
			
			if ($rows2Count > 0)
			{
				$pageExists = true;
				
				if ($rows2['isadmin'] == 1)
					$adminPage = true;
			}
			else $pageExists = false;
			
			if ($page == 'categories')
			{
				$sql2 = "SELECT `categories` FROM `config`";
				$query2 = mysql_query($sql2);
				$result = mysql_fetch_assoc($query2);
				if ($result['categories'] > 0)
					$pageExists = true;
				else $pageExists = false;
			}
			else if ($page == 'banners')
			{
				$sql2 = "SELECT `banners` FROM `config`";
				$query2 = mysql_query($sql2);
				$result = mysql_fetch_assoc($query2);
				if ($result['banners'] > 0)
					$pageExists = true;
				else $pageExists = false;
			}
		}
		
		$final = ($pageExists && ((!$adminUser && !$adminPage) || $adminUser)) ? true : false;
		
		return $final;
	}

	function getDatabase($name)
	{
		include('configuration.php');
		
		$sql = "SELECT `database` FROM `" . $menu_db . "` WHERE `link`='" . $name . "'";
		$rows = mysql_fetch_array(mysql_query($sql));
		
		return $rows['database'];
	}
	
	function getFieldsArray($db)
	{
		$columns = NULL;
		$i = 0;
		$sql = "SELECT `columnname` FROM `" . $db . "fields`";
		$query = mysql_query($sql);
		while($rows = mysql_fetch_array($query))
		{
			$columns[$i] = $rows['columnname'];
			$i++;
		}

		return $columns;
	}
	
	function getFieldsTypes($db)
	{
		$fields = NULL;
		$i = 0;
		$sql = "SELECT * FROM `" . $db . "fields` ORDER BY `order` ASC";
		$query = mysql_query($sql);
		while($rows = mysql_fetch_array($query))
		{
			$fields[$i] = $rows;
			$i++;
		}

		return $fields;
	}
	
	function getMultipleOptions($id)
	{
		$sql = "SELECT `value`, `number` FROM `multipleoptions` WHERE `id`=" . $id . " ORDER BY `value` ASC";
		$query = mysql_query($sql);
		$i = 0;
		$options = NULL;
		while($rows = mysql_fetch_array($query))
		{
			$options[$i] = $rows;
			$i++;
		}
		return $options;
	}
	
	function getItemsArray($pagelink, $search, $pagenumber, $maxPageItems)
	{
		$db = getDatabase($pagelink);
		
		if ($search != '')
		{
			$sql = "SELECT `name`, `id` FROM `" . $db . "` WHERE ";
			
			$fields = getFieldsArray($db);
			
			for ($i = 0; $i < count($fields); $i++)
			{
				if ($i > 0)
					$sql .= ' || ';
				
				$sql .= "`" . $fields[$i] . "`" . " LIKE '%" . $search . "%'";
			}
		}
		else $sql = "SELECT `name`, `id` FROM " . $db;
		
		$sql .= " ORDER BY `order` ASC";
		$query = mysql_query($sql);
		
		if ($query != FALSE)
		{
			$iStart = 0;
			if (mysql_num_rows($query) > $maxPageItems && $maxPageItems > 0)
				$iStart = $maxPageItems * ($pagenumber - 1);
			
			$i = 0;
			$j = 0;
			while($rows = mysql_fetch_array($query))
			{
				if ($maxPageItems <= 0)
				{
					$names[$j] = $rows['name'];
					$ids[$j] = $rows['id'];
					$j++;
				}
				else if ($i >= $iStart && $i < $iStart + $maxPageItems)
				{
					$names[$j] = $rows['name'];
					$ids[$j] = $rows['id'];
					$j++;
				}
				$i++;
			}
			
			if ($maxPageItems > 0)
			{
				$pages = intval($i / $maxPageItems);
				if ($i % $maxPageItems != 0)
					$pages++;
			}
			else $pages = 0;
		}
		
		if (isset($names))
			return array($names, $ids, $pages);
		else return array(0, 0, 0);
	}

	function addPager($actPage, $numPages, $link)
	{
		if ($numPages > 1)
		{
			$pageStart = 1;
			$pageEnd = $numPages;
			if ($numPages > 9)
			{
				if ($actPage > 4 && $actPage < $numPages - 4)
				{
					$pageStart = $actPage - 4;
					$pageEnd = $actPage + 4;
				}
				else
				{
					if ($actPage < 4)
					{
						$pageStart = 1;
						$pageEnd = 9;
					}
					else if ($actPage > $numPages - 4)
					{
						$pageStart = $numPages - 8;
						$pageEnd = $numPages;
					}
				}
			}
			
			$width = (($pageEnd - $pageStart + 3) * 22) + 4;
			
			$optStyle = 'padding-left: 0;';
			if ($actPage == 1)
				$optStyle = 'padding-left: 44px;';
			
			echo "<ul id=\"pager\" style=\"width: " . $width . "px;" . $optStyle . "\">\n";
			
			if ($actPage > 1)
				echo "<a href=\"" . $link . "&page=" . ($actPage - 1) . "\"><li>&#60;</li></a>\n";
			for ($i = $pageStart; $i <= $pageEnd; $i++)
			{
				$class = '';
				if ($i == $actPage)
					$class = ' class="selected"';
				
				echo "<a href=\"" . $link . "&page=" . $i . "\"><li" . $class .">" . $i . "</li></a>\n";
			}
			if ($actPage < $numPages)
				echo "<a href=\"" . $link . "&page=" . ($actPage + 1) . "\"><li>&#62;</li></a>\n";
			
			echo "</ul>";
		}
	}

	function getValues($db, $id = -1)
	{
		include('configuration.php');
		$sql = "SELECT `type` FROM `{$menu_db}` WHERE `database`='{$db}'";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		$values = NULL;
		if ($rows['type'] == 'singleregistration')
		{
			$sql = "SELECT * FROM `{$db}`";
			$values = mysql_fetch_assoc(mysql_query($sql));
		}
		else
		{
			if ($id > -1)
			{
				$sql = "SELECT * FROM `{$db}` WHERE `id`={$id}";
				$values = mysql_fetch_assoc(mysql_query($sql));
			}
			else
			{
				$fields = getFieldsArray($db);
				foreach($fields as $fld)
				{ $values[$fld] = NULL; }
			}
		}
		return $values;
	}
	
	function generateClass($required, $type)
	{
		$class = '';
		if (strpos($type, ' extensions[') !== false)
		{
			$classname = 'Arquivos ';
			$minpos = strpos($type, ' extensions[') + 12;
			$minendpos = strpos(substr($type, $minpos), ']');
			
			$value = substr($type, $minpos, $minendpos);
			
			$st = 0;
			$commas = NULL;
			for ($i = 0; strpos($value, ',', $st) !== false; $i++)
			{
				$commas[$i] = strpos($value, ',', $st);
				$st = $commas[$i] + 1;
			}
			
			for ($j = 0; $j <= count($commas); $j++)
			{
				if ($j == 0)
				{
					if (count($commas) == 0)
					{
						$class[0] = $value;
					}
					else
					{
						$class[0] = substr($value, 0, $commas[0]);
					}
					$classname .= '.' . $class[0];
				}
				else if ($j == count($commas))
				{
					$class[$j] = substr($value, $commas[$j - 1] + 1);
					$classname .= ' e .' . $class[$j];
				}
				else
				{
					$class[$j] = substr($value, $commas[$j - 1] + 1, $commas[$j] - $commas[$j - 1] - 1);
					$classname .=  ', ' . '.' . $class[$j];
				}
			}
			
			$class['name'] = $classname;
			
			if (substr($type, $minpos + $minendpos + 1, 1) == '[')
			{
				$minendpos2 = strpos($type, ']', $minpos + $minendpos + 1);
				$class['size'] = intval(substr($type, $minpos + $minendpos + 2, $minendpos2 - $minpos - $minendpos - 2));
			}
		}
		else if ($required != 0 || $type != NULL)
		{
			$class .= 'validate[';
			if ($required == 1)
				$class .= 'required';
			
			if (strpos($type, ' letternumber ') !== false)
			{
				if ($class != 'validate[')
					$class .= ',';
				
				$class .= 'custom[onlyLetterNumber]';
			}
			
			if (strpos($type, ' e-mail ') !== false)
			{
				if ($class != 'validate[')
					$class .= ',';
				
				$class .= 'custom[email]';
			}
			
			if (strpos($type, ' integer ') !== false)
			{
				if ($class != 'validate[')
					$class .= ',';
				
				$class .= ' custom[onlyNumber] ';
			}
			
			if (strpos($type, ' number ') !== false)
			{
				if ($class != 'validate[')
					$class .= ',';
				
				$class .= ' custom[number] ';
			}
			
			if (strpos($type, ' letter ') !== false)
			{
				if ($class != 'validate[')
					$class .= ',';
				
				$class .= ' custom[onlyLetterSp] ';
			}
			
			if (strpos($type, ' min[') !== false)
			{
				if ($class != 'validate[')
					$class .= ',';
				
				$minpos = strpos($type, ' min[') + 5;
				$minendpos = strpos(substr($type, $minpos), ']');
				
				$value = substr($type, $minpos, $minendpos);
				
				$class .= 'minSize[' . $value . ']';
			}
			
			if (strpos($type, ' max[') !== false)
			{
				if ($class != 'validate[')
					$class .= ',';
				
				$maxpos = strpos($type, ' max[') + 5;
				$maxendpos = strpos(substr($type, $maxpos), ']');
				
				$value = substr($type, $maxpos, $maxendpos);
				
				$class .= 'maxSize[' . $value . ']';
			}
			
			$class .= ']';
			
			if (strpos($type, ' radio ') !== false)
			{
				$class .= 'radio';
			}
		}
		return $class;
	}

	function addForm($page, $id, $single = FALSE)
	{
		$new = $single ? FALSE : ($id >= 0 ? FALSE : TRUE);
		
		include('configuration.php');
		echo '<script src="inc/formFunc.js" type="text/javascript" charset="utf-8"></script>' . "\n";
		echo '<script src="js/dropzone.min.js" type="text/javascript" charset="utf-8"></script>' . "\n";
		if ($single)
		{ echo '<form name="theForm" id="theForm" method="post" class="theForm" action="javascript: singlefinalized(' . "'" . $page . "','" . $website_link . "'" . ');">'; }
		else if ($new)
		{ echo '<form name="theForm" id="theForm" method="post" class="theForm" action="javascript: addfinalized(' . "'" . $page . "','" . $website_link . "'" . ');">'; }
		else echo '<form name="theForm" id="theForm" method="post" class="theForm" action="javascript: finalized(' . "'" . $page . "','" . $website_link . "'" . ');">';
		
		$db = getDatabase($page);
		$fields = getFieldsTypes($db);
		$values = $single ? getValues($db) : getValues($db, $id);
		
		if (!$new && !$single)
			echo '<input type="hidden" name="id" value="' . $id . '">';
		
		for ($i = 0; $i < count($fields); $i++)
		{
			echo '<h1>' . $fields[$i]['name'] . '</h1>';
			
			if ($fields[$i]['type'] != 'multiple')
				$itemValue = $values[$fields[$i]['columnname']];
			
			$class = generateClass($fields[$i]['required'], $fields[$i]['requiredtype']);
			
			switch ($fields[$i]['type'])
			{
				case 'url':
					echo '<input class="stringField ' . $class . '" id="' . $i . '" type="text" name="' . $fields[$i]['columnname'] . "\" value=\"" . $itemValue ."\">";
					break;
				case 'string':
					echo '<input class="stringField ' . $class . '" id="' . $i . '" type="text" name="' . $fields[$i]['columnname'] . "\" value=\"" . $itemValue ."\">";
					break;
				case 'text':
					echo '<textarea class="textField ' . $class . '" id="' . $i . '" name="' . $fields[$i]['columnname'] . '">' . $itemValue . '</textarea>';
					break;
				case 'youtube':
					if ($itemValue != NULL && $itemValue != '')
						$youtubeValue = 'http://www.youtube.com/watch?v=' . $itemValue;
					else $youtubeValue = '';
					echo '<input class="shortStringField ' . $class . '" id="' . $i . '" type="text" name="' . $fields[$i]['columnname'] . "\" value=\"" . $youtubeValue ."\">";
					echo '<input title="Visualizar" class="youtubeButton" type="button" onclick="openYoutube(' . $i . ')" />';
					break;
				case 'options':
					echo '<select id="options' . $i . '" name="' . $fields[$i]['columnname'] . '" class="optionsSelect ' . $class . '">' . "\n";
					
					if ($fields[$i]['required'] == 0)
					{ echo "<option value=\"NULL\">Nenhuma opção</option>"; }
					
					$options = getMultipleOptions($fields[$i]['value']);
					foreach ($options as $opt)
					{
						$selected = $opt['number'] == $itemValue ? ' selected' : '';
						echo "<option value=\"" . $opt['number'] . "\"{$selected}>" . $opt['value'] . "</option>\n";
					}
					echo "</select>\n";
					break;
				case 'image':
					// addMultipleUploader($extArray, $description, $listItem, $db, $itemID, $fileSizeLimit)
					addMultipleUploader('images', 'Arquivos de Imagem', $i, $db, $id, 2560);
?>
<input id="<?=$db.$i;?>" type="hidden" name="<?=$fields[$i]['columnname'];?>" value="<?=$itemValue;?>" />
<?php
					break;
				case 'files':
					if (isset($class['size']))
						$size = $class['size'];
					else $size = 2048;
					// addMultipleUploader($extArray, $description, $listItem, $db, $itemID, $fileSizeLimit)
					addMultipleUploader($class, $class['name'], $i, $db, $id, $size);
?>
<input id="<?=$db.$i;?>" type="hidden" name="<?=$fields[$i]['columnname'];?>" value="<?=$itemValue;?>" />
<?php
					break;
				case 'password':
					echo '<input class="stringField ' . $class . '" type="password" id="password" name="' . $fields[$i]['columnname'] . "\" value=\"\">";
					echo '<h1>Repetir Senha</h1>';
					if ($new != 'true')
						echo '<input class="stringField validate[equals[password]]" type="password" id="' . $i . "' value=\"\">";
					else echo '<input class="stringField validate[required,equals[password]]" type="password" id="' . $i . "\" value=\"\">";
					break;
				case 'category':
					$sql = "SELECT * FROM `config`";
					$rows = mysql_fetch_assoc(mysql_query($sql));
					
					$dValue = strpos($itemValue, '-');
					if ($dValue < 1)
					{
						$catValue = $itemValue;
						$subValue = -1;
					}
					else
					{
						$catValue = substr($itemValue, 0, $dValue);
						$subValue = substr($itemValue, ($dValue + 1), (strlen($itemValue) - $dValue - 1));
					}
					
					if ($rows['categories'] > 0)
					{
						if ($rows['subcategories'] > 0)
						{
							echo '<script type="text/javascript">' . "\n";
							echo 'function ChangeSubs' . $i . '()' . "\n{\n";
							echo "var value = \$('select#cat" . $i . "').find('option:selected').val();\n";
							echo "\$('select#sub" . $i . "').find('option').remove();\n";
							echo "\$.post('inc/registration/load_subcategories.php', { category: value }, function(result){\n";
							echo "\$('select#sub" . $i . "').append(result);\n});\n}\n</script>\n";
							echo '<select id="cat' . $i . '" class="catSelect" name="' . $fields[$i]['columnname'] . '" onchange="ChangeSubs' . $i . '();" >';
						}
						else echo '<select id="cat' . $i . '" class="catSelect" name="' . $fields[$i]['columnname'] . '">';
						
						if ($fields[$i]['required'] == 0)
						{ echo '<option value="z"></option>'; }
						
						$sql2 = "SELECT * FROM `categories`";
						$query2 = mysql_query($sql2);
						
						for ($k = 0; $rows2 = mysql_fetch_assoc($query2); $k++)
						{
							$select = $catValue == $rows2['id'] ? ' selected' : '';
							echo '<option value="' . $rows2['id'] . '"' . $select . '>' . $rows2['name'] . '</option>';
						}
						
						echo '</select>';
						
						if ($rows['subcategories'] > 0)
						{
							echo '<select id="sub' . $i . '" class="subSelect" name="' . $fields[$i]['columnname'] . "_sub\">";
							echo '<option value="z"></option>';
							if ($catValue != NULL)
							{
								$sql2 = "SELECT * FROM `subcategories` WHERE `categoryid`=" . $catValue;
								$query2 = mysql_query($sql2);
								
								if (mysql_num_rows($query2) > 0)
								{
									while ($rows2 = mysql_fetch_assoc($query2))
									{
										$select = $subValue == $rows2['subcategoryid'] ? ' selected' : '';
										echo '<option value="' . $rows2['subcategoryid'] . '"' . $select . '>' . $rows2['name'] . '</option>';
									}
									
								}
							}
							echo '</select>';
						}
						echo '<div class="clearfix"></div>';
					}
					break;
				case 'table':
					echo '<select id="table' . $i . '" name="' . $fields[$i]['columnname'] . '" class="tableSelect ' . $class . '">';
					
					if ($fields[$i]['required'] == 0)
					{ echo "<option value=\"NULL\">Nenhuma opção</option>"; }
					
					$sql2 = "SELECT `id`, `name` FROM `" . $fields[$i]['value'] . "` ORDER BY `name` ASC";
					$query2 = mysql_query($sql2);
					while ($rows2 = mysql_fetch_assoc($query2))
					{
						$selected = $rows2['id'] == $itemValue ? ' selected' : '';
						echo "<option value=\"" . $rows2['id'] . "\"{$selected}>" . $rows2['name'] . "</option>";
					}
					
					echo '</select>';
					break;
				case 'time':
					$timeH = ''; $timeM = ''; $timeS = '';
					if ($itemValue != NULL && strlen($itemValue) > 6)
					{
						$timeH = substr($itemValue, 0, 2);
						$timeM = substr($itemValue, 3, 2);
						$timeS = substr($itemValue, 6, 2);
					}
					echo '<input type="text" id="timeh' . $i . '" class="timehour validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[0],max[23]]" name="' . $fields[$i]['columnname'] . '_hour" maxlength="2" value="' . $timeH . '" /> horas ' . "\n";
					echo '<input type="text" id="timem' . $i . '" class="timemin validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[0],max[59]]" name="' . $fields[$i]['columnname'] . '_min" maxlength="2" value="' . $timeM . '" /> minutos ' . "\n";
					echo '<input type="text" id="times' . $i . '" class="timesec validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[0],max[59]]" name="' . $fields[$i]['columnname'] . '_sec" maxlength="2" value="' . $timeS . '" /> segundos ' . "\n";
					break;
				case 'date':
					$dateY = ''; $dateM = ''; $dateD = '';
					$months = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
					if ($itemValue != NULL && strlen($itemValue) > 8)
					{
						$dateY = substr($itemValue, 0, 4);
						$dateM = intval(substr($itemValue, 5, 2));
						$dateD = substr($itemValue, 8, 2);
					}
					echo '<input type="text" id="dated' . $i . '" class="dateday validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[1],max[31]]" name="' . $fields[$i]['columnname'] . '_day" maxlength="2" value="' . $dateD . '" /> de ' . "\n";
					echo '<select id="datem' . $i . '" class="datemonth' . ($fields[$i]['required'] == 1 ? ' validate[required]' : '') . '" name="' . $fields[$i]['columnname'] . '_month">';
					echo "<option value=\"NULL\">---Selecione---</option>\n";
					for($j = 1; $j <= 12; $j++)
					{
						$sel = $j == $dateM ? ' selected' : '';
						echo "<option value=\"{$j}\"{$sel}>{$months[$j - 1]}</option>\n";
					}
					echo '</select> de ' . "\n";
					echo '<input type="text" id="datey' . $i . '" class="dateyear validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[1900],max[3000]]" name="' . $fields[$i]['columnname'] . '_year" maxlength="4" value="' . $dateY . '" />' . "\n";
					break;
				case 'datetime':
					$dateY = ''; $dateM = ''; $dateD = ''; $timeH = ''; $timeM = ''; $timeS = '';
					$months = array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
					if ($itemValue != NULL && strlen($itemValue) > 8)
					{
						$dateY = substr($itemValue, 0, 4);
						$dateM = intval(substr($itemValue, 5, 2));
						$dateD = substr($itemValue, 8, 2);
						$timeH = substr($itemValue, 11, 2);
						$timeM = substr($itemValue, 14, 2);
						$timeS = substr($itemValue, 17, 2);
					}
					echo '<input type="text" id="dated' . $i . '" class="dateday validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[1],max[31]]" name="' . $fields[$i]['columnname'] . '_day" maxlength="2" value="' . $dateD . '" /> de ' . "\n";
					echo '<select id="datem' . $i . '" class="datemonth validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . '" name="' . $fields[$i]['columnname'] . '_month">';
					echo "<option value=\"NULL\">---Selecione---</option>\n";
					for($j = 1; $j <= 12; $j++)
					{
						$sel = $j == $dateM ? ' selected' : '';
						echo "<option value=\"{$j}\"{$sel}>{$months[$j - 1]}</option>\n";
					}
					echo '</select> de ' . "\n";
					echo '<input type="text" id="datey' . $i . '" class="dateyear validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[1900],max[3000]]" name="' . $fields[$i]['columnname'] . '_year" maxlength="4" value="' . $dateY . '" />' . "\n";
					echo ' <b>,</b> <input type="text" id="timeh' . $i . '" class="timehour validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[0],max[23]]" name="' . $fields[$i]['columnname'] . '_hour" maxlength="2" value="' . $timeH . '" /> h ' . "\n";
					echo '<input type="text" id="timem' . $i . '" class="timemin validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[0],max[59]]" name="' . $fields[$i]['columnname'] . '_min" maxlength="2" value="' . $timeM . '" /> min ' . "\n";
					echo '<input type="text" id="times' . $i . '" class="timesec validate[' . ($fields[$i]['required'] == 1 ? 'required,' : '') . 'custom[integer],min[0],max[59]]" name="' . $fields[$i]['columnname'] . '_sec" maxlength="2" value="' . $timeS . '" /> seg ' . "\n";
					break;
				case 'multiple':
					echo "<a id=\"multiplefield\" class=\"fancyboxAlbum\" href=\"inc/registration/multiple.php?db={$db}&order={$i}&itemid={$id}\"><img src=\"imgs/edit.jpg\" /></a>";
					break;
			}
		}
		
		echo '<div id="bottom"><a title="Concluir"><input class="submit" id="submit" type="submit" value="" /></a>
		<a href="index.php?menu=' . $page . '" title="Cancelar"><img src="imgs/remove.png" /></a></div>' . "\n";
		echo '</form>' . "\n\n";
	}
	
	function addMultipleUploader($extArray, $description, $listItem, $db, $itemID, $fileSizeLimit)
	{
		if ($extArray == 'images')
		{
			$fileExtensionsString = '.jpg,.jpeg,.bmp,.gif,.png';
		}
		else
		{
			$lessNumb = 1;
			if (isset($extArray['size']))
				$lessNumb = 2;
			for ($i = 0; $i < count($extArray) - $lessNumb; $i++)
			{
				$fileExtensionsString .= "," . $extArray[$i];
			}
		}

		$linkParams = 'db=' . $db . '&itemid=' . $itemID . '&order=' . $listItem;
?>
<div id="fileUpload<?=$listItem;?>" class="<?=$listItem;?>" style="cursor:pointer;">
	<img src="imgs/upload.jpg" alt="Upload" style="pointer-events:none;" />
</div>
<div id="editImages" style="margin-bottom:20px;">
	<a id="editExisting" class="fancyboxAlbum" href="inc/registration/album.php?<?=$linkParams;?>">
		<img src="inc/multiupload/edit_images.jpg" alt="" />
	</a>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#fileUpload<?=$listItem;?>').dropzone({
		url: 'inc/multiupload/upload_name.php',
		uploadMultiple: true,
		createImageThumbnails: false,
		maxFilesize: <?=$fileSizeLimit;?>,
		acceptedFiles: '<?=$fileExtensionsString;?>',
		queuecomplete: function(){
			$.get('inc/multiupload/move_temp.php', {
				db: '<?=$db;?>',
				id: <?=$itemID;?>,
				itemOrder: <?=$listItem;?>
			}, function(result){
				$('form#theForm').find('input#<?=$db;?><?=$listItem;?>').val(result);
			});
		}
	});
});
</script>
<?php
	}
		
	function getAlbumItems($albumID)
	{
		if ($albumID != NULL)
		{
			$sql = "SELECT * FROM `albuns` WHERE `id`='" . $albumID . "' ORDER BY `order` ASC";
			$query = mysql_query($sql);
			$i = 0;
			$links = NULL;
			while($rows = mysql_fetch_array($query))
			{
				$links[$i] = $rows;
				$i++;
			}
			return $links;
		}
		else return NULL;
	}
	
	function countItems($db)
	{
		$count = 0;
		$sql = "SELECT * FROM $db";
		$query = mysql_query($sql);
		if ($query != false)
		{
			while($rows = mysql_fetch_array($query))
				$count++;
		}
		return $count;
	}
	
	function setOrder($db, $numb, $iStart, $iEnd)
	{
		$count = countItems($db);
		if ($iEnd == 0)
			$iEnd = $count;
		
		if ($numb > 0)
		{
			for ($i = $iEnd; $i >= $iStart; $i--)
			{
				$sql = "UPDATE `" . $db . "` SET `order`='" . ($i+1) . "' WHERE `order`='" . $i . "'";
				mysql_query($sql);
			}
		}
		else
		{
			for ($i = $iStart; $i <= $iEnd; $i++)
			{
				$sql = "UPDATE `" . $db . "` SET `order`='" . ($i-1) . "' WHERE `order`='" . $i . "'";
				mysql_query($sql);
			}
		}
	}
?>