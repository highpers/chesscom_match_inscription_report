<?php

$lang = isset($_SESSION['cmir']['lang'])? $_SESSION['cmir']['lang'] : 'es';

if($lang === 'en'){
        $club_name = 'Team name' ;
        $match_id = 'Match_id (optional)';
        $compromisedTitle = 'Additional players';
        $not_found = 'not found';
        $not_matches_open = 'has no matches open';
        $id_match_not_found = 'Match with id loaded not found';
        $empty_players = 'Not players registered, or match is not open anymore';
        $not_players = 'There are no open matches, or those that are have no registered players';
        $match_bad_or_not_belongs = 'The match with the provided id does not belong to the team, is not open or does not exist';
        $max_time_out_allowed = 'Maximum percentage of TO allowed';
        $already_registered = 'already registered in the match';
        $boards_eq = 'Boards even';
        $boards_dis = 'Boards with disadvantage';
        $boards_adv = 'Boards with advantage';
        $proms = 'Average rating';
        $total_boards = 'Total boards registered';
        $registered_match = 'Registered';
        $our = 'our';
        $opponent = 'opponent';
        $problematic_compromised_label = 'Additional players observed';
        $high_TO_label = 'Players with excesive Time Outs';
        $including_compromised = 'Including additional players';
        $max_rating_allowed = 'Rating limit (0: no limit)';
        $diff_label = 'Differences';
        $new_report = 'New report';
        $left = 'Left';
        $right = 'Right';
        $position_axis = 'Position axis';
        $with_compromised = 'Add. players' ;
        $goto_match = 'Go to match';
        $select = 'Select';
        $not_daily_rating = 'without daily rating' ;
        $xBoard = 'Board';
        $yRating = 'Rating';
        $oneXline = 'One per line';
        $applying_classic = 'Applying classic ratings';
        $classic_label = 'Classic rt.';


}elseif($lang === 'es'){
    
        $club_name= 'Nombre del club' ;   
        $match_id = 'Id del match (opcional)' ;
        $compromisedTitle = 'Jugadores adicionales' ;
        $not_found = 'no encontrado';   
        $not_matches_open = 'no tiene matches abiertos' ;
        $id_match_not_found = 'No se encuentra un match con ese id';
        $empty_players = 'No hay jugadores inscriptos o el match ya no está abierto';
        $not_players = 'No hay matches abiertos, o los que lo están no tienen jugadores inscriptos' ;
        $match_bad_or_not_belongs = 'El match con la identificación proporcionada no pertenece al equipo, no está abierto o no existe';
        $not_team_players = ' no tiene jugadores registrados en este match';
        $max_time_out_allowed = 'Máximo porcentaje de TO permitido';
        $already_registered = ' ya registrado en el match';
        $boards_eq = 'Tableros igualados';
        $boards_adv = 'Tableros con ventaja';
        $boards_dis = 'Tableros con desventaja';
        $proms = 'Promedio de rating';
        $total_boards = 'Total de tableros en juego';
        $registered_match = 'Inscriptos ';
        $our = 'propios';
        $opponent = 'adversario';
        $problematic_compromised_label = 'Jugadores adicionales observados';
        $high_TO_label = 'Jugadores con alto porcentaje de TO';
        $including_compromised = 'Incluyendo jugadores adicionales';
        $max_rating_allowed = 'Rating máximo (0: sin máximo)';
        $diff_label = 'Diferencias';
        $new_report = 'Nueva consulta';
        $left = 'Izquierda';
        $right = 'Derecha';
        $position_axis = 'Position del 2° eje Y';
        $with_compromised = "Jug. adic.";
        $goto_match = 'Ver match';
        $select = 'Seleccione';
        $not_daily_rating = 'no tiene rating de diarias';
        $xBoard = 'Tablero';
        $yRating = 'Rating';
        $oneXline = 'Uno por línea';
        $applying_classic = 'Aplicando ratings de standard';
        $classic_label = 'Rt. Standard';
        

}
