<?php
require_once "config.php";

// Destroy all session data and redirect to login
$_SESSION = [];
session_destroy();

header("Location: login.php");
exit;
