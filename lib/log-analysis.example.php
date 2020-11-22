<?php
//$DEBUG = true;
header('content-type: text/html; charset=utf-8');

if(isset($DEBUG)) {
	if($DEBUG) {
		error_reporting(E_ALL);
		ini_set('display_errors','On');
		echo "<h3 style='color:red'><span class='icon-warning-sign' style='padding-right:5px'></span>DEBUG_MODE<span class='icon-warning-sign' style='padding-left:5px'></h3>";
	}
}

if(isset($DEBUG)) {
	if($DEBUG) {
		$path = "floty/log_file_analysis_test";
	} else {
		$path = "log_file_analysis";
	}
} else {
	$path = "log_file_analysis";
}
$regex = '/[<>:&#"{}=?\']/';
error_reporting(E_ALL);
ini_set("display_errors", 1);
include $path."/config/upload_defs.php";

if(isset($_GET['ID'])) {

	$id = preg_replace('/[^abcdef0123456789]/','',$_GET['ID']);

	if(strlen($id)==40) {

		$log_file = $path."/upload/".$id.".pdb";

		include $path."/functions.php";

		if(file_exists($log_file)) {
?>

	<link rel="stylesheet" href="<?php echo $path ?>/config/format.css" type="text/css" media="all" />
	<!--<script type="text/javascript" src="<?php echo $path ?>/inc/jquery-1.8.2.js"></script>-->

<?php
		
		if($db = new PDO(DB,DB_USER,DB_PWD)) {
		
			$query = $db->prepare("SELECT TIMESTAMPDIFF(SECOND,Timestamp,CURRENT_TIMESTAMP()) as time_diff FROM log_file_analysis_upload_admin WHERE ID='".$id."'");
			$query->execute();
			if($row = $query->fetch(PDO::FETCH_ASSOC)) {
				$time_diff = max(0,SEC_COUNT_ID-$row['time_diff']);
				$time_diff_hour = floor($time_diff/3600);
				$time_diff_min = floor($time_diff/60)-$time_diff_hour*60;
				$time_diff_sec = floor($time_diff)-$time_diff_min*60-$time_diff_hour*3600;
			
				echo "<p style='text-align:right;font-style:italic'>This log file analysis is still valid for ".$time_diff_hour." h ".sprintf('%02d',$time_diff_min)." min ".sprintf('%02d',$time_diff_sec)." sec.</p>";
			}
		
		}

			// detect all games
		if($db = new PDO("sqlite:".$log_file)) {
			$query = $db->prepare("SELECT UniqueGameID FROM game");
			$query->execute();

			$uniqueGameIDArray = array();

?>

    <div style="width:100%;text-align:center">

		<!-- select game -->
		<form method="get">
			<input type="hidden" name="ID" value="<?php echo $id ?>">
			<select align=center class="game" name="UniqueGameID" size="1" onchange="javascript:this.form.submit()">
				<option value="" selected>Select a game</option>
				<?php while($row = $query->fetch(PDO::FETCH_ASSOC)) {
					if(is_numeric($row['UniqueGameID']) & $row['UniqueGameID']>0) {
						echo "<option value=\"".$row['UniqueGameID']."\" >Game ".$row['UniqueGameID']."</option>\n";
						$uniqueGameIDArray[] = $row['UniqueGameID'];
					}
				} ?>
			</select>
		</form>

		<?php
	if(isset($_GET['UniqueGameID'])) {
		if(is_numeric($_GET['UniqueGameID']) & $_GET['UniqueGameID']>0 & in_array($_GET['UniqueGameID'],$uniqueGameIDArray,true)) {

			$uniqueGameID = preg_replace('/[^0123456789]/','',$_GET['UniqueGameID']);

		?>

	<script>
		<!--
		var selected_player = 0;
		var stacked = false;
		
		jQuery( document ).ready(function() {
			resize_charts();
		  });
		
		jQuery( window ).resize(function() {
			resize_charts();
		  });
		
		
		function resize_charts() {
			if(stacked) hand_cash_stacked();
			else hand_cash_line();
			pot_size();
			var width = get_width();
			jQuery('#div_course_game').css('width',width);
			jQuery('div.element').css('max-width',width);
			if(width < 600) {
				jQuery('td.hand_name').css('white-space','normal');
				jQuery('td.player').css('white-space','normal');
				
				// hidden colums of played hands table
				th = document.getElementById("played_hands_count_head_1");
				if(th !== null)th.outerHTML = th.outerHTML.replace('colspan="2"','colspan="1"');
				th = document.getElementById("played_hands_count_head_2");
				if(th !== null)th.outerHTML = th.outerHTML.replace('colspan="2"','colspan="1"');
				th = document.getElementById("played_hands_count_head_3");
				if(th !== null)th.outerHTML = th.outerHTML.replace('colspan="2"','colspan="1"');
				th = document.getElementById("played_hands_count_head_4");
				if(th !== null)	th.outerHTML = th.outerHTML.replace('colspan="2"','colspan="1"');
				jQuery('td.played_hands_count_data').css('display','none');
				
			} else {
				jQuery('td.hand_name').css('white-space','nowrap');
				jQuery('td.player').css('white-space','nowrap');
				
				// display colums of played hands table
				th = document.getElementById("played_hands_count_head_1");
				if(th !== null)th.outerHTML = th.outerHTML.replace('colspan="1"','colspan="2"');
				th = document.getElementById("played_hands_count_head_2");
				if(th !== null)th.outerHTML = th.outerHTML.replace('colspan="1"','colspan="2"');
				th = document.getElementById("played_hands_count_head_3");
				if(th !== null) th.outerHTML = th.outerHTML.replace('colspan="1"','colspan="2"');
				th = document.getElementById("played_hands_count_head_4");
				if(th !== null)	th.outerHTML = th.outerHTML.replace('colspan="1"','colspan="2"');
				jQuery('td.played_hands_count_data').css('display','block');
				
			}
			if(width < 400) {
				jQuery('table.data').css('font-size','12px');
			} else {
				jQuery('table.data').css('font-size','14px');
			}
		}

		function get_width() {
			return parseInt(jQuery('div#print_area').width());
		}

		function hand_cash_line() {
			stacked = false;
			hand_cash_line_select_player(selected_player);
			jQuery('#hand_cash_line').css('backgroundImage','url(/log_file_analysis/pic/hand_cash_top.png)');
			jQuery('#hand_cash_stacked').css("backgroundImage", "url(/log_file_analysis/pic/hand_cash_top_inactive.png)");
		}

		function hand_cash_line_select_player(i) {
			if(!stacked) {
				var id = '<?php echo $id ?>';
				var uId = '<?php echo $uniqueGameID ?>';
				var src = '/log_file_analysis/charts/hand_cash.php?ID=';
				src += id+'&UniqueGameID='+uId+'&stacked=0&player='
				src += i+'&width='+get_width();
				jQuery('img[name="hand_cash"]').attr("src", src);
			}
		}

		function hand_cash_stacked() {
			stacked = true;
			var id = '<?php echo $id ?>';
			var uId = '<?php echo $uniqueGameID ?>';
			var src = '/log_file_analysis/charts/hand_cash.php?ID='+id+'&UniqueGameID='+uId+'&stacked=1&player=0&width='+get_width();
			jQuery('img[name="hand_cash"]').attr("src", src);
			jQuery('#hand_cash_line').css('backgroundImage','url(/log_file_analysis/pic/hand_cash_top_inactive.png)');
			jQuery('#hand_cash_stacked').css('backgroundImage','url(/log_file_analysis/pic/hand_cash_top.png)');
		}
		function pot_size() {
			var id = '<?php echo $id ?>';
			var uId = '<?php echo $uniqueGameID ?>';
			var src = '/log_file_analysis/charts/pot_size.php?ID='+id+'&UniqueGameID='+uId+'&width='+get_width();
			jQuery('img[name="pot_size"]').attr("src", src);
		}
		function expand_collapse() {
			if(jQuery('#div_expand_collapse').attr('class') == 'expand') {
				jQuery('#div_expand_collapse').attr('class','collapse');
				jQuery('#span_expand_collapse').remove();
				jQuery('#div_expand_collapse').prepend("<span id='span_expand_collapse'>collapse all<span class='icon-resize-small' style='font-size:14px;padding-left:10px;'></span></span>");
				show_all_table();
			} else {
				jQuery('#div_expand_collapse').attr('class','expand');
				jQuery('#span_expand_collapse').remove();
				jQuery('#div_expand_collapse').prepend("<span id='span_expand_collapse'>&nbsp;expand all<span class='icon-resize-full' style='font-size:14px;padding-left:10px;'></span></span>");
				hide_all_table();
			}
		}
		function toggle_table(id_table,id_icon) {
			if(jQuery('#'+id_table).css('visibility')=='hidden') {
				show_table(id_table,id_icon);
			} else {
				hide_table(id_table,id_icon);
			}
		}
		function show_table(id_table,id_icon) {
			jQuery('#'+id_table).css('visibility','visible');
			jQuery('#'+id_table).css('position','relative');
			jQuery('#'+id_icon).attr('class','icon-chevron-up');
			if(id_table=='table_course_game') {
				jQuery('img.line').css('visibility','visible');
			}
			if(id_table == 'table_highest_wins') {
				jQuery('#table_highest_wins_add').css('visibility','visible');
				jQuery('#table_highest_wins_add').css('position','relative');
			}
		}
		function hide_table(id_table,id_icon) {
			jQuery('#'+id_table).css('visibility','hidden');
			jQuery('#'+id_table).css('position','absolute');
			jQuery('#'+id_icon).attr('class','icon-chevron-down');
			if(id_table=='table_course_game') {
				jQuery('img.line').css('visibility','hidden');
			}
			if(id_table == 'table_highest_wins') {
				jQuery('#table_highest_wins_add').css('visibility','hidden');
				jQuery('#table_highest_wins_add').css('position','absolute');
			}
		}
		function show_all_table() {
			show_table('table_course_game','icon_course_game');
			show_table('table_played_hands','icon_played_hands');
			show_table('table_best_hands','icon_best_hands');
			show_table('table_most_wins','icon_most_wins');
			show_table('table_highest_wins','icon_highest_wins');
			show_table('table_series_wins','icon_series_wins');
			show_table('table_series_losses','icon_series_losses');
			show_table('table_most_raise','icon_most_raise');
			show_table('table_most_all_in','icon_most_all_in');
		}

		function hide_all_table() {
			hide_table('table_course_game','icon_course_game');
			hide_table('table_played_hands','icon_played_hands');
			hide_table('table_best_hands','icon_best_hands');
			hide_table('table_most_wins','icon_most_wins');
			hide_table('table_highest_wins','icon_highest_wins');
			hide_table('table_series_wins','icon_series_wins');
			hide_table('table_series_losses','icon_series_losses');
			hide_table('table_most_raise','icon_most_raise');
			hide_table('table_most_all_in','icon_most_all_in');
		}

		function select_player(seat) {
			if(selected_player == seat) {
				jQuery('table.data tr').css('font-weight','normal');
				jQuery('table.data tr').css('color','rgb(20,20,20)');
				jQuery('table.data td').css('background-image','linear-gradient(rgb(180, 245, 120), rgb(147, 199, 80) 75%, rgb(147, 199, 80))');
				jQuery('td.rank').css('font-weight','bold');
				hand_cash_line_select_player(0);
				selected_player = 0;
			} else {
				jQuery('table.data tr').css('font-weight','normal');
				jQuery('table.data tr').css('color','rgb(20,20,20)');
				jQuery('table.data td').css('background-image','linear-gradient(rgb(180, 245, 120), rgb(147, 199, 80) 75%, rgb(147, 199, 80))');
				jQuery('td.rank').css('font-weight','bold');
				jQuery('tr.player_'+seat).css('font-weight','bold');
				jQuery('tr.player_'+seat).css('color','rgb(0,0,0)');				
				jQuery('tr.player_'+seat+' td').css('background-image','linear-gradient(rgb(110, 225, 170), rgb(173, 240, 208) 75%, rgb(173, 240, 208))');
				hand_cash_line_select_player(seat);
				selected_player = seat;
			}
		}

	//-->
	</script>

	<div id="print_area">

		<table style="border:0px;width:100%;padding:0px;margin:0px;border-spacing:0px">
			<tr style="font-size:30px;font-weight:400;height:45px;font-family: 'ArvoRegular',Helvetica,Arial,sans-serif;vertical-align:middle;text-align:center">
				<td style="background-image:url(<?php echo $path ?>/pic/h_background_left.png);background-repeat:no-repeat; background-position:right;min-width:50px;"></td>
				<td style="background-image:url(<?php echo $path ?>/pic/h_background.png);">
					Game <?php echo $uniqueGameID ?>
				</td>
				<td style="background-image:url(<?php echo $path ?>/pic/h_background_right.png);background-repeat:no-repeat; background-position:left;min-width:50px;text-align:right">
					<span class="icon-print" style="font-size:24px;cursor:pointer;" onclick="printdiv('print_area')"></span>
				</td>
			</tr>
			<tr style="height:20px;"><td><td></tr>
		</table>

<?php
	$player_list = get_player_list($db,$uniqueGameID);
	$hand_cash = get_hand_cash($db,$uniqueGameID);
	$blind_steps = get_blind_steps($db,$uniqueGameID);

	$query = $db->prepare("SELECT Startmoney,StartSb FROM game WHERE UniqueGameID=".$uniqueGameID);
	$query->execute();
	if($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row['Startmoney']) & $row['Startmoney']>0) $startmoney = $row['Startmoney'];
		if(is_numeric($row['StartSb']) & $row['StartSb']>0) $startsmallblind = $row['StartSb'];
	} else {
		$startmoney = -1;
		$startsmallblind = -1;
	}

	$query = "
			SELECT Player
			FROM (
				SELECT Player, Seat
				FROM Player
				WHERE UniqueGameID=".$uniqueGameID."
			) NATURAL JOIN (
				SELECT Player as Seat
				FROM Action
				WHERE Action='wins game' AND UniqueGameID=".$uniqueGameID."
			)";
	$query = $db->prepare($query);
	$query->execute();
	if($row = $query->fetch(PDO::FETCH_ASSOC)) {
//		$winner = preg_replace($regex,'',$row['Player']);
                $winner = replace_spec_char($row['Player']);
	} else {
		$winner = "game aborted";
	}

?>

<!-- basic data -->
		<div class="element">
			<h2>Basic data</h2>
			<table style="text-align:left;margin:auto auto">
				<tr>
					<td><b>Number of players:</b></td>
					<td style="text-align:right"><?php
						if(empty($player_list)) echo "n/a";
						else echo count($player_list[0]);
					?></td>
				</tr>
				<tr>
					<td><b>Winner:</b></td>
					<td style="text-align:right"><?php echo $winner ?></td>
				</tr>
				<tr>
					<td><b>Hands:</b></td>
					<td style="text-align:right"><?php
						if(empty($player_list[3][0])) echo "0";
						else echo $player_list[3][0];
					?></td>
				</tr>
				<tr>
					<td><b>Startmoney:</b></td>
					<td style="text-align:right"><?php
						if($startmoney<0) echo "n/a";
						else echo "$".$startmoney;
					?></td>
				</tr>
				<tr>
					<td><b>Start big blind:</b></td>
					<td style="text-align:right"><?php 
						if($startsmallblind<0) echo "n/a";
						else echo "$".(2*$startsmallblind);
					?></td>
				</tr>
				<tr>
					<td><b>End big blind:</b></td>
					<td style="text-align:right"><?php
						if(empty($blind_steps)) {
							if($startsmallblind<0) echo "n/a";
							else echo "$".(2*$startsmallblind);
						} else echo "$".$blind_steps[count($blind_steps)-1][1];
					?></td>
				</tr>
				<tr><td style="height:20px"></td></tr>
				<tr>
					<td colspan=2 style="text-align:center">
						<div id="div_expand_collapse" onClick="javascript:expand_collapse()" class="expand">
							<span id="span_expand_collapse">
								&nbsp;expand all
								<span class="icon-resize-full" style="font-size:14px;padding-left:10px;"></span>
							</span>
						</div>
					</td>
				</tr>
			</table>
		</div>

<!-- ranking -->
	<?php if(!empty($player_list)) { ?>
		<div class="element">
			<h2>Ranking</h2>
			<table class="data">
				<tr>
					<th></th>
					<th class="player">Player</th>
					<th class="hand">Hand</th>
					<th></th>
					<th></th>
				</tr><?php
			for($i=0;$i<count($player_list[0]);$i++)	{ ?>

				<tr class="player_<?php echo $player_list[0][$i]; ?>">
					<td class="rank"><?php echo $player_list[2][$i]; ?>.</td>
					<td class="player" onclick="javascript:select_player(<?php echo $player_list[0][$i]; ?>)"><?php echo $player_list[1][$i]; ?></td>
					<td class="hand"><?php
						if(empty($player_list[3][$i])) echo "0";
						else echo $player_list[3][$i];
					?></td>
					<td style="padding-right:10px"><img class="line" src="/log_file_analysis/charts/line.php?player=<?php echo $player_list[2][$i]; ?>" /></td>
					<td style="font-size:11px;padding-right:10px">
					<?php if(is_array($player_list) && is_array($player_list[7]) && is_array($player_list[7][$i]) && count($player_list[7][$i])>0) {
						if($player_list[7][$i]==-1) echo "has left the game";
						else {
							if($player_list[6][$i] == 1) echo "wins with ";
							else echo "eliminated by ";
							for($j=0;$j<count($player_list[7][$i]);$j++) {
								if($j>0) {
									if(count($player_list[7][$i]) > 1 & $j==count($player_list[7][$i])-1) echo " and ";
									else echo ", ";
								}
								echo $player_list[7][$i][$j];
							}
						}
					} ?>
					</td>
					<?php
			} ?>

				</tr>
			</table>
		</div>

<!-- course of the game -->
		<div id="div_course_game" class="element">
			<span onClick="javascript:toggle_table('table_course_game','icon_course_game')" style="cursor:pointer">
				<h2>Course of the game</h2>
				<span id="icon_course_game" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_course_game" class="toggle">
				<tr>
					<td>
						<div style="margin-left:20px">
							<div id="hand_cash_line" style="width:60px;background-image:url(/log_file_analysis/pic/hand_cash_top.png);text-align:center;float:left">
								<a href="javascript:hand_cash_line()" onFocus="if(this.blur)this.blur()">line</a>
							</div>
							<div id="hand_cash_stacked" style="width:60px;background-image:url(/log_file_analysis//pic/hand_cash_top_inactive.png);text-align:center;float:left">
								<a href="javascript:hand_cash_stacked()" onFocus="if(this.blur)this.blur()">stacked</a>
							</div>
						</div>
						<div style="clear:left">
							<img name="hand_cash" src="" />
						</div>
						<div style="clear:left">
							<img name="pot_size" src="" />
						</div>
					</td>
				</tr>
			</table>
			<script type="text/javascript">
				<!--
					jQuery('#div_course_game').css('width',get_width());
				//-->
			</script>
		</div>

<?php array_multisort($player_list[0],$player_list[1],$player_list[2],$player_list[3],$player_list[4],$player_list[5],$player_list[6],$player_list[7]); ?>

<!-- hands played -->
		<div id='div_played_hands' class="element">
			<span onClick="javascript:toggle_table('table_played_hands','icon_played_hands')" style="cursor:pointer">
				<h2>Most hands played</h2>
				<span id="icon_played_hands" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_played_hands" class="data toggle">
				<tr>
					<th></th>
					<th class="player">Player</th>
					<th id="played_hands_count_head_1" class="hand" colspan="2" style="text-align:left">Count</th>
					<th id="played_hands_count_head_2" colspan="2">10&nbsp;to&nbsp;7<br>player</th>
					<th id="played_hands_count_head_3" colspan="2">6&nbsp;to&nbsp;4<br>player</th>
					<th id="played_hands_count_head_4" colspan="2">3&nbsp;to&nbsp;1<br>player</th>
				</tr><?php
			$played_hands = get_played_hands($db,$player_list[3],$uniqueGameID,$regex);
			for($i=0;$i<count($played_hands[0]);$i++) { ?>
				<tr class="player_<?php echo $played_hands[0][$i] ?>">
					<td style="padding-right:15px;padding-left:10px;text-align:right;"><?php echo ($i+1) ?>.</td>
					<td class="player" onclick="javascript:select_player(<?php echo $played_hands[0][$i] ?>)"><?php echo $played_hands[1][$i] ?></td>
					<td style="text-align:right;font-weight:bold"><?php echo round($played_hands[4][$i])."%" ?></td>
					<td class="played_hands_count_data" style="font-size:10px;text-align:left;padding-left:5px"><?php echo "(".$played_hands[2][$i]."/".$played_hands[3][$i]." hands)" ?></td>
					<td style="padding-left:20px;text-align:right"><?php echo round($played_hands[7][$i])."%" ?></td>
					<td class="played_hands_count_data" style="font-size:10px;text-align:left;padding-left:5px"><?php echo "(".$played_hands[5][$i]."/".$played_hands[6][$i].")" ?></td>
					<td style="padding-left:20px;text-align:right"><?php if($played_hands[10][$i]>0) { echo round($played_hands[10][$i])."%"; } else { echo "-"; } ?></td>
					<td class="played_hands_count_data" style="font-size:10px;text-align:left;padding-left:5px"><?php if($played_hands[10][$i]>0) { echo "(".$played_hands[8][$i]."/".$played_hands[9][$i].")"; } else { echo "&nbsp;"; } ?></td>
					<td style="padding-left:20px;text-align:right"><?php if($played_hands[13][$i]>0) { echo round($played_hands[13][$i])."%"; } else { echo "-"; } ?></td>
					<td class="played_hands_count_data" style="font-size:10px;text-align:left;padding-left:5px"><?php if($played_hands[13][$i]>0) { echo "(".$played_hands[11][$i]."/".$played_hands[12][$i].")"; } else { echo "&nbsp;"; } ?></td>
				</tr><?php
			} ?>
			</table>
			<script type="text/javascript">
				<!--
					jQuery('#div_most_wins').css('width',jQuery('#table_most_wins').css('width'));
				//-->
			</script>
		</div>

<?php array_multisort($played_hands[0],$played_hands[1],$played_hands[2],$played_hands[3],$played_hands[4],$played_hands[5],$played_hands[6],$played_hands[7],$played_hands[8],$played_hands[9],$played_hands[10],$played_hands[11],$played_hands[12],$played_hands[13]); ?>

<!-- best hands -->
		<div id='div_best_hands' class="element">
			<span onClick="javascript:toggle_table('table_best_hands','icon_best_hands')" style="cursor:pointer">
				<h2>Best hands</h2>
				<span id="icon_best_hands" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_best_hands" class="toggle data">
				<tr>
					<th></th>
					<th style="text-align:left">Cards</th>
					<th class="player">Player</th>
					<th class="hand">Hand</th>
					<th style="padding-right:10px">Result</th>
				</tr><?php
			$best_hands = get_best_hands($db,$hand_cash,10,$uniqueGameID,$regex);
		  if(!empty($best_hands)) {
				for($i=0;$i<count($best_hands[0]);$i++) { ?>
				<tr class="player_<?php echo $best_hands[0][$i] ?>">
					<td style="padding-right:15px;padding-left:10px;text-align:right;"><?php echo ($i+1) ?>.</td>
					<td class="hand_name" style="font-weight:bold"><?php echo $best_hands[2][$i] ?></td>
					<td class="player" onclick="javascript:select_player(<?php echo $best_hands[0][$i] ?>)"><?php echo $best_hands[1][$i] ?></td>
					<td class="hand"><?php echo $best_hands[3][$i] ?></td>
					<td class="result">
					<?php
					if($best_hands[4][$i] >= 0) echo "wins $".$best_hands[4][$i];
					else echo "<span style=\"color:red\">loses $".(-$best_hands[4][$i])."</span>"; ?>
					</td>
				</tr><?php
				}
			} ?>
			</table>
			<script type="text/javascript">
				<!--
					jQuery('#div_best_hands').css('width',jQuery('#table_best_hands').css('width'));
				//-->
			</script>
		</div>

<!-- most wins -->
		<div id='div_most_wins' class="element">
			<span onClick="javascript:toggle_table('table_most_wins','icon_most_wins')" style="cursor:pointer">
				<h2>Most wins</h2>
				<span id="icon_most_wins" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_most_wins" class="data toggle">
				<tr>
					<th></th>
					<th class="player">Player</th>
					<th class="hand" colspan="2">Count&nbsp;*</th>
					<th class="amount">Highest</th>
				</tr><?php
			$most_wins = get_most_wins($db,$hand_cash,$played_hands[2],$uniqueGameID,$regex);
			for($i=0;$i<count($most_wins[0]);$i++) { ?>
				<tr class="player_<?php echo $most_wins[0][$i] ?>">
					<td style="padding-right:15px;padding-left:10px;text-align:right;"><?php echo ($i+1) ?>.</td>
					<td class="player" onclick="javascript:select_player(<?php echo $most_wins[0][$i] ?>)"><?php echo $most_wins[1][$i] ?></td>
					<td style="text-align:right;font-weight:bold;padding-right:5px"><?php echo $most_wins[2][$i] ?></td>
					<td style="text-align:left;padding-left:5px;padding-right:15px"><?php echo "(".round($most_wins[3][$i])."%)" ?></td>
					<td class="amount"><?php if($most_wins[4][$i]>0) echo "$"; echo $most_wins[4][$i] ?></td>
					</tr><?php
			} ?>
			</table>
			<script type="text/javascript">
				<!--
					jQuery('#div_most_wins').css('width',jQuery('#table_most_wins').css('width'));
				//-->
			</script>
		</div>

<!-- highest wins -->
		<div id="div_highest_wins" class="element">
			<span onClick="javascript:toggle_table('table_highest_wins','icon_highest_wins')" style="cursor:pointer">
				<h2>Highest wins</h2>
				<span id="icon_highest_wins" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_highest_wins" class="data toggle">
				<tr>
					<th></th>
					<th class="amount">Amount</th>
					<th class="player">Player</th>
					<th></th>
					<th class="hand">Hand</th>
				</tr><?php
			$highest_win = get_highest_win($db,$hand_cash,10,$uniqueGameID,$regex);
			$side_pot_attendance = false;
			if(!empty($highest_win)) {
				for($i=0;$i<count($highest_win[0]);$i++) { ?>
				<tr class="player_<?php echo $highest_win[0][$i] ?>">
					<td style="padding-right:15px;padding-left:10px;text-align:right;"><?php echo ($i+1) ?>.</td>
					<td class="amount" style="font-weight:bold">$<?php echo $highest_win[4][$i] ?></td>
					<td class="player" onclick="javascript:select_player(<?php echo $highest_win[0][$i] ?>)"><?php echo $highest_win[1][$i] ?></td>
					<td style="font-size:8px;vertical-align:top">
					<?php
						if($highest_win[3][$i]) {
							echo "(*)";
							$side_pot_attendance = true;
						} else {
							echo "&nbsp;";
						}
					?></td>
					<td class="hand"><?php echo $highest_win[2][$i]?></td>
				</tr><?php
				}
			} ?>
			</table><?php
				if($side_pot_attendance) { ?>
					<div id="table_highest_wins_add" class="side_pot_div" style="float:left;text-align:right;font-size:10px;color:#1A4109">(*) side pot</div>
				<?php } ?>
			<script type="text/javascript">
				<!--
					jQuery('#div_highest_wins').css('width',jQuery('#table_highest_wins').css('width'));
					jQuery('#table_highest_wins_add').css('width',jQuery('#table_highest_wins').css('width'));
				//-->
			</script>
		</div>

<!-- longest series of wins -->
		<div id='div_series_wins' class="element">
			<span onClick="javascript:toggle_table('table_series_wins','icon_series_wins')" style="cursor:pointer">
				<h2>Longest series of wins</h2>
				<span id="icon_series_wins" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_series_wins" class="data toggle">
				<tr>
					<th></th>
					<th class="hand">Duration</th>
					<th class="player">Player</th>
					<th colspan=3 class="hands">Hands</th>
					<th class="amount">Total gain</th>
				</tr><?php
			$longest_series_win = get_longest_series_win($db,$hand_cash,$uniqueGameID,$regex);
			if(!empty($longest_series_win)) {
				for($i=0;$i<min(count($longest_series_win[0]),10);$i++) { ?>
				<tr class="player_<?php echo $longest_series_win[0][$i] ?>">
					<td style="padding-right:15px;padding-left:10px;text-align:right;"><?php echo ($i+1) ?>.</td>
					<td class="hand" style="font-weight:bold;padding-right:25px"><?php echo $longest_series_win[2][$i] ?></td>
					<td class="player" onclick="javascript:select_player(<?php echo $longest_series_win[0][$i] ?>)"><?php echo $longest_series_win[1][$i] ?></td>
					<td class="hand" style="padding-right:2px"><?php echo $longest_series_win[3][$i] ?></td>
					<td>-</td>
					<td class="hand" style="padding-right:20px;"><?php echo $longest_series_win[4][$i] ?></td>
					<td class="amount">$<?php echo $longest_series_win[5][$i] ?></td>
				</tr><?php
				}
			} ?>
			</table>
			<script type="text/javascript">
				<!--
					jQuery('#div_series_wins').css('width',jQuery('#table_series_wins').css('width'));
				//-->
			</script>
		</div>

<!-- longest series of losses -->
		<div id='div_series_losses' class="element">
			<span onClick="javascript:toggle_table('table_series_losses','icon_series_losses')" style="cursor:pointer">
				<h2>Longest series of losses</h2>
				<span id="icon_series_losses" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_series_losses" class="data toggle">
				<tr>
					<th></th>
					<th class="hand">Duration</th>
					<th class="player">Player</th>
					<th colspan=3 class="hands">Hands</th>
					<th class="amount">Total Loss</th>
				</tr><?php
			$longest_series_loose = get_longest_series_loose($db,$player_list,$hand_cash,$uniqueGameID);
			if(!empty($longest_series_loose)) {
				for($i=0;$i<min(count($longest_series_loose[0]),10);$i++) { ?>
				<tr class="player_<?php echo $longest_series_loose[0][$i] ?>">
					<td style="padding-right:15px;padding-left:10px;text-align:right;"><?php echo ($i+1) ?>.</td>
					<td class="hand" style="font-weight:bold;padding-right:25px"><?php echo $longest_series_loose[2][$i] ?></td>
					<td class="player" onclick="javascript:select_player(<?php echo $longest_series_loose[0][$i] ?>)"><?php echo $longest_series_loose[1][$i] ?></td>
					<td class="hand" style="padding-right:2px"><?php echo $longest_series_loose[3][$i] ?></td>
					<td>-</td>
					<td class="hand" style="padding-right:20px"><?php echo $longest_series_loose[4][$i] ?></td>
					<td class="amount">$<?php echo $longest_series_loose[5][$i] ?></td>
				</tr><?php
				}
			} ?>
			</table>
			<script type="text/javascript">
				<!--
					jQuery('#div_series_losses').css('width',jQuery('#table_series_losses').css('width'));
				//-->
			</script>
		</div>

<!-- most bet / raise -->
		<div id='div_most_raise' class="element">
			<span onClick="javascript:toggle_table('table_most_raise','icon_most_raise')" style="cursor:pointer">
				<h2>Most bet/raise</h2>
				<span id="icon_most_raise" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_most_raise" class="data toggle">
				<tr>
					<th></th>
					<th class="player">Player</th>
					<th class="hand" colspan="2">Count&nbsp;**</th>
				</tr><?php
			$most_raise = get_most_raise($db,$player_list[0],$player_list[1],$played_hands[2],$uniqueGameID);
			if(!empty($most_raise)) {
				for($i=0;$i<count($most_raise[0]);$i++) { ?>
				<tr class="player_<?php echo $most_raise[0][$i] ?>">
					<td style="padding-right:15px;padding-left:10px;text-align:right;"><?php echo ($i+1) ?>.</td>
					<td class="player" onclick="javascript:select_player(<?php echo $most_raise[0][$i] ?>)"><?php echo $most_raise[1][$i] ?></td>
					<td style="text-align:right;font-weight:bold;padding-right:5px;padding-left:15px"><?php echo $most_raise[2][$i] ?></td>
					<td style="text-align:left;padding-left:5px;padding-right:10px"><?php echo "(".round($most_raise[4][$i])."%)" ?></td>
					
				</tr><?php
				}
			} ?>
			</table>
			<script type="text/javascript">
				<!--
					jQuery('#div_most_raise').css('width',jQuery('#table_most_raise').css('width'));
				//-->
			</script>
		</div>

<!-- most all in -->
		<div id='div_most_all_in' class="element">
			<span onClick="javascript:toggle_table('table_most_all_in','icon_most_all_in')" style="cursor:pointer">
				<h2>Most all in</h2>
				<span id="icon_most_all_in" class="icon-chevron-down" style="font-size:12px;padding-left:10px;vertical-align:top"></span>
			</span>
			<table id="table_most_all_in" class="data toggle">
				<tr>
					<th></th>
					<th class="player">Player</th>
					<th class="hand" style="white-space:normal;padding-left:10px" colspan="2">Total count&nbsp;*</th>
					<th class="hand" style="white-space:normal">In preflop</th>
					<th class="hand" style="white-space:normal">First 5&nbsp;hands</th>
					<th class="hand" style="white-space:normal">Total won</th>
				</tr><?php
			$most_all_in = get_most_all_in($db,$uniqueGameID,$played_hands[2],$regex);
			for($i=0;$i<count($player_list[0]);$i++) { ?>
				<tr class="player_<?php echo $most_all_in[0][$i] ?>">
					<td style="padding-right:15px;padding-left:10px;text-align:right;"><?php echo ($i+1) ?>.</td>
					<td class="player" onclick="javascript:select_player(<?php echo $most_all_in[0][$i] ?>)"><?php echo $most_all_in[1][$i] ?></td>
					<td style="text-align:right;font-weight:bold;padding-left:15px"><?php echo $most_all_in[2][$i] ?></td>
					<td style="text-align:left;padding-left:5px"><?php echo "(".round($most_all_in[3][$i])."%)" ?></td>
					<td class="hand" style="padding-right:30px"><?php echo $most_all_in[4][$i] ?></td>
					<td class="hand" style="padding-right:25px"><?php echo $most_all_in[5][$i] ?></td>
					<td class="hand" style="padding-right:20px"><?php echo $most_all_in[6][$i] ?></td>
				</tr><?php
			} ?>
			</table>
			<script type="text/javascript">
				<!--
					jQuery('#div_most_all_in').css('width',jQuery('#table_most_all_in').css('width'));
				//-->
			</script>
		</div>
	<?php } ?>
		<!-- footnode -->
		<div style="display:inline-block">
			<table style="border:0px;text-align:left;font-size:10px;color:rgb(80,80,80);padding:0px;margin:0px;border-spacing:0px">
				<tr><td>*)</td><td>percental value: absolute value in relation to hands played</td></tr>
				<tr><td>**)</td><td>percental value: number of hands with at least one bet/raise in relation to all hands played</td></tr>
			</table>
		</div>
	
	</div>

<?php
				$db = null;
				} else {
					echo "<p><b>This Game ID is not valid.</b> Please choose a Game from the select box.</p>";
				}
				}
