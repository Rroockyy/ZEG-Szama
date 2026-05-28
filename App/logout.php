<?php
session_start();

$_SESSION = [];

session_destroy();

header('Location: menu.php');
exit;
