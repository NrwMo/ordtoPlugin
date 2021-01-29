<?php
function ordto_products_view()
{
    if (file_exists(__DIR__ . '/api_key.txt')) {
        if (!empty(file_get_contents(__DIR__ . '/api_key.txt'))) {

            ordto_product_data_submit();

            $api_key = file_get_contents(__DIR__ . '/api_key.txt');
            $json_products_res = file_get_contents('https://cloud.ord.to/api/v1/product/list?apiKey=' . $api_key);
            $products = json_decode($json_products_res, true);

            if (empty($_POST['add_new_product']) || !empty($_POST['come_back_to_products_list'])) {
                ?>
                <h2>Products on your site:</h2>
                <table>
                    <tr style="text-align: left;">
                        <th>ON</th>
                        <th>Name</th>
                        <th>Price</th>
                    </tr>
                    <?php
                    for ($i = 0; $i <= count($products[data]) - 1; ++$i) {
                        ?>
                        <tr>
                            <td width="30"><input form="status_change" name="status_checkbox<?php echo $i; ?>"
                                                  type="checkbox" value="true"
                                    <?php if (!empty($products[data][$i][forSell])) {
                                        ?>
                                        checked
                                        <?php
                                    }
                                    ?>
                                ></td>
                            <td width="150"><?php echo $products[data][$i][name]; ?></td>
                            <td width="80"><?php echo $products[data][$i][price] . " " . $products[data][$i][currency][name]; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <br>
                <form id="status_change" method="post">
                    <input type="submit" name="save_changes_in_products" value="Save changes">
                    <input type="submit" name="add_new_product" value="Add new product">
                </form>
                <?php
            } elseif (!empty($_POST['add_new_product'])) {
                ordto_add_product();
            }
        } else {
            ?>
            <div id="new_user_banner"
                 style='padding: 15px; margin-top: 20px; margin-right: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; color: #765c3c; background-color: #f0e8d8; border-color: #e9dfc6;'>
                Specify your API key in the Configuration tab!
            </div>
            <?php
        }
    } else {
        ?>
        <div id="new_user_banner"
             style='padding: 15px; margin-top: 20px; margin-right: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; color: #765c3c; background-color: #f0e8d8; border-color: #e9dfc6;'>
            Specify your API key in the Configuration tab!
        </div>
        <?php
    }
}

function ordto_add_product()
{
    ?>
    <br>
    <form method="post">
        <input class="come_back_to_" type="submit" name="come_back_to_products_list"
               value="← come back to products list">
    </form>
    <h2>Add product: </h2>
    <form enctype="multipart/form-data" method="post" class="prod_form">
        <label for="prod_name"><h3>Product name*</h3></label>
        <input class="input_prod" id="prod_name" type="text" name="product_name" placeholder="Country Pizza"
               required><br>
        <label for="prod_tagline"><h3>Tagline</h3></label>
        <input class="input_prod" id="prod_tagline" type="text" name="product_tagline"
               placeholder="Product tagline"><br>
        <label for="prod_descrip"><h3>Short description*</h3></label>
        <input class="input_prod" id="prod_descrip" type="text" name="product_description"
               placeholder="Product description"><br>
        <label for="prod_price"><h3>Price*</h3></label>
        <input class="input_prod" id="prod_price" type="text" name="product_price" placeholder="Product price"><br>
        <label for="product_photo"><h3>Product photo</h3></label>
        <input class="input_prod" type="file" name="product_photo[]" multiple accept="image/*"><br><br>

        <input type='reset' name='res2' value="Reset">
        <input type='submit' name='sub2' value="Submit"><br><br>
    </form>
    <?php
}

function ordto_product_data_submit()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['save_changes_in_products'])) {

            $api_key = file_get_contents(__DIR__ . '/api_key.txt');
            $json_products_res = file_get_contents('https://cloud.ord.to/api/v1/product/list?apiKey=' . $api_key);
            $products = json_decode($json_products_res, true);

            for ($i = 0; $i <= count($products[data]) - 1; ++$i) {
                $new_status = ['forSell' => $_POST['status_checkbox' . $i]];

                $json_new_product_status = json_encode($new_status);

                $ch = curl_init('https://cloud.ord.to/api/v1/product/' . $products[data][$i][id] . '/status?apiKey=' . $api_key);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_new_product_status);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($json_new_product_status))
                );
                curl_exec($ch);
            }
        } elseif (!empty($_POST['sub2'])) {
            $new_product = ["name" => $_POST['product_name'],
                "tagline" => $_POST['product_tagline'],
                "description" => $_POST['product_description'],
                "price" => $_POST['product_price'],
                "images" => []];

            for ($k = 0; $k < count($_FILES['product_photo']['name']); ++$k) {
                $new_product[images] += [$k => ["image" => stripslashes('/') . $_FILES['product_photo']['name'][$k]]];
            }

            $api_key = file_get_contents(__DIR__ . '/api_key.txt');
            $json_new_product = json_encode($new_product);
            $bla = stripslashes($json_new_product);

            $ch = curl_init('https://cloud.ord.to/api/v1/product/add?apiKey=' . $api_key);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bla);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($bla))
            );
            curl_exec($ch);
//            echo "<pre>";
//            print_r($new_product);
//            echo "</pre>";
        }
    }
}

function ordto_products_list_arr()
{
    $api_key = file_get_contents(__DIR__ . '/api_key.txt');
    $json_products_res = file_get_contents('https://cloud.ord.to/api/v1/product/list?apiKey=' . $api_key);
    $products = json_decode($json_products_res, true);

    echo "<pre>";
    print_r($products);
    echo "</pre>";

}

?>