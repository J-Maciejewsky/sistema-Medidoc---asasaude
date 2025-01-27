<?php
session_start();
require('../includes/conexão.php'); // Arquivo de conexão com o banco

$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email == '' || $password == '') {
        $error = "Por favor, preencha todos os campos.";
    } else {
        // Utilize a variável correta da conexão (geralmente $conn)
        $stmt = $conn->prepare("SELECT * FROM medicos WHERE email = ?");
        
        if (!$stmt) {
            die("Erro ao preparar a consulta: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $doctor = $result->fetch_assoc();
            if (password_verify($password, $doctor['password'])) {
                // Salvar dados na sessão
                $_SESSION['doctor_id'] = $doctor['id'];
                $_SESSION['doctor_name'] = $doctor['name'];
                header("Location: compromissos.php");
                exit;
            } else {
                $error = "Senha incorreta.";
            }
        } else {
            $error = "E-mail não encontrado.";
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
    <title>Login - Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center mb-3">Login do Médico</h3>
            <?php if ($error) : ?>
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
            <div class="text-center mt-3">
                <a href="register.php">Não tem conta? Crie uma!</a>
            </div>
        </div>
    </div>
</body>
</html>
