<?php
namespace app\Controllers;

class ContractController {
    
    public function index() {
        

        // Carrega a view passando a variável para ela
        require_once __DIR__ . '/../Views/contracts/index.php';
    }
}