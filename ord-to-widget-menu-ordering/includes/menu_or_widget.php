<?php

function add_js_script_button()
{
    ?>
    <script type="text/javascript">
        let ul = document.getElementsByClassName("menu-wrapper")[0];
        let new_li = document.createElement('li');

        new_li.id = "new_li_id";
        ul.append(new_li);

        let li = document.getElementById("new_li_id");
        let new_form = document.createElement('form');

        new_form.id = "new_form_id"
        new_form.method = "POST";
        li.append(new_form);

        let form = document.getElementById("new_form_id");
        let new_but = document.createElement('button');

        new_but.id = "menu_view_button";
        new_but.name = "frame_menu_view";
        new_but.type = "submit"
        // new_but.value = "MENU";
        form.append(new_but);

        let but = document.getElementById("menu_view_button");

        but.append("MENU");
    </script>
    <?php
}

function ordto_view_public()
{
    if (file_exists(__DIR__ . '/wm.txt')) {

        $mode = file_get_contents(__DIR__ . '/wm.txt');

        if ($mode === 'widget') {

            $widget = file_get_contents(__DIR__ . '/widget_code.php');
            echo $widget;

        } elseif ($mode == 'menu') {

            add_action('wp_footer', 'add_js_script_button');
            ordto_view_frame();

        }
    }
}

function ordto_view_frame()
{
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_POST['frame_menu_view'])) {
            ?>
            <div style="z-index: 9; position: fixed; bottom: 0; top: 50px; left: 50px; right: 50px; background: #FFFFFF">
                <form method="post">
                    <button name="menu_but" type="submit" style="width: 100%; background: #ff937d;"></button>
                </form>
                <div style="bottom: 0; right: 20px; background: #FFFFFF">
                    <iframe src="<?php echo file_get_contents(__DIR__ . '/url_site.txt'); ?>" width="100%"
                            height="100%">
                    </iframe>
                </div>
            </div>
            <?php
        } elseif (!empty($_POST['menu_but'])) return;
    }
}

?>