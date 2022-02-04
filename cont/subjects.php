<?php
    $content .= '';

    $stmt = $sql->prepare('SELECT `subjects`.* FROM `subjects` WHERE `user_id` = ?');
    $stmt->execute([$_SESSION['login']]);
    $subjects_list = $stmt->fetchAll();

    $content .= '<div class="card m-2">';
        $content .= '<h3 class="card-header text-center font-weight-bold text-uppercase py-4">Przedmioty<span class="table-add float-right"><a href="javascript:void(0)" class="text-success" id="addRow"><i class="fas fa-plus" aria-hidden="true"></i></a></span></h3>';

        $content .= '<div class="card-body">';
            if(count($subjects_list)) {
                $content .= '<div class="table-responsive fixed-table-body">';
                    $content .= '<table class="table table-striped table-hover table-bordered">';
                        $content .= '<thead>';
                            $content .= '<tr>';
                                $content .= '<th style="display: none;">#</th>';
                                $content .= '<th style="width: 25%" scope="col">Nazwa przedmiotu</th>';
                                $content .= '<th style="width: 15%" scope="col">Nauczyciel</th>';
                                $content .= '<th style="width: 15%" scope="col">Grupa</th>';
                                $content .= '<th scope="col">Opis</th>';
                            $content .= '</tr>';
                        $content .= '</thead>';
                        $content .= '<tbody id="tableOfSubjects">';

                            foreach($subjects_list as $subject) { 
                                $content .= '<tr class="subTR" id="subTR'.$subject['id'].'">';
                                    $content .= '<th style="display: none;">'.$subject['id'].'</th>';
                                    $content .= '<td>'.$subject['name'].'</td>';
                                    $content .= '<td>'.$subject['teacher'].'</td>';
                                    $content .= '<td>'.$subject['group'].'</td>';
                                    $content .= '<td>'.$subject['description'].'</td>';
                                $content .= '</tr>';
                            }

                        $content .= '</tbody>';
                    $content .= '</table>';
                $content .= '</div>';
            }
            else
                $content .= '<h3 class="text-center">Aby dodać nowy przedmiot naciśnij zielonego plusika! </h3>';
        $content .= '</div>';

    $content .= '</div>';

    $content .= '<script id="scrTR">$(".subTR").on("click", function(){ editSubjects($(this)); });</script>';

    $content .= '<script>$("#addRow").on("click", function(){ addSubject(); });</script>';

?>