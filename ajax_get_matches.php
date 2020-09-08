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

if ($team_matches === false) {
    die($team_label . '" ' . $not_found . '.');
} elseif ($team_matches === 0) {
    die($team_label . '" ' . $not_matches_open . '.');
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
    echo '<form id="match_form" >';

    echo "<br><span class='label_form'>$select $opponent</span><br>";
    $checked = 'checked';
    foreach ($team_matches as $match) {
        $match->num_id = substr($match->id, strrpos($match->id, '/') + 1);
        $match_url = 'https://www.chess.com/club/matches/' . $match->num_id;
        $match->rival = ucwords(str_replace('-', ' ', substr($match->opponent, strrpos($match->opponent, '/') + 1)));

        echo '<input type="radio" name="match_id" ' . $checked . ' value='.$match->num_id.'> &nbsp;<a class="radio_label" href="' . $match_url . '" title="' . $goto_match . '" target="_blank">' . $match->rival . '</a><br>';
        $checked = '';
    }
        echo "<span class='label_form'>$compromised</span><br>"; 
        echo '<textarea cols="24" rows="6" style="margin.top:9px;margin-bottom:8px;width:284px !important" id="cab" name="compromised" class="form-control"></textarea><span class="label_form">'.$max_time_out_allowed.'</span><input type="number" value="25" max="100" min="0" class="form-control" style="width:88px" name="to_percent"><br>
        <span class="label_form">'.$max_rating_allowed. '</span><input type="number" value="0" min="0" class="form-control" style="width:88px" name="max_rating" id="max_rating"> <br clear="all">
        <input type="submit" class="form-control" style="width:104px; background:#ccc; font-size:0.98em">
        <br clear="all">';


    echo '</form>';
}