?>
	</div>
<?php
			}
		} else {
		
			$time_count_id_hour = floor(SEC_COUNT_ID/3600);
			$time_count_id_min = floor(SEC_COUNT_ID/60)-$time_count_id_hour*60;
			$time_count_id_sec = floor(SEC_COUNT_ID)-$time_count_id_min*60-$time_count_id_hour*3600;
	
			echo "<p style='color:rgb(255,0,0)'><span class='icon-warning-sign' style='font-size:16px;padding-right:5px'></span><b>Session is not valid.</b><span class='icon-warning-sign' style='font-size:16px;padding-left:5px;padding-right:10px'></span> This can have two reasons:</p><ul style='color:rgb(255,0,0)'><li>The storage time of the log file analysis is expired (current storage time: ".$time_count_id_hour." h ".sprintf('%02d',$time_count_id_min)." min ".sprintf('%02d',$time_count_id_sec)." sec).</li><li>You've entered the session ID wrongly. Please check your session ID for mistakes.</li><ul>";
		
		}
	} else {
	
		$time_count_id_hour = floor(SEC_COUNT_ID/3600);
		$time_count_id_min = floor(SEC_COUNT_ID/60)-$time_count_id_hour*60;
		$time_count_id_sec = floor(SEC_COUNT_ID)-$time_count_id_min*60-$time_count_id_hour*3600;
	
		echo "<p style='color:rgb(255,0,0)'><span class='icon-warning-sign' style='font-size:16px;padding-right:5px'></span><b>Session is not valid.</b><span class='icon-warning-sign' style='font-size:16px;padding-left:5px;padding-right:10px'></span> This can have two reasons:</p><ul style='color:rgb(255,0,0)'><li>The storage time of the log file analysis is expired (current storage time: ".$time_count_id_hour." h ".sprintf('%02d',$time_count_id_min)." min ".sprintf('%02d',$time_count_id_sec)." sec).</li><li>You've entered the session ID wrongly. Please check your session ID for mistakes.</li><ul>";
	}
	
} else {
	echo "<p style='color:rgb(255,0,0)'><span class='icon-warning-sign' style='font-size:16px;padding-right:5px'></span><b>No Session ID is specified.</b><span class='icon-warning-sign' style='font-size:16px;padding-left:5px;padding-right:10px'></span> Please use the Button 'Analyse Logfile...' in your PokerTH Client.</p>";
}

