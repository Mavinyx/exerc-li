<?php

require_once __DIR__ . '/controllers/ClienteController';
require_once __DIR__ . '/controllers/ProdutoController';
require_once __DIR__ . '/controllers/VendaController';

$rota = $_GET['rota'] ?? 'vendas';

switch ($rota) {
    case 'clientes':
        $controller = new ClienteController();
        $controller->index();
        break;

    case 'produtos':
        $controller = new ProdutoController();
        $controller->index();
        break;

    case 'vendas':
    default:
        $controller = new VendaController();
        $controller->index();
        break;
}