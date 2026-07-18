<?php
/**
 * includes/auth.php
 * ---------------------------------------------------------
 * Small helper functions used everywhere to check:
 *  - is someone logged in?
 *  - are they an admin?
 * Include this AFTER config.php (config.php starts the session).
 * ---------------------------------------------------------
 */

// Redirect to login page if nobody is logged in
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Redirect away if the logged-in user is NOT an admin
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php?error=You are not authorized to view that page");
        exit;
    }
}

// Simple boolean check you can use inside pages (e.g. to hide buttons)
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
