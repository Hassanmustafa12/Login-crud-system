<?php
/**
 * api/config.php
 * ---------------------------------------------------------
 * Same database as your main site, but this file is used by
 * the API only. No session_start() here — the API is
 * "stateless": every request proves who it is with a token
 * in the Authorization header instead of a cookie.
 * ---------------------------------------------------------
 */

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // allow calls from any frontend (tighten this in production)
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Browsers send an OPTIONS "preflight" request before real ones — just say OK
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host   = "localhost";
$dbname = "login_crud_system";
$dbuser = "root";
$dbpass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit;
}
