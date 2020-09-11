<?php

session_start();
require('funcs.php');
require('glossary.php');


$team = $_POST['name'];

$team_name = trim(strtolower(htmlspecialchars($team))); // team name for program search

$team_label = ucwords(str_replace('-', ' ', $team_name)); // team name to show
$team_name = str_replace(' ', '-', $team_name);

replace_accents($team_name);

$team_matches = get_team_matches($team_name);
// muestraArrayUobjeto($team_matches);
if ($team_matches === false) {
    die("<br><span class='not-message'><span style='font-weight:bold'>$team_label</span> " . $not_found .'.</span><hr>');
} elseif ($team_matches === 0) {
    die("<br><span class='not-message'><span style='font-weight:bold'>$team_label</span> " . $not_matches_open . '.</span><hr>');
} else { // match form

?><style>
        .label_form {
            font-weight: bold;
        }

        input[type="radio"] {
            margin-top: -2px;
            vertical-align: middle;
        }
    </style>
<?php
    echo '<form id="match_form" method="post" >';
    echo '<input type="hidden" value="'.$team_name.'" name="team_name">';
    echo "<br><span class='label_form'>$select $opponent</span><br>";
    $checked = 'checked';
    foreach ($team_matches as $match) {
        // muestraArrayUobjeto($match , __FILE__ , __LINE__ , 1 , 0);
        $match->num_id = substr($match->id, strrpos($match->id, '/') + 1);
        $match_url = 'https://www.chess.com/club/matches/' . $match->num_id;
        $match_rival = substr($match->opponent, strrpos($match->opponent, '/')+1);
        $match->rival = ucwords(str_replace('-', ' ', $match_rival));

        echo '<input type="radio" name="match_data" ' . $checked . ' value='.$match->num_id.':'.$match_rival.'> &nbsp;<a class="radio_label" href="' . $match_url . '" title="' . $goto_match . '" target="_blank">' . $match->name . '</a><br>';
        $checked = '';
    }

        echo "<br><span class='label_form'>$compromisedTitle <span style='font-size:0.8em'>($oneXline)</span></span><br>"; 
        echo '<textarea cols="24" rows="4" style="margin.top:9px;margin-bottom:8px;width:284px !important" id="cab" name="compromised" class="form-control"></textarea><span class="label_form">'.$max_time_out_allowed.'</span><input type="number" value="25" max="100" min="0" class="form-control" style="width:88px" name="to_percent"><br>
        <span class="label_form">'.$max_rating_allowed. '</span><input type="number" value="0" min="0" class="form-control" style="width:88px" name="max_rating" id="max_rating"> <br clear="all">
        <input type="submit" class="form-control" style="width:104px; background:#ccc; font-size:0.98em">
        <br clear="all">';


    echo '</form>';
}
