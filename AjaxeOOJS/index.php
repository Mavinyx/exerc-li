<?php

require_once __DIR__ . '/controllers/ClienteController';
require_once __DIR__ . '/controllers/ProdutoController';
require_once __DIR__ . '/controllers/VendaController';

$rota = $_GET['rota'] ?? 'vendas';

switch ($rota) {
    case 'api_clientes':
        $controller = new ClienteController();
        $controller->listarApi();
        break;
    case 'clientes':
        $controller = new ClienteController();
        $controller->index();
        break;

    case 'api_produtos':
        $controller = new ProdutoController();
        $controller->listarApi();
        break;

    case 'produtos':
        $controller = new ProdutoController();
        $controller->index();
        break;
    
    case 'api_vendas':
        $controller = new VendaController();
        $controller->listarApi();
        break;
    case 'vendas':
    default:
        $controller = new VendaController();
        $controller->index();
        break;
}