<?php
require_once "config.php";
require_once "helpers.php";

$user = authenticate($pdo);
require_admin($user); // everything in this file is admin-only

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($method) {

    case 'GET':
        // GET /api/users.php -> list everyone
        $stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC");
        json_response(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    case 'POST':
        // POST /api/users.php -> create account
        $input = get_json_input();
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';
        $role = $input['role'] ?? 'user';

        if ($username === '' || $password === '') {
            json_response(["success" => false, "error" => "Username and password are required"], 400);
        }
        if (strlen($password) < 6) {
            json_response(["success" => false, "error" => "Password must be at least 6 characters"], 400);
        }
        if (!in_array($role, ['admin', 'user'])) {
            json_response(["success" => false, "error" => "Invalid role"], 400);
        }

        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            json_response(["success" => false, "error" => "Username already taken"], 409);
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed, $role]);

        json_response(["success" => true, "message" => "User created", "id" => $pdo->lastInsertId()], 201);
        break;

    case 'DELETE':
        // DELETE /api/users.php?id=5 -> remove account (can't delete yourself)
        if (!$id) {
            json_response(["success" => false, "error" => "User id is required"], 400);
        }
        if ((int)$id === (int)$user['id']) {
            json_response(["success" => false, "error" => "You cannot delete your own account"], 400);
        }

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        json_response(["success" => true, "message" => "User deleted"]);
        break;

    default:
        json_response(["success" => false, "error" => "Method not allowed"], 405);
}
