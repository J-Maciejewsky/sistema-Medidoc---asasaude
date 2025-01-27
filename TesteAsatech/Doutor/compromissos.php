<?php 
session_start();
require('../includes/conexão.php'); // Arquivo de conexão ao banco de dados

// Verifica se o médico está logado
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

// Verificar se a conexão foi definida corretamente
if (!isset($conn) || $conn->connect_error) {
    die("<div class='alert alert-danger'>Erro: Conexão ao banco de dados não foi estabelecida.</div>");
}

// Obtém os pacientes associados ao médico logado
$stmt = $conn->prepare("
    SELECT 
        agendamentos.id AS agendamento_id,
        pacientes.nome AS paciente_nome,
        agendamentos.data_agendamento 
    FROM agendamentos
    JOIN pacientes ON agendamentos.paciente_id = pacientes.id
    WHERE agendamentos.doctor_id = ?
    ORDER BY agendamentos.data_agendamento DESC
");

if (!$stmt) {
    die("<div class='alert alert-danger'>Erro ao preparar a consulta: " . $conn->error . "</div>");
}

$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

// Obtém os dados dos pacientes
$pacientes = [];
while ($row = $result->fetch_assoc()) {
    $pacientes[] = $row;
}

$stmt->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Pacientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['doctor_name']); ?>!</h1>
        <p>Abaixo está o relatório dos seus pacientes:</p>

        <?php if (count($pacientes) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID do Agendamento</th>
                        <th>Nome do Paciente</th>
                        <th>Data do Agendamento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pacientes as $paciente): ?>
                        <tr>
                            <td><?= $paciente['agendamento_id']; ?></td>
                            <td><?= htmlspecialchars($paciente['paciente_nome']); ?></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($paciente['data_agendamento'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                Nenhum paciente associado aos seus agendamentos no momento.
            </div>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-danger">Sair</a>
    </div>
</body>
</html>
