<?php
require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'POST' && $path === '/login') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (($input['username'] === 'student' && $input['password'] === 'student123') ||
        ($input['username'] === 'teacher' && $input['password'] === 'teacher123')) {

        $payload = [
            'iss' => 'localhost',
            'aud' => 'localhost',
            'iat' => time(),
            'exp' => time() + (60 * 60), // 1 hour
            'username' => $input['username'],
            'role' => $input['username']
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        echo json_encode(['success' => true, 'token' => $jwt, 'role' => $input['username']]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
}
?>
