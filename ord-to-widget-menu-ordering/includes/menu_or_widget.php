<?php

function ordto_add_widget()
{
    $url = fopen(__DIR__ . '/url_site.txt', 'r');
    $new_url = fread($url, strlen(file_get_contents(__DIR__ . '/url_site.txt')) - 1);
    fclose($url);

    ?>
    <script type="text/javascript" src="<?php echo $new_url; ?>/widget/widget.min.js"></script>
    <div id="miniorders-widget-wrapper" style="display: none;">
        <div data-miniorders-widget-url="<?php echo $new_url; ?>" onclick="event.preventDefault();
         miniordersStartWidget();" id="miniorders-widget-tab">
            <a id="miniorders-widget-tab-name" href="#"></a>
        </div>
        <iframe id="miniorders-iframe" width="0" height="0"></iframe>
        <div id="miniorders-widget-close" onclick="event.preventDefault(); miniordersStartWidget();">
            <div id="miniorders-widget-close-img"></div>
        </div>
    </div>
    <?php

}

function add_js_script_button()
{
    ?>
    <script type="text/javascript">
        let ul = document.getElementsByClassName("menu-item")[0].parentNode;
        let new_li = document.createElement('li');

        new_li.id = "menu-item";
        new_li.className = "menu-item menu-item-type-custom";
        ul.prepend(new_li);

        let li = document.getElementById("menu-item");
        let new_form = document.createElement('form');

        new_form.id = "new_form_id"
        new_form.method = "POST";
        new_form.style = "text-align: center;";
        li.append(new_form);

        let form = document.getElementById("new_form_id");
        let new_but = document.createElement('button');

        new_but.id = "menu_view_button";
        new_but.type = "submit";
        new_but.name = "frame_menu_view";
        new_but.style = "font-size: 18px;";
        form.append(new_but);

        let but = document.getElementById("menu_view_button");
        but.append("MENU");
    </script>
    <?php
}

function ordto_view_frame()
{
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_POST['frame_menu_view'])) {
            ?>
            <div style="z-index: 999999; position: fixed; bottom: 0; top: 50px; left: 50px; right: 50px; background: #FFFFFF">
                <div style="width: 100%; height: 100%; background: #ffffff">
                    <div style="position: absolute; right: 15px; top: 15px; ">
                        <form method="post">
                            <button name="menu_but" type="submit" style="opacity: 0.5; background: #ffffff;">✖</button>
                        </form>

                    </div>
                    <div style="width: 100%; height: 100%;">
                        <iframe style="width: 100%; height: 100%;"
                                src="<?php echo file_get_contents(__DIR__ . '/url_site.txt') . '?hideheader=1'; ?>">
                        </iframe>
                    </div>
                </div>
            </div>
            <?php
        } elseif (!empty($_POST['menu_but'])) return;
    }
}

function ordto_view_public()
{
    if (file_exists(__DIR__ . '/wm.txt')) {

        $mode = file_get_contents(__DIR__ . '/wm.txt');

        if ($mode === 'widget' && file_exists(__DIR__ . '/url_site.txt')) {

            add_action('wp_footer', 'ordto_add_widget');

        } elseif ($mode == 'menu' && file_exists(__DIR__ . '/url_site.txt')) {

            add_action('wp_footer', 'add_js_script_button');
            add_action('wp_footer', 'ordto_view_frame');

        }
    }
}

?>