<?php
require_once "config.php";
require_once "helpers.php";

$user = authenticate($pdo); // every records.* action requires a valid token
$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($method) {

    case 'GET':
        if ($id) {
            // GET /api/records.php?id=5  -> single record
            $stmt = $pdo->prepare("
                SELECT records.*, users.username AS creator
                FROM records LEFT JOIN users ON records.created_by = users.id
                WHERE records.id = ?
            ");
            $stmt->execute([$id]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$record) {
                json_response(["success" => false, "error" => "Record not found"], 404);
            }
            json_response(["success" => true, "data" => $record]);
        } else {
            // GET /api/records.php -> all records
            $stmt = $pdo->query("
                SELECT records.*, users.username AS creator
                FROM records LEFT JOIN users ON records.created_by = users.id
                ORDER BY records.created_at DESC
            ");
            json_response(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }
        break;

    case 'POST':
        // POST /api/records.php -> create (both roles allowed, same as your dashboard)
        $input = get_json_input();
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');

        if ($title === '') {
            json_response(["success" => false, "error" => "Title is required"], 400);
        }

        $stmt = $pdo->prepare("INSERT INTO records (title, description, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $user['id']]);

        json_response(["success" => true, "message" => "Record created", "id" => $pdo->lastInsertId()], 201);
        break;

    case 'PUT':
        // PUT /api/records.php?id=5 -> update (admin only)
        require_admin($user);
        if (!$id) {
            json_response(["success" => false, "error" => "Record id is required"], 400);
        }

        $input = get_json_input();
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');

        if ($title === '') {
            json_response(["success" => false, "error" => "Title is required"], 400);
        }

        $stmt = $pdo->prepare("UPDATE records SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $id]);

        json_response(["success" => true, "message" => "Record updated"]);
        break;

    case 'DELETE':
        // DELETE /api/records.php?id=5 -> delete (admin only)
        require_admin($user);
        if (!$id) {
            json_response(["success" => false, "error" => "Record id is required"], 400);
        }

        $stmt = $pdo->prepare("DELETE FROM records WHERE id = ?");
        $stmt->execute([$id]);

        json_response(["success" => true, "message" => "Record deleted"]);
        break;

    default:
        json_response(["success" => false, "error" => "Method not allowed"], 405);
}
