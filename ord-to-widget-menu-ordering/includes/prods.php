<?php

function ordto_products_view()
{
    if (file_exists(__DIR__ . '/api_key.txt')) {
        if (!empty(file_get_contents(__DIR__ . '/api_key.txt'))) {

            $limit = 20;

            ordto_product_data_submit($limit);

            $api_key = file_get_contents(__DIR__ . '/api_key.txt');
            $json_products_res = file_get_contents('https://cloud.ord.to/api/v1/product/list?apiKey=' . $api_key);
            $products = json_decode($json_products_res, true);

            $products_list_store = [];

            for ($i = 0; $i < count($products[data]); ++$i) {
                $products_list_store += [$i => [
                    "id" => $products[data][$i][id],
                    "on" => $products[data][$i][forSell],
                    "name" => $products[data][$i][name],
                    "price" => $products[data][$i][price],
                    "currency" => $products[data][$i][currency][name]
                ]
                ];
            }

            $page_count = ceil(count($products_list_store) / $limit);

            if (empty($_POST['add_new_product']) || !empty($_POST['come_back_to_products_list'])) {
                ?>
                <div>
                    <div class="new_user_banner" style='padding: 15px; margin-top: 20px; margin-right: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; color: #566d86; background-color: #deedf5; border-color: #d2e5ef;'>
                        Here you can view information about your products,
                        change their sale statuses and add new ones
                    </div>
                    <div style="position: absolute; bottom: 37px; left: 300px">
                        <form method="post">
                            <?php echo count($products_list_store); ?> items
                            <input type="submit" name="the_first_page" value="«">
                            <input type="submit" name="previous_page" value="‹">
                            <input style="text-align: center;" type="text" size="1" name="new_page_value" value="<?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                if (file_get_contents(__DIR__ . '/pagination/page_num_prods.txt') == $_POST['new_page_value']) {
                                    if (!empty($_POST['next_page'])) {
                                        if ($_POST['new_page_value'] < $page_count) {
                                            ordto_save_new_page_products((int)($_POST['new_page_value'] + 1));

                                        } else {
                                            ordto_save_new_page_products($page_count);

                                        }
                                    } elseif (!empty($_POST['previous_page'])) {
                                        if ($_POST['new_page_value'] > 1) {
                                            ordto_save_new_page_products((int)($_POST['new_page_value'] - 1));

                                        } else {
                                            ordto_save_new_page_products(1);

                                        }
                                    } elseif (!empty($_POST['the_first_page'])) {
                                        ordto_save_new_page_products(1);

                                    } elseif (!empty($_POST['the_last_page'])) {
                                        ordto_save_new_page_products($page_count);

                                    }
                                } elseif (!empty($_POST['new_page_value'])) {
                                    if ($_POST['new_page_value'] >= 1 && $_POST['new_page_value'] <= $page_count) {
                                        ordto_save_new_page_products((int)$_POST['new_page_value']);

                                    } elseif ($_POST['new_page_value'] < 1) {
                                        ordto_save_new_page_products(1);

                                    } elseif ($_POST['new_page_value'] > $page_count) {
                                        ordto_save_new_page_products($page_count);

                                    }
                                } else {
                                    ordto_save_new_page_products(file_get_contents(__DIR__ . '/pagination/page_num_prods.txt'));

                                }
                            } else {
                                ordto_save_new_page_products(1);

                            } ?>">
                            <span>of <?php echo $page_count; ?></span>
                            <input type="submit" name="next_page" value="›">
                            <input type="submit" name="the_last_page" value="»">
                        </form>
                    </div>

                    <?php
                    $page = file_get_contents(__DIR__ . '/pagination/page_num_prods.txt');
                    $start = ($page - 1) * $limit;
                    $res = [];
                    for ($j = $start; $j < $start + $limit; ++$j) {
                        if (!empty($products_list_store[$j])) {
                            $res += [$j => $products_list_store[$j]];
                        }
                    }
                    ?>

                    <h2>Products on your site:</h2>
                    <form method="post">
                        <input style="display: inline-block;
                            background-color: #1ab394;
    border-color: #1ab394;
    color: #FFFFFF;
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    touch-action: manipulation;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;" class="btn" type="submit" name="add_new_product" value="Add product">
                    </form>
                    <br>
                    <table>
                        <tr style="text-align: left;">
                            <th>ON</th>
                            <th>Name</th>
                            <th>Price</th>
                        </tr>
                        <?php
                        for ($i = $start; $i < $start + $limit; ++$i) {
                            if (!empty($res[$i])) {
                                ?>
                                <tr>
                                    <td width="30"><input form="status_change" name="status_checkbox<?php echo $i; ?>"
                                                          type="checkbox" value="true"
                                            <?php if (!empty($res[$i][on])) {
                                                ?>
                                                checked
                                                <?php
                                            }
                                            ?>
                                        ></td>
                                    <td width="150"><?php echo $res[$i][name]; ?></td>
                                    <td width="80"><?php echo $res[$i][price] . " " . $res[$i][currency]; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </table>
                    <br>
                    <div style="position: absolute; bottom: 40px;">
                        <form id="status_change" method="post">
                            <input type="submit" name="save_changes_in_products" value="Save changes">
                        </form>
                    </div>
                </div>
                <?php

            } elseif (!empty($_POST['add_new_product'])) {
                ordto_add_product();
            }
        } else {
            ?>
            <div class="new_user_banner"
                 style='padding: 15px; margin-top: 20px; margin-right: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; color: #765c3c; background-color: #f0e8d8; border-color: #e9dfc6;'>
                Specify your API key in the Configuration tab!
            </div>
            <?php
        }
    } else {
        ?>
        <div class="new_user_banner"
             style='padding: 15px; margin-top: 20px; margin-right: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; color: #765c3c; background-color: #f0e8d8; border-color: #e9dfc6;'>
            Specify your API key in the Configuration tab!
        </div>
        <?php
    }
}