//echo "<h3>$path</h3>";

?>

<script>
	<!--
		function printdiv(printdivname) {
			var tPath = '<?php echo $path; ?>';
			var headstr = '<html><head><title>Booking Details</title></head><body>';
			headstr	+= '<link rel="stylesheet" href="';
			headstr	+= tPath + '/config/format.css" type="text/css" media="screen" />';
			headstr	+= '<link rel="stylesheet" href="';
			headstr	+= tPath + '/config/format.css" type="text/css"';
			headstr	+= 'media="print" />';
			var footstr = '<' + '/' + 'body>';

			var newstr = document.getElementById(printdivname).innerHTML;
			var oldstr = document.body.innerHTML;

			document.body.innerHTML = headstr+newstr+footstr;
			window.print();

			document.body.innerHTML = oldstr;
			return false;
		}
	//-->
</script>
<?php

function get_player_list($db,$uniqueGameID) {

	// player_list:
	// 0 --> seat
	// 1 --> player name
	// 2 --> ranking
	// 3 --> max hand id
	// 4 --> start cash last hand
	// 5 --> player sits out
	// 6 --> player wins game
	// 7 --> eliminated by (player name) / winner: winning hand

	$query = "
		SELECT *
		FROM (
			SELECT Player, Seat, HandID, LastCash
			FROM (
				SELECT *
				FROM player
				WHERE UniqueGameID=".$uniqueGameID."
			) NATURAL JOIN (";
			for($i=1;$i<=10;$i++) {
				$query = $query."SELECT max(HandID) as HandID, ".$i." as Seat, Seat_".$i."_Cash as LastCash FROM hand WHERE UniqueGameID=".$uniqueGameID." AND Seat_".$i."_Cash NOT NULL";
				if($i<10) $query = $query." UNION ";
			}
			$query = $query.")
		)
		NATURAL LEFT JOIN (
			SELECT HandID, Player as Seat, 1 as Eliminated
			FROM action
			WHERE Action='sits out' AND UniqueGameID=".$uniqueGameID."
		)
		NATURAL LEFT JOIN (
			SELECT HandID, Player as WinnerSeat
			FROM action
			WHERE UniqueGameID=".$uniqueGameID." AND (Action='wins' OR Action='wins (side pot)')
		)
		NATURAL LEFT JOIN (
			SELECT Seat as WinnerSeat, Player as WinnerPlayer
			FROM player
			WHERE UniqueGameID=".$uniqueGameID."
		)
		WHERE Seat IS NOT NULL ORDER By Seat, WinnerSeat";
	$query = $db->prepare($query);
	$query->execute();
	
	$player_list = array();

	$seat_ctr = 0;
	while($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10) {
			if(is_numeric($row['HandID']) & $row['HandID']>0 & is_numeric($row['LastCash']) & $row['LastCash'] > 0) {
				$new_player = false;
				if($seat_ctr>0) {
					if( $row['Seat']!=$player_list[0][$seat_ctr-1] ) {
						$new_player = true;
					}
				}

				if( $seat_ctr==0 | $new_player ) {
					$player_list[0][$seat_ctr] = $row['Seat'];
		//			$player_list[1][$seat_ctr] = preg_replace($regex,'',$row['Player']);
					$player_list[1][$seat_ctr] = replace_spec_char($row['Player']);
					$player_list[3][$seat_ctr] = $row['HandID'];
					$player_list[4][$seat_ctr] = $row['LastCash'];
					$player_list[5][$seat_ctr] = 0;
					$player_list[6][$seat_ctr] = 0;
					if(!is_null($row['WinnerPlayer'])) {
						if(is_numeric($row['Eliminated']) & $row['Eliminated']==1) {
							$player_list[7][$seat_ctr][] = replace_spec_char($row['WinnerPlayer']);
						} else {
							// -1 for player left game and wasn't eliminated
							$player_list[7][$seat_ctr] = -1;
						}
					} else {
						$player_list[7][$seat_ctr] = array();
					}
					$seat_ctr++;
				} else {
					if(!is_null($row['WinnerPlayer'])) {
						$player_list[7][$seat_ctr-1][] = replace_spec_char($row['WinnerPlayer']);
					}
				}
			} else {
				// unkown player - only seat known
				$player_list[0][$seat_ctr] = $row['Seat'];
				$player_list[1][$seat_ctr] = replace_spec_char($row['Player']);
				$player_list[3][$seat_ctr] = 0;
				$player_list[4][$seat_ctr] = 0;
				$player_list[5][$seat_ctr] = 0;
				$player_list[6][$seat_ctr] = 0;
				$player_list[7][$seat_ctr] = array();
				$seat_ctr++;
			}
		} else {
			// default values
			$player_list[0][$seat_ctr] = 0;
			$player_list[1][$seat_ctr] = '';
			$player_list[3][$seat_ctr] = 0;
			$player_list[4][$seat_ctr] = 0;
			$player_list[5][$seat_ctr] = 0;
			$player_list[6][$seat_ctr] = 0;
			$player_list[7][$seat_ctr] = array();
			$seat_ctr++;
		}
	}
	

	
	if(!empty($player_list)) {
		$query = $db->prepare("SELECT Player as Seat, HandID FROM action WHERE UniqueGameID=".$uniqueGameID." AND Action='wins game'");
		$query->execute();
		if($row = $query->fetch(PDO::FETCH_ASSOC)) {
			// game not aborted
			if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10) {
				$player_list[6][$row['Seat']-1] = 1;
				$query_1 = $db->prepare("SELECT Seat_".$row['Seat']."_Card_1 as Card_1, Seat_".$row['Seat']."_Card_2 as Card_2, Seat_".$row['Seat']."_Hand_text as Hand_text FROM hand WHERE UniqueGameID=".$uniqueGameID." AND HandID=".$row['HandID']);
				$query_1->execute();
				if($row_1 = $query_1->fetch(PDO::FETCH_ASSOC)) {
					if(is_numeric($row_1['Card_1']) & $row_1['Card_1']>=0 & $row_1['Card_1']<52 & is_numeric($row_1['Card_2']) & $row_1['Card_2']>=0 & $row_1['Card_2']<52) {
						$player_list[7][$row['Seat']-1] = array();
						$hand_text = $row_1['Hand_text'];
						$end_text = strpos($row_1['Hand_text'],',');
						if(!$end_text) $end_text = strlen($hand_text);
						$player_list[7][$row['Seat']-1][0] = "[".int2string($row_1['Card_1']).",".int2string($row_1['Card_2'])."] ".substr($hand_text,0,$end_text);
					} else $player_list[7][$row['Seat']-1] = array();
				} else $player_list[7][$row['Seat']-1] = array();
			}
		} else {
			// game aborted
			$query = $db->prepare("SELECT * FROM action WHERE UniqueGameID=".$uniqueGameID." AND Action='wins' AND HandID=".max($player_list[3]));
			$query->execute();
			if($row = $query->fetch(PDO::FETCH_ASSOC)) {			
				// hand is played to the end --> all who don't sits out are going to the next hand
				$query = "
					SELECT Player as Seat
					FROM action
					WHERE UniqueGameID=".$uniqueGameID." AND HandID=".max($player_list[3])." AND Action='sits out'";
				$query = $db->prepare($query);
				$query->execute();
				while($row = $query->fetch(PDO::FETCH_ASSOC)) {
					if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10) {
						$player_list[5][$row['Seat']-1] = 1;
					}
				}
				// no elimination notation for active when game aborted
				$query = "
					SELECT DISTINCT Player as Seat
					FROM action
					WHERE UniqueGameID=".$uniqueGameID." AND HandID=".max($player_list[3]);
				$query = $db->prepare($query);
				$query->execute();
				while($row = $query->fetch(PDO::FETCH_ASSOC)) {
					if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10 & $player_list[5][$row['Seat']-1] != 1) {
						$player_list[7][$row['Seat']-1] = array();
					}
				}
			}
		}
	
			// 	if($game_aborted) {
	// 		$query = $db->prepare("SELECT * FROM action WHERE UniqueGameID=".$uniqueGameID." AND Action='wins' AND HandID=".max($player_list[3]));
	// 		$query->execute();
	// 		// hand is played to the end --> all who don't sits out are going to the next hand
	// 		if($row = $query->fetch(PDO::FETCH_ASSOC)) {			
	// 			$query = "
	// 				SELECT Player as Seat
	// 				FROM action
	// 				WHERE UniqueGameID=".$uniqueGameID." AND HandID=".max($player_list[3])." AND Player NOT IN (
	// 																			SELECT Player
	// 																			FROM action
	// 																			WHERE UniqueGameID=".$uniqueGameID." AND HandID=".max($player_list[3])." AND Action='sits out')
	// 				GROUP BY Player";
	// 			$query = $db->prepare($query);
	// 			$query->execute();
	// 			while($row = $query->fetch(PDO::FETCH_ASSOC)) {
	// 				if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10) {
	// 					$player_list[3][$row['Seat']-1] = $player_list[3][$row['Seat']-1]+1;
	// 				}
	// 			}
	// 		}
	// 	}

		// sort array regarding max action id
		array_multisort($player_list[6],SORT_DESC,$player_list[3],SORT_DESC,$player_list[5],$player_list[4],SORT_DESC,$player_list[0],$player_list[1],$player_list[7]);

		$tmp = 0;
		// evaluate index 2 (ranking)
		for($i=0;$i<count($player_list[0]);$i++) {
			if($player_list[6][$i]==0 & $i<count($player_list[0])-1 & $player_list[3][$i]==$player_list[3][min($i+1,count($player_list[0])-1)] & $player_list[4][$i]==$player_list[4][min($i+1,count($player_list[0])-1)]) {
				$tmp++;
			} else {
				$player_list[2][$i] = $i+1;
				for($j=1;$j<=$tmp;$j++) {
					$player_list[2][$i-$j] = $i+1;
				}
				$tmp = 0;
			}
		}
		
	}

	return $player_list;
}

