<?php
session_start();

$_SESSION['cmir']['lang'] = 'es';

header('location: ../index.php');
