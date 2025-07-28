<?php
session_start();

// Configurações do banco de dados
$host = 'localhost';
$user = 'root'; // usuário padrão do XAMPP
$password = ''; // senha padrão do XAMPP (vazia)
$database = 'login'; // substitua pelo nome do seu banco de dados

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Receber dados do forms
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Lógica de cadastro
if (!empty($dados["SendRegister"])) {
    $usuario = $dados["usuario"] ?? '';
    $senha = $dados["senha_usuario"] ?? '';
    $senha_confirm = $dados["senha_confirm"] ?? '';

    if (empty($usuario) || empty($senha) || empty($senha_confirm)) {
        echo "<p style='color: red'>Erro: Preencha todos os campos de cadastro!</p>";
    } elseif ($senha !== $senha_confirm) {
        echo "<p style='color: red'>Erro: As senhas não coincidem!</p>";
    } else {
        // Verificar se usuário já existe
        $query_check = "SELECT id FROM usuarios WHERE usuario = ? LIMIT 1";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("s", $usuario);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "<p style='color: red'>Erro: Usuário já existe!</p>";
        } else {
            // Inserir novo usuário com senha criptografada
            $senha_hash = md5($senha);
            $query_insert = "INSERT INTO usuarios (usuario, senha) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($query_insert);
            $stmt_insert->bind_param("ss", $usuario, $senha_hash);
            if ($stmt_insert->execute()) {
                echo "<p style='color: green'>Cadastro realizado com sucesso! Faça login abaixo.</p>";
            } else {
                echo "<p style='color: red'>Erro: Falha ao cadastrar usuário.</p>";
            }
        }
    }
}

// Lógica de login
if (!empty($dados["Sendlogin"])) {
    // Preparar a consulta SQL
    $query_usuario = "SELECT id, senha FROM usuarios WHERE usuario = ? LIMIT 1";
    $stmt = $conn->prepare($query_usuario);
    $stmt->bind_param("s", $dados["usuario"]);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        // Usuário encontrado, verificar senha
        $row_usuario = $resultado->fetch_assoc();
        if (md5($dados["senha_usuario"]) === $row_usuario['senha']) {
            // Senha correta - iniciar sessão e redirecionar
            $_SESSION['id'] = $row_usuario['id'];
            $_SESSION['usuario'] = $dados["usuario"];

            header("Location: dashboard.php"); // redireciona para página restrita
            exit();
        } else {
            echo "<p style='color: red'>Erro: Senha incorreta!</p>";
        }
    } else {
        echo "<p style='color: red'>Erro: Usuário não encontrado!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login e Cadastro</title>
</head>
<body>

<h2>Login</h2>
<form method="POST" action="">
    <label>Usuário: </label>
    <input type="text" name="usuario" placeholder="Digite o usuário" required><br><br>

    <label>Senha: </label>
    <input type="password" name="senha_usuario" placeholder="Digite a senha" required><br><br>

    <input type="submit" name="Sendlogin" value="Acessar">
</form>

<hr>

<h2>Cadastro</h2>
<form method="POST" action="">
    <label>Usuário: </label>
    <input type="text" name="usuario" placeholder="Digite o usuário" required><br><br>

    <label>Senha: </label>
    <input type="password" name="senha_usuario" placeholder="Digite a senha" required><br><br>

    <label>Confirme a Senha: </label>
    <input type="password" name="senha_confirm" placeholder="Confirme a senha" required><br><br>

    <input type="submit" name="SendRegister" value="Cadastrar">
</form>

</body>
</html>