function get_hand_cash($db,$uniqueGameID) {

	$query = $db->prepare("SELECT Startmoney FROM game WHERE UniqueGameID=".$uniqueGameID);
	$query->execute();
	if($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row['Startmoney']) & $row['Startmoney']>0) $startmoney = $row['Startmoney'];
	} else {
		$startmoney = -1;
	}

	$query = "SELECT HandID";
	for($i=1;$i<=10;$i++) {
		$query = $query.", Seat_".$i."_Cash";
	}
	$query = $query." FROM hand WHERE UniqueGameID=".$uniqueGameID;
	$query = $db->prepare($query);
	$query->execute();

	$hand_cash[10][0] = 0;

	$j=0;
	while($row = $query->fetch(PDO::FETCH_ASSOC)) {
		for($i=0;$i<10;$i++) {
			if($row['Seat_'.($i+1).'_Cash']) {
				if(is_numeric($row['Seat_'.($i+1).'_Cash']) & $row['Seat_'.($i+1).'_Cash'] > 0) {
					$hand_cash[$i][$j] = $row['Seat_'.($i+1).'_Cash'];
				}
			} else {
				if($j>0 && $hand_cash[$i][$j-1] > 0) {
					$hand_cash[$i][$j] = 0;
				} else {
					$hand_cash[$i][$j] = -1;
				}
			}
		}
		$j++;
		if(is_numeric($row['HandID']) & $row['HandID'] > 0) $hand_cash[10][$j] = $row['HandID'];
	}
	$sum_cash = 0;
	for($i=0;$i<10;$i++) {
		if($j>0 && $hand_cash[$i][$j-1] > 0) {
			$hand_cash[$i][$j] = 0;
			$sum_cash += $hand_cash[$i][$j-1];
		} else {
			$hand_cash[$i][$j] = -1;
		}
	}
	
	$query = "SELECT Player as Seat FROM action WHERE Action=\"wins game\" AND UniqueGameID=".$uniqueGameID;
	$query = $db->prepare($query);
	$query->execute();
	if($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10) {
			$hand_cash[$row['Seat']-1][$j] = $sum_cash;
		}
	}
        
	return $hand_cash;

}

