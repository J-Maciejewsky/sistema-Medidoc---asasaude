<?php
// Conectar ao banco de dados
$host = "localhost";
$user = "root"; // Substitua com seu usuário
$password = ""; // Substitua com sua senha
$database = "doctor"; // Nome do banco de dados
$conn = new mysqli($host, $user, $password, $database);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Buscar médicos disponíveis
$sql_medicos = "SELECT id, name FROM medicos";
$result_medicos = $conn->query($sql_medicos);

// Se o paciente escolher um médico e uma data, mostrar os horários disponíveis
$horarios = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['medico_id']) && isset($_POST['data'])) {
    $medico_id = $_POST['medico_id'];
    $data = $_POST['data'];

    $sql_agenda = "SELECT id, hora_inicio, hora_fim FROM agenda WHERE medico_id = ? AND data = ? AND disponivel = TRUE";
    $stmt = $conn->prepare($sql_agenda);
    $stmt->bind_param('is', $medico_id, $data);
    $stmt->execute();
    $result_agenda = $stmt->get_result();
    while ($row = $result_agenda->fetch_assoc()) {
        $horarios[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Consultas</title>
    <!-- Link do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilo do Header fixo no topo */
        .header {
            background-color: #343a40;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .header .navbar {
            margin-bottom: 0;
        }
        .header .navbar-brand {
            color: #fff;
        }
        .header .nav-link {
            color: #fff;
        }
    </style>
</head>
<body>

  <!-- Header fixo -->
  <div class="header">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Sistema de Agendamento</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Sair</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>








    <div class="container my-5" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
    <div class="text-center">
        <h1 class="mb-4">Agendar Consulta</h1>

        <!-- Formulário para o paciente -->
        <form method="POST" action="agendar.php">
            <div class="mb-3">
                <label for="medico" class="form-label">Escolha o Médico:</label>
                <select id="medico" name="medico_id" class="form-select" required>
                    <option value="">Selecione um Médico</option>
                    <?php while($medico = $result_medicos->fetch_assoc()): ?>
                        <option value="<?php echo $medico['id']; ?>"><?php echo $medico['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="data" class="form-label">Escolha a Data:</label>
                <input type="date" id="data" name="data" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Ver Horários Disponíveis</button>
        </form>

        <?php if (!empty($horarios)): ?>
            <h2 class="mt-5">Horários Disponíveis</h2>
            <form action="confirmar_agendamento.php" method="POST">
    <div class="mb-3">
        <label for="hora" class="form-label">Escolha o Horário:</label>
        <select id="hora" name="hora_id" class="form-select" required>
            <option value="">Selecione um Horário</option>
            <?php foreach ($horarios as $horario): ?>
                <option value="<?php echo $horario['id']; ?>">
                    <?php echo $horario['hora_inicio'] . " - " . $horario['hora_fim']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <input type="hidden" name="medico_id" value="<?php echo $medico_id; ?>">
    <input type="hidden" name="data" value="<?php echo $data; ?>">
    <input type="hidden" name="doctor_id" value="<?php echo $_SESSION['doctor_id']; ?>"> <!-- Adicionado -->

    <button type="submit" class="btn btn-success w-100">Agendar Consulta</button>
</form>

        <?php endif; ?>

        <?php if (isset($horarios) && empty($horarios)): ?>
            <div class="alert alert-warning mt-3" role="alert">
                Não há horários disponíveis para a data selecionada.
            </div>
        <?php endif; ?>
    </div>
</div>


    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
