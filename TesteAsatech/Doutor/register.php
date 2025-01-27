<?php
require('../includes/conexão.php'); // Arquivo de conexão com o banco

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $crm = trim($_POST['crm']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($name == '' || $crm == '' || $email == '' || $password == '' || $confirm_password == '') {
        $error = "Por favor, preencha todos os campos.";
    } elseif ($password !== $confirm_password) {
        $error = "As senhas não coincidem.";
    } else {
        $stmt = $db->prepare("SELECT * FROM medicos WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Este e-mail já está registrado.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO medicos (name, crm, email, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $crm, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = "Cadastro realizado com sucesso! Faça login.";
            } else {
                $error = "Erro ao registrar. Tente novamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center mb-3">Cadastro do Médico</h3>
            <?php if ($error) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success) : ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="crm" class="form-label">CRM</label>
                    <input type="text" name="crm" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar Senha</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">Registrar</button>
            </form>
            <div class="text-center mt-3">
                <a href="login.php">Já tem uma conta? Faça login!</a>
            </div>
        </div>
    </div>
</body>
</html>
