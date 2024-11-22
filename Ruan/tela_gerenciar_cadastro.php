<?php
include 'conexao.php';

$filtro_status = $_GET['status'] ?? '';
$filtro_criticidade = $_GET['criticidade'] ?? '';

$query = "SELECT c.id_chamado, cl.nome AS cliente, c.descricao, c.criticidade, c.status, co.nome AS colaborador 
          FROM chamados c
          LEFT JOIN clientes cl ON c.id_cliente = cl.id_cliente
          LEFT JOIN colaboradores co ON c.id_colaborador = co.id_colaborador";

if ($filtro_status || $filtro_criticidade) {
    $query .= " WHERE ";
    $conditions = [];
    if ($filtro_status) {
        $conditions[] = "c.status = ?";
    }
    if ($filtro_criticidade) {
        $conditions[] = "c.criticidade = ?";
    }
    $query .= implode(" AND ", $conditions);
}

$statement = $conn->prepare($query);

if ($filtro_status && $filtro_criticidade) {
    $statement->bind_param("ss", $filtro_status, $filtro_criticidade);
} elseif ($filtro_status) {
    $statement->bind_param("s", $filtro_status);
} elseif ($filtro_criticidade) {
    $statement->bind_param("s", $filtro_criticidade);
}

$statement->execute();
$statement->bind_result($id_chamado, $cliente, $descricao, $criticidade, $status, $colaborador);

$chamados = [];
while ($statement->fetch()) {
    $chamados[] = [
        'id_chamado' => $id_chamado,
        'cliente' => $cliente,
        'descricao' => $descricao,
        'criticidade' => $criticidade,
        'status' => $status,
        'colaborador' => $colaborador,
    ];
}

$statement->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilo.css">
    <title>Gerenciamento de Chamados</title>
</head>
<body>
    <h1>Gerenciamento de Chamados</h1>
    <form method="GET">
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="">Todos</option>
            <option value="aberto" <?= $filtro_status === 'aberto' ? 'selected' : '' ?>>Aberto</option>
            <option value="em andamento" <?= $filtro_status === 'em andamento' ? 'selected' : '' ?>>Em andamento</option>
            <option value="resolvido" <?= $filtro_status === 'resolvido' ? 'selected' : '' ?>>Resolvido</option>
        </select>
        <label for="criticidade">Criticidade:</label>
        <select name="criticidade" id="criticidade">
            <option value="">Todas</option>
            <option value="baixa" <?= $filtro_criticidade === 'baixa' ? 'selected' : '' ?>>Baixa</option>
            <option value="média" <?= $filtro_criticidade === 'média' ? 'selected' : '' ?>>Média</option>
            <option value="alta" <?= $filtro_criticidade === 'alta' ? 'selected' : '' ?>>Alta</option>
        </select>
        <button type="submit">Filtrar</button>
    </form>
    <table>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Descrição</th>
            <th>Criticidade</th>
            <th>Status</th>
            <th>Colaborador</th>
        </tr>
        <?php foreach ($chamados as $chamado): ?>
            <tr>
                <td><?= htmlspecialchars($chamado['id_chamado']) ?></td>
                <td><?= htmlspecialchars($chamado['cliente']) ?></td>
                <td><?= htmlspecialchars($chamado['descricao']) ?></td>
                <td><?= htmlspecialchars($chamado['criticidade']) ?></td>
                <td><?= htmlspecialchars($chamado['status']) ?></td>
                <td><?= htmlspecialchars($chamado['colaborador'] ?? 'Não atribuído') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
