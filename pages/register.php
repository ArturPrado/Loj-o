<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($fullName) || empty($phone) || !$email || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios e devem ser válidos.']);
        exit;
    }


    $_SESSION['user'] = $email;
    echo json_encode(['success' => true, 'message' => 'Cadastro realizado com sucesso.']);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
}
?>
