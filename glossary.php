<?php

$lang = isset($_SESSION['cmir']['lang'])? $_SESSION['cmir']['lang'] : 'es';

if($lang === 'en'){
        $club_name = 'Team name' ;
        $match_id = 'Match_id (optional)';
        $compromised = 'Compromised players';
        $not_found = 'not found';
        $not_matches_open = 'has no matches open';
        $id_match_not_found = 'Match with id loaded not found';
        $empty_players = 'Not players registered, or match is not open anymore';
        $not_players = 'There are no open matches, or those that are have no registered players';
        
}elseif($lang === 'es'){

    
        $club_name= 'Nombre del club' ;   
        $match_id = 'Id del match (opcional)' ;
        $compromised = 'Jugadores comprometidos' ;
        $not_found = 'no encontrado';   
        $not_matches_open = 'no tiene matches abiertos' ;
        $id_match_not_found = 'No se encuentra un match con ese id';
        $empty_players = 'No hay jugadores inscriptos o el match ya no está abierto';
        $not_players = 'No hay matches abiertos, o los que lo están no tienen jugadores inscriptos' ;
        
}
