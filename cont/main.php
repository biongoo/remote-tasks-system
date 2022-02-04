<?php
    $content .= '';

    $stmt = $sql->prepare('SELECT `homeworks`.*, `subjects`.`name` FROM `homeworks` INNER JOIN `subjects` ON `homeworks`.`subject_id` = `subjects`.`id` WHERE `homeworks`.`user_id` = ? AND `status` = 0 ORDER BY CASE WHEN endline = \'0000-00-00\' THEN 2 ELSE 1 END, endline');
    $stmt->execute([$_SESSION['login']]);
    $homeworks_list = $stmt->fetchAll();

    $content .= '<div class="card m-2">';
        $content .= '<h3 class="card-header text-center font-weight-bold text-uppercase py-4">System Zadań Zdalnych<span class="table-add float-right"><a href="javascript:void(0)" class="text-success" id="addRowMain"><i class="fas fa-plus" aria-hidden="true"></i></a></span></h3>';

        $content .= '<div class="card-body">';
            if(count($homeworks_list)) {
                $content .= '<div class="table-responsive fixed-table-body">';
                    $content .= '<table class="table table-striped table-hover table-bordered">';
                        $content .= '<thead>';
                            $content .= '<tr>';
                                $content .= '<th style="display: none;">#</th>';
                                $content .= '<th scope="col" class="text-center">Deadline</th>';
                                $content .= '<th scope="col" class="text-center">Przedmiot</th>';
                                $content .= '<th scope="col" class="text-center">Treść</th>';
                                //$content .= '<th scope="col" class="text-center">Pliki</th>';
                            $content .= '</tr>';
                        $content .= '</thead>';
                        $content .= '<tbody id="tableOfHomeworks">';

                            foreach($homeworks_list as $homework) { 
                                $content .= '<tr class="homTR" id="homTR'.$homework['id'].'">';
                                    $content .= '<th style="display: none;">'.$homework['id'].'</th>';

                                    if($homework['endline'] != NULL) {
                                        $sub = (strtotime($homework['endline']) - strtotime(date("Y-m-d"))) / 86400;

                                        if($sub <= 1)
                                            $content .= '<td class="text-nowrap text-danger">'.$homework['endline'].'</td>';
                                        elseif($sub <= 3)
                                            $content .= '<td class="text-nowrap text-warning">'.$homework['endline'].'</td>';
                                        else
                                            $content .= '<td class="text-nowrap">'.$homework['endline'].'</td>';
                                    }
                                    else
                                        $content .=  '<td class="text-nowrap">Brak</td>';

                                    $content .= '<td>'.$homework['name'].'</td>';
                                    $content .= '<td>'.$homework['description'].'</td>';
                                $content .= '</tr>';
                            }

                        $content .= '</tbody>';
                    $content .= '</table>';
                $content .= '</div>';
            }
            else
                $content .= '<h3 class="text-center">Aby dodać nowe zadanie naciśnij zielonego plusika! </h3>';
        $content .= '</div>';

    $content .= '</div>';

    $content .= '<script id="scrTR">$(".homTR").on("click", function(){ showHomework($(this)); });</script>';

    $content .= '<script>$("#addRowMain").on("click", function(){ addHomework(); });</script>';

?>