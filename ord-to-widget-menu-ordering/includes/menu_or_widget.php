<?php
function ordto_select_view_mode()
{
    if (file_exists(__DIR__ . '/wm.txt')) {

        $mode = file_get_contents(__DIR__ . '/wm.txt');

        if ($mode === 'widget') {
            $widget = file_get_contents(__DIR__ . '/widget_code.php');
            echo $widget;
        } elseif ($mode === 'menu') {
            if (!empty($_POST['menu_page_view'])) {
                ordto_menu_frame_view();
            } elseif (!empty($_POST['menu_but'])) {
                menu_button_view();
            } else menu_button_view();
        }
    }
}