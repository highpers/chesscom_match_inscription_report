<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Chesscom team matches moveby</title>

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">


  <!-- Custom styles for this template -->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <style>
    @font-face {
      font-family: olsen;
      src: url(fonts/OlsenTF-Regular.otf);
    }

    * {
      font-family: olsen;
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



      <?php


      if (!empty($_POST)) {

        // get registered matches



        $records = array(); // records to show in the report table


        $team_name = strtolower(htmlspecialchars($_POST['team'])); // team name for program search

        $team_label = ucwords(str_replace('-', ' ', $team_name)); // team name to show



        $team_name = str_replace(' ', '-', $team_name);
        replace_accents($team_name); // this avoids "not found" result when user search for name that contains something like "Atlético"

        if (!empty($_POST['match_id'])) { // only show 1 match info
          $match_id_given = $_POST['match_id'];
          if (!empty($_POST['compromised'])) {
            $list_compromised = explode("\n", $_POST['compromised']);
            // muestraArrayUobjeto($list_compromised , __FILE__ , __LINE__ , 1 , 0);
          }
        } else { // show all team's matches
          $list_compromised = null;
          $match_id_given = 0;
        }

        $team_matches = get_team_matches($team_name);
        // muestraArrayUobjeto($team_matches , __FILE__ , __LINE__ , 1 , 0);
        if ($team_matches === false) {
          die('Team "' . $team_label . '" ' . $not_found . '.');
        } elseif ($team_matches === 0) {
          die('Team "' . $team_label . '" ' . $not_matches_open . '.');
        }
        //  muestraArrayUobjeto($team_matches , __FILE__ , __LINE__ , 1 , 0);
        if (count($team_matches)) {

          // muestraArrayUobjeto($team_matches , __FILE__ , __LINE__ , 1 , 0);
          $players_registered = false;

          foreach ($team_matches as $match) {
            $match->num_id = substr($match->id, strrpos($match->id, '/') + 1);

            if ($match_id_given) {
              if ($match->num_id !== $match_id_given) { // not the match we need
                continue;
              }
            }
            $match_url = 'https://www.chess.com/club/matches/' . $match->num_id;
            $match->rival = ucwords(str_replace('-', ' ', substr($match->opponent, strrpos($match->opponent, '/') + 1)));


            // muestraArrayUobjeto($match->rival , __FILE__ , __LINE__ , 1 , 0);
            $match_players = get_match_players($match->num_id, $team_name);


            if (empty($match_players)) {

              if ($match_id_given) {
                die($empty_players);
              } else {
                continue;
              }
            } else {
              $players_registered = true;
            }

            // muestraArrayUobjeto($match_players , __FILE__ , __LINE__ , 1 , 0);

            // here we have an array with both list of players. 

            echo "<h5><a href='$match_url' target='blank'>$team_label vs. $match->rival</a></h5>";
            $both_have = true;
            if (empty($match_players['we'])) {

              echo "<br>'$team_label' $not_team_players";

              $both_have = false;
            }
            if (empty($match_players['they'])) {

              echo "<br>'$match->rival' $not_team_players'";

              $both_have = false;
            }

            if (!$both_have) {
              echo '<hr>';

              continue;
            }

            //  0. armar una lista con los ratings 'we' y otra para los ratings 'them'
            $ratings_we = $ratings_them = $players_high_TO = $ratings_compromised = $problematic_compromised = array();

            // muestraArrayUobjeto($match_players['we'], __FILE__, __LINE__, 0, 0);
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
              // muestraArrayUobjeto($list_compromised, __FILE__, __LINE__, 0, 0);
              // muestraArrayUobjeto($_POST , __FILE__ , __LINE__ , 1 , 0);
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
                $data_compromised = get_player_stats(trim($compromised));

                if (is_null($data_compromised)) { //player not found
                  $problematic_compromised[] = $compromised . ': ' . $not_found;
                } else {
                 
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

            foreach ($match_players['they'] as $player) {
              if (empty($player->rating)) {
                continue;
              }

              $ratings_they[] = $player->rating;
            }

            // muestraArrayUobjeto($ratings_we, __FILE__, __LINE__, 0, 0);
            // muestraArrayUobjeto($ratings_they, __FILE__, __LINE__, 0, 0);

            rsort($ratings_we);
            rsort($ratings_they);

            // muestraArrayUobjeto($players_high_TO, __FILE__, __LINE__, 0, 0);
            // muestraArrayUobjeto($ratings_we, __FILE__, __LINE__, 0, 0);
            // muestraArrayUobjeto($ratings_they, __FILE__, __LINE__, 0, 0);
            // muestraArrayUobjeto($ratings_compromised, __FILE__, __LINE__, 0, 0);
            // muestraArrayUobjeto($problematic_compromised, __FILE__, __LINE__, 0, 0);



            // calculate values to show

            $boards = min(count($ratings_we), count($ratings_they));

            // slice registered to boards number
            $active_ratings_we = array_slice($ratings_we, 0, $boards);
            $active_ratings_they = array_slice($ratings_they, 0, $boards);

            $prom_we = number_format(array_sum($active_ratings_we) / $boards, 2);
            $prom_they = number_format(array_sum($active_ratings_they) / $boards, 2);

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
            //  muestraArrayUobjeto($board_diffs , __FILE__ , __LINE__ , 0 , 0);


            // echo '<table><tr><td></td><td>'.$team_label.'</td><td>'.$match->rival.'</td><td>
            echo '<div style="font-size:0.78em">';
            echo "$registered_match $our: " . count($ratings_we) . " // $registered_match $opponent: " . count($ratings_they);
            echo "<br>$total_boards: $boards<p>";
            echo $proms . ': ' . $prom_we . ' - ' . $prom_they . '<br>';
            echo $boards_adv . ': ' . $boards_advantage . '<br>';
            echo $boards_dis . ': ' . $boards_disadvantage . '<br>';
            echo $boards_eq . ': ' . $boards_equal . '<br>';


            if (!empty($players_high_TO)) {
              echo '<dl><dt>' . $high_TO_label . '</dt>';
              foreach ($players_high_TO as $prob) {
                echo "<dd>$prob</dd>";
              }
              echo '</dl>';
            }
            if (!empty($list_compromised)) {
              $ratings_with_compromised = array_merge($ratings_we, $ratings_compromised);
              rsort($ratings_with_compromised);

              // calculate values to show including compromised

              $boards2 = min(count($ratings_with_compromised), count($ratings_they));

              // slice registered to boards number
              $active_ratings_we2 = array_slice($ratings_with_compromised, 0, $boards2);
              $active_ratings_they2 = array_slice($ratings_they, 0, $boards2);

              $prom_we2 = number_format(array_sum($active_ratings_we2) / $boards2, 2);
              $prom_they = number_format(array_sum($active_ratings_they) / $boards2, 2);

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


              echo '<span style="font-weight:bold">'.$including_compromised.'</span><br>';
              echo "$registered_match $our: " . count($ratings_with_compromised) . " // $registered_match $opponent: " . count($ratings_they);
              echo "<br>$total_boards: $boards2<p>";
              echo $proms . ': ' . $prom_we2 . ' - ' . $prom_they . '<br>';
              echo $boards_adv . ': ' . $boards_advantage2 . '<br>';
              echo $boards_dis . ': ' . $boards_disadvantage2 . '<br>';
              echo $boards_eq . ': ' . $boards_equal2 . '<br>';


              if (!empty($problematic_compromised)) {
                echo '<br><dl><dt>' . $problematic_compromised_label . '</dt>';
                foreach ($problematic_compromised as $prob) {
                  echo "<dd>$prob</dd>";
                }
                echo '</dl>';
              }
            }
            echo '</div><hr>';


            // falta: si el rating no está,pasamos por alto al jugador, en el caso que vi se trata de un over 1500 en match u1500
            // El promedio del equipo más numeroso se está calculando con la suma del total, hay que eliminar los que sobran. array_slice($arr , 0 , $boards);


          }
        }
        /*
Caballería del ca zodchy en match 1162298:
Ded-Banan
anna1705 
Uzhegov
tartufo_xadfad43c
professor2

Ded-Banan
anna1705  (ya inscripto)
Uzhegov
tartufo_xadfad43c (no existe)
professor2


            1. si hay núm de match, ver si mandaron caballería
           */




        /*
            
            y en tal caso:



               armar una lista de nombres de los inscriptos 'we' y una copia con caballería.
                Para cada caballero - verificar que no esté inscripto
                  Si no está, consultar a la api para obtener su rating, agregar ese rating a la lista con caballería
            
            2. Ordenar las tres (o dos) listas de ratings
            
            3. Armar una lista con las diferencias para cada posición de ellas.

            4. Graficar con las 4 listas (o 3 si no hay caballería).
          */

        /*if (!$players_registered) {
            if ($match_id_given) {
              die($match_bad_or_not_belongs);
            } else {
              die($not_players);
            }
          }
        }

*/

      ?>
        <hr>

      <?php  } ?>
      <div id="container-form" style="font-size:0.73em">
        <form method="post">
          <?= $club_name ?> &nbsp;
          <input required type="text" class="form-control" id="team" name="team" style="width:393px">
          <?= $match_id ?> &nbsp;
          <input type="number" class="form-control" id="id_match" name="match_id" style="width:284px" onblur="switch_cab(this.value)">
          <br clear="all">
          <textarea cols="24" rows="6" style="display:none;margin-bottom:25px;width:284px !important" id="cab" name="compromised" placeholder="<?= $compromised ?>" class="form-control"></textarea>
          <?= $max_time_out_allowed ?><input type="number" value="25" max="100" min="0" class="form-control" style="width:88px" name="to_percent"><br>
          <div id="rating_limit" style="display:none;margin-bottom:9px;">
            <?= $max_rating_allowed ?><input type="number" value="0" min="0" class="form-control" style="width:88px" name="max_rating" id="max_rating"></div>
          <input type="submit" class="form-control" style="width:104px; background:#ccc; font-size:0.98em" >
          <br clear="all">
          <hr>
        </form>
      </div>


      <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <footer class="sticky-footer bg-white">

    </footer>
    <!-- End of Footer -->

  </div>
  <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->


  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/funcs.js"></script>
  <!-- <script src="js/sb-admin-2.min.js"></script> -->

  <!-- Page level plugins -->
  <!-- <script src="vendor/datatables/jquery.dataTables.min.js"></script> -->
  <!-- <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script> -->

  <!-- Page level custom scripts -->
  <!-- <script src="js/demo/datatables-demo.js"></script> -->

</body>

</html>