function get_pot_size($db,$uniqueGameID) {

	$query = "
			SELECT HandID, SUM(Amount) as Pot
			FROM Action
			WHERE (Action='wins' OR Action='wins (side pot)') AND UniqueGameID=".$uniqueGameID."
			GROUP BY HandID";
	$query = $db->prepare($query);
	$query->execute();
	while($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row['Pot']) & $row['Pot']>0 & is_numeric($row['HandID']) & $row['HandID']>0) {
			$pot_size[0][] = 100000-$row['Pot'];
			$pot_size[1][] = $row['HandID'];
		}
	}

	return $pot_size;

}

function get_total_start_cash($db,$uniqueGameID) {

	$query = "SELECT HandID";
	for($i=1;$i<=10;$i++) {
		$query = $query.", Seat_".$i."_Cash";
	}
	$query = $query." FROM hand WHERE UniqueGameID=".$uniqueGameID." AND HandID=1";
	$query = $db->prepare($query);
	$query->execute();
	$start_cash = 0;
	if($row = $query->fetch(PDO::FETCH_ASSOC)) {
		for($i=1;$i<=10;$i++) {
			if(is_numeric($row['Seat_'.$i.'_Cash']) & $row['Seat_'.$i.'_Cash']>0) {
				$start_cash += $row['Seat_'.$i.'_Cash'];
			}
		}
	}

	return $start_cash;

}

