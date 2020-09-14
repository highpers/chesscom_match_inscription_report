<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <script language="javascript" type="text/javascript" src="vendor/flot-master/source/jquery.js"></script>
  <script src="js/funcs.js"></script>
  <title>Chess.com team inscription to match report</title>


  <!-- Custom styles for this template -->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <!-- Custom styles for this page -->
  <style>

    @font-face {
      font-family: olsen;
      src: url(fonts/OlsenTF-Regular.otf);
    }

    * {
      font-family: olsen;
    }

    .li-prob {
      margin-top: 0px;
      font-size: 0.88em;
    }

    .not-message {
      color: red;
      font-size: 1.2em;
    }

    #text-report {
      font-size: 0.89em;
      line-height: 166%;
    }

    .subtitle-info {
      margin-top:12px;
      margin-bottom: 0px;
      font-weight:bold;


    }

    #info_match {
      margin-top: -29px;
      font-size: 0.89em
    }
  </style>
</head>

<?php
session_start();
require('funcs.php');
require('glossary.php');


?>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">


    <div class="container-fluid" style="margin-top:28px ; line-height:198%">

      <div id="container-form" style="font-size:0.73em">
        <form method="post" id="form_team" onsubmit="return load_matches()" ;>
          <?= $club_name ?> &nbsp;
          <input required type="text" id="team" name="team" style="width:393px">
          <input type="submit">
        </form>
        <hr>

        </form>
      </div>

      <div id="info_match">
        <?php

        if (!empty($_POST)) {
          $team_name = $_POST['team_name'];
          $team_label = ucwords(str_replace('-', ' ', $team_name));

          $players_registered = false;
          $match_data = explode(':', $_POST['match_data']);
          $id_match = $match_data[0];
          $rival = ucwords(str_replace('-', ' ', $match_data[1]));

          $match_url = 'https://www.chess.com/club/matches/' . $id_match;

          $match_players_and_type = get_match_players_and_type($id_match, $team_name);

          // muestraArrayUobjeto($match_players_and_type, __FILE__, __LINE__, 0, 0);
          $match_players = $match_players_and_type['players'];
          if (empty($match_players)) {
            die($empty_players);
          }

          // here we have an array with both list of players. 

          echo "<br clear='all'><h5><a href='$match_url' target='blank'>$team_label vs. $rival</a></h5>";
          if (empty($match_players['we'])) {

            die("<br>'$team_label' $not_team_players");
          }
          if (empty($match_players['they'])) {

            die("<br>'$rival' $not_team_players'");
          }
          $match_type = $match_players_and_type['type'];

          if($match_type == '960'){
            /* we need:

            $boards_registered : array with classical rating we and them with classical and 960 rating, ordered by 960 rating
            if there are compromised - array including compromised with  960 and classical rating ordered by 960 rating

            */
            
            for($i = 0 ; $i < count($match_players['we']) ; ++$i){
              // find out classic rating

              if (empty($match_players['we'][$i]->rating)) { // may be a match with rating limits, then the api doesn't show rating of out of bounds player
                continue;
              }

              if ($match_players['we'][$i]->timeout_percent > $_POST['to_percent']) {
                $players_high_TO[] = $match_players['we'][$i]->username . ' (' . $match_players['we'][$i]->timeout_percent . ' %)';
              }

              $data_player = get_player_stats($match_players['we'][$i]->username);
              $match_players['we'][$i]->rating_classic = $data_player['rating'] ;


            }

            // We have our players, with both ratings ordered by username. Let's order by rating (960)
            $ordered_players_we = sort_object($match_players['we'] , 'rating' , 'int');
            // muestraArrayUobjeto($ordered_players_we, __FILE__, __LINE__, 0, 0);
            
            
            $boards_we_classic = $boards_we_960 = $ratings_we = array();
            $i = 1;
            foreach($ordered_players_we as $pl){
              $boards_we_classic[$i] = $pl->rating_classic;
              $boards_we_960[$i] = $pl->rating;
              // load same variable names as in classic match
              $ratings_we[] = $pl->rating;

              ++$i;
            }

            // muestraArrayUobjeto($boards_we_960 , __FILE__ , __LINE__ , 0 , 0);
            // muestraArrayUobjeto($boards_we_classic , __FILE__ , __LINE__ , 1 , 0);

            // same process for opponent

            for ($i = 0; $i < count($match_players['they']); ++$i) {

              if (empty($match_players['they'][$i]->rating)) { // may be a match with rating limits, then the api doesn't show rating of out of bounds player
                continue;
              }

              // find out classic rating
              $data_player = get_player_stats($match_players['they'][$i]->username);
                
              $match_players['they'][$i]->rating_classic = $data_player['rating'];
            }
          //  muestraArrayUobjeto($match_players['they'], __FILE__, __LINE__, 0, 0);

            // We have them players, with both ratings ordered by username. Let's order by rating (960)

            $ordered_players_they = sort_object($match_players['they'], 'rating', 'int');

            $boards_they_classic = $boards_they_960 = $ratings_they = array();
            $i = 1;

            // muestraArrayUobjeto($ordered_players_they , __FILE__ , __LINE__ , 1 , 0);
            foreach ($ordered_players_they as $pl) {
           
              $boards_they_classic[$i] = $pl->rating_classic;
              $boards_they_960[$i] = $pl->rating;

              // load same variable names as in classic match

              $ratings_they[] = $pl->rating;
              ++$i;
            }
          

          }else{ // classic match


            $ratings_we = $ratings_they = $players_high_TO = array();

            foreach ($match_players['we'] as $player) {

              if (empty($player->rating)) { // may be a match with rating limits, then the api doesn't show rating of out of bounds player
                continue;
              }
              if ($player->timeout_percent > $_POST['to_percent']) {
                $players_high_TO[] = $player->username . ' (' . $player->timeout_percent . ' %)';
              }
              $ratings_we[] = $player->rating;
            }

          }
          
          
          // $list_compromised = explode(PHP_EOL, trim($_POST['compromised'],PHP_EOL));
          $list_compromised_dirty = explode(PHP_EOL, $_POST['compromised']);
          
          $list_compromised = array();
          // clean empty items
        
          for($i=0 ; $i < count($list_compromised_dirty) ; ++$i){
            $list_compromised_dirty[$i] = str_replace("\r", "", $list_compromised_dirty[$i]);
            $list_compromised_dirty[$i] = str_replace("\n", "", $list_compromised_dirty[$i]);
          }

          for ($i = 0; $i < count($list_compromised_dirty); ++$i) {
            if (!empty($list_compromised_dirty[$i])) {
              $list_compromised[] = $list_compromised_dirty[$i];
            }
          }

          if (!empty($list_compromised)) {
            $ratings_compromised = $problematic_compromised = array();
            $i = 0;
            foreach ($list_compromised as $compromised) {
              // find out if player is alreaey registered in the match list
              $registered = false;

              foreach ($match_players['we'] as $user) {
                if (strtolower(trim($user->username)) == trim(strtolower($compromised))) {
                  $problematic_compromised[] = $compromised . ': ' . $already_registered;
                  $registered = true;
                  break;
                }
              }
              if ($registered) {
                continue;
              }
              ++$i;
              
              $data_compromised = get_player_stats(trim(strtolower($compromised)), $i);
               muestraArrayUobjeto($data_compromised , __FILE__ , __LINE__ , 0 , 0);
               $match_rating = $match_type == '960' ? $data_compromised['rating_960'] : $data_compromised['rating']; // if match is 960 consider correspondent rating
              if (is_null($data_compromised)) { //player not found
                $problematic_compromised[] = $compromised . ': ' . $not_found;
              } else {
                  if(empty($data_compromised['rating'])){ 
                  $problematic_compromised[] = $compromised. ': '.$not_daily_rating ;
                }else{
                  if(!empty($_POST['max_rating']) and $match_rating > $_POST['max_rating']) {
                  $problematic_compromised[] = $compromised . ': Rating ' . $match_rating;
                  continue;
                  }
                }
               
                
                if($match_type == '960'){
                  $ratings_compromised[] = $data_compromised['rating_960'];
                  $ratings_compromised_classic[] = $data_compromised['rating'];
                }else{
                  $ratings_compromised[] = $data_compromised['rating']; 
                }
                if ($data_compromised['to'] > $_POST['to_percent']) {
                  $problematic_compromised[] = $compromised . ': ' .  $data_compromised['to'] . ' % TO';
                }
              } 
              }

              // muestraArrayUobjeto($ratings_compromised_classic , __FILE__ , __LINE__ , 0 , 0);
              // muestraArrayUobjeto($ratings_compromised , __FILE__ , __LINE__ , 1 , 0);

            }
         
        if($match_type=='classic'){
          foreach ($match_players['they'] as $player) { 
            if (empty($player->rating)) {
              continue;
            }

            $ratings_they[] = $player->rating;
          }

          rsort($ratings_we);
          rsort($ratings_they);
         
        } 

          // calculate values to show

          $boards = min(count($ratings_we), count($ratings_they));
          // muestraArrayUobjeto($ratings_we, __FILE__, __LINE__, 1, 0);

var_dump($boards) ; 

          // slice registered to boards number
          $active_ratings_we = array_slice($ratings_we, 0, $boards);
          $active_ratings_they = array_slice($ratings_they, 0, $boards);

          $prom_we = array_sum($active_ratings_we) / $boards;
          $prom_they = array_sum($active_ratings_they) / $boards;

          $boards_advantage = $boards_disadvantage = $boards_equal = 0;

          $board_diffs = array();
          for ($i = 0; $i < $boards; ++$i) {

            if ($ratings_we[$i] > $ratings_they[$i]) {
              ++$boards_advantage;
            }
            if ($ratings_we[$i] < $ratings_they[$i]) {
              ++$boards_disadvantage;
            }
            if ($ratings_we[$i] == $ratings_they[$i]) {
              ++$boards_equal;
            }

            $board_diffs[] = $ratings_we[$i] - $ratings_they[$i];
          }

          if ($lang == 'es') {
            $prom_we_show = number_format($prom_we, 2, ',', '.');
            $prom_they_show = number_format($prom_they, 2, ',', '.');
          } else {
            $prom_we_show = number_format($prom_we, 2);
            $prom_they_show = number_format($prom_they, 2);
          }

          echo '<div id="text-report">';
          echo "$registered_match $our: " . count($ratings_we) . " // $registered_match $opponent: " . count($ratings_they);


          echo "<br>$total_boards: $boards<p>";
          echo $proms . ': ' . $prom_we_show . ' - ' . $prom_they_show . '<br>';
          echo $boards_adv . ': ' . $boards_advantage . '<br>';
          echo $boards_dis . ': ' . $boards_disadvantage . '<br>';
          echo $boards_eq . ': ' . $boards_equal . '<br>';


          if($match_type == '960'){ // show report based on classic ratings
           echo '<p class="subtitle-info">'.$applying_classic.'</p>';

           $active_classic_ratings_we = array_slice($boards_we_classic , 0 , $boards);
           $active_classic_ratings_they = array_slice($boards_they_classic , 0 , $boards);

           $classic_prom_we = array_sum($active_classic_ratings_we) / $boards;
           $classic_prom_they = array_sum($active_classic_ratings_they) / $boards;

            $boards_advantage_classic = $boards_disadvantage_classic = $boards_equal_classic = 0;

            $board_diffs_classic = array();
           for($i=1; $i <= $boards; ++$i){
              if ($boards_we_classic[$i] > $boards_they_classic[$i]) {
                ++$boards_advantage_classic;
              }
              if ($boards_we_classic[$i] < $boards_they_classic[$i]) {
                ++$boards_disadvantage_classic;
              }
              if ($boards_we_classic[$i] == $boards_they_classic[$i]) {
                ++$boards_equal_classic;
              }

              $board_diffs_classic[] = $boards_we_classic[$i] - $boards_they_classic[$i];
              
           }

            if ($lang == 'es') {
              $prom_we_show_classic = number_format($classic_prom_we, 2, ',', '.');
              $prom_they_show_classic = number_format($classic_prom_they, 2, ',', '.');
            } else {
              $prom_we_show_classic = number_format($prom_we_classic, 2);
              $prom_they_show_classic = number_format($prom_they_classic, 2);
            }


            echo $proms . ': ' . $prom_we_show_classic . ' - ' . $prom_they_show_classic . '<br>';
            echo $boards_adv . ': ' . $boards_advantage_classic . '<br>';
            echo $boards_dis . ': ' . $boards_disadvantage_classic . '<br>';
            echo $boards_eq . ': ' . $boards_equal_classic . '<br>';


          }


          if (!empty($players_high_TO)) {
            echo  '<p class="subtitle-info">' . $high_TO_label . '</p><ul">';
            foreach ($players_high_TO as $prob) {
              echo "<li class='li-prob'>$prob</li>";
            }
            echo '</ul>';
          }
 

          if (!empty($list_compromised)) {

            $ratings_with_compromised = array_merge($ratings_we, $ratings_compromised);
            
            // muestraArrayUobjeto($ratings_compromised , __FILE__ , __LINE__ , 1 , 0);

            rsort($ratings_with_compromised);

            // calculate values to show including compromised

            $boards2 = min(count($ratings_with_compromised), count($ratings_they));

            // slice registered to boards number
            $active_ratings_we2 = array_slice($ratings_with_compromised, 0, $boards2);
            $active_ratings_they2 = array_slice($ratings_they, 0, $boards2);

            $prom_we2 = array_sum($active_ratings_we2) / $boards2;
            $prom_they2 = array_sum($active_ratings_they2) / $boards2;

            $boards_advantage2 = $boards_disadvantage2 = $boards_equal2 = 0;

            $board_diffs2 = array();

            for ($i = 0; $i < $boards2; ++$i) {

              if ($ratings_with_compromised[$i] > $ratings_they[$i]) {
                ++$boards_advantage2;
              }
              if ($ratings_with_compromised[$i] < $ratings_they[$i]) {
                ++$boards_disadvantage2;
              }
              if ($ratings_with_compromised[$i] == $ratings_they[$i]) {
                ++$boards_equal2;
              }

              $board_diffs2[] = $ratings_with_compromised[$i] - $ratings_they[$i];
            }

            if ($lang == 'es') {
              $prom_we2_show = number_format($prom_we2, 2, ',', '.');
              $prom_they2_show = number_format($prom_they2, 2, ',', '.');
            } else {
              $prom_we2_show = number_format($prom_we2, 2);
              $prom_they2_show = number_format($prom_they2, 2);
            }


            echo '<p class="subtitle-info">' . $including_compromised . '</p>';
            echo "$registered_match $our: " . count($ratings_with_compromised) . " // $registered_match $opponent: " . count($ratings_they);
            echo "<br>$total_boards: $boards2<p>";
            echo $proms . ': ' . $prom_we2_show . ' - ' . $prom_they2_show . '<br>';
            echo $boards_adv . ': ' . $boards_advantage2 . '<br>';
            echo $boards_dis . ': ' . $boards_disadvantage2 . '<br>';
            echo $boards_eq . ': ' . $boards_equal2 . '<br>';

            if (!empty($problematic_compromised)) {
              echo '<dt>' . $problematic_compromised_label . '</dt>';
              foreach ($problematic_compromised as $prob) {
                echo "<li class='liprob'>$prob</li>";
              }
              echo '</dl>';
            }
          }
/* 
we 960
they 960
dif 960
we classic
they classic
diff classic
diff jug. adic classic
we caballerìa 960
we caballerìa classic

*/
          // arrays for chart

          $we_ch = $they_ch = $diff_ch = $diff_ch_classic = $we_ch_classic = $they_ch_classic = '[';

          for ($i = 0; $i < $boards; ++$i) {
            $board = $i + 1;
            $diff_ch .= "[$board," . $board_diffs[$i] . '],';
            if($match_type== '960'){
              $diff_ch_classic .= "[$board," . $board_diffs_classic[$i] . '],';
            }
          }

          if ($match_type == '960') {
            foreach ($boards_we_classic as $board => $rating) {
              $we_ch_classic .= "[$board," . $rating . '],';
            }
            foreach ($boards_they_classic as $board => $rating) {
              $they_ch_classic .= "[$board," . $rating . '],';
            }
          }  

          foreach ($ratings_we as $i => $rating) {
            $board = $i + 1;
            $we_ch .= "[$board," . $rating . '],';
          }


          foreach ($ratings_they as $i => $rating) {
            $board = $i + 1;
            $they_ch .= "[$board," . $rating . '],';
          }
          $we_ch .= ']';
          $they_ch .= ']';
          $diff_ch .= ']';
          if ($match_type == '960') {
            $we_ch_classic .= ']' ;
            $they_ch_classic .= ']' ;
            $diff_ch_classic .= ']';
          } 
          if (!empty($list_compromised)) {

            $we_ch2 = $diff_ch2 = '[';

            foreach ($ratings_with_compromised as $i => $rating) {
              $board = $i + 1;
              $we_ch2 .= "[$board," . $ratings_with_compromised[$i] . '],';
            }

            for ($i = 0; $i < $boards2; ++$i) {
              $board = $i + 1;
              $diff_ch2 .= "[$board," . $board_diffs2[$i] . '],';
            }


            $we_ch2 .= ']';
            $diff_ch2 .= ']';
          } //


          include('chart.php');

          echo '</div><hr>';

        }

        ?>
      </div><!-- end #info_match -->

      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">

      </footer>
      <!-- End of Footer -->
      <!-- /.container-fluid -->

    </div>
  </div>
  <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

</body>

</html>