<?php
    $content = '';
    require_once 'inc/header.php';

    if(isset($_GET['fbclid']))
        $urlOfFile =  "cont/main.php";
    else
        $urlOfFile = (key($_GET) == '') ? "cont/main.php" : "cont/" . key($_GET) . ".php";

    if(!isset($_SESSION['login']))
        $scriptContent .= 'insertPasswd(); if(!$(\'header\').is(":hidden")) $(\'header\').hide();';
    else {
        if(file_exists($urlOfFile))
            require_once $urlOfFile;
        else {
            require_once 'cont/main.php';
            $scriptContent .= 'bootbox.alert("Nie ma takiej strony! Wyświetlamy stronę główną!");';
        }
        $content .= '<script>if($(\'header\').is(":hidden")) $(\'header\').show();</script>';
    }
        

    require_once 'inc/footer.php';
?>