function get_blind_steps($db,$uniqueGameID) {

	$query = "
		SELECT MIN(HandID) as HandID, Sb_Amount, Bb_Amount
		FROM hand
		WHERE UniqueGameID=".$uniqueGameID."
		GROUP BY Sb_Amount,Bb_Amount";
	$query = $db->prepare($query);
	$query->execute();
	$blind_steps = array();
	while($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row['Sb_Amount']) & $row['Sb_Amount']>0 & is_numeric($row['Bb_Amount']) & $row['Bb_Amount']>0 & is_numeric($row['HandID']) & $row['HandID']>0) {
			$blind_steps[] = array($row['Sb_Amount'],$row['Bb_Amount'],$row['HandID']);
		}
	}

	return $blind_steps;

}

function get_played_hands($db,$player_list_hands,$uniqueGameID,$regex) {

	// played_hands
	// 0 --> seat
	// 1 --> player names
	// 2 --> played hands
	// 3 --> all hands
	// 4 --> played hands relative
	// 5 --> played hands 10 to 7 player
	// 6 --> all hands 10 to 7 player
	// 7 --> played hands relative 10 to 7 player
	// 8 --> played hands 6 to 4 player
	// 9 --> all hands 6 to 4 player
	// 10 --> played hands relative 6 to 4 player
	// 11 --> played hands 3 to 1 player
	// 12 --> all hands 3 to 1 player
	// 13 --> played hands relative 3 to 1 player
	
	$player_list_hands_sort = $player_list_hands;
	rsort($player_list_hands_sort);
	
	if(count($player_list_hands)>6) $hand_number_10 = $player_list_hands_sort[6];
	else $hand_number_10 = 0;
	if(count($player_list_hands)>3) $hand_number_6 = $player_list_hands_sort[3];
	else $hand_number_6 = 0;
	$hand_number_3 = $player_list_hands_sort[0];

	$query_1 = "
		SELECT Player, Seat, PlayedHands, PlayedHands_10, PlayedHands_6, PlayedHands_3
		FROM (
			SELECT *
			FROM (
				SELECT *
				FROM (
					SELECT *
					FROM (
						SELECT *
						FROM player
						WHERE UniqueGameID=".$uniqueGameID."
					)
					NATURAL LEFT JOIN (
						SELECT Player as Seat, count(*) as PlayedHands
						FROM (
							SELECT DISTINCT HandID,Player
							FROM action
							WHERE Amount IS NOT null AND Action<>'posts small blind' AND Action<>'posts big blind' AND UniqueGameID=".$uniqueGameID."
						)
						GROUP BY Player
					)
				)
				NATURAL LEFT JOIN (
					SELECT Player as Seat, count(*) as PlayedHands_10
					FROM (
						SELECT DISTINCT HandID,Player
						FROM action
						WHERE Amount IS NOT null AND Action<>'posts small blind' AND Action<>'posts big blind' AND HandID<=".$hand_number_10." AND UniqueGameID=".$uniqueGameID."
					)
					GROUP BY Player
				)
			)
			NATURAL LEFT JOIN (
				SELECT Player as Seat, count(*) as PlayedHands_6
				FROM (
					SELECT DISTINCT HandID,Player
					FROM action
					WHERE Amount IS NOT null AND Action<>'posts small blind' AND Action<>'posts big blind' AND HandID<=".$hand_number_6." AND HandID>".$hand_number_10." AND UniqueGameID=".$uniqueGameID."
				)
				GROUP BY Player
			)
		)
		NATURAL LEFT JOIN (
			SELECT Player as Seat, count(*) as PlayedHands_3
			FROM (
				SELECT DISTINCT HandID,Player
				FROM action
				WHERE Amount IS NOT null AND Action<>'posts small blind' AND Action<>'posts big blind' AND HandID>".$hand_number_6." AND UniqueGameID=".$uniqueGameID."
			)
			GROUP BY Player
		)
		ORDER BY Seat";

	$query_1 = $db->prepare($query_1);
	$query_1->execute();
	while($row_1 = $query_1->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row_1['Seat']) & $row_1['Seat']>0 & $row_1['Seat']<=10) {
			$played_hands[0][] = $row_1['Seat'];
			$played_hands[1][] = replace_spec_char($row_1['Player']);
			$played_hands_all = $row_1['PlayedHands'];
			if(!($played_hands_all>0)) $played_handes_all = 0;

			
			$played_hands_6 = $row_1['PlayedHands_6'];
			if(!($played_hands_6>0)) $played_handes_6 = 0;
			$played_hands_3 = $row_1['PlayedHands_3'];
			if(!($played_hands_3>0)) $played_handes_3 = 0;
			$played_hands[2][] = $played_hands_all;
			$played_hands[3][] = $player_list_hands[$row_1['Seat']-1];
			if($player_list_hands[$row_1['Seat']-1]>0) $played_hands[4][] = $played_hands_all/$player_list_hands[$row_1['Seat']-1]*100;
			else $played_hands[4][] = 0;
			// 10 to 7 player
			$played_hands_10 = $row_1['PlayedHands_10'];
			if(!($played_hands_10>0)) $played_hands_10 = 0;
			$played_hands[5][] = $played_hands_10;
			$all_hands_10 = min($hand_number_10,$player_list_hands[$row_1['Seat']-1]);
			if(!($all_hands_10>0)) {
				$all_hands_10 = 0;
				$played_hands[7][] = 0;
			} else {
				$played_hands[7][] = $played_hands_10/$all_hands_10*100;
			}
			$played_hands[6][] = $all_hands_10;
			// 6 to 4 player
			$played_hands_6 = $row_1['PlayedHands_6'];
			if(!($played_hands_6>0)) $played_hands_6 = 0;
			$played_hands[8][] = $played_hands_6;
			$all_hands_6 = min($hand_number_6,$player_list_hands[$row_1['Seat']-1])-$hand_number_10;
			if(!($all_hands_6>0)) {
				$all_hands_6 = 0;
				$played_hands[10][] = 0;
			} else {
				$played_hands[10][] = $played_hands_6/$all_hands_6*100;
			}
			$played_hands[9][] = $all_hands_6;
			// 3 to 1 player
			$played_hands_3 = $row_1['PlayedHands_3'];
			if(!($played_hands_3>0)) $played_hands_3 = 0;
			$played_hands[11][] = $played_hands_3;
			$all_hands_3 = min($hand_number_3,$player_list_hands[$row_1['Seat']-1])-$hand_number_6;
			if(!($all_hands_3>0)) {
				$all_hands_3 = 0;
				$played_hands[13][] = 0;
			} else {
				$played_hands[13][] = $played_hands_3/$all_hands_3*100;
			}
			$played_hands[12][] = $all_hands_3;
		}
	}

	array_multisort(
		$played_hands[4],SORT_DESC,
		$played_hands[2],SORT_DESC,
		$played_hands[3],SORT_DESC,
		$played_hands[7],SORT_DESC,
		$played_hands[5],SORT_DESC,
		$played_hands[6],SORT_DESC,
		$played_hands[10],SORT_DESC,
		$played_hands[8],SORT_DESC,
		$played_hands[9],SORT_DESC,
		$played_hands[13],SORT_DESC,
		$played_hands[11],SORT_DESC,
		$played_hands[12],SORT_DESC,
		$played_hands[1],
		$played_hands[0]
	);

	return $played_hands;
}

