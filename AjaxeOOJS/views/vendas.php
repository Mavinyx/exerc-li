<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Terminal - Frente de Caixa</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require __DIR__ . '/menu.php'; ?>

    <h2>Pedidos</h2>

    <?php if ($mensagem): ?>
        <div class="msg-sucesso"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="msg-erro">Erro: <?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?rota=vendas" class="caixa-pdv">
        <label>Cliente:
            <select name="cliente_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c->getId() ?>"><?= htmlspecialchars($c->getNome()) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Produto:
            <select name="produto_id" required>
                <option value="">Selecione...</option>
                <?php foreach ($produtos as $p): ?>
                    <option value="<?= $p->getId() ?>">
                        <?= htmlspecialchars($p->getNome()) ?> (Estoque: <?= $p->getEstoque() ?> | R$ <?= number_format($p->getPreco(), 2, ',', '.') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Qtd:
            <input type="number" name="quantidade" value="1" min="1" required style="width: 60px;">
        </label>

        <button type="submit" class="btn-finalizar">Registrar Venda</button>
    </form>

    <h3>Histórico de Vendas</h3>
    <table class="tabela-dados">
        <thead>
            <tr>
                <th width="60">Cód</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Produto</th>
                <th>Qtd</th>
                <th>Total</th>
                <th width="100">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($vendas)): ?>
                <tr><td colspan="7">Nenhuma venda realizada.</td></tr>
            <?php else: ?>
                <?php foreach ($vendas as $v): ?>
                    <tr>
                        <td>#<?= $v->getId() ?></td>
                        <td><?= $v->getDataVenda() ? date('d/m/Y H:i', strtotime($v->getDataVenda())) : '-' ?></td>
                        <td><?= htmlspecialchars($mapaClientes[$v->getClienteId()] ?? 'ID ' . $v->getClienteId()) ?></td>
                        <td><?= htmlspecialchars($mapaProdutos[$v->getProdutoId()] ?? 'ID ' . $v->getProdutoId()) ?></td>
                        <td><?= $v->getQuantidade() ?></td>
                        <td>R$ <?= number_format($v->getValorTotal(), 2, ',', '.') ?></td>
                        <td>
                            <a href="index.php?rota=vendas&acao=deletar&id=<?= $v->getId() ?>" 
                               class="btn-estornar" 
                               onclick="return confirm('Deseja estornar esta venda e devolver os itens ao estoque?');">[Estornar]</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>