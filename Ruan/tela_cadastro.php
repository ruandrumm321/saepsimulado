<?php
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);

    if ($nome && $email && $telefone) {
        $query = "INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)";
        $statement = $conn->prepare($query);

        if ($statement) {
            $statement->bind_param("sss", $nome, $email, $telefone);
            if ($statement->execute()) {
                $mensagem = "Cliente cadastrado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar cliente: " . $statement->error;
            }
            $statement->close();
        } else {
            $mensagem = "Erro ao preparar a consulta: " . $conn->error;
        }
    } else {
        $mensagem = "Todos os campos devem ser preenchidos!";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/estilo.css">
    <title>Cadastro de Clientes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Cadastro de Clientes</h1>
    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>
        <label for="email">E-mail:</label>
        <input type="email" name="email" id="email" required>
        <label for="telefone">Telefone:</label>
        <input type="text" name="telefone" id="telefone" required>
        <button type="submit">Cadastrar</button>
    </form>
    <?php if (isset($mensagem)) echo "<p>$mensagem</p>"; ?>
</body>
</html>
