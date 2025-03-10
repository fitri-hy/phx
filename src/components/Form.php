<?php
namespace Src\Components;

use Core\PHXFramework;

class Form {
    public function index() {
        echo PHXFramework::render("
            [form]
                [input placeholder:'Masukan sesuatu ..']
                [button type:'submit']Kirim[/button]
            [/form]
        ");
    }
}
