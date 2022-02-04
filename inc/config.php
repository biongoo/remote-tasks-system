<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    //$password = '1';
    $name = '';

    $content = '';
    $scriptContent = '';

    if (!isset($_SESSION))
        session_start();

    //unset($_SESSION['login']);

    $server_ip = 'x.x.x.x';
    $db_name = 'systemZZ';
    $login = 'xxxxxxx';
    $passwd = 'xxxxxxx';
    $charset = "utf8";

    try {
        $dsn = "mysql:host=".$server_ip.";dbname=".$db_name.";charset=".$charset;
        $sql = new PDO($dsn, $login, $passwd);
        $sql -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        echo "Połączenie nieudane: ".$e->getMessage();
        exit;
    }

    define ('SITE_ROOT', realpath(dirname(__FILE__)."/.."));
    ini_set('upload_max_filesize', '10M');

    require_once 'functions.php';
?>