<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal - Clientes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require __DIR__ . '/menu.php'; ?>

    <h2>Cadastro de Clientes</h2>

    <?php if ($mensagem): ?>
        <div class="msg-sucesso"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="msg-erro">Erro: <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?rota=clientes" class="formulario">
        <label>Nome: <input type="text" name="nome" required></label>
        <label>Telefone: <input type="text" name="telefone" required></label>
        <button type="submit" class="btn-cadastrar-cliente">Cadastrar Cliente</button>
    </form>

    <table class="tabela-dados">
        <thead>
            <tr>
                <th width="80">ID</th>
                <th>Nome</th>
                <th>Telefone</th>
                <th width="100">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($clientes)): ?>
                <tr><td colspan="4">Nenhum cliente cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?= $c->getId() ?></td>
                        <td><?= htmlspecialchars($c->getNome()) ?></td>
                        <td><?= htmlspecialchars($c->getTelefone()) ?></td>
                        <td>
                            <a href="index.php?rota=clientes&acao=deletar&id=<?= $c->getId() ?>" 
                               class="btn-excluir" 
                               onclick="return confirm('Deseja realmente excluir este cliente?');">[Excluir]</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>