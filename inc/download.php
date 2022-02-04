<?php
    require_once 'config.php';

    if(!isset($_SESSION['login']))
        exit;

    if(isset($_GET['downloadFile'])) {
        $stmt = $sql->prepare('SELECT `files` FROM `homeworks` WHERE `homeworks`.`user_id` = ? AND `homeworks`.`id` = ?');
        $stmt->execute([$_SESSION['login'], $_GET['downloadHomId']]);
        $files = $stmt->fetchColumn();

        $array = json_decode($files);

        if(!empty($array)) {
            if (!file_exists(SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_GET['downloadFile']) || !in_array($_GET['downloadFile'], $array))
                exit;

            $file_url = SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_GET['downloadFile'];
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . $_GET['downloadFile'] . "\""); 
            readfile($file_url);
        }
    }

    if(isset($_GET['downloadFileTheory'])) {
        $stmt = $sql->prepare('SELECT `files` FROM `theory` WHERE `theory`.`user_id` = ? AND `theory`.`id` = ?');
        $stmt->execute([$_SESSION['login'], $_GET['downloadTheId']]);
        $files = $stmt->fetchColumn();

        $array = json_decode($files);

        if(!empty($array)) {
            $m = file_exists(SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_GET['downloadFileTheory']);
            echo $_GET['downloadFileTheory'];
            if (!file_exists(SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_GET['downloadFileTheory']) || !in_array($_GET['downloadFileTheory'], $array)) {
                exit;
            }

            $file_url = SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_GET['downloadFileTheory'];
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . $_GET['downloadFileTheory'] . "\""); 
            readfile($file_url);
        }
    }
?>