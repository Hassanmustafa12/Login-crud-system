<?php
require_once "config.php";
require_once "helpers.php";

$token = get_bearer_token();
if (!$token) {
    json_response(["success" => false, "error" => "Missing Authorization token"], 401);
}

$stmt = $pdo->prepare("DELETE FROM api_tokens WHERE token = ?");
$stmt->execute([$token]);

json_response(["success" => true, "message" => "Logged out"]);
