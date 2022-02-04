<?php
    require_once 'config.php';

    $content .= '<!DOCTYPE html>';
        $content .= '<html lang="pl" dir="ltr">';
        $content .= '<head>';
            $content .= '<meta charset="utf-8">';
            $content .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=yes">';
            $content .= '<meta name="author" content="biongoo">';
            $content .= '<meta http-equiv="X-Ua-Compatible" content="IE=edge">';

            $content .= '<title>System Zadań Zdalnych</title>';

            // CSS
            $content .= '<link rel="stylesheet" href="css/all.css">';
            $content .= '<link rel="stylesheet" href="css/bootstrap.min.css">';
            $content .= '<link rel="stylesheet" href="css/main.css">';

            $content .= '<script src="js/jquery-3.4.1.min.js"></script>';

        $content .= '</head>';
        $content .= '<body class="bg-dark">';

            $content .= '<header>';
                $content .= '<nav class="navbar navbar-expand-lg navbar-dark bg-primary">';
                    $content .= '<a class="navbar-brand" href="javascript:void(0)" onclick="change(\'main\');">System Zadań Zdalnych</a>';
                    $content .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
                        $content .= '<span class="navbar-toggler-icon"></span>';
                    $content .= '</button>';
                    
                    $content .= '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
                        $content .= '<ul class="navbar-nav mr-auto">';

                            $content .= '<li class="nav-item">';
                                $content .= '<a class="nav-link" href="javascript:void(0)" onclick="change(\'main\');">Strona Główna</a>';
                            $content .= '</li>';

                            $content .= '<li class="nav-item">';
                                $content .= '<a class="nav-link" href="javascript:void(0)" onclick="change(\'theory\');">Teoria</a>';
                            $content .= '</li>';

                            $content .= '<li class="nav-item">';
                                $content .= '<a class="nav-link" href="javascript:void(0)" onclick="change(\'subjects\');">Przedmioty</a>';
                            $content .= '</li>';

                        $content .= '</ul>';
                    $content .= '</div>';
                $content .= '</nav>';
            $content .= '</header>';

            $content .= '<div class="container p-0" id="main">';
?>