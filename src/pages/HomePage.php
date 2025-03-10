<?php
namespace Src\Pages;

use Core\PHXController;

class HomePage {
    public function index() {
        PHXController::render("
        [div class:'center']
            [h1]
                Halo, Dunia!
            [/h1]
            [p]
                Ini adalah paragraf pertama saya di HTML.
            [/p]
            [[Form]]
        [/div]
        ");
    }
}
