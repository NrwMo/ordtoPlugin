<?php
function menu_button_view()
{

    ?>
    <form method="POST">
        <input name="menu_page_view" type="submit" style="position: fixed; bottom: 10px; right: 10px;" value="MENU"">
    </form>
    <?php
}

function ordto_menu_frame_view()
{
    ?>
    <div style="position: fixed; bottom: 0; top: 50px; left: 50px; right: 50px; background: #FFFFFF">
        <form method="post">
            <button name="menu_but" type="submit" style="width: 100%; background: #ff937d;"></button>
        </form>
        <div style="bottom: 0; right: 20px; background: #FFFFFF">
            <iframe src='<?php echo file_get_contents(__DIR__ . '/url_site.txt'); ?>' width="100%" height="100%">
            </iframe>
        </div>
    </div>
    <?php
}

?>
