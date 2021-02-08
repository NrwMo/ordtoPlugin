<?php
function ordto_config_view()
{
    ordto_post_config();
    if (!file_exists(__DIR__ . '/api_key.txt')) {
        ?>
        <div class="banner attention-banner">
            If you do not have an account at ord.to,
            then you can create one
            <a href="https://cloud.ord.to/register" target="_blank">here</a>
        </div>
        <?php
    }
    ?>
    <div class="banner info-banner">
        Here you can change all your configuration settings at one time,
        change your API key only,
        change your site URL only,
        change view mode only (widget/menu)
    </div>
    <form method='post'>
        <label for="inp_api">
            <p> Input your API from ord.to -> Integrations -> API -> API key for this company:</p>
        </label>
        <input type="text" id="inp_api" name='api_key' size="40"
            <?php if (file_exists(__DIR__ . '/api_key.txt')){
            ?> value="<?php echo file_get_contents(__DIR__ . '/api_key.txt') ?>" <?php
               }else { ?>placeholder="API key" <?php } ?>autofocus>
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
        <input class="but reset-but" type='reset' name='res1' value="Reset">
        <input class='but save-but' type='submit' name='sub1' value="Save">
    </form>
    <?php
}

function ordto_post_config()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        if (!empty($_POST['api_key']) && !empty($_POST['url_site']) && !empty($_POST['menu/widget'])) {

            file_put_contents(__DIR__ . '/api_key.txt', stripslashes($_POST['api_key']));
            file_put_contents(__DIR__ . '/url_site.txt', stripslashes($_POST['url_site']));
            file_put_contents(__DIR__ . '/wm.txt', stripslashes($_POST['menu/widget']));

            echo "<h3>All configuration added successfully!</h3>";

        } elseif (empty($_POST['api_key']) && !empty($_POST['url_site']) && !empty($_POST['menu/widget'])) {

            file_put_contents(__DIR__ . '/url_site.txt', stripslashes($_POST['url_site']));
            file_put_contents(__DIR__ . '/wm.txt', stripslashes($_POST['menu/widget']));

            echo "<h3>Site URL added successfully!</h3>";

        } elseif (!empty($_POST['api_key']) && empty($_POST['url_site']) && empty($_POST['menu/widget'])) {

            file_put_contents(__DIR__ . '/api_key.txt', stripslashes($_POST['api_key']));
            echo "<h3>API key added successfully!</h3>";

        } else {

            file_put_contents(__DIR__ . '/wm.txt', stripslashes($_POST['menu/widget']));
            echo "<h3>View mode changed successfully!</h3>";

        }
    }
}

?>