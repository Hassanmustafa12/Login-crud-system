<?php
require_once "config.php";
require_once "helpers.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(["success" => false, "error" => "Method not allowed, use POST"], 405);
}

$input = get_json_input();
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    json_response(["success" => false, "error" => "Username and password are required"], 400);
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    json_response(["success" => false, "error" => "Invalid username or password"], 401);
}

// Generate a random 64-character token and store it, valid for 7 days
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+7 days'));

$stmt = $pdo->prepare("INSERT INTO api_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
$stmt->execute([$user['id'], $token, $expires]);

json_response([
    "success" => true,
    "token" => $token,
    "expires_at" => $expires,
    "user" => [
        "id" => $user['id'],
        "username" => $user['username'],
        "role" => $user['role']
    ]
]);
