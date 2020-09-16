<?php

function curl_get_contents($url)
{

	$output = @file_get_contents($url);
	return $output; // dejo esto por el tema de que en localhost o windows puede fallar

	// Initiate the curl session
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

	// Set the URL
	curl_setopt($ch, CURLOPT_URL, $url);
	// Removes the headers from the output
	curl_setopt($ch, CURLOPT_HEADER, 0);
	// Return the output instead of displaying it directly
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1');

	// Execute the curl session
	$output = curl_exec($ch);
	// Close the curl session
	curl_close($ch);
	// Return the output as a variable

	return $output;
}

function get_player_stats($user)
{
	$data = curl_get_contents('https://api.chess.com/pub/player/' . $user . '/stats');
	if (empty($data or strpos($data, 'not found'))) { // user name not found

		return null;
	}
	

	$stats = json_decode($data);

	if(empty($stats->chess_daily)){
		$datos['rating'] = false; 
	}else{
		$datos['rating'] = $stats->chess_daily->last->rating;
	}
	if(empty($stats->chess960_daily)){
		$datos['rating_960'] = false ;
	}else{
		$datos['rating_960'] = $stats->chess960_daily->last->rating;
	}	
	
	$datos['to'] = $stats->chess_daily->record->timeout_percent;
		

	return $datos;
}

function get_team_matches(string $team, int $id_match = 0)
{

	$data = curl_get_contents('https://api.chess.com/pub/club/' . $team . '/matches');

	if (empty($data)) { // team name not found

		return false;

	} else {
		$matches = json_decode(str_replace('@', '', $data));
		if (isset($matches->code) and $matches->code == 0) { // club not found
			return false;
		}

		if (count($matches->registered)) {
			if ($id_match) {
				return $matches->registered;
			}
			return $matches->registered;
		} else { // team name found but 0 matches in progress
			return 0;
		}
	}
}


function get_match_players_and_type(int $id_match, string $team)
{

	$data = curl_get_contents('https://api.chess.com/pub/match/' . $id_match);

	if ($data === FALSE) { // team name not found

		return false;
	} else {

		$match_data = json_decode($data);
		if ($match_data->status != 'registration') {
			
			// wrong number or match not open anymore, 

			return false;
		}

		// muestraArrayUobjeto($match_data , __FILE__ , __LINE__ , 1 , 0);
		// find out what game team number we're working with
		$our_team_num = strpos($match_data->teams->team1->url, $team) ? 'team1' : 'team2';
		$them_team_num = $our_team_num == 'team1' ? 'team2' : 'team1';



		// take only players with game in progress
		$players_registered['we'] = $match_data->teams->$our_team_num->players;
		$players_registered['they'] = $match_data->teams->$them_team_num->players;
	}
	
		$players_and_type['players'] = $players_registered;

		$type = strpos($match_data->settings->rules, '960') ? '960' : 'classic';

		$players_and_type['type'] = $type;
	return $players_and_type;
}


/**
 * Get the array of team games where its players must make next move
 * 
 * @param string $player
 * @param int $hours_max
 * @param array $team_matches
 * 
 * return array()
 * 
 */


function get_games_with_moveby(string $player, string $team, int $hours_max, array $team_matches, $rival)
{


	$data = curl_get_contents('https://api.chess.com/pub/player/' . $player . '/games');


	if ($data === FALSE) { // team name not found

		return false;
	} else {
		$player_games = json_decode($data);


		$record_list = array();

		foreach ($player_games->games as $game) {
			if (empty($game->match)) { // the game doesn't belong to any match
				continue;
			}


			if ($game->move_by) { // it is player's turn and

				// find out if this match is team's
				$id_match = substr($game->match, strrpos($game->match, '/') + 1);

				if (!in_array($id_match, $team_matches)) { // match doesn't belong to the team
					continue;
				}

				$time_over = get_time_info($game->move_by);

				if ($time_over['hours'] >= $hours_max) {
					continue;
				} else {
					// collect data to report

					$record['TO_moment'] = $time_over['TO_moment'];
					$record['time_remaining'] = $time_over['hours'] . ':' . $time_over['minutes'] . ':' . $time_over['seconds'];
					$record['colour'] = strpos($game->white, '$player') ? 'White' : 'Black';
					$record['player'] = $player;
					$record['rival'] = $rival;

					$record_list[] = $record;
				}
			} else { // it is not player's turn
				continue;
			}
		}
	}
	return $record_list;
}

function get_time_info(int $moveBy)
{

	$time_over_moment = new DateTime(date('Y-m-d H:i:s', $moveBy));
	$now = new DateTime(date('Y-m-d H:i:s'));
	$oDiff = $time_over_moment->diff($now);
	$result['hours'] = $oDiff->h + $oDiff->d * 24; // hours + days * 24
	$result['minutes'] = str_pad($oDiff->i, 2, '0', STR_PAD_LEFT);
	$result['seconds'] = str_pad($oDiff->s, 2, '0', STR_PAD_LEFT);
	$result['TO_moment'] = date('M d H:i:s', $moveBy);

	return $result;
}

function get_games_to_report(int $hours_max, string $board, string $player, $rival, $status)
{

	$result = array();

	$data = curl_get_contents($board);

	if ($data === FALSE) { // team name not found

		return false;
	} else {

		$board_info = json_decode($data);
		foreach ($board_info->games as $game) {
			if (empty($game->move_by)) {
				continue;
			}
			$colour = strpos($game->white, $player) ? 'white' : 'black';
			if ($colour == $game->turn) {
				$time_over = get_time_info($game->move_by);

				if ($time_over['hours'] < $hours_max) {

					$record['TO_moment'] = $time_over['TO_moment'];
					$record['time_remaining'] = str_pad($time_over['hours'], 2, '0', STR_PAD_LEFT) . ':' . $time_over['minutes'] . ':' . $time_over['seconds'];
					$record['colour'] = $colour;
					$record['player'] = $player;
					$record['status'] = $status;
					$record['rival'] = $rival;
					$record['url'] = $game->url;
					$result[] = $record;
				}
			}
		}
		return $result;
	}
}


function replace_accents(&$str)
{

	//Reemplazamos la A y a
	$str = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$str
	);

	//Reemplazamos la E y e
	$str = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$str
	);

	//Reemplazamos la I y i
	$str = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$str
	);

	//Reemplazamos la O y o
	$str = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$str
	);

	//Reemplazamos la U y u
	$str = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$str
	);

	//Reemplazamos la N, n, C y c
	$str = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$str
	);
}

function muestraArrayUObjeto($obj, $arch = '', $linea = '', $die = 0, $conDump = 0)
{

	echo "En archivo $arch - linea $linea ";
	echo '<pre>';
	if (!$conDump)
		print_r($obj);
	else
		var_dump($obj);
	echo '</pre>';

	if ($die)
		die();
}

function sort_object($list, $k_sort , $type = 'str')
{

	usort($list, function ($a, $b) use ($k_sort , $type) {

		if($type=='str'){
				return strcmp($b->$k_sort, $a->$k_sort)  ;
		}else{	
			return intcmp($b->$k_sort, $a->$k_sort)  ;
		}	
	});

	return $list;
}



function sort_array($list, $k_sort , $type = 'str')
{

	usort($list, function ($a, $b) use ($k_sort , $type) {
		if ($type == 'str') {
			return strcmp($b[$k_sort], $a[$k_sort]);
		}else{
			return intcmp($b[$k_sort], $a[$k_sort]);
		}	
	});

	return $list;
}

function intcmp($a, $b)
{
	return ($a - $b) ? ($a - $b) / abs($a - $b) : 0;
}