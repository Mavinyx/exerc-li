<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal - Produtos</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require __DIR__ . '/menu.php'; ?>

    <h2>Estoque de Produtos</h2>

    <?php if ($mensagem): ?>
        <div class="msg-sucesso"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="msg-erro">Erro: <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?rota=produtos" class="formulario">
        <label>Nome: <input type="text" name="nome" required></label>
        <label>Preço: <input type="number" step="0.01" name="preco" required></label>
        <label>Estoque: <input type="number" name="estoque" required></label>
        <button type="submit" class="btn-cadastrar-produto">Adicionar Produto</button>
    </form>

    <table class="tabela-dados">
        <thead>
            <tr>
                <th width="80">ID</th>
                <th>Produto</th>
                <th>Preço Unitário</th>
                <th>Estoque</th>
                <th width="100">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($produtos)): ?>
                <tr><td colspan="5">Nenhum produto cadastrado.</td></tr>
            <?php else: ?>
                <?php foreach ($produtos as $p): ?>
                    <tr>
                        <td><?= $p->getId() ?></td>
                        <td><?= htmlspecialchars($p->getNome()) ?></td>
                        <td>R$ <?= number_format($p->getPreco(), 2, ',', '.') ?></td>
                        <td><?= $p->getEstoque() ?></td>
                        <td>
                            <a href="index.php?rota=produtos&acao=deletar&id=<?= $p->getId() ?>" 
                               class="btn-excluir" 
                               onclick="return confirm('Deseja realmente excluir este produto?');">[Excluir]</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <script>
        fetch('index.php?rota=api_produtos')
            .then(response =>{
                if(!response.ok){
                    throw new Error('Erro na resposta do server: ' + response.status); 
                }
                return response.json();
                })
            .then(dadosProdutos => {
                dadosProdutos.forEach(produto => {
                console.log(`ID: ${produto.id} - ${produto.nome} (R$ ${produto.preco})`);
            });
            })
            .catch(erro => {
            console.error('Erro ao buscar produtos:', erro);
        });
    </script>
</body>
</html>