<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email ou senha inválidos.']);
        exit;
    }

    $validEmail = 'user@example.com';
    $validPassword = '123456';

    if ($email === $validEmail && $password === $validPassword) {
        $_SESSION['user'] = $email;
        echo json_encode(['success' => true, 'message' => 'Login realizado com sucesso.']);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
}
?>
