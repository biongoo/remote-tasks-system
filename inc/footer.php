<?php
            $content .= '';
            $content .= '</div>';

            $content .= '<script src="js/jquery-3.4.1.min.js"></script>';
            $content .= '<script src="js/popper.min.js"></script>';
            $content .= '<script src="js/bootstrap.min.js"></script>';
            $content .= '<script src="js/bootbox.min.js"></script>';
            $content .= '<script src="js/main.js"></script>';
            $content .= '<script>'.$scriptContent.'</script>';
            
        $content .= '</body>';
        $content .= '</html>';

    echo $content;
?>