 <?php
	/*
	Variáveis
	
	$db = Nome da tabela no banco de dados
	$id = ID do item na tabela
	$orderStart = Número em que o grupo começa, por exemplo, para começar pelo 10º item, usar valor 9
	$many = Quantidade de itens
	$attr = Atributo do item
	
	retorna $variavel([numeroDoItem])['atributo']([numeroDaImagem]['atributoDaImagem'])
	
	Obs: função GetValuesSingle($db, $id) e GetFirst($db) não retorna múltiplos itens, portanto, não necessita de [numeroDoItem]
	*/
	
	include('inc/functions.php');
	connectDatabase();
	
	//Não deletar, usar ou modificar
	function GetAlbum($albumID)
	{
		$sql = "SELECT * FROM `albuns` WHERE `id`='" . $albumID . "' ORDER BY `order`,`id` ASC";
		$query = mysql_query($sql);
		
		$subvalues = NULL;
		for($j = 0; $rows = mysql_fetch_assoc($query); $j++)
		{
			if ($rows['cover'] == 1)
			{ $subvalues['cover'] = $rows; }
			
			$subvalues[$j] = $rows;
		}
		
		if (!isset($subvalues['cover']) && isset($subvalues[0]))
		{ $subvalues['cover'] = $subvalues[0]; }
		
		return $subvalues;
	}
	
	function GetUserValues($id)
	{
		if (!empty($id)) {
			$sql = "SELECT `name`, `login` FROM `users` WHERE `id`=" . $id;
			$rows = mysql_fetch_assoc(mysql_query($sql));
			return $rows;
		} else return array('name'=>'Empresadois','login'=>'e2');
	}
	
	function GetDateValues($value)
	{
		$n1 = '00'; $n2 = '00'; $n3 = '00'; $n4 = '00'; $n5 = '00'; $n6 = '00';
		$date = strpos($value, '-') != FALSE ? TRUE : FALSE;
		$time = strpos($value, ':') != FALSE ? TRUE : FALSE;
		$offset = 0;
		$ans = NULL;
		
		if ($date)
		{
			$n1 = substr($value, 0, 4); //Year
			$n2 = substr($value, 5, 2); //Month
			$n3 = substr($value, 8, 2); //Day
			$offset = 11;
		}
		if ($time)
		{
			$n4 = substr($value, $offset, 2); //Hour
			$n5 = substr($value, $offset + 3, 2); //Minute
			$n6 = substr($value, $offset + 6, 2); //Second
		}
		
		if ($date)
		{
			$mkt = mktime($n4, $n5, $n6, $n2, $n3, $n1);
			$ans['sql'] = date('Y-m-d H:i:s', $mkt);
			$ans['year'] = date('Y', $mkt);
			$ans['month'] = date('m', $mkt);
			$ans['mkt'] = $mkt;
			switch($ans['month'])
			{
				case 1: $ans['monthname'] = 'jan'; break;
				case 2: $ans['monthname'] = 'fev'; break;
				case 3: $ans['monthname'] = 'mar'; break;
				case 4: $ans['monthname'] = 'abr'; break;
				case 5: $ans['monthname'] = 'mai'; break;
				case 6: $ans['monthname'] = 'jun'; break;
				case 7: $ans['monthname'] = 'jul'; break;
				case 8: $ans['monthname'] = 'ago'; break;
				case 9: $ans['monthname'] = 'set'; break;
				case 10: $ans['monthname'] = 'out'; break;
				case 11: $ans['monthname'] = 'nov'; break;
				case 12: $ans['monthname'] = 'dez'; break;
			}
			$ans['day'] = date('d', $mkt);
			$ans['weekday'] = date('D', $mkt);
		}
		if ($time)
		{
			$ans['hours'] = $n4;
			$ans['minutes'] = $n5;
			$ans['seconds'] = $n6;
		}
		
		return $ans;
	}
	
	function GetValuesSingle($db, $id)
	{
		$values = NULL;
		$idspec = $id == -1 ? '' : ' WHERE `id`=' . $id;
		
		$sqliv = "SELECT * FROM `{$db}`" . $idspec;
		$itemvalues = mysql_fetch_assoc(mysql_query($sqliv));
		
		$sql = "SELECT * FROM `" . $db . "fields`";
		$query = mysql_query($sql);
		
		while ($rows = mysql_fetch_assoc($query))
		{
			switch($rows['type'])
			{
				case 'multiple':
					$sql2 = "SELECT `value` FROM `{$db}{$rows['id']}`" . $idspec;
					$query2 = mysql_query($sql2);
					$values[$rows['columnname']] = NULL;
					while($rows2 = mysql_fetch_array($query2))
					{
						switch($rows['value'])
						{
							case 'date':
							case 'time':
							case 'datetime':
								$values[$rows['columnname']][] = GetDateValues($rows2['value']);
								break;
							case 'string':
							case 'text':
							case 'youtube':
							case 'url':
								$values[$rows['columnname']][] = $rows2['value'];
								break;
							default:
								if (substr($rows['value'], 0, 5) == 'table') {
									$table = substr($rows['value'], 6);
									$values[$rows['columnname']][] = GetValuesSingle($table, $rows2['value']);
								}
								break;
								break;
						}
					}
					break;
				case 'image':
				case 'files':
					if ($itemvalues[$rows['columnname']] != NULL)
						$values[$rows['columnname']] = GetAlbum($itemvalues[$rows['columnname']]);
					else $values[$rows['columnname']] = NULL;
					break;
				case 'date':
				case 'time':
				case 'datetime':
					$values[$rows['columnname']] = GetDateValues($itemvalues[$rows['columnname']]);
					break;
				case 'options':
					$sql3 = "SELECT `value`, `number` FROM `multipleoptions` WHERE `number`={$itemvalues[$rows['columnname']]} && `id`={$rows['value']}";
					$rows3 = mysql_fetch_array(mysql_query($sql3));
					$values[$rows['columnname']] = $rows3;
					break;
				case 'category';
					if ($itemvalues[$rows['columnname']] != NULL)
					{
						$divPos = strpos($itemvalues[$rows['columnname']], '-');
						if ($divPos > 0)
						{
							$catID = substr($itemvalues[$rows['columnname']], 0, $divPos);
							$subID = substr($itemvalues[$rows['columnname']], $divPos + 1, strlen($itemvalues[$rows['columnname']]) - $divPos - 1);
						}
						else
						{
							$catID = $itemvalues[$rows['columnname']];
							$subID = NULL;
						}
	
						$sql3 = "SELECT * FROM `categories` WHERE `id`=" . $catID;
						$rows3 = mysql_fetch_array(mysql_query($sql3));
						
						if ($subID != NULL)
						{
							$sql4 = "SELECT * FROM `subcategories` WHERE `categoryid`=" . $catID . " && `subcategoryid`=" . $subID;
							$rows4 = mysql_fetch_array(mysql_query($sql4));
						}
						else $rows4 = NULL;
						
						$values[$rows['columnname']]['cat'] = $rows3;
						$values[$rows['columnname']]['sub'] = $rows4;
					}
					else
					{
						$values[$rows['columnname']]['cat'] = NULL;
						$values[$rows['columnname']]['sub'] = NULL;
					}
					break;
				case 'table':
					if (!empty($rows['value']) && !empty($itemvalues[$rows['columnname']])){
						$values[$rows['columnname']] = GetValuesSingle($rows['value'], $itemvalues[$rows['columnname']]);
					} else $values[$rows['columnname']] = NULL;
					break;
				default:
					$values[$rows['columnname']] = $itemvalues[$rows['columnname']];
					break;
			}
		}
		
		$sql = 'SELECT ' . ($id == -1 ? '' : '`order`, ') . "`creationuser`, `modificationuser`, `creationdate`, `modificationdate` FROM `{$db}`" . $idspec;
		$rows = mysql_fetch_assoc(mysql_query($sql));
		
		$values['id'] = $id;
		if ($id != -1) { $values['order'] = $rows['order']; }
		$values['creationuser'] = GetUserValues($rows['creationuser']);
		$values['modificationuser'] = GetUserValues($rows['modificationuser']);;
		$values['creationdate'] = GetDateValues($rows['creationdate']);
		$values['modificationdate'] = GetDateValues($rows['modificationdate']);
		
		return $values;
	}
	
	function GetValuesGroup($db, $orderStart, $many)
	{
		$values = NULL;
		if (($orderStart + $many) > countItems($db))
			$orderEnd = countItems($db) - 1;
		else $orderEnd = $orderStart + $many - 1;
		
		$j = 0;
		for ($i = $orderStart; $i <= $orderEnd; $i++)
		{
			$sql = "SELECT `id` FROM " . $db . " WHERE `order`=" . $i;
			$rows = mysql_fetch_array(mysql_query($sql));
			$values[$j] = GetValuesSingle($db, $rows['id']);
			$j++;
		}
		return $values;
	}
	
	function GetValuesRandom($db, $many)
	{
		$count = countItems($db);
		$numbs = NULL;
		$many = $many > $count ? $count : $many;
		for ($i = 0; $i < $many; $i++)
		{
			$ok = false;
			while(!$ok)
			{
				$random = rand(0, $count - 1);
				$ok = true;
				if ($i != 0)
				{
					foreach($numbs as $n)
					{
						$ok = $n == $random ? false : true;
					}
				}
			}
			
			$numbs[$i] = $random;
		}
		
		$values = NULL;
		for ($j = 0; $j < $many; $j++)
		{
			$sql = "SELECT `id` FROM " . $db . " WHERE `order`=" . $numbs[$j];
			$rows = mysql_fetch_array(mysql_query($sql));
			$values[$j] = GetValuesSingle($db, $rows['id']);
		}
		return $values;
	}
	
	function GetValuesAll($db)
	{
		include('inc/configuration.php');
		$sql = "SELECT `type` FROM `{$menu_db}` WHERE `link`='{$db}'";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		$values = NULL;
		if ($rows['type'] == 'registration')
		{
			for ($i = 0; $i < countItems($db); $i++)
			{
				$sql = "SELECT `id` FROM `" . $db . "` WHERE `order`=" . $i;
				$rows = mysql_fetch_array(mysql_query($sql));
				$values[$i] = GetValuesSingle($db, $rows['id']);
			}
		}
		else $values = GetValuesSingle($db, -1);
		return $values;
	}
	
	function GetFirst($db)
	{
		include('inc/configuration.php');
		$values = NULL;
		
		$sql = "SELECT `type` FROM `{$menu_db}` WHERE `link`='{$db}'";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		$idspec = $rows['type'] == 'singleregistration' ? '' : ' WHERE `order`=0';
		
		$sql = "SELECT `id` FROM `{$db}`" . $idspec;
		$rows = mysql_fetch_array(mysql_query($sql));
		return GetValuesSingle($db, $rows['id']);
	}
	
	function GetAttributeSingle($db, $id, $attr)
	{
		$values = NULL;
		$idspec = $id < 0 ? '' : ' WHERE `id`=' . $id;
		$sql = "SELECT * FROM `{$db}fields` WHERE `columnname`='{$attr}'";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		
		$sqliv = "SELECT `{$attr}` FROM `{$db}`" . $idspec;
		$itemvalues = mysql_fetch_assoc(mysql_query($sqliv));
		
		switch($rows['type'])
		{
			case 'multiple':
				$sql2 = "SELECT `value` FROM `{$db}{$rows['id']}`{$idspec} ORDER BY `id` ASC";
				$query2 = mysql_query($sql2);
				while($rows2 = mysql_fetch_array($query2))
				{
					switch($rows['value'])
					{
						case 'date':
						case 'time':
						case 'datetime':
							$values[] = GetDateValues($rows2['value']);
							break;
						case 'string':
						case 'text':
						case 'youtube':
						case 'url':
							$values[] = $rows2['value'];
							break;
						default:
							if (substr($rows['value'], 0, 5) == 'table')
							{
								$table = substr($rows['value'], 6);
								$sql3 = "SELECT `name` FROM `{$table}` WHERE `id`={$rows2['value']}";
								$rows3 = mysql_fetch_assoc(mysql_query($sql3));
								$values[] = $rows3['name'];
							}
							break;
					}
				}
				break;
			case 'image':
			case 'files':
				if ($itemvalues[$rows['columnname']] != NULL)
					$values = GetAlbum($itemvalues[$rows['columnname']]);
				else $values = NULL;
				break;
			case 'date':
			case 'time':
			case 'datetime':
				$values = GetDateValues($itemvalues[$rows['columnname']]);
				break;
			case 'options':
				$sql3 = "SELECT `value`, `number` FROM `multipleoptions` WHERE `id`=" . $itemvalues[$rows['columnname']];
				$rows3 = mysql_fetch_array(mysql_query($sql3));
				$values = $rows3;
				break;
			case 'category';
				if ($itemvalues[$rows['columnname']] != NULL)
				{
					$divPos = strpos($itemvalues[$rows['columnname']], '-');
					if ($divPos > 0)
					{
						$catID = substr($itemvalues[$rows['columnname']], 0, $divPos - 1);
						$subID = substr($itemvalues[$rows['columnname']], $divPos + 1, strlen($itemvalues[$rows['columnname']]) - $divPos - 1);
					}
					else
					{
						$catID = $itemvalues[$rows['columnname']];
						$subID = NULL;
					}

					$sql3 = "SELECT * FROM `categories` WHERE `id`=" . $catID;
					$rows3 = mysql_fetch_array(mysql_query($sql3));
					
					if ($subID != NULL)
					{
						$sql4 = "SELECT * FROM `subcategories` WHERE `categoryid`=" . $catID . " && `subcategoryid`=" . $subID;
						$rows4 = mysql_fetch_array(mysql_query($sql4));
					}
					else $rows4 = NULL;
					
					$values['cat'] = $rows3;
					$values['sub'] = $rows4;
				}
				else
				{
					$values['cat'] = NULL;
					$values['sub'] = NULL;
				}
				break;
			case 'table':
				$values = GetValuesSingle($rows['value'], $itemvalues[$rows['columnname']]);
				break;
			default:
				$values = $itemvalues[$rows['columnname']];
				break;
		}
		return $values;
	}
	
	function GetAttributeGroup($db, $orderStart, $many, $attr)
	{
		$values = NULL;
		if (($orderStart + $many) > countItems($db))
			$orderEnd = countItems($db) - 1;
		else $orderEnd = $orderStart + $many - 1;
		
		for ($i = $orderStart; $i <= $orderEnd; $i++)
		{
			$sql = "SELECT `id` FROM " . $db . " WHERE `order`=" . $i;
			$rows = mysql_fetch_array(mysql_query($sql));
			$values[$i] = GetAttributeSingle($db, $rows['id'], $attr);
		}
		return $values;
	}
	
	function GetAttributeAll($db, $attr)
	{
		include('inc/configuration.php');
		$values = NULL;
		$sql = "SELECT `type` FROM `{$menu_db}` WHERE `link`='{$db}'";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		if ($rows['type'] == 'singleregistration')
		{ $values = GetAttributeSingle($db, -1, $attr); } else {
			for ($i = 0; $i < countItems($db); $i++)
			{
				$sql = "SELECT `id` FROM `{$db}`' WHERE `order`={$i}";
				$rows = mysql_fetch_array(mysql_query($sql));
				$values[$i]['id'] = $rows['id'];
				$values[$i][$attr] = GetAttributeSingle($db, $rows['id'], $attr);
			}
		}
		return $values;
	}
	
	function GetRandomImagesFromDatabase($db, $attr, $many)
	{
		$values = NULL;
		$sql = "SELECT `id`, `{$attr}` FROM `{$db}` WHERE `{$attr}`>=0 LIMIT {$many}";
		$query = mysql_query($sql);
		
		for ($i = 0; $rows = mysql_fetch_assoc($query); $i++)
		{
			$sql2 = "SELECT * FROM `albuns` WHERE `id`={$rows[$attr]}";
			$query2 = mysql_query($sql2);
			
			$cover = FALSE;
			while($rows2 = mysql_fetch_assoc($query2))
			{
				if (!$cover)
				{
					$image = $rows2;
					if ($rows2['cover'] == 1)
					{ $cover = TRUE; }
				}
			}
			
			$values[$i]['id'] = $rows['id'];
			$values[$i][$attr] = $image;
		}
		
		return $values;
	}
	
	function CountDatabaseItems($db)
	{
		$sql = "SELECT `id` FROM `" . $db . "`";
		$query = mysql_query($sql);
		
		$count = 0;
		while($rows = mysql_fetch_assoc($query))
		{ $count++; }
		
		return $count;
	}
	
	function GetBanners()
	{
		$sql = "SELECT * FROM `banners` ORDER BY `order` ASC";
		$query = mysql_query($sql);
		
		$subvalues = NULL;
		for($j = 0; $rows = mysql_fetch_assoc($query); $j++)
		{ $subvalues[$j] = $rows; }
		
		return $subvalues;
	}
	
	//Não deletar, usar ou modificar
	function GetWordsAndSentences($root)
	{
		if (substr_count($root, ' ') > 0)
		{
			while (substr($root, 0, 1) == ' ')
			{ $root = substr($root, 1); }
			while (strpos($root, strlen($root) - 1, 1) == ' ')
			{ $root = substr($root, 0, strlen($root) - 1); }
			$spaces = NULL;
			for ($index = 0; strpos($root, ' ', $index) > 0; $index = $spaces[count($spaces) - 1] + 1)
			{ $spaces[] = strpos($root, ' ', $index); }
			for ($words = count($spaces) + 1; $words > 0; $words--)
			{
				for ($number = 0; $number < count($spaces) - $words + 2; $number++)
				{
					if ($number == 0)
					{ $tempWord = $words == count($spaces) + 1 ? $root : substr($root, 0, $spaces[$words - 1]); }
					else if ($number == count($spaces))
					{ $tempWord = substr($root, $spaces[$number - 1] + 1, strlen($root) - $spaces[$number - 1]); }
					else $tempWord = $words + $number == count($spaces) + 1 ? substr($root, $spaces[$number - 1] + 1, strlen($root) - $spaces[$number - 1]) : substr($root, $spaces[$number - 1] + 1, $spaces[$number + $words - 1] - $spaces[$number - 1] - 1);
					if (strlen($tempWord) > 2) { $final['a' . $words][] = $tempWord; }
				}
			}
		}
		else $final['a1'][] = $root;
		return $final;
	}
	
	function GetSearch($db, $search)
	{
		$sql = "SELECT `type` FROM `menu` WHERE `database`='{$db}'";
		$rows = mysql_fetch_assoc(mysql_query($sql));
		if ($rows['type'] == 'registration' && !empty($search))
		{
			//Inicializar Variáveis
			$srcQ = NULL; //Palavras e sentenças entre aspas
			$src = NULL; //Palavras e sentenças fora de aspas
			$quotes = NULL;
			$result = NULL;
			$values = NULL;
			
			//Adaptar $search recebido P>p; á>a
			$search = str_replace(array('!','@','#','$','%','¨','&','*','(',')','[',']','{','}','^','.',',','<','>',':',';','~','´','`','+','=','-','º','ª','¹','²','³',"'",'?','\\','|','/','_'), ' ', strtolower($search));
			$search = str_replace(array('á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç'), array('a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c'), $search);
			while(substr_count($search,'  ')){$search = str_replace('  ',' ',$search);}
			
			//Interpretar valores dados pelo usuário
			if (substr_count($search, '"') > 0)
			{
				for ($index = 0; strpos($search, '"', $index) > -1; $index = $quotes[count($quotes) - 1] + 1)
				{ $quotes[] = strpos($search, '"', $index); }
				if (count($quotes) % 2 == 0 && count($quotes) > 1)
				{
					for ($index = 0; $index + 1 < count($quotes); $index += 2)
					{
						$word = substr($search, $quotes[$index] + 1, $quotes[$index + 1] - $quotes[$index] - 1);
						if (strlen($word) > 0)
						{
							$get = GetWordsAndSentences($word);
							$srcQ = $srcQ == NULL ? $get : array_merge_recursive($srcQ, $get);
						}
					}
				}
				else
				{
					$quotes = NULL;
					$search = str_replace('"', '', $search);
				}
			}
			
			if ($quotes != NULL)
			{
				for ($index = 0; $index <= count($quotes); $index += 2)
				{
					$word = $index == 0 ? ($quotes[0] > 0 ? substr($search, 0, $quotes[0] - 1) : '') : ($index == count($quotes) ? substr($search, $quotes[$index - 1] + 1) : substr($search, $quotes[$index - 1] + 1, $quotes[$index] - $quotes[$index - 1] - 2));
					if (strlen($word) > 0)
					{
						$get = GetWordsAndSentences($word);
						$src = $src == NULL ? $get : array_merge_recursive($src, $get);
					}
				}
			}
			else $src = GetWordsAndSentences($search);
			
			$fields = getFieldsTypes($db);
			foreach ($fields as $f)
			{
				switch ($f['type'])
				{
					case 'string':
						for ($i = 1; $i <= count($srcQ); $i++)
						{
							for ($j = 0; $j < count($srcQ['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}` LIKE '%" . ($srcQ['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($result[$row['id']]))
										$result[$row['id']] += $i * $i * 12;
									else $result[$row['id']] = $i * $i * 12;
								}
							}
						}
						for ($i = 1; $i <= count($src); $i++)
						{
							for ($j = 0; $j < count($src['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}` LIKE '%" . ($src['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($result[$row['id']]))
										$result[$row['id']] += $i * 12;
									else $result[$row['id']] = $i * 12;
								}
							}
						}
						break;
					case 'text':
						for ($i = 1; $i <= count($srcQ); $i++)
						{
							for ($j = 0; $j < count($srcQ['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}` LIKE '%" . ($srcQ['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($result[$row['id']]))
										$result[$row['id']] += $i * $i * 8;
									else $result[$row['id']] = $i * $i * 8;
								}
							}
						}
						for ($i = 1; $i <= count($src); $i++)
						{
							for ($j = 0; $j < count($src['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}` LIKE '%" . ($src['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($result[$row['id']]))
										$result[$row['id']] += $i * 8;
									else $result[$row['id']] = $i * 8;
								}
							}
						}
						break;
					case 'multiple':
						$sql2 = "SELECT `value` FROM `{$db}{$f['id']}` ORDER BY `id` ASC";
						$query2 = mysql_query($sql2);
						while($rows2 = mysql_fetch_array($query2))
						{
							switch($f['value'])
							{
								case 'string':
								case 'text':
								case 'url':
									for ($i = 1; $i <= count($srcQ); $i++)
									{
										for ($j = 0; $j < count($srcQ['a' . $i]); $j++)
										{
											$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}` LIKE '%" . ($srcQ['a' . $i][$j]) . "%'");
											while ($row = mysql_fetch_assoc($query))
											{
												if (isset($result[$row['id']]))
													$result[$row['id']] += $i * $i * 4;
												else $result[$row['id']] = $i * $i * 4;
											}
										}
									}
									for ($i = 1; $i <= count($src); $i++)
									{
										for ($j = 0; $j < count($src['a' . $i]); $j++)
										{
											$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}` LIKE '%" . ($src['a' . $i][$j]) . "%'");
											while ($row = mysql_fetch_assoc($query))
											{
												if (isset($result[$row['id']]))
													$result[$row['id']] += $i * 4;
												else $result[$row['id']] = $i * 4;
											}
										}
									}
									break;
								default:
									if (substr($f['value'], 0, 5) == 'table') {
										$table = substr($f['value'], 6);
										//Search in the table
										$Tresult = NULL;
										for ($i = 1; $i <= count($srcQ); $i++)
										{
											for ($j = 0; $j < count($srcQ['a' . $i]); $j++)
											{
												$minl = GetIdsSearch($table, $srcQ['a' . $i][$j]);
												if (count($minl) > 0) { foreach ($minl as $minl2) {
													if (isset($Tresult[$minl2]))
														$Tresult[$minl2] += $i * $i;
													else $Tresult[$minl2] = $i * $i;
												} }
											}
										}
										for ($i = 1; $i <= count($src); $i++)
										{
											for ($j = 0; $j < count($src['a' . $i]); $j++)
											{
												$minl = GetIdsSearch($table, $src['a' . $i][$j]);
												if (count($minl) > 0) { foreach ($minl as $minl2) {
													if (isset($Tresult[$minl2]))
														$Tresult[$minl2] += $i;
													else $Tresult[$minl2] = $i;
												} }
											}
										}
										if (count($Tresult) > 0)
										{
											$Tind = array_keys($Tresult);
											for ($i = 0; $i < count($Tind); $i++) {
												$query = mysql_query("SELECT `id` FROM `{$db}{$f['id']}` WHERE `value`='{$Tind[$i]}'");
												while ($row = mysql_fetch_assoc($query))
												{
													if (isset($result[$row['id']]))
														$result[$row['id']] += $Tresult[$Tind[$i]] * 3;
													else $result[$row['id']] = $Tresult[$Tind[$i]] * 3;
												}
											}
										}
									}
									break;
							}
						}
						break;
					case 'table';
						$Tresult = NULL;
						for ($i = 1; $i <= count($srcQ); $i++)
						{
							for ($j = 0; $j < count($srcQ['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `id` FROM `{$f['value']}` WHERE `name` LIKE '%" . ($srcQ['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($Tresult[$row['id']]))
										$Tresult[$row['id']] += $i * $i;
									else $Tresult[$row['id']] = $i * $i;
								}
							}
						}
						for ($i = 1; $i <= count($src); $i++)
						{
							for ($j = 0; $j < count($src['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `id` FROM `{$f['value']}` WHERE `name` LIKE '%" . ($src['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($Tresult[$row['id']]))
										$Tresult[$row['id']] += $i;
									else $Tresult[$row['id']] = $i;
								}
							}
						}
						if (count($Tresult) > 0)
						{
							$Tind = array_keys($Tresult);
							for ($i = 0; $i < count($Tind); $i++)
							{
								$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}`='{$Tind[$i]}'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($result[$row['id']]))
										$result[$row['id']] += $Tresult[$Tind[$i]] * 3;
									else $result[$row['id']] = $Tresult[$Tind[$i]] * 3;
								}
							}
						}
						break;
					case 'category';
						//Category
						$Cresult = NULL;
						for ($i = 1; $i <= count($srcQ); $i++)
						{
							for ($j = 0; $j < count($srcQ['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `id` FROM `categories` WHERE `name` LIKE '%" . ($srcQ['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($Cresult[$row['id']]))
										$Cresult[$row['id']] += $i * $i;
									else $Cresult[$row['id']] = $i * $i;
								}
							}
						}
						for ($i = 1; $i <= count($src); $i++)
						{
							for ($j = 0; $j < count($src['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `id` FROM `categories` WHERE `name` LIKE '%" . ($src['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($Cresult[$row['id']]))
										$Cresult[$row['id']] += $i;
									else $Cresult[$row['id']] = $i;
								}
							}
						}
						if (count($Cresult) > 0)
						{
							$Cind = array_keys($Cresult);
							for ($i = 0; $i < count($Cind); $i++)
							{
								$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}`='{$Cind[$i]}' || `{$f['columnname']}` LIKE '{$Cind[$i]}-%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($result[$row['id']]))
										$result[$row['id']] += $Cresult[$Cind[$i]] * 9;
									else $result[$row['id']] = $Cresult[$Cind[$i]] * 9;
								}
							}
						}
						//Subcategory
						$Sresult = NULL;
						for ($i = 1; $i <= count($srcQ); $i++)
						{
							for ($j = 0; $j < count($srcQ['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `categoryid`,`subcategoryid` FROM `subcategories` WHERE `name` LIKE '%" . ($srcQ['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($Sresult[$row['categoryid'] . '-' . $row['subcategoryid']]))
										$Sresult[$row['categoryid'] . '-' . $row['subcategoryid']] += $i * $i;
									else $Sresult[$row['categoryid'] . '-' . $row['subcategoryid']] = $i * $i;
								}
							}
						}
						for ($i = 1; $i <= count($src); $i++)
						{
							for ($j = 0; $j < count($src['a' . $i]); $j++)
							{
								$query = mysql_query("SELECT `categoryid`,`subcategoryid` FROM `subcategories` WHERE `name` LIKE '%" . ($src['a' . $i][$j]) . "%'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($Sresult[$row['categoryid'] . '-' . $row['subcategoryid']]))
										$Sresult[$row['categoryid'] . '-' . $row['subcategoryid']] += $i;
									else $Sresult[$row['categoryid'] . '-' . $row['subcategoryid']] = $i;
								}
							}
						}
						if (count($Sresult) > 0)
						{
							$Sind = array_keys($Sresult);
							for ($i = 0; $i < count($Sind); $i++)
							{
								$query = mysql_query("SELECT `id` FROM `{$db}` WHERE `{$f['columnname']}`='{$Sind[$i]}'");
								while ($row = mysql_fetch_assoc($query))
								{
									if (isset($result[$row['id']]))
										$result[$row['id']] += $Sresult[$Sind[$i]] * 11;
									else $result[$row['id']] = $Sresult[$Sind[$i]] * 11;
								}
							}
						}
						break;
				}
			}
			if (count($result) > 0)
			{
				$keys = array_keys($result);
				for ($i = 0; $i + 1 < count($keys); $i++)
				{
					for ($j = $i + 1; $j < count($keys); $j++)
					{
						if ($result[$keys[$j]] > $result[$keys[$i]])
						{
							$temp = $keys[$j];
							$keys[$j] = $keys[$i];
							$keys[$i] = $temp;
						}
					}
				}
				for ($i = 0; $i < count($keys); $i++)
				{ $values[] = GetValuesSingle($db ,$keys[$i]); }
				return $values;
			}
			else return NULL;
		}
		else return GetValuesAll($db);
	}

	function GetIdsSearch($db, $term) {
		$sql = "SELECT `id` FROM `{$db}` WHERE";
		$fields = getFieldsTypes($db);
		for ($i = 0; $i < count($fields); $i++) {
			if ($i != 0) { $sql .= ' ||'; }
			$sql .= " `{$fields[$i]['columnname']}` LIKE '%{$term}%'";
		}
		$query = mysql_query($sql);
		$res = NULL;
		while ($row = mysql_fetch_assoc($query))
		{ $res[] = $row['id']; }
		return $res;
	}
	
	function GetCategories() {
		$categories = NULL;
		$subcateg = mysql_fetch_assoc(mysql_query("SELECT `subcategories` FROM `config`"));
		$query = mysql_query("SELECT * FROM `categories` ORDER BY `id` ASC");
		while ($row = mysql_fetch_assoc($query)) {
			if ($subcateg['subcategories'] == 1) {
				$query2 = mysql_query("SELECT * FROM `subcategories` WHERE `categoryid`='{$row['id']}' ORDER BY `subcategoryid` ASC");
				if (mysql_num_rows($query2) > 0) {
					while ($row2 = mysql_fetch_assoc($query2)) {
						$row['subcategories'][] = $row2;
					}
				} else $row['subcategories'] = NULL;
			}
			$categories[] = $row;
		}
		return $categories;
	}
	
	function namelink($str) {
		$str = strtolower($str);
		
		$from = array('Á','Â','À','Ä','Ã','á','â','à','ä','ã','ª','ç','Ç','É','Ê','È','Ë','é','ê','è','ë','Í','Ì','Î','Ï','í','ì','î','ï','Ó','Ô','Ò','Ö','Õ','ó','ô','ò','ö','õ','º','Ú','Û','Ù','Ü','ú','û','ù','ü','/','\\',':','=','&','!','%','#','@','*','"',"'",',',' ');
		$to  =  array('a','a','a','a','a','a','a','a','a','a','a','c','c','e','e','e','e','e','e','e','e','i','i','i','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','-', '-','-','-','-','-','-','-','-','-','-','-','-','-');
		
		$str = str_replace($from, $to, $str);
		$str = str_replace('--', '-', $str);
		
		return $str;
	}
	
	function cleanString($str){ return str_replace(array("'",'"'),'',$str); }
?>