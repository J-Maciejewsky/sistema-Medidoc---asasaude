<?php
session_start();
require(__DIR__ . '/includes/conexão.php');

// Verificar se a conexão com o banco de dados está ativa
if (!isset($conn) || $conn->connect_error) {
    die("<div class='alert alert-danger'>Erro: Conexão ao banco de dados não foi estabelecida.</div>");
}

// Verificar se o método de envio é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar os dados enviados pelo formulário
    $hora_id = $_POST['hora_id'] ?? null;
    $medico_id = $_POST['medico_id'] ?? null;
    $data = $_POST['data'] ?? null;
    $doctor_id = $_POST['doctor_id'] ?? null;

    // Simular o paciente logado (ajuste conforme sua lógica de autenticação)
    $paciente_id = $_SESSION['paciente_id'] ?? 1;

    // Validar os dados antes de prosseguir
    if ($hora_id && $medico_id && $data && $doctor_id) {
        // Inserir o agendamento na tabela
        $sql = "INSERT INTO agendamentos (paciente_id, agenda_id, doctor_id, data_agendamento) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("<div class='alert alert-danger'>Erro ao preparar a consulta: " . $conn->error . "</div>");
        }

        // Formatar a data para o formato esperado
        $data_completa = "$data 00:00:00";
        $stmt->bind_param('iiis', $paciente_id, $hora_id, $doctor_id, $data_completa);

        // Executar a consulta e verificar o resultado
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Agendamento realizado com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao agendar: " . $conn->error . "</div>";
        }

        // Fechar a consulta
        $stmt->close();
    } else {
        echo "<div class='alert alert-warning'>Por favor, preencha todos os campos obrigatórios.</div>";
    }
}
?>
