<?php

session_start();
header("Content-Type:application/json");

$produtosFile = __DIR__ . '/produtos.json';

if (isset($_SESSION['produtos'])) {
    $produtos = $_SESSION['produtos'];
} elseif (file_exists($produtosFile)) {
    $produtos = json_decode(file_get_contents($produtosFile), true);
    if (!is_array($produtos)) $produtos = [];
    $_SESSION['produtos'] = $produtos;
} else {
    $produtos = [
        ["id" => 1, "nome" => "Produto A", "preco" => 10.00],
        ["id" => 2, "nome" => "Produto B", "preco" => 20.00],
        ["id" => 3, "nome" => "Produto C", "preco" => 30.00],
    ];
    file_put_contents($produtosFile, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $_SESSION['produtos'] = $produtos;
}

function response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getProdutos($produtos) {
    response(['success' => true, 'data' => $produtos]);
}

function getProduto($produtos, $id) {
    foreach ($produtos as $produto) {
        if ($produto['id'] == $id) {
            response(['success' => true, 'data' => $produto]);
        }
    }
    response(['success' => false, 'message' => 'Produto não encontrado'], 404);
}

function createProduto(&$produtos) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['nome']) || !isset($input['preco'])) {
        http_response_code(400);
        echo json_encode(['erro' => 'Dados inválidos ou faltando.'], 400);
        exit;
    }
    $last = end($produtos);
    $newId = $last ? $last['id'] + 1 : 1;
    $novo = [
        "id" => $newId,
        "nome" => $input['nome'],
        "preco" => floatval($input['preco'])
    ];
    $produtos[] = $novo;
    $_SESSION['produtos'] = $produtos;
    response(['success' => true, 'data' => $novo], 201);
}

function updateProduto(&$produtos, $id) {
    $input = json_decode(file_get_contents('php://input'), true);
    foreach ($produtos as &$produto) {
        if ($produto['id'] == $id) {
            if (isset($input['nome'])) $produto['nome'] = $input['nome'];
            if (isset($input['preco'])) $produto['preco'] = floatval($input['preco']);
            $_SESSION['produtos'] = $produtos;
            response(['success' => true, 'data' => $produto]);
        }
    }
    response(['success' => false, 'message' => 'Produto não encontrado'], 404);
}

function deleteProduto(&$produtos, $id) {
    foreach ($produtos as $i => $produto) {
        if ($produto['id'] == $id) {
            array_splice($produtos, $i, 1);
            $_SESSION['produtos'] = $produtos;
            response(['success' => true, 'message' => 'Produto removido']);
        }
    }
    response(['success' => false, 'message' => 'Produto não encontrado'], 404);
}

// Roteamento
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Exemplo de rotas: /produtos ou /produtos/(id)
$routes = [
    'GET' => [
        '#^/produtos$#' => function() use ($produtos) {
            getProdutos($produtos);
        },
        '#^/produtos/(\d+)$#' => function($id) use ($produtos) {
            getProduto($produtos, intval($id));
        }
    ],
    'POST' => [
        '#^/produtos$#' => function() use (&$produtos) {
            createProduto($produtos);
        }
    ],
    'PUT' => [
        '#^/produtos/(\d+)$#' => function($id) use (&$produtos) {
            updateProduto($produtos, intval($id));
        }
    ],
    'DELETE' => [
        '#^/produtos/(\d+)$#' => function($id) use (&$produtos) {
            deleteProduto($produtos, intval($id));
        }
    ]
];

if (!isset($routes[$method])) {
    response(['success' => false, 'message' => 'Método não suportado'], 405);
}

foreach ($routes[$method] as $pattern => $handler) {
    if (preg_match($pattern, $uri, $matches)) {
        array_shift($matches);
        call_user_func_array($handler, $matches);
        exit;
    }
}

response(['success' => false, 'message' => 'Rota não encontrada'], 404);



