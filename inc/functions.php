<?php
    function alertToHome($info){
        //$_SESSION['msg'] = $info;
        header('Location: /');
		exit;
    }

    function deleteFiles($files) {
        foreach($files as $file) {
            $filename = SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $file;
            if(file_exists($filename))
                unlink($filename);
            else
                return 0;
        }
        return 1;
    }

    function error($info) {
        $array = [0, $info];
        echo json_encode($array);
        exit;
    }

    function logout($info) {
        unset($_SESSION['login']);
        unset($_SESSION['passwd']);
    }
?>