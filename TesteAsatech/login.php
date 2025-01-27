<?php
session_start();
require('includes/conexão.php'); // Conexão ao banco de dados

// Processar login
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email == '' || $password == '') {
        $error = "Por favor, preencha todos os campos.";
    } else {
        // Ajustar a variável para usar $conn em vez de $db
        $stmt = $conn->prepare("SELECT * FROM pacientes WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();

        // Verificar se o usuário existe e a senha está correta
        if ($doctor && password_verify($password, $doctor['password'])) {
            $_SESSION['doctor_id'] = $doctor['id'];
            $_SESSION['doctor_name'] = $doctor['name'];
            header("Location: agendar.php");
            exit;
        } else {
            $error = "E-mail ou senha inválidos.";
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Sistema Médico - Login</h1>
        <div class="card p-4 shadow mx-auto" style="max-width: 400px;">
            <h3 class="text-center mb-3">Entrar</h3>
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Entrar</button>
            </form>
            <p class="text-center mt-3">
                Não tem uma conta? <a href="criar_conta.php">Criar conta</a>
            </p>
        </div>
    </div>
</body>
</html>
