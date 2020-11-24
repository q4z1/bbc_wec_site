<?php
namespace App\Http\Controllers;

use App\Models\Game;
use \PDO;
use Illuminate\Http\Request;

class LogFileController extends Controller
{
    private $pdo = null;
    private $game_id;

	public function process_log($url){
		$pdb = $this->fetch_database($url);
		if(!file_exists("/home/www/pokerth/log_file_analysis/upload/$pdb") && !file_exists("../storage/app/pdb/$pdb")){
			return false;
		}elseif(file_exists("/home/www/pokerth/log_file_analysis/upload/$pdb") && !file_exists("../storage/app/pdb/$pdb")){
			copy("/home/www/pokerth/log_file_analysis/upload/$pdb", "../storage/app/pdb/$pdb");
		}elseif(!file_exists("../storage/app/pdb/$pdb")){
			return false;
		}
		$this->pdo = new PDO("sqlite:../storage/app/pdb/".$pdb);
		// sqlite error handling necessary?

		$player_list = $this->get_player_list();
		$hand_cash = $this->get_hand_cash();
		$played_hands = $this->get_played_hands($player_list[3]);
		$best_hands = $this->get_best_hands();
		$most_wins = $this->get_most_wins($hand_cash, $played_hands[2]);
		$highest_win = $this->get_highest_win($hand_cash);
		$longest_wins = $this->get_longest_series_win($hand_cash);
		$longest_losses = $this->get_longest_series_loose($player_list,$hand_cash);
		$most_raises = $this->get_most_raise($player_list[0],$player_list[1],$played_hands[2]);	
		$most_all_in = $this->get_most_all_in($played_hands[2]);
		$pot_size = $this->get_pot_size();
        list($result_table, $hands) = $this->fetch_result_table_hands();
        
		$game = array(
			"result" => $result_table,
			"player_list" => $player_list,
			"most hands played" => $played_hands,
			"best hands" => $best_hands,
			"most wins" => $most_wins,
			"highest wins" => $highest_win,
			"longest series of wins" => $longest_wins,
			"longest series of losses" => $longest_losses,
			"most bet/raise" => $most_raises,
			"most all in" => $most_all_in,
			"hand_cash" => $hand_cash,
			"pot_size" => $pot_size,
			"pdb" => $pdb
		);
		return $game;
	}
	
