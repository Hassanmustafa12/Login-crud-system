<?php
/**
 * api/helpers.php
 * ---------------------------------------------------------
 * Small reusable functions every endpoint needs.
 * ---------------------------------------------------------
 */

// Send a JSON response and stop execution
function json_response($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Read whatever the client sent in the JSON body (for POST/PUT)
function get_json_input() {
    $data = json_decode(file_get_contents("php://input"), true);
    return is_array($data) ? $data : [];
}

// Pull the token out of the "Authorization: Bearer xxxxx" header
function get_bearer_token() {
    $headers = null;

    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(
            array_map('ucwords', array_keys($requestHeaders)),
            array_values($requestHeaders)
        );
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }

    if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }
    return null;
}

// Check the token is valid and return the logged-in user, or stop with 401
function authenticate($pdo) {
    $token = get_bearer_token();
    if (!$token) {
        json_response(["success" => false, "error" => "Missing Authorization token"], 401);
    }

    $stmt = $pdo->prepare("
        SELECT users.id, users.username, users.role
        FROM api_tokens
        JOIN users ON users.id = api_tokens.user_id
        WHERE api_tokens.token = ?
        AND (api_tokens.expires_at IS NULL OR api_tokens.expires_at > NOW())
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        json_response(["success" => false, "error" => "Invalid or expired token"], 401);
    }
    return $user;
}

// Stop with 403 if the current user isn't an admin
function require_admin($user) {
    if ($user['role'] !== 'admin') {
        json_response(["success" => false, "error" => "Admin access required"], 403);
    }
}
