<?php

$request = $_SERVER['REQUEST_URI'];

if (strpos($request, '?') !== false) {
    $request = strstr($request, '?', true);
}

$request = ltrim($request, '/');

$parts = explode('/', $request);

$controller = !empty($parts[0]) ? ucfirst($parts[0]) . 'Controller' : 'ProdutoController';
$method = isset($parts[1]) ? $parts[1] : 'index';

$params = array_slice($parts, 2);

$controllerPath = __DIR__ . '/../app/controllers/' . $controller . '.php';

if (file_exists($controllerPath)) {
    require_once $controllerPath;

    if (class_exists($controller)) {
        $obj = new $controller();

        if (method_exists($obj, $method)) {
            call_user_func_array([$obj, $method], $params);
            exit;
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Método '$method' não encontrado no controller '$controller'.";
            exit;
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Controller '$controller' não encontrado.";
        exit;
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Controller não encontrado.";
    exit;
}