function ordto_save_new_page_products($page_num)
{
    $new_page_num = fopen(__DIR__ . '/pagination/page_num_prods.txt', 'w');
    fwrite($new_page_num, $page_num);
    fclose($new_page_num);
    echo file_get_contents(__DIR__ . '/pagination/page_num_prods.txt');
}

function ordto_add_product()
{
    ?>
    <br>
    <form method="post">
        <input class="come_back_to_" type="submit" name="come_back_to_products_list"
               value="← Back">
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
        <input style="display: inline-block;
                            background-color: #1ab394;
    border-color: #1ab394;
    color: #FFFFFF;
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    touch-action: manipulation;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;" type='submit' name='sub2' value="Add product"><br><br>
    </form>
    <?php
}

function ordto_product_data_submit($limit)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['save_changes_in_products'])) {

            $api_key = file_get_contents(__DIR__ . '/api_key.txt');
            $json_products_res = file_get_contents('https://cloud.ord.to/api/v1/product/list?apiKey=' . $api_key);
            $products = json_decode($json_products_res, true);

            $products_list_store = [];

            for ($i = 0; $i < count($products[data]); ++$i) {
                $products_list_store += [$i => [
                    "id" => $products[data][$i][id],
                    "on" => $products[data][$i][forSell],
                    "name" => $products[data][$i][name],
                    "price" => $products[data][$i][price],
                    "currency" => $products[data][$i][currency][name]
                ]
                ];
            }

            $page = file_get_contents(__DIR__ . '/pagination/page_num_prods.txt');
            $start = ($page - 1) * $limit;
            $res = [];
            for ($j = $start; $j < $start + $limit; ++$j) {
                if (!empty($products_list_store[$j])) {
                    $res += [$j => $products_list_store[$j]];
                }
            }

            for ($i = $start; $i < $start + $limit; ++$i) {
                if (!empty($res[$i])) {
                    $new_status = ['forSell' => $_POST['status_checkbox' . $i]];

                    $json_new_product_status = json_encode($new_status);

                    $ch = curl_init('https://cloud.ord.to/api/v1/product/' . $res[$i][id] . '/status?apiKey=' . $api_key);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_new_product_status);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . $json_new_product_status)
                    );
                    curl_exec($ch);
                }
            }
        } elseif (!empty($_POST['sub2'])) {
            $new_product = ["name" => $_POST['product_name'],
                "tagline" => $_POST['product_tagline'],
                "description" => $_POST['product_description'],
                "price" => $_POST['product_price'],
                "images" => []];

            $api_key = file_get_contents(__DIR__ . '/api_key.txt');

            for ($i = 0; $i < count($_FILES['product_photo']['name']); ++$i) {
                if (!empty($_FILES['product_photo']['name'][$i])) {

                    $ch = curl_init();
                    $cfile = new CURLFile($_FILES[product_photo][tmp_name][$i], $_FILES[product_photo][type][$i], $_FILES[product_photo][name][$i]);
                    $new_image = ["image" => $cfile];
                    curl_setopt($ch, CURLOPT_URL, "https://cloud.ord.to/api/v1/product/upload-image?apiKey=$api_key");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $new_image);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);

                    $file_to_upload = json_decode($result, true);

                    $new_product["images"] += [$i => ["image" => $file_to_upload[data][file]]];
                }
            }

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
    $api_key = file_get_contents(__DIR__ . '/api_key.txt');
    $json_products_res = file_get_contents('https://cloud.ord.to/api/v1/product/list?apiKey=' . $api_key);
    $products = json_decode($json_products_res, true);

    echo "<pre>";
    print_r($products);
    echo "</pre>";

}

?>