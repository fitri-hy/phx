<?php
namespace Src\Api;

class Welcome {
    public function index() {
        header('Content-Type: application/json');
        echo json_encode(["message" => "Welcome to API!"]);
        exit;
    }
}