    private function get_pot_size(){
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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

    private function get_most_all_in($played_hands) {
	
		// most_all_in
		// 0 --> seat
		// 1 --> player name
		// 2 --> total count
		// 3 --> total count relative
		// 4 --> in preflop
		// 5 --> in first 5 hands
		// 6 --> cnt won
		$regex = '/[<>:&#"{}=?\']/';
		$most_all_in = array();
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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
				$most_all_in[1][] = $this->replace_spec_char($row['Player']);
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
	
    private function get_most_raise($player_list_seat,$player_list_name,$played_hands) {

		// most_raise:
		// 0 --> seat
		// 1 --> player name
		// 2 --> bet/raise count total
		// 3 --> bet/raise count hands
		// 4 --> bet/raise count hands rel.
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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

    private function get_longest_series_loose($player_list,$hand_cash) {

		// longest serie loose:
		// 0 --> seat
		// 1 --> player name
		// 2 --> serie length
		// 3 --> start hand
		// 4 --> end hand
		// 5 --> amount
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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

    private function get_highest_win($hand_cash) {

		// most_wins
		// 0 --> seat
		// 1 --> player names
		// 2 --> hand
		// 3 --> side pot?
		// 4 --> amount
		$regex = '/[<>:&#"{}=?\']/';
		$db = $this->pdo;
		$count = 10;
		$uniqueGameID = $this->game_id;
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
									$highest_win[1][] = $this->replace_spec_char($row['Player']);
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

    private function get_longest_series_win($hand_cash) {

		// longest serie win:
		// 0 --> seat
		// 1 --> player name
		// 2 --> serie length
		// 3 --> start hand
		// 4 --> end hand
		// 5 --> amount
		$regex = '/[<>:&#"{}=?\']/';
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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
							$tmp_data = array($row['HandID'],$row['Seat'],$this->replace_spec_char($row['Player']),$amount_tmp);
	
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

    private function get_hand_cash() {
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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
	
	private function fetch_result_table_hands(){
		$query = "SELECT a.Player as Seat, a.HandID, p.Player, h.*, w.Player as winner, wp.Player as winnername
		FROM Action a 
		LEFT JOIN Player p ON (a.Player = p.Seat and a.UniqueGameID = p.UniqueGameID)
		LEFT JOIN Hand h ON (h.HandID = a.HandID and h.UniqueGameID = a.UniqueGameID)
		LEFT JOIN Action w ON (a.HandID = w.HandID and w.Action IN('wins', 'wins the game'))
		LEFT JOIN Player wp ON (w.Player = wp.Seat and wp.UniqueGameID = p.UniqueGameID)
		WHERE a.Action IN('wins game' ,'sits out') AND a.UniqueGameID= ".$this->game_id."
		ORDER BY a.ActionID DESC;";
		$query = $this->pdo->prepare($query);
		$query->execute();
		$game = [];
		while($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$game[] = $row;
		}
		$result_table = array();
		foreach($game as $i => $p){
			$result_table[$i+1] = array(
				"player" => $p['Player'],
				"hand" => $p['HandID'],
				"eleminatedBy" => $p['winnername']
			);
		}

		$hands = [];
		$query = $this->pdo->prepare("SELECT * FROM Hand");
		$query->execute();
		$game = [];
		while($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$hands[] = $row;
        }
        return [$result_table, $hands];
	}

	private function get_played_hands($player_list_hands) {

		$regex = '/[<>:&#"{}=?\']/';
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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
				$played_hands[1][] = $this->replace_spec_char($row_1['Player']);
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
	
	private function get_best_hands() {

		// best_hands:
		// 0 --> seat
		// 1 --> player name
		// 2 --> hand text
		// 3 --> hand id
		// 4 --> amount

		$regex = '/[<>:&#"{}=?\']/';
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
		$count = 10;
		$hand_cash = $this->get_hand_cash();
	
		$query_1 = "";
		$first_player_found = false;
		for($i=1;$i<=10;$i++) {
			$query_0 = $db->prepare("SELECT Player FROM player WHERE Seat=".$i." AND UniqueGameID=".$uniqueGameID);
			$query_0->execute();
			if($row_0 = $query_0->fetch(PDO::FETCH_ASSOC)) {
				if($first_player_found) $query_1 = $query_1." UNION ";
				else $first_player_found = true;
	
							$query_1 = $query_1."SELECT \"".$this->replace_spec_char($row_0['Player'])."\" as Player, ".$i." as Seat, HandID, Seat_".$i."_Hand_int as Seat_Hand_int, Seat_".$i."_Hand_text as Seat_Hand_text FROM hand WHERE Seat_Hand_text NOT NULL AND UniqueGameID=".$uniqueGameID;
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

    private function fetch_database($url){
		$parts = parse_url($url);
        parse_str($parts['query'], $query);
		$this->game_id = $query['UniqueGameID'];
		return $query['ID'] . '.pdb';
	}

    private function get_player_list() {

		// player_list:
		// 0 --> seat
		// 1 --> player name
		// 2 --> ranking
		// 3 --> max hand id
		// 4 --> start cash last hand
		// 5 --> player sits out
		// 6 --> player wins game
		// 7 --> eliminated by (player name) / winner: winning hand
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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
						$player_list[1][$seat_ctr] = $this->replace_spec_char($row['Player']);
						$player_list[3][$seat_ctr] = $row['HandID'];
						$player_list[4][$seat_ctr] = $row['LastCash'];
						$player_list[5][$seat_ctr] = 0;
						$player_list[6][$seat_ctr] = 0;
						if(!is_null($row['WinnerPlayer'])) {
							if(is_numeric($row['Eliminated']) & $row['Eliminated']==1) {
								$player_list[7][$seat_ctr][] = $this->replace_spec_char($row['WinnerPlayer']);
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
							$player_list[7][$seat_ctr-1][] = $this->replace_spec_char($row['WinnerPlayer']);
						}
					}
				} else {
					// unkown player - only seat known
					$player_list[0][$seat_ctr] = $row['Seat'];
					$player_list[1][$seat_ctr] = $this->replace_spec_char($row['Player']);
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
							$player_list[7][$row['Seat']-1][0] = "[".$this->int2string($row_1['Card_1']).",".$this->int2string($row_1['Card_2'])."] ".substr($hand_text,0,$end_text);
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


    private function replace_spec_char($string) {
    
		$string = htmlentities($string, ENT_QUOTES, 'UTF-8');
		$string = preg_replace('/[?]/','&#063;',$string);
		$string = preg_replace('/[:]/','&#058;',$string);
		$string = preg_replace('/[=]/','&#061;',$string);
		$string = preg_replace('/[{]/','&#123;',$string);
		$string = preg_replace('/[}]/','&#125;',$string);
		
		return $string;
	}
	
    private function int2string($int) {
		
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

    private function get_most_wins($hand_cash, $played_hands) {
		// $db,$hand_cash,$played_hands[2],$uniqueGameID,$regex
		$db = $this->pdo;
		$uniqueGameID = $this->game_id;
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
							$most_wins[1][] = $this->replace_spec_char($row_1['Player']);
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
}