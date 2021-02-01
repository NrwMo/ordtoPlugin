<?php
function ordto_config_view()
{
    ordto_post_config();
    if(!file_exists(__DIR__ . '/api_key.txt')){
        ?>
        <div class="new_user_banner" style='padding: 15px; margin-top: 20px; margin-right: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; color: #765c3c; background-color: #f0e8d8; border-color: #e9dfc6;'>
            If you do not have an account at ord.to,
            then you can create one
            <a href="https://cloud.ord.to/register" target="_blank">here</a>
        </div>
        <?php
    }
    ?>
    <form method='post'>
        <p>Here you can:</p>
        <p> - change all your configuration settings at one time</p>
        <p> - change your API key only</p>
        <p> - change your site configuration only (widget script and site URL)</p>
        <p> - change view mode only (widget/menu)</p>
        <hr>
        <label for="inp_api">
            <p> Input your API from ord.to -> Integrations -> API -> API key for this company:</p>
        </label>
        <input type="text" id="inp_api" name='api_key' size="40"
            <?php if (file_exists(__DIR__ . '/api_key.txt')){
            ?> value="<?php echo file_get_contents(__DIR__ . '/api_key.txt') ?>" <?php
               }else { ?>placeholder="API key" <?php } ?>autofocus>
        <br>
        <label for="inp_widget">
            <p> Input your widget script from ord.to -> Settings -> Widget:</p>
        </label>
        <input type="text" id="inp_widget" name='widget_code' size="40"
            <?php if (file_exists(__DIR__ . '/widget_code.php')) {
                ?> value='<?php echo(file_get_contents(__DIR__ . '/widget_code.php')); ?>' <?php
            } else { ?>placeholder="Widget script"<?php } ?> >
        <br>
        <label for="site_url">
            <p> Input your site URL from ord.to -> Go to your page:</p>
        </label>
        <input type="text" id="site_url" name='url_site' size="40"
            <?php if (file_exists(__DIR__ . '/url_site.txt')) {
                ?> value="<?php echo file_get_contents(__DIR__ . '/url_site.txt') ?>" <?php
            } else { ?>placeholder="Site URL"<?php } ?> >
        <br>
        <p> Select a view mode:</p>
        <input id="menu" type="radio" name="menu/widget" value="menu"
            <?php if (file_exists(__DIR__ . '/wm.txt')) {
                if (file_get_contents(__DIR__ . '/wm.txt') == 'menu') {
                    ?> checked <?php
                }
            } ?> > <label for="menu"> Menu</label>
        <br>
        <input id="widget" type="radio" name="menu/widget" value="widget"
            <?php if (file_exists(__DIR__ . '/wm.txt')) {
                if (file_get_contents(__DIR__ . '/wm.txt') == 'widget') {
                    ?> checked <?php
                }
            } ?> > <label for="widget"> Widget</label>
        <br>
        <br>
        <input type='reset' name='res1' value="Reset">
        <input type='submit' name='sub1' value="Submit">
    </form>
    <?php
}

function ordto_post_config()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!empty($_POST['api_key']) && !empty($_POST['widget_code']) && !empty($_POST['url_site'])) {

            file_put_contents(__DIR__ . '/api_key.txt', stripslashes($_POST['api_key']));
            file_put_contents(__DIR__ . '/widget_code.php', stripslashes($_POST['widget_code']));
            file_put_contents(__DIR__ . '/url_site.txt', stripslashes($_POST['url_site']));
            file_put_contents(__DIR__ . '/wm.txt', stripslashes($_POST['menu/widget']));
            echo "<h3>All configuration added successfully!</h3>";

        } elseif (empty($_POST['api_key']) && !empty($_POST['widget_code']) && !empty($_POST['url_site'])) {

            file_put_contents(__DIR__ . '/widget_code.php', stripslashes($_POST['widget_code']));
            file_put_contents(__DIR__ . '/url_site.txt', stripslashes($_POST['url_site']));
            echo "<h3>Widget script and the site URL changed successfully!</h3>";

        } elseif (!empty($_POST['api_key']) && empty($_POST['widget_code']) && empty($_POST['url_site'])) {

            file_put_contents(__DIR__ . '/api_key.txt', stripslashes($_POST['api_key']));
            echo "<h3>API key changed successfully!</h3>";

        } else {

            file_put_contents(__DIR__ . '/wm.txt', stripslashes($_POST['menu/widget']));
            echo "<h3>View mode changed successfully!</h3>";

        }
    }
}

?>