function get_best_hands($db,$hand_cash,$count,$uniqueGameID,$regex) {

	// best_hands:
	// 0 --> seat
	// 1 --> player name
	// 2 --> hand text
	// 3 --> hand id
	// 4 --> amount

	$query_1 = "";
	$first_player_found = false;
	for($i=1;$i<=10;$i++) {
		$query_0 = $db->prepare("SELECT Player FROM player WHERE Seat=".$i." AND UniqueGameID=".$uniqueGameID);
		$query_0->execute();
		if($row_0 = $query_0->fetch(PDO::FETCH_ASSOC)) {
			if($first_player_found) $query_1 = $query_1." UNION ";
			else $first_player_found = true;

                        $query_1 = $query_1."SELECT \"".replace_spec_char($row_0['Player'])."\" as Player, ".$i." as Seat, HandID, Seat_".$i."_Hand_int as Seat_Hand_int, Seat_".$i."_Hand_text as Seat_Hand_text FROM hand WHERE Seat_Hand_text NOT NULL AND UniqueGameID=".$uniqueGameID;
		}
	}
	$query_1 = $query_1." ORDER BY Seat_Hand_int DESC LIMIT ".$count;
	$query_1 = $db->prepare($query_1);
	$query_1->execute();
	$best_hands = array();
	while($row_1 = $query_1->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row_1['HandID']) & $row_1['HandID']>0 & $row_1['HandID']<count($hand_cash[$row_1['Seat']-1])) {
			$amount = $hand_cash[$row_1['Seat']-1][$row_1['HandID']] - $hand_cash[$row_1['Seat']-1][$row_1['HandID']-1];
			$best_hands[0][] = $row_1['Seat'];
			$best_hands[1][] = $row_1['Player'];
			$best_hands[2][] = preg_replace($regex,'',$row_1['Seat_Hand_text']);
			$best_hands[3][] = $row_1['HandID'];
			$best_hands[4][] = $amount;
		}
	}
	return $best_hands;
}

function get_most_wins($db,$hand_cash,$played_hands,$uniqueGameID,$regex) {

	// most_wins
	// 0 --> seat
	// 1 --> player names
	// 2 --> count wins
	// 3 --> count wins relative
	// 4 --> highest win

	$query_1 = "
		SELECT Player, Seat, Count
		FROM (
			SELECT *
			FROM player
			WHERE UniqueGameID=".$uniqueGameID."
		)
		NATURAL LEFT JOIN (
			SELECT Player as Seat, count(*) as Count
			FROM action
			WHERE Action='wins' AND UniqueGameID=".$uniqueGameID."
			GROUP BY Player
		)
		ORDER BY Seat";
	$query_1 = $db->prepare($query_1);
	$query_1->execute();
	while($row_1 = $query_1->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row_1['Seat']) & $row_1['Seat']>0 & $row_1['Seat']<=10) {
			$most_wins[0][] = $row_1['Seat'];
                        $most_wins[1][] = replace_spec_char($row_1['Player']);
			$count = $row_1['Count'];
			if(!($count>0)) $count = 0;
			$most_wins[2][] = $count;
			if($played_hands[$row_1['Seat']-1]>0) $most_wins[3][] = $count/$played_hands[$row_1['Seat']-1]*100;
			else $most_wins[3][] = 0;
			$query_2 = "
				SELECT HandID
				FROM action
				WHERE Action='wins' AND Player=".$row_1['Seat']." AND UniqueGameID=".$uniqueGameID."
				ORDER BY Amount DESC";
			$query_2 = $db->prepare($query_2);
			$query_2->execute();
			$tmp = array();
			while($row_2 = $query_2->fetch(PDO::FETCH_ASSOC)) {
				if(is_numeric($row_2['HandID']) & $row_2['HandID']>0 & $row_2['HandID']<count($hand_cash[$row_1['Seat']-1])) {
					$tmp[] = $hand_cash[$row_1['Seat']-1][$row_2['HandID']] - $hand_cash[$row_1['Seat']-1][$row_2['HandID']-1];
				}
			}
			if(count($tmp)>0) {
				sort($tmp);
				$most_wins[4][] = array_pop($tmp);
			} else {
				$most_wins[4][] = "&nbsp;";
			}
		}
	}

	array_multisort(
		$most_wins[2],SORT_DESC,
		$most_wins[3],SORT_DESC,
		$most_wins[4],SORT_DESC,
		$most_wins[1],
		$most_wins[0]
	);

	return $most_wins;
}

function get_highest_win($db,$hand_cash,$count,$uniqueGameID,$regex) {

	// most_wins
	// 0 --> seat
	// 1 --> player names
	// 2 --> hand
	// 3 --> side pot?
	// 4 --> amount

	$query = "
		SELECT Player, Seat, HandID, Action
		FROM (
			SELECT *
			FROM player
			WHERE UniqueGameID=".$uniqueGameID."
		)
		NATURAL JOIN (
			SELECT Player as Seat, HandID, Action
			FROM action
			WHERE ( Action='wins' OR Action='wins (side pot)' ) AND UniqueGameID=".$uniqueGameID."
		)";
	$query = $db->prepare($query);
	$query->execute();
	$highest_win = array();
	while($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(preg_replace($regex,'',$row['Action']) == 'wins') $side_pot = false;
		else $side_pot = true;
		if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10 & is_numeric($row['HandID']) & $row['HandID']>0 & $row['HandID']<count($hand_cash[$row['Seat']-1])) {
			$amount = $hand_cash[$row['Seat']-1][$row['HandID']] - $hand_cash[$row['Seat']-1][$row['HandID']-1];
			if($amount>0) {
				$highest_win[0][] = $row['Seat'];
                                $highest_win[1][] = replace_spec_char($row['Player']);
				$highest_win[2][] = $row['HandID'];
				$highest_win[3][] = $side_pot;
				$highest_win[4][] = $amount;
			}
		}
	}

	if(!empty($highest_win)) {
		array_multisort(
			$highest_win[4],SORT_DESC,
			$highest_win[2],
			$highest_win[0],
			$highest_win[1],
			$highest_win[3]
		);
		$highest_win[0] = array_slice($highest_win[0],0,$count);
	}

	return $highest_win;

}

function get_longest_series_win($db,$hand_cash,$uniqueGameID,$regex) {

	// longest serie win:
	// 0 --> seat
	// 1 --> player name
	// 2 --> serie length
	// 3 --> start hand
	// 4 --> end hand
	// 5 --> amount

	$query = "
		SELECT Player, Seat, HandID
		FROM (
			SELECT *
			FROM player
			WHERE UniqueGameID=".$uniqueGameID."
		)
		LEFT JOIN (
			SELECT HandID, Player as Seat
			FROM action
			WHERE Action='wins' AND UniqueGameID=".$uniqueGameID."
		) USING (Seat)
		WHERE HandID NOT NULL
		ORDER BY HandID";
	$query = $db->prepare($query);
	$query->execute();
	$tmp_data = array(0,0,0,0);
	$serie = 1;
	$amount = 0;
	$start_hand = 0;
	$longest_series_win = array();

	while($row = $query->fetch(PDO::FETCH_ASSOC)) {

		if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10 & is_numeric($row['HandID']) & $row['HandID']>0) {

			if($tmp_data[0] != 0) {
				if($row['Seat'] == $tmp_data[1]) {
					if($serie==1) $start_hand = $tmp_data[0];
					$serie++;
					$amount+=$tmp_data[3];
				} else {
					if($serie>1) {
						$longest_series_win[0][] = $tmp_data[1];
						$longest_series_win[1][] = $tmp_data[2];
						$longest_series_win[2][] = $serie;
						$longest_series_win[3][] = $start_hand;
						$longest_series_win[4][] = $tmp_data[0];
						$longest_series_win[5][] = $amount+$tmp_data[3];
						$serie = 1;
						$amount = 0;
						$start_hand = 0;
					}
				}
			}
			if($row['HandID']<count($hand_cash[$row['Seat']-1])) {
				$amount_tmp = $hand_cash[$row['Seat']-1][$row['HandID']] - $hand_cash[$row['Seat']-1][$row['HandID']-1];
			} else {
				$amount_tmp = 0;
			}
                        $tmp_data = array($row['HandID'],$row['Seat'],replace_spec_char($row['Player']),$amount_tmp);

		} else {
			break;
		}
	}
	if($serie>1) {
		$longest_series_win[0][] = $tmp_data[1];
		$longest_series_win[1][] = $tmp_data[2];
		$longest_series_win[2][] = $serie;
		$longest_series_win[3][] = $start_hand;
		$longest_series_win[4][] = $tmp_data[0];
		$longest_series_win[5][] = $amount+$tmp_data[3];
	}

	if(!empty($longest_series_win)) {
		array_multisort(
			$longest_series_win[2],SORT_DESC,
			$longest_series_win[5],SORT_DESC,
			$longest_series_win[0],
			$longest_series_win[1],
			$longest_series_win[3],
			$longest_series_win[4]
		);
	}

	return $longest_series_win;

}

function get_longest_series_loose($db,$player_list,$hand_cash,$uniqueGameID) {

	// longest serie loose:
	// 0 --> seat
	// 1 --> player name
	// 2 --> serie length
	// 3 --> start hand
	// 4 --> end hand
	// 5 --> amount

	$longest_serie_loose = array();
	for($i=0;$i<count($player_list[0]);$i++) {
		$query = "SELECT HandID FROM hand WHERE HandID NOT IN ( SELECT HandID FROM action WHERE ( Action='wins' OR Action='wins (side pot)' ) AND UniqueGameID=".$uniqueGameID." AND Player=".$player_list[0][$i]." ) AND UniqueGameID=".$uniqueGameID." ORDER BY HandID";
		$query = $db->prepare($query);
		$query->execute();
		$handID_tmp = -1;
		$serie = 1;
		$amount = 0;
		while($row = $query->fetch(PDO::FETCH_ASSOC)) {
			if(is_numeric($row['HandID']) & $row['HandID']>0) {
				if($row['HandID']>$player_list[3][$i]) break;
				if($row['HandID'] == $handID_tmp + 1) {
					if($serie==1) {
						$start_hand = $handID_tmp;
					}
					$end_hand = $row['HandID'];
					$serie++;
				} else {
					if($serie > 1) {
						// end of serie
						$longest_serie_loose[0][] = $player_list[0][$i];
						$longest_serie_loose[1][] = $player_list[1][$i];
						$longest_serie_loose[2][] = $serie;
						$longest_serie_loose[3][] = $start_hand;
						$longest_serie_loose[4][] = $end_hand;
						$longest_serie_loose[5][] = $hand_cash[$player_list[0][$i]-1][$start_hand-1] - $hand_cash[$player_list[0][$i]-1][$end_hand];
						$serie = 1;
						$amount = 0;
					}
				}
				$handID_tmp = $row['HandID'];
			}
		}
		if($serie > 1) {
			// end of serie
			$longest_serie_loose[0][] = $player_list[0][$i];
			$longest_serie_loose[1][] = $player_list[1][$i];
			$longest_serie_loose[2][] = $serie;
			$longest_serie_loose[3][] = $start_hand;
			$longest_serie_loose[4][] = $end_hand;
			$longest_serie_loose[5][] = max(0,$hand_cash[$player_list[0][$i]-1][$start_hand-1]) - max(0,$hand_cash[$player_list[0][$i]-1][$end_hand]);
			$serie = 1;
			$amount = 0;
		}
	}

	if(!empty($longest_serie_loose)) {
		array_multisort(
			$longest_serie_loose[2],SORT_DESC,
			$longest_serie_loose[5],SORT_DESC,
			$longest_serie_loose[0],
			$longest_serie_loose[1],
			$longest_serie_loose[3],
			$longest_serie_loose[4]
		);
	}

	return $longest_serie_loose;

}


