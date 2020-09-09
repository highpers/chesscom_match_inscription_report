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
  <link href="css/sb-admin-2.css" rel="stylesheet">

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

          $match_data = explode(':', $_POST['match_data']);
          $id_match = $match_data[0];
          $rival = ucwords(str_replace('-', ' ', $match_data[1]));

          $match_url = 'https://www.chess.com/club/matches/' . $id_match;
          
          $match_players = get_match_players($id_match, $team_name);

          if (empty($match_players)) {

            die($empty_players);
          } else {
            $players_registered = true;
          }

          // here we have an array with both list of players. 

          echo "<br clear='all'><h5><a href='$match_url' target='blank'>$team_label vs. $rival</a></h5>";
          $both_have = true;
          if (empty($match_players['we'])) {

            echo "<br>'$team_label' $not_team_players";

            $both_have = false;
          }
          if (empty($match_players['they'])) {

            echo "<br>'$rival' $not_team_players'";

            $both_have = false;
          }

          $ratings_we = $ratings_they = $players_high_TO = $ratings_compromised = $problematic_compromised = array();
  
          foreach ($match_players['we'] as $player) {

            if (empty($player->rating)) { // may be a match with rating limits, then the api doesn't show rating of out of bounds player
              continue;
            }
            if ($player->timeout_percent > $_POST['to_percent']) {
              $players_high_TO[] = $player->username . ' (' . $player->timeout_percent . ' %)';
            }
            $ratings_we[] = $player->rating;
          }

          if (!empty($list_compromised)) {
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
              if (is_null($data_compromised)) { //player not found
                $problematic_compromised[] = $compromised . ': ' . $not_found;
              } else {
                if(empty($data_compromised['rating'])){ 
                  $problematic_compromised[] = $compromised. ': '.$not_daily_rating ;
                }else{
                if (!empty($_POST['max_rating']) and $data_compromised['rating'] > $_POST['max_rating']) {

                  $problematic_compromised[] = $compromised . ': Rating ' . $data_compromised['rating'];
                  continue;
                }
                $ratings_compromised[] = $data_compromised['rating'];
                if ($data_compromised['to'] > $_POST['to_percent']) {
                  $problematic_compromised[] = $compromised . ': ' .  $data_compromised['to'] . ' % TO';
                }
              } 
              }
            }
          }

          foreach ($match_players['they'] as $player) {
            if (empty($player->rating)) {
              continue;
            }

            $ratings_they[] = $player->rating;
          }

          rsort($ratings_we);
          rsort($ratings_they);


          // calculate values to show

          $boards = min(count($ratings_we), count($ratings_they));

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


          if (!empty($players_high_TO)) {
            echo  '<p class="subtitle-info">' . $high_TO_label . '</p><ul">';
            foreach ($players_high_TO as $prob) {
              echo "<li class='li-prob'>$prob</li>";
            }
            echo '</ul>';
          }
          if (!empty($list_compromised)) {
            $ratings_with_compromised = array_merge($ratings_we, $ratings_compromised);
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

          // arrays for chart

          $we_ch = $they_ch = $diff_ch = '[';

          for ($i = 0; $i < $boards; ++$i) {
            $board = $i + 1;
            $diff_ch .= "[$board," . $board_diffs[$i] . '],';
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