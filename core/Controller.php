<?php

class Controller {
    protected $pdo;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        global $pdo;
        $this->pdo = $pdo;
    }

    public function model($model) {
        require_once __DIR__ . '/../app/models/' . $model . '.php';
        return new $model($this->pdo);
    }

    public function view($view, $data = []) {
        extract($data);
        require_once __DIR__ . '/../app/views/' . $view . '.php';
    }
}
