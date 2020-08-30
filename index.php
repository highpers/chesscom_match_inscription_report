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

    <!-- Sidebar -->


    <!-- Begin Page Content -->
    <div class="container-fluid" style="margin-top:28px">

      <form method="post">
        <?= $club_name ?> &nbsp;
        <input required type="text" class="form-control" id="team" name="team" style="width:393px">
        <br clear="all">
        <?= $match_id ?> &nbsp;
        <input type="number" class="form-control" id="id_match" name="match_id" style="width:273px" onChange="switch_cab(this.value)">
        <br clear="all">
        <textarea cols="24" rows="6" style="display:none;margin-bottom:25px" id="cab" name="compromised" placeholder="<?=$compromised?>"></textarea>
        
        <input type="submit" class="form-control" style="width:164px; background:#ccc">
        <br clear="all">
        <hr>
      </form>

      <?php


      if (!empty($_POST)) {
// muestraArrayUobjeto($_POST , __FILE__ , __LINE__ , 1 , 0);

// get registered matches

        date_default_timezone_set('America/Argentina/Buenos_Aires');
        

        $records = array(); // records to show in the report table


        $team_name = strtolower(htmlspecialchars($_POST['team'])); // team name for program search
        
        $team_label = ucwords(str_replace('-', ' ', $team_name)); // team name to show



        $team_name = str_replace(' ', '-', $team_name);
        replace_accents($team_name); // this avoids "not found" result when user search for name that contains something like "Atlético"

        if(!empty($_POST['match_id'])){ // only show 1 match info
          $match_id_given = $_POST['match_id'];
          if(!empty($_POST['compromised'])){
            $list_compromised = explode("\n",$_POST['compromised']);
            // muestraArrayUobjeto($list_compromised , __FILE__ , __LINE__ , 1 , 0);
          }

          $team_matches_ids = [$match_id_given];

        }else{
          $list_compromised = null ;
          $team_matches_ids = array();
          $match_id_given = 0 ;
          $team_matches = get_team_matches($team_name);
          if ($team_matches === false) {
            die('Team "' . $team_label . '" ' . $not_found . '.');
          } elseif ($team_matches === 0) {
            die('Team "' . $team_label . '" ' . $not_matches_open . '.');
          }
          //  muestraArrayUobjeto($team_matches , __FILE__ , __LINE__ , 1 , 0);

       if (count($team_matches)) {

        $players_registered = false;

          foreach($team_matches as $match){
             $match->num_id = substr($match->id, strrpos($match->id, '/') + 1);
             $match->rival = ucwords(str_replace('-', ' ', substr($match->opponent, strrpos($match->opponent, '/') + 1)));
          
          
muestraArrayUobjeto($match->rival , __FILE__ , __LINE__ , 1 , 0);
          $match_players = get_match_players($match->num_id, $team_name);
          // $match_players = array(); // desarrollo - sacar
          if(empty($match_players)){
            // If the match started recently, it may still be listed as "registered" in the club's match list.
            if($match_id_given){
              die($empty_players);
            }else{
              continue;
            }

          }else{
            $players_registered = true ;
          }
              
          muestraArrayUobjeto($match_players , __FILE__ , __LINE__ , 1 , 0);
         
// here we habe an array with both list of players. 

          
          //  0. armar una lista con los ratings 'we' y otra para los ratings 'them'
          $ratings_we = $ratings_them = array();

          foreach($match_players['we'] as $player){



          }
               
          /*

            1. si hay núm de match, ver si mandaron caballería y en tal caso:
               armar una lista de nombres de los inscriptos 'we' y una copia con caballería.
                Para cada caballero - verificar que no esté inscripto
                  Si no está, consultar a la api para obtener su rating, agregar ese rating a la lista con caballería
            
            2. Ordenar las tres (o dos) listas de ratings
            
            3. Armar una lista con las diferencias para cada posición de ellas.

            4. Graficar con las 4 listas (o 3 si no hay caballería).
          */



        }
        if(!$players_registered){
          die($not_players);
        }
      }else{ // only case to be here is bad match number. If not match number given and not matches, script died with $not_matches_open message previously
        die($id_match_not_found);
      }
    }


      ?>

        <h4><?= $team_label ?></h4>
        
        if (empty($records)) {
          die('There are no games with less than ' . $hours_max . ' hours to move');
        }

        $records_sorted = sort_list($records, 'time_remaining');

        $thead = $tfoot = ' <tr style="text-align:center;background:#999;color:white">
                    <th>Player</th>
                    <th>User type</th>
                    <th>Opponent team</th>
                    <th>Colour</th>
                    <th>Time remaining</td>
                    <th>Time over on</th>
                    <th>Watch game</th>
                  </tr>'

        ?>


        <div class="card shadow mb-4">
          <div class="card-header py-3">

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
                    <?= $thead ?>
                  </thead>
                  <tfoot>
                    <?= $tfoot ?>
                  </tfoot>
                  <tbody>
                    <?php
                    $even_color = '#ececef';
                    $bgcolor = $even_color;
                    foreach ($records_sorted as $game) {

                      $bgcolor = $bgcolor === $even_color ? 'white' : $even_color;

                      echo '<tr style="font-size:0.88em;background:' . $bgcolor . '">';
                      echo '<td><span style="display:none">' . $game['time_remaining'] . '</span>' .  $game['player'] . '</td>';
                      echo '<td>' . ucfirst($game['status']) . '</td>';
                      echo '<td>' . $game['rival'] . '</td>';
                      echo '<td style="text-align:center">' . ucfirst($game['colour']) . '</td>';
                      echo '<td style="text-align:center">' . $game['time_remaining'] . '</td>';
                      echo '<td style="text-align:center">' . $game['TO_moment'] . '</td>';
                      echo '<td style="text-align:center"><a href="' . $game['url'] . '" target="_blank"><img src="board.png" style="width:28px;" title="Watch game"></td>';

                      echo '</tr>';
                    }
                    ?>

                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <footer class="sticky-footer bg-white">
      <div class="container my-auto">

      </div>
    </footer>
    <!-- End of Footer -->

  </div>
  <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  <p class="mb-4" style="font-size:0.7em;margin:12px">DataTables is a third party plugin that is used to generate the demo table below. For more information about DataTables, please visit the <a target="_blank" href="https://datatables.net">official DataTables documentation</a>.</p>
<?php  } ?>


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