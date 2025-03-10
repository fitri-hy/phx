<?php
namespace Src;

use Core\PHXFramework;

class App {
    public static function App($Children) {
        echo PHXFramework::render("
            [!PHX html]
            [html]
            [head]
                [title] Welcome to PHX! [/title]
                [meta name:'viewport' content:'width=device-width, initial-scale=1.0']
                [link rel:'icon' href:'/images/logo/favicon.ico' type:'image/x-icon']
                [link rel:'shortcut icon' href:'/images/logo/favicon.ico' type:'image/x-icon']
                [style]
                    .center {
                        text-align: center;
                        font-family: Arial, sans-serif;
                    }
                [/style]
                [script src:'https@cdn.tailwindcss.com'][/script]
            [/head]
            [body]
                $Children
                http@cdn.tailwindcss.com
            [/body]
            [/html]
        ");
    }
}
