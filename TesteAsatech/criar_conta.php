<?php
require('includes/conexão.php'); // Conexão ao banco de dados

// Processar cadastro
if (isset($_POST['register'])) {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $data_nascimento = $_POST['data_nascimento'];
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    if ($nome == '' || $cpf == '' || $data_nascimento == '' || $email == '') {
        $register_error = "Todos os campos são obrigatórios.";
    } else {
        $stmt = $db->prepare("INSERT INTO pacientes (nome, cpf, data_nascimento, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nome, $cpf, $data_nascimento, $email, $password);

        if ($stmt->execute()) {
            $register_success = "Conta criada com sucesso! Faça login.";
            header("Location: login.php");
            exit;
        } else {
            $register_error = "Erro ao criar conta. Verifique os dados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Sistema Médico - Criar Conta</h1>
        <div class="card p-4 shadow mx-auto" style="max-width: 500px;">
            <h3 class="text-center mb-3">Criar Conta</h3>
            <?php if (isset($register_error)) : ?>
                <div class="alert alert-danger"><?= $register_error ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" name="cpf" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" name="data_nascimento" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="register" class="btn btn-success w-100">Criar Conta</button>
            </form>
            <p class="text-center mt-3">
                Já tem uma conta? <a href="login.php">Login</a>
            </p>
        </div>
    </div>
</body>
</html>
