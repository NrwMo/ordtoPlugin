<?php
function ordto_products_view()
{
    if (file_exists(__DIR__ . '/api_key.yaml')) {
        if (!empty(file_get_contents(__DIR__ . '/api_key.yaml'))) {

            $api_key = file_get_contents(__DIR__ . '/api_key.yaml');
            $json_products_res = file_get_contents('https://cloud.ord.to/api/v1/product/list?apiKey=' . $api_key);
            $products = json_decode($json_products_res, true);

            ?>
            <h2>Products on your site:</h2>
            <table>
                <tr style="text-align: left;">
                    <th>Name</th>
                    <th>Price</th>

                </tr>
                <?php
                for ($i = 0; $i <= count($products[data]) - 1; ++$i) {
                    ?>
                    <tr>
                        <td width="150"><?php echo $products[data][$i][name]; ?></td>
                        <td width="80"><?php echo $products[data][$i][price] . " " . $products[data][$i][currency][name]; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
            ordto_add_product();
        } else echo 'Specify your API key in the Configuration tab!';
    } else echo 'Specify your API key in the Configuration tab!';
}

function ordto_add_product()
{
    ?>
    <br>
    <hr>
    <h2>Add product: </h2>
    <form method="post" class="prod_form">
        <label for="prod_name"><h3>Product name*</h3></label>
        <input class="input_prod" id="prod_name" type="text" name="product_name" placeholder="Country Pizza" required><br>
        <label for="prod_tagline"><h3>Tagline</h3></label>
        <input class="input_prod" id="prod_tagline" type="text" name="product_tagline" placeholder="Product tagline"><br>
        <label for="prod_descrip"><h3>Short description*</h3></label>
        <input class="input_prod" id="prod_descrip" type="text" name="product_description" placeholder="Product description"><br>
        <label for="prod_price"><h3>Price*</h3></label>
        <input class="input_prod" id="prod_price" type="text" name="product_price" placeholder="Product price"><br><br>

        <input type='reset' name='res2' value="Reset">
        <input type='submit' name='sub2' value="Submit">
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['product_name']) && !empty($_POST['product_description']) && !empty($_POST['product_price'])) {
            $new_product = ["name" => $_POST['product_name'],
                "tagline" => $_POST['product_tagline'],
                "description" => $_POST['product_description'],
                "price" => $_POST['product_price']];

            $api_key = file_get_contents(__DIR__ . '/api_key.yaml');
            $json_new_product = json_encode($new_product);

            $ch = curl_init('https://cloud.ord.to/api/v1/product/add?apiKey=' . $api_key);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_new_product);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($json_new_product))
            );
            curl_exec($ch);
        }
    }
}

function ordto_products_list_arr()
{
    $api_key = file_get_contents(__DIR__ . '/api_key.yaml');
    $json_products_res = file_get_contents('https://cloud.ord.to/api/v1/product/list?apiKey=' . $api_key);
    $products = json_decode($json_products_res, true);

    echo "<pre>";
    print_r($products);
    echo "</pre>";

}

?>
