<?php
function ordto_config_view()
{
    ?>
    <form method='post'>
        <p>Here you can:</p>
        <p> - change all your configuration settings at one time</p>
        <p> - change your API key only</p>
        <p> - change your site configuration only (widget script and site URL)</p>
        <p> - change view mode only (widget/menu)</p>
        <hr>
        <label for="inp_api">
            <p> Input your API from ord . to -> Integrations -> API -> API key for this company:</p>
        </label>
        <textarea id="inp_api" name='api_key' cols="40" rows="1" placeholder="API key" autofocus></textarea>
        <br>
        <label for="inp_widget">
            <p> Input your widget script from ord . to -> Settings -> Widget:</p>
        </label>
        <textarea id="inp_widget" name='widget_code' cols="40" rows="1" placeholder="Widget script"></textarea>
        <br>
        <label for="site_url">
            <p> Input your site URL from ord . to -> Go to your page:</p>
        </label>
        <textarea id="site_url" name='url_site' cols="40" rows="1" placeholder="Site URL"></textarea>
        <br>
        <p> Select a view mode:</p>
        <input id="menu" type="radio" name="menu/widget" value="menu" checked> <label for="menu"> Menu</label>
        <br>
        <input id="widget" type="radio" name="menu/widget" value="widget"> <label for="widget"> Widget</label>
        <br>
        <br>
        <input type='reset' name='res1' value="Reset">
        <input type='submit' name='sub1' value="Submit">
    </form>
    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!empty($_POST['api_key']) && !empty($_POST['widget_code']) && !empty($_POST['url_site'])) {

            file_put_contents(__DIR__ . '/api_key.yaml', stripslashes($_POST['api_key']));
            file_put_contents(__DIR__ . '/widget_code.php', stripslashes($_POST['widget_code']));
            file_put_contents(__DIR__ . '/url_site.txt', stripslashes($_POST['url_site'])); //+full screen tumbler
            file_put_contents(__DIR__ . '/wm.txt', stripslashes($_POST['menu/widget']));
            echo "<br>All configuration added successfully!";

        } elseif (empty($_POST['api_key']) && !empty($_POST['widget_code']) && !empty($_POST['url_site'])) {

            file_put_contents(__DIR__ . '/widget_code.php', stripslashes($_POST['widget_code']));
            file_put_contents(__DIR__ . '/url_site.txt', stripslashes($_POST['url_site']));
            echo "<br>Widget script and the site URL changed successfully!";

        } elseif (!empty($_POST['api_key']) && empty($_POST['widget_code']) && empty($_POST['url_site'])) {

            file_put_contents(__DIR__ . '/api_key.yaml', stripslashes($_POST['api_key']));
            echo "<br>API key changed successfully!";

        } else {

            file_put_contents(__DIR__ . '/wm.txt', stripslashes($_POST['menu/widget']));
            echo "<br>View mode changed successfully!";

        }
    }
}

?>