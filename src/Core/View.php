<?php

namespace App\Core;

class View
{
    public static function render($view, $data = [])
    {
        extract($data);
        $viewFile = BASE_PATH . '/src/Views/' . $view . '.php';

        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();

            // Strip UTF-8 BOM if present (PowerShell sometimes adds it)
            if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
                $content = substr($content, 3);
            }

            // If the view declared its own full HTML page, output standalone
            if (stripos(trim($content), '<!DOCTYPE html>') === 0 ||
                stripos(trim($content), '!DOCTYPE html>') === 0) {
                echo $content;
            } else {
                require BASE_PATH . '/src/Views/layouts/main.php';
            }
        } else {
            die("View $view not found.");
        }
    }

}
