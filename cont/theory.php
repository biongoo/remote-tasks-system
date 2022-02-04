<?php
    $content .= '';

    $stmt = $sql->prepare('SELECT `theory`.*, `subjects`.`name` FROM `theory` INNER JOIN `subjects` ON `theory`.`subject_id` = `subjects`.`id` WHERE `theory`.`user_id` = ? AND `status` = 0 ORDER BY `date` DESC');
    $stmt->execute([$_SESSION['login']]);
    $theory_list = $stmt->fetchAll();

    $content .= '<div class="card m-2">';
        $content .= '<h3 class="card-header text-center font-weight-bold text-uppercase py-4">Teoria<span class="table-add float-right"><a href="javascript:void(0)" class="text-success" id="addRowMain"><i class="fas fa-plus" aria-hidden="true"></i></a></span></h3>';

        $content .= '<div class="card-body">';
            if(count($theory_list)) {
                $content .= '<div class="table-responsive fixed-table-body">';
                    $content .= '<table class="table table-striped table-hover table-bordered">';
                        $content .= '<thead>';
                            $content .= '<tr>';
                                $content .= '<th style="display: none;">#</th>';
                                $content .= '<th scope="col" class="text-center">Data</th>';
                                $content .= '<th scope="col" class="text-center">Przedmiot</th>';
                                $content .= '<th scope="col" class="text-center">Krótki Opis</th>';
                            $content .= '</tr>';
                        $content .= '</thead>';
                        $content .= '<tbody id="tableOfTheory">';

                            foreach($theory_list as $theory) { 
                                $content .= '<tr class="theTR" id="theTR'.$theory['id'].'">';
                                    $content .= '<th style="display: none;">'.$theory['id'].'</th>';
                                    $content .= '<td class="text-nowrap">'.$theory['date'].'</td>';
                                    $content .= '<td>'.$theory['name'].'</td>';
                                    $content .= '<td>'.$theory['description'].'</td>';
                                $content .= '</tr>';
                            }

                        $content .= '</tbody>';
                    $content .= '</table>';
                $content .= '</div>';
            }
            else
                $content .= '<h3 class="text-center">Aby dodać nową teorię naciśnij zielonego plusika! </h3>';
        $content .= '</div>';

    $content .= '</div>';

    $content .= '<script id="scrTR">$(".theTR").on("click", function(){ showTheory($(this)); });</script>';

    $content .= '<script>$("#addRowMain").on("click", function(){ addTheory(); });</script>';

?>