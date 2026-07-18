<?php
/**
 * config.php
 * ---------------------------------------------------------
 * This file connects PHP to the MySQL database using PDO.
 * Every other page includes this file first.
 * Change the values below to match your own MySQL setup.
 * ---------------------------------------------------------
 */

$host   = "localhost";        // usually "localhost" on XAMPP/WAMP/Laragon
$dbname = "login_crud_system";
$dbuser = "root";             // default XAMPP username
$dbpass = "";                 // default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    // Throw real exceptions on errors instead of silent failures
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start the session on every page that includes this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
