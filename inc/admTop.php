<?php include("configuration.php");
	include("functions.php");
	include('protect.php'); ?>
<div id="admTop">
	<div id="admTopCenter">
		<div id="admTopLogo"></div>
		<div id="admTopBox">
			<img id="admTopInfoIcon" src="imgs/info_icon.png" />
			<img id="admTopDateIcon" src="imgs/calendar_icon.png" />
			<div id="admTopInfo">
				<b>Área Administrativa</b><br/>
				<?php echo $website_name; ?>
			</div>
			
			<div id="admTopDate">
			<?php
				$time = GetTime();
				$weekday = $time['weekday'];
				$day = $time['day'];
				$month = $time['month'];
				
				switch ($time['weekday'])
				{
					case "Sun":
						$weekday = "Domingo";
						break;
					case "Mon":
						$weekday = "Segunda";
						break;
					case "Tue":
						$weekday = "Ter&ccedil;a";
						break;
					case "Wed":
						$weekday = "Quarta";
						break;
					case "Thu":
						$weekday = "Quinta";
						break;
					case "Fri":
						$weekday = "Sexta";
						break;
					case "Sat":
						$weekday = "S&aacute;bado";
						break;
				}
				
				if ($day < 10)
				{
					$day = substr($day, 1, 1);
					if($day == 1)
					{ $day .= "º"; }
				}
				
				switch ($month)
				{
					case 1:
						$month = "Janeiro";
						break;
					case 2:
						$month = "Fevereiro";
						break;
					case 3:
						$month = "Março";
						break;
					case 4:
						$month = "Abril";
						break;
					case 5:
						$month = "Maio";
						break;
					case 6:
						$month = "Junho";
						break;
					case 7:
						$month = "Julho";
						break;
					case 8:
						$month = "Agosto";
						break;
					case 9:
						$month = "Setembro";
						break;
					case 10:
						$month = "Outubro";
						break;
					case 11:
						$month = "Novembro";
						break;
					case 12:
						$month = "Dezembro";
						break;
				}
				
				echo $weekday . ",<br />" . $day . " de " . $month . " de " . $time['year'];
			?>
			</div>
		</div>
	</div>
</div>
<img src="imgs/top_shadow.png" />