function get_most_raise($db,$player_list_seat,$player_list_name,$played_hands,$uniqueGameID) {

	// most_raise:
	// 0 --> seat
	// 1 --> player name
	// 2 --> bet/raise count total
	// 3 --> bet/raise count hands
	// 4 --> bet/raise count hands rel.

	$query = "
		SELECT Seat, HandID, BeRo, Amount, Action
		FROM (
			SELECT Seat
			FROM player
			WHERE UniqueGameID=".$uniqueGameID."
		) NATURAL JOIN (
			SELECT HandID, BeRo, Player as Seat, Amount,Action
			FROM action
			WHERE (Action='bets' OR Action='is all in with') AND Amount NOT NULL AND UniqueGameID=".$uniqueGameID."
			ORDER BY ActionID
		)";
	$query = $db->prepare($query);
	$query->execute();
	$most_raise = array();
	$most_raise[0] = $player_list_seat;
	$most_raise[1] = $player_list_name;
	$most_raise[2] = array();
	$most_raise[3] = array();
	$most_raise[4] = array();
	$most_raise_found_hand = array();
	$oldHandID = -1;
	$oldAmount = -1;
	$aborted = false;
	for($i=0;$i<count($player_list_seat);$i++) {
		$most_raise[2][$i] = 0;
		$most_raise[3][$i] = 0;
		$most_raise[4][$i] = 0;
	}
	while($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row['HandID']) & $row['HandID']>0 & is_numeric($row['BeRo']) & $row['BeRo']>=0 & $row['BeRo']<=4 & is_numeric($row['Amount']) & $row['Amount']>=0 & is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10) {
			if($row['HandID'] != $oldHandID) {
				for($i=0;$i<count($player_list_seat);$i++) {
					$most_raise_found_hand[$i] = false;
				}
			}
			if($row['Action']=='bets' || ($row['Action']=='is all in with' && $row['Amount']>$oldAmount ) ) {
				$most_raise[2][$row['Seat']-1]++;
				if(!$most_raise_found_hand[$row['Seat']-1]) {
					$most_raise[3][$row['Seat']-1]++;
					$most_raise_found_hand[$row['Seat']-1] = true;
				}
			}
			$oldAmount = $row['Amount'];
			$oldHandID = $row['HandID'];
		}
	}
	
	for($i=0;$i<count($player_list_seat);$i++) {
		if($played_hands[$i]>0) $most_raise[4][$i] = $most_raise[3][$i]/$played_hands[$i]*100;
		else $most_raise[4][$i] = 0;
	}

	array_multisort(
		$most_raise[2],SORT_DESC,
		$most_raise[4],SORT_DESC,
		$most_raise[3],SORT_DESC,
		$most_raise[0],
		$most_raise[1]
	);

	if($aborted) return array();
	return $most_raise;
}

function get_most_all_in($db,$uniqueGameID,$played_hands,$regex) {
	
	// most_all_in
	// 0 --> seat
	// 1 --> player name
	// 2 --> total count
	// 3 --> total count relative
	// 4 --> in preflop
	// 5 --> in first 5 hands
	// 6 --> cnt won
	$most_all_in = array();

	$query = "
		SELECT Player, Seat, Count, Count_Preflop, Count_5_Hands, Count_Won
		FROM (
			SELECT *
			FROM (
				SELECT *
				FROM (
					SELECT *
					FROM (
						SELECT Player, Seat
						FROM player
						WHERE UniqueGameID=".$uniqueGameID."
					) NATURAL LEFT JOIN (
						SELECT Player as Seat, count(*) as Count
						FROM action
						WHERE Action='is all in with' AND UniqueGameID=".$uniqueGameID."
						GROUP BY Player
					)
				) NATURAL LEFT JOIN (
					SELECT Player as Seat, count(*) as Count_Preflop
					FROM action
					WHERE Action='is all in with' AND BeRo=0 AND UniqueGameID=".$uniqueGameID."
					GROUP BY Player
				)
			) NATURAL LEFT JOIN (
				SELECT Player as Seat, count(*) as Count_5_Hands
				FROM action
				WHERE Action='is all in with' AND HandID<=5 AND UniqueGameID=".$uniqueGameID."
				GROUP BY Player
			)
		) NATURAL LEFT JOIN (
			SELECT Player as Seat, SUM(won) as Count_Won
			FROM (
				SELECT tmp2.HandID, tmp2.Player, tmp1.Amount>tmp2.Amount as won
				FROM (
					SELECT HandID, Player, Amount
					FROM action
					WHERE (Action='wins' OR Action='wins (side pot)') AND UniqueGameID=".$uniqueGameID."
				) as tmp1
				LEFT JOIN (
					SELECT HandID, Player, Amount
					FROM action
					WHERE Action='is all in with' AND UniqueGameID=".$uniqueGameID."
				) as tmp2 ON tmp1.HandID=tmp2.HandID AND tmp1.Player=tmp2.Player AND won=1
			)
			WHERE HandID NOT NULL
			GROUP BY Player
		)
		ORDER BY Count DESC, Count_Won DESC, Count_Preflop, Count_5_Hands";
	$query = $db->prepare($query);
	$query->execute();
	while($row = $query->fetch(PDO::FETCH_ASSOC)) {
		if(is_numeric($row['Seat']) & $row['Seat']>0 & $row['Seat']<=10) {
			$most_all_in[0][] = $row['Seat'];
            $most_all_in[1][] = replace_spec_char($row['Player']);
			$count = $row['Count'];
			if(!($count>0)) $count = 0;
			$count_preflop = $row['Count_Preflop'];
			if(!($count_preflop>0)) $count_preflop = 0;
			$count_5_hands = $row['Count_5_Hands'];
			if(!($count_5_hands>0)) $count_5_hands = 0;
			$count_won = $row['Count_Won'];
			if(!($count_won>0)) $count_won = 0;
                        // workaround for all in with blinds
                        $query_1 = "
                            SELECT *, RoundStartCash-BlindAmount as Amount_Diff
                            FROM (
                                SELECT HandID, Amount as BlindAmount
                                FROM Action 
                                WHERE Player=".$row['Seat']." AND UniqueGameID=".$uniqueGameID." AND (Action='posts small blind' OR Action='posts big blind')
                            ) NATURAL LEFT JOIN (
                                SELECT Seat_".$row['Seat']."_Cash as RoundStartCash, HandID
                                FROM Hand
                                WHERE UniqueGameID=".$uniqueGameID."
                            ) NATURAL LEFT JOIN (
                                SELECT HandID, Action
                                FROM Action
                                WHERE Player=".$row['Seat']." AND (Action='wins' OR Action='wins (side pot)') AND UniqueGameID=".$uniqueGameID."
                            )";
                        $query_1 = $db->prepare($query_1);
                        $query_1->execute();
                        while($row_1 = $query_1->fetch(PDO::FETCH_ASSOC)) {
                            if(is_numeric($row_1['HandID']) & $row_1['HandID']>0 & is_numeric($row_1['Amount_Diff'])) {
                                if($row_1['Amount_Diff']==0) {
                                    $count++;
                                    $count_preflop++;
                                    if($row_1['HandID']<=5) $count_5_hands++;
                                    if(!is_null($row_1['Action'])) $count_won++;
                                }
                            }
                        }
			$most_all_in[2][] = $count;
			if($played_hands[$row['Seat']-1]>0) $most_all_in[3][] = $count/$played_hands[$row['Seat']-1]*100;
			else $most_all_in[3][] = 0;
			$most_all_in[4][] = $count_preflop;
			$most_all_in[5][] = $count_5_hands;
			$most_all_in[6][] = $count_won;
		}
	}

	return $most_all_in;

}

function replace_spec_char($string) {
    
    $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
    $string = preg_replace('/[?]/','&#063;',$string);
    $string = preg_replace('/[:]/','&#058;',$string);
    $string = preg_replace('/[=]/','&#061;',$string);
    $string = preg_replace('/[{]/','&#123;',$string);
    $string = preg_replace('/[}]/','&#125;',$string);
    
    return $string;
}

function int2string($int) {
	
	switch ($int%13) {
		case 0: $string = "2"; break;
		case 1: $string = "3"; break;
		case 2: $string = "4"; break;
		case 3: $string = "5"; break;
		case 4: $string = "6"; break;
		case 5: $string = "7"; break;
		case 6: $string = "8"; break;
		case 7: $string = "9"; break;
		case 8: $string = "T"; break;
		case 9: $string = "J"; break;
		case 10: $string = "Q"; break;
		case 11: $string = "K"; break;
		case 12: $string = "A"; break;
		default: $string = ""; break;
	}
	
	switch (floor($int/13)) {
		case 0: $string = $string."<span style='font-size:13px'>&diams;</span>"; break;
		case 1: $string = $string."<span style='font-size:13px'>&hearts;</span>"; break;
		case 2: $string = $string."<span style='font-size:13px'>&spades;</span>"; break;
		case 3: $string = $string."<span style='font-size:13px'>&clubs;</span>"; break;
	}
	
	return $string;
}

?>