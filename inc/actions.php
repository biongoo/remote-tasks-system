<?php
    require_once 'config.php';

    if(isset($_POST['addHomework'])) {
        if(!isset($_SESSION['login']))
            error('Sesja została zakończona. Odśwież stronę!');

        $addHMSubject = $_POST['addHomework'];
        $addHMDate = $_POST['addHMDate'];
        $addHMEndline = $_POST['addHMEndline'];
        $addHMDescription = $_POST['addHMDescription'];

        $allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'html', 'zip', 'mp3', 'wma', 'mpg', 'flv', 'avi', 'jpg', 'jpeg', 'png', 'gif', 'rar'];

        if(isset($_FILES['files']) && !empty($_FILES['files'])) {
            $no_files = count($_FILES["files"]['name']);

            if(count($_FILES["files"]["name"]) > 10)
                error('Na raz możesz przesłać maksymalnie 10 plików!');
            
            $totalFileSize = array_sum($_FILES['files']['size']);

            if($totalFileSize > 41943040)
                error('Rozmiar wszystkich plików nie może przekraczać 40MB!');

            for ($i = 0; $i < $no_files; $i++) {
                if($_FILES['files']['size'][$i] > 10485760) //10MB
                    error('Twój plik <b style="word-break: break-all;">'.$_FILES["files"]["name"][$i].'</b> jest za duży! Posiada więcej niż 10MB. Aby dodawać większe pliki, skontaktuj się z Administratorem.');

                if ($_FILES["files"]["error"][$i] > 0)
                    error("Error: " . $_FILES["files"]["error"][$i] . "<br>");
                else {
                    $path_parts = pathinfo($_FILES["files"]["name"][$i]);
                    $extension = strtolower($path_parts['extension']);

                    if(!in_array($extension, $allowedExt))
                        error('Niedozwolony typ pliku: <b>' . $extension . '</b>. Skontaktuj się z Administratorem, aby umożliwić wysyłanie tego typu plików.');

                    if (file_exists(SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_FILES["files"]["name"][$i]))
                        error('Podany plik już znajduje się na serwerze : <b style="word-break: break-all;">' . $_FILES["files"]["name"][$i] . "</b>. Aby dodać plik usuń go z innych zadań/teorii, bądź zmień nazwę nowego pliku!<br><br>");

                }
            }

            for ($i = 0; $i < $no_files; $i++) {
                if ($_FILES["files"]["error"][$i] > 0)
                    error("Error: " . $_FILES["files"]["error"][$i] . "<br>");
                else
                    move_uploaded_file($_FILES["files"]["tmp_name"][$i], SITE_ROOT.'/files/' . $_SESSION['login'] . '->' .  $_FILES["files"]["name"][$i]);
            }

            $filesName = json_encode($_FILES["files"]['name']);
        }
        else
            $filesName = '';

        $stmt = $sql->prepare('SELECT `user_id` FROM `subjects` WHERE `subjects`.`id` = ?');
        $stmt->execute([$addHMSubject]);
        $user_id = $stmt->fetchColumn();

        if($user_id != $_SESSION['login'])
            error('Błąd nr: 2');

        if(empty($addHMDate)) 
			$addHMDate = date('Y.m.d');

        if(empty($addHMEndline)) 
			$addHMEndline = NULL;

        $stmt = $sql->prepare('INSERT INTO `homeworks` (`subject_id`, `user_id`, `date`, `endline`, `files`, `description`) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$addHMSubject, $user_id, $addHMDate, $addHMEndline, $filesName, $addHMDescription]);
        $del_count = $stmt->rowCount();
        $lastInId = $sql->lastInsertId();

        if($del_count == 1) {
            $stmt = $sql->prepare('SELECT `homeworks`.*, `subjects`.`name` FROM `homeworks` INNER JOIN `subjects` ON `homeworks`.`subject_id` = `subjects`.`id` WHERE `homeworks`.`user_id` = ? AND `status` = 0 ORDER BY CASE WHEN endline = \'0000-00-00\' THEN 2 ELSE 1 END, endline');
            $stmt->execute([$_SESSION['login']]);
            $rank_list = $stmt->fetchAll();

            $i = 0;

            foreach($rank_list as $rank) {
                if($rank[0] == $lastInId)
                    break;
                $i++;
            }
                

            $array = [1, $lastInId, $i];
            echo json_encode($array);
        }
        else
            echo 0;
    }

    if(isset($_POST['addSubject'])) {
        if(!isset($_SESSION['login'])) {
            echo 0;
            exit;
        }

        $addName = $_POST['addSubject'];
        $addTeacher = $_POST['addSubjectTeacher'];
        $addGroup = $_POST['addSubjectGroup'];
        $addDescription = $_POST['addSubjectDescription'];

        $stmt = $sql->prepare('INSERT INTO `subjects` (`name`, `teacher`, `group`, `description`, `user_id`) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$addName, $addTeacher, $addGroup, $addDescription, $_SESSION['login']]);
        $del_count = $stmt->rowCount();

        if($del_count == 1)
            echo $sql->lastInsertId();
        else
            echo 0;

    }

    if(isset($_POST['addTheory'])) {
        if(!isset($_SESSION['login']))
            error('Błąd nr: 1');

        $addTHSubject = $_POST['addTheory'];
        $addTHDate = $_POST['addTHDate'];
        $addTHDescription = $_POST['addTHDescription'];

        $allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'html', 'zip', 'mp3', 'wma', 'mpg', 'flv', 'avi', 'jpg', 'jpeg', 'png', 'gif', 'rar'];

        if(isset($_FILES['files']) && !empty($_FILES['files'])) {
            $no_files = count($_FILES["files"]['name']);

            if(count($_FILES["files"]["name"]) > 10)
                error('Na raz możesz przesłać maksymalnie 10 plików!');
            
            $totalFileSize = array_sum($_FILES['files']['size']);

            if($totalFileSize > 41943040)
                error('Rozmiar wszystkich plików nie może przekraczać 40MB!');

            for ($i = 0; $i < $no_files; $i++) {
                if($_FILES['files']['size'][$i] > 10485760) //10MB
                    error('Twój plik <b style="word-break: break-all;">'.$_FILES["files"]["name"][$i].'</b> jest za duży! Posiada więcej niż 10MB. Aby dodawać większe pliki, skontaktuj się z Administratorem.');

                if ($_FILES["files"]["error"][$i] > 0)
                    error("Error: " . $_FILES["files"]["error"][$i] . "<br>");
                else {
                    $path_parts = pathinfo($_FILES["files"]["name"][$i]);
                    $extension = strtolower($path_parts['extension']);

                    if(!in_array($extension, $allowedExt))
                        error('Niedozwolony typ pliku: <b>' . $extension . '</b>. Skontaktuj się z Administratorem, aby umożliwić wysyłanie tego typu plików.');

                    if (file_exists(SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_FILES["files"]["name"][$i]))
                        error('Podany plik już znajduje się na serwerze : <b style="word-break: break-all;">' . $_FILES["files"]["name"][$i] . "</b>. Aby dodać plik usuń go z innych zadań/teorii, bądź zmień nazwę nowego pliku!<br><br>");

                }
            }

            for ($i = 0; $i < $no_files; $i++) {
                if ($_FILES["files"]["error"][$i] > 0)
                    error("Error: " . $_FILES["files"]["error"][$i] . "<br>");
                else
                    move_uploaded_file($_FILES["files"]["tmp_name"][$i], SITE_ROOT.'/files/' . $_SESSION['login'] . '->' .  $_FILES["files"]["name"][$i]);
            }

            $filesName = json_encode($_FILES["files"]['name']);
        }
        else
            $filesName = '';

        $stmt = $sql->prepare('SELECT `user_id` FROM `subjects` WHERE `subjects`.`id` = ?');
        $stmt->execute([$addTHSubject]);
        $user_id = $stmt->fetchColumn();

        if($user_id != $_SESSION['login'])
            error('Błąd nr: 2');

        if(empty($addTHDate)) 
			$addTHDate = date('Y.m.d');

        $stmt = $sql->prepare('INSERT INTO `theory` (`subject_id`, `user_id`, `date`, `files`, `description`) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$addTHSubject, $user_id, $addTHDate, $filesName, $addTHDescription]);
        $del_count = $stmt->rowCount();
        $lastInId = $sql->lastInsertId();

        if($del_count == 1) {
            $stmt = $sql->prepare('SELECT `theory`.*, `subjects`.`name` FROM `theory` INNER JOIN `subjects` ON `theory`.`subject_id` = `subjects`.`id` WHERE `theory`.`user_id` = ? AND `status` = 0 ORDER BY `date` DESC');
            $stmt->execute([$_SESSION['login']]);
            $rank_list = $stmt->fetchAll();

            $i = 0;

            foreach($rank_list as $rank) {
                if($rank[0] == $lastInId)
                    break;
                $i++;
            }
                

            $array = [1, $lastInId, $i];
            echo json_encode($array);
        }
        else
            echo 0;
    }

    if(isset($_POST['changePage'])) {
        $nameOfPage = $_POST['changePage'];
        $urlOfFile = ($nameOfPage == '') ? "../cont/main.php" : "../cont/" . $nameOfPage . ".php";

        if(!isset($_SESSION['login']))
            $content .= '<script>insertPasswd(); if(!$(\'header\').is(":hidden")) $(\'header\').hide();</script>';
        else {
            if(file_exists($urlOfFile))
                require_once $urlOfFile;
            else {
                require_once '../cont/main.php';
                $content .= '<script>bootbox.alert("Nie ma takiej strony! Wyświetlamy stronę główną!");</script>';
            }

            $content .= '<script>if($(\'header\').is(":hidden")) $(\'header\').show();</script>';
        }
        echo $content;
    }
    
    if(isset($_POST['deleteHome'])) {
        if(!isset($_SESSION['login'])) {
            echo 0;
            exit;
        }

        $stmt = $sql->prepare('SELECT `files` FROM `homeworks` WHERE `homeworks`.`user_id` = ? AND `homeworks`.`id` = ?');
        $stmt->execute([$_SESSION['login'], $_POST['deleteHome']]);
        $files = $stmt->fetchColumn();

        $stmt = $sql->prepare('DELETE FROM `homeworks` WHERE `homeworks`.`id` = ? AND `homeworks`.`user_id` = ?');
        $stmt->execute([$_POST['deleteHome'], $_SESSION['login']]);
        $del_count = $stmt->rowCount();

        if($del_count == 1) {
            $array = json_decode($files);

            if(!empty($array)) {
                if(deleteFiles($array))
                    echo 1;
                else
                    echo 0;
            }
            else
                echo 1;
        }
        else
            echo 0;
    }

    if(isset($_POST['deleteSubject'])) {
        if(!isset($_SESSION['login'])) {
            echo 0;
            exit;
        }

        $stmt = $sql->prepare('DELETE FROM `subjects` WHERE `subjects`.`id` = ? AND `subjects`.`user_id` = ?');
        $stmt->execute([$_POST['deleteSubject'], $_SESSION['login']]);
        $del_count = $stmt->rowCount();

        $array = [];

        $stmt = $sql->prepare('SELECT `files` FROM `homeworks` WHERE `homeworks`.`user_id` = ? AND `homeworks`.`subject_id` = ?');
        $stmt->execute([$_SESSION['login'], $_POST['deleteSubject']]);
        while ($row = $stmt->fetch()) {
            $tempArray = json_decode($row['files']);
            if(count($tempArray))
                $array = array_merge($array, $tempArray);
        }

        if($del_count == 1) {
            $stmt = $sql->prepare('DELETE FROM `homeworks` WHERE `homeworks`.`subject_id` = ? AND `homeworks`.`user_id` = ?');
            $stmt->execute([$_POST['deleteSubject'], $_SESSION['login']]);
            
            if(!empty($array)) {
                if(deleteFiles($array))
                    echo 1;
                else
                    echo 0;
            }
            else
                echo 1;
        }
        else
            echo 0;

    }

    if(isset($_POST['deleteTheory'])) {
        if(!isset($_SESSION['login'])) {
            echo 0;
            exit;
        }

        $stmt = $sql->prepare('SELECT `files` FROM `theory` WHERE `theory`.`user_id` = ? AND `theory`.`id` = ?');
        $stmt->execute([$_SESSION['login'], $_POST['deleteTheory']]);
        $files = $stmt->fetchColumn();

        $stmt = $sql->prepare('DELETE FROM `theory` WHERE `theory`.`id` = ? AND `theory`.`user_id` = ?');
        $stmt->execute([$_POST['deleteTheory'], $_SESSION['login']]);
        $del_count = $stmt->rowCount();

        if($del_count == 1) {
            $array = json_decode($files);

            if(!empty($array)) {
                if(deleteFiles($array))
                    echo 1;
                else
                    echo 0;
            }
            else
                echo 1;
        }
        else
            echo 0;
    }

    if(isset($_POST['downloadFile'])) {
        if(!isset($_SESSION['login'])) {
            echo 'Sesja została zakończona. Odśwież stronę!';
            exit;
        }

        $stmt = $sql->prepare('SELECT `files` FROM `homeworks` WHERE `homeworks`.`user_id` = ? AND `homeworks`.`id` = ?');
        $stmt->execute([$_SESSION['login'], $_POST['downloadHomId']]);
        $files = $stmt->fetchColumn();

        $array = json_decode($files);

        if(!empty($array)) {
            if (!file_exists(SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_POST['downloadFile']) || !in_array($_POST['downloadFile'], $array)) {
                echo "Błąd pobierania pliku! Nie odnaleziono pliku na serwerze.";
                exit;
            } 
            echo 1;
        }
        else 
            echo "Błąd pobierania pliku! Nie odnaleziono pliku na serwerze.";
    }

    if(isset($_POST['downloadFileTheory'])) {
        if(!isset($_SESSION['login'])) {
            echo 'Sesja została zakończona. Odśwież stronę!';
            exit;
        }

        $stmt = $sql->prepare('SELECT `files` FROM `theory` WHERE `theory`.`user_id` = ? AND `theory`.`id` = ?');
        $stmt->execute([$_SESSION['login'], $_POST['downloadTheId']]);
        $files = $stmt->fetchColumn();

        $array = json_decode($files);

        if(!empty($array)) {
            if (!file_exists(SITE_ROOT.'/files/' . $_SESSION['login'] . '->' . $_POST['downloadFileTheory']) || !in_array($_POST['downloadFileTheory'], $array)) {
                echo "Błąd pobierania pliku! Nie odnaleziono pliku na serwerze.";
                exit;
            } 
            echo 1;
        }
        else 
            echo "Błąd pobierania pliku! Nie odnaleziono pliku na serwerze.";
    }

    if(isset($_POST['editSubject'])) {
        if(!isset($_SESSION['login'])) {
            echo 0;
            exit;
        }

        $subjectId = $_POST['editSubject'];
        $newName = $_POST['editSubjectName'];
        $newTeacher = $_POST['editSubjectTeacher'];
        $newGroup = $_POST['editSubjectGroup'];
        $newDescription = $_POST['editSubjectDescription'];

        $stmt = $sql->prepare('UPDATE `subjects` SET `subjects`.`name` = ?, `subjects`.`teacher` = ?, `subjects`.`group` = ?, `subjects`.`description` = ? WHERE `subjects`.`id` = ? AND `subjects`.`user_id` = ?');
        $stmt->execute([$newName, $newTeacher, $newGroup, $newDescription, $subjectId, $_SESSION['login']]);
        $del_count = $stmt->rowCount();

        if($del_count == 1)
            echo 1;
        else
            echo 0;

    }

    if(isset($_POST['passwd'])) {
        $stmt = $sql->prepare('SELECT `id` FROM `users` WHERE `users`.`password` = ?');
        $stmt->execute([$_POST['passwd']]);
        $id = $stmt->fetchColumn();

        if($id) {
            $_SESSION['login'] = $id;
            $_SESSION['passwd'] = $_POST['passwd'];
            require_once '../cont/main.php';
            $content .= '<script>if($(\'header\').is(":hidden")) $(\'header\').show();</script>';
            echo $content;
        }
        else
            echo 0;
    }

    if(isset($_POST['showSubjects'])) {
        if(!isset($_SESSION['login'])) {
            echo 0;
            exit;
        }

        $stmt = $sql->prepare('SELECT `id`, `name` FROM `subjects` WHERE `user_id` = ?');
        $stmt->execute([$_SESSION['login']]);
        $options_list = $stmt->fetchAll();

        $options = '<option value=""></option>';

        foreach($options_list as $option) { 
            $options .= '<option value="' . $option['id'] . '">' . $option['name'] . '</option>';
        }

        
        echo $options;

    }

    if(isset($_POST['showHomework'])) {
        if(!isset($_SESSION['login'])) {
            echo 0;
            exit;
        }

        $stmt = $sql->prepare('SELECT `homeworks`.`date`, `homeworks`.`endline`, `homeworks`.`description`, `homeworks`.`files`, `subjects`.`name` FROM `homeworks` INNER JOIN `subjects` ON `homeworks`.`subject_id` = `subjects`.`id` WHERE `homeworks`.`user_id` = ? AND `homeworks`.`id` = ?');
        $stmt->execute([$_SESSION['login'], $_POST['showHomework']]);
        $homework = $stmt->fetch();

        echo json_encode($homework);
    }

    if(isset($_POST['showTheory'])) {
        if(!isset($_SESSION['login'])) {
            echo 0;
            exit;
        }

        $stmt = $sql->prepare('SELECT `theory`.`date`, `theory`.`description`, `theory`.`files`, `subjects`.`name` FROM `theory` INNER JOIN `subjects` ON `theory`.`subject_id` = `subjects`.`id` WHERE `theory`.`user_id` = ? AND `theory`.`id` = ?');
        $stmt->execute([$_SESSION['login'], $_POST['showTheory']]);
        $theory = $stmt->fetch();

        echo json_encode($theory);
    }
?>