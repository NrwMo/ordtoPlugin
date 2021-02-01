<?php

function ordto_orders_view()
{
    if (file_exists(__DIR__ . '/api_key.txt')) {
        if (!empty(file_get_contents(__DIR__ . '/api_key.txt'))) {

            ordto_save_order_status();

            $api_key = file_get_contents(__DIR__ . '/api_key.txt');
            $json_orders_list = file_get_contents("https://cloud.ord.to/api/v1/orders?apiKey=$api_key&page=1");
            $orders_list = json_decode($json_orders_list, true);

            $orders_list_page_count = ceil($orders_list[count] / $orders_list[limit]);

            $json_order_type = file_get_contents('https://cloud.ord.to/api/v1/order/type?apiKey=' . $api_key);
            $order_type = json_decode($json_order_type, true);

            $as_status = [1 => "New",
                4 => "Rejected",
                5 => "Returned",
                6 => "To accept",
                7 => "Preparing",
                8 => "In delivery",
                9 => "Delivered"];

            $as_payment_status = [1 => 'Waiting',
                2 => "Transfer",
                3 => "PayPal",
                4 => "Stripe",
                5 => "DotPay",
                6 => "Cash on delivery",
                7 => "PayLane",
                8 => "Card n delivery",
                9 => "P24",
                10 => "Status square"];

            if (empty($_POST['order_id']) || !empty($_POST['come_back_to_orders'])) {
                ?>

                <div>
                    <div class="new_user_banner" style='padding: 15px; margin-top: 20px; margin-right: 20px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; color: #566d86; background-color: #deedf5; border-color: #d2e5ef;'>
                        Here you can view information about your existing orders,
                        see details and change order status
                    </div>
                    <div style="position: absolute; bottom: 37px; left: 150px">
                        <form method="post">
                            <?php echo $orders_list[count] ?> items
                            <input type="submit" name="the_first_page" value="«">
                            <input type="submit" name="previous_page" value="‹">
                            <input style="text-align: center;" type="text" size="1" name="new_page_value" value="<?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                if (file_get_contents(__DIR__ . '/pagination/page_num_ords.txt') == $_POST['new_page_value']) {
                                    if (!empty($_POST['next_page'])) {
                                        if ($_POST['new_page_value'] < $orders_list_page_count) {
                                            ordto_save_new_page_orders((int)($_POST['new_page_value'] + 1));

                                        } else {
                                            ordto_save_new_page_orders($orders_list_page_count);

                                        }
                                    } elseif (!empty($_POST['previous_page'])) {
                                        if ($_POST['new_page_value'] > 1) {
                                            ordto_save_new_page_orders((int)($_POST['new_page_value'] - 1));

                                        } else {
                                            ordto_save_new_page_orders(1);

                                        }
                                    } elseif (!empty($_POST['the_first_page'])) {
                                        ordto_save_new_page_orders(1);

                                    } elseif (!empty($_POST['the_last_page'])) {
                                        ordto_save_new_page_orders($orders_list_page_count);

                                    }
                                } elseif (!empty($_POST['new_page_value'])) {
                                    if ($_POST['new_page_value'] >= 1 && $_POST['new_page_value'] <= $orders_list_page_count) {
                                        ordto_save_new_page_orders((int)$_POST['new_page_value']);

                                    } elseif ($_POST['new_page_value'] < 1) {
                                        ordto_save_new_page_orders(1);

                                    } elseif ($_POST['new_page_value'] > $orders_list_page_count) {
                                        ordto_save_new_page_orders($orders_list_page_count);

                                    }
                                } else {
                                    ordto_save_new_page_orders(file_get_contents(__DIR__ . '/pagination/page_num_ords.txt'));

                                }
                            } else {
                                ordto_save_new_page_orders(1);

                            } ?>">
                            <span>of <?php echo $orders_list_page_count; ?></span>
                            <input type="submit" name="next_page" value="›">
                            <input type="submit" name="the_last_page" value="»">
                        </form>
                    </div>

                    <h2>Orders on your site:</h2>
                    <table>
                        <tr>
                            <th>No</th>
                            <th>Delivery date</th>
                            <th>Value</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Payment status</th>
                        </tr>
                        <?php
                        $p = file_get_contents(__DIR__ . '/pagination/page_num_ords.txt');

                        $json_orders_list = file_get_contents("https://cloud.ord.to/api/v1/orders?apiKey=$api_key&page=$p");
                        $orders_list = json_decode($json_orders_list, true);

                        for ($i = 0; $i <= count($orders_list[data]) - 1; ++$i) {

                            $json_order_info = file_get_contents('https://cloud.ord.to/api/v1/order/' . $orders_list[data][$i][id] . '?apiKey=' . $api_key);
                            $order_info = json_decode($json_order_info, true);
                            ?>
                            <div>
                                <tr>
                                    <td width="80">
                                        <form method="post"><input class="order_number" type="submit" name="order_id"
                                                                   value="<?php echo "#" . $order_info[data][number]; ?>">
                                        </form>
                                    </td>
                                    <td width="230"><?php $date = $orders_list[data][$i][order_date];
                                        echo date("F j, Y, g:i a", strtotime($date)); ?></td>
                                    <td width="100"> <?php echo $orders_list[data][$i][price] . " " . $orders_list[data][$i][currency][name]; ?></td>
                                    <td width="100"><select name="sel<?php echo $i; ?>" form="order_status_change">
                                            <option <?php if ($as_status[1] == $as_status[$orders_list[data][$i][status]]) {
                                                ?>
                                                selected
                                                <?php
                                            } ?> ><?php echo $as_status[1]; ?></option>
                                            <option <?php if ($as_status[4] == $as_status[$orders_list[data][$i][status]]) {
                                                ?>
                                                selected
                                                <?php
                                            } ?> ><?php echo $as_status[4]; ?></option>
                                            <option <?php if ($as_status[5] == $as_status[$orders_list[data][$i][status]]) {
                                                ?>
                                                selected
                                                <?php
                                            } ?> ><?php echo $as_status[5]; ?></option>
                                            <option <?php if ($as_status[6] == $as_status[$orders_list[data][$i][status]]) {
                                                ?>
                                                selected
                                                <?php
                                            } ?> ><?php echo $as_status[6]; ?></option>
                                            <option <?php if ($as_status[7] == $as_status[$orders_list[data][$i][status]]) {
                                                ?>
                                                selected
                                                <?php
                                            } ?> ><?php echo $as_status[7]; ?></option>
                                            <option <?php if ($as_status[8] == $as_status[$orders_list[data][$i][status]]) {
                                                ?>
                                                selected
                                                <?php
                                            } ?> ><?php echo $as_status[8]; ?></option>
                                            <option <?php if ($as_status[9] == $as_status[$orders_list[data][$i][status]]) {
                                                ?>
                                                selected
                                                <?php
                                            } ?> ><?php echo $as_status[9]; ?></option>
                                        </select></td>
                                    <td width="100"><?php for ($j = 0; $j <= count($order_type[data]); $j++) {
                                            if ($order_type[data][$j][id] == $order_info[data][type]) {
                                                echo $order_type[data][$j][name];
                                            }
                                        } ?></td>
                                    <td width="200"><?php echo $as_payment_status[$order_info[data][payment_status]]; ?></td>
                                </tr>
                            </div>
                            <?php
                        }
                        ?>
                    </table>
                    <br>
                    <div style="position: absolute; bottom: 40px;">
                        <form id="order_status_change" method="post">
                            <input type="submit" name="save_order_status" value="Save changes">
                        </form>
                    </div>
                </div>
                <?php
            } elseif (!empty($_POST['order_id'])) {
                $p = file_get_contents(__DIR__ . '/pagination/page_num_ords.txt');

                $json_orders_list = file_get_contents("https://cloud.ord.to/api/v1/orders?apiKey=$api_key&page=$p");
                $orders_list = json_decode($json_orders_list, true);

                for ($i = 0; $i < count($orders_list[data]); ++$i) {
                    $json_order_info = file_get_contents('https://cloud.ord.to/api/v1/order/' . $orders_list[data][$i][id] . '?apiKey=' . $api_key);
                    $order_info = json_decode($json_order_info, true);

                    if ("#{$order_info[data][number]}" == $_POST['order_id']) {
                        ?>
                        <br>
                        <form method="post">
                            <input class="come_back_to_" type="submit" name="come_back_to_orders"
                                   value="← Back">
                        </form>
                        <h2>Order <?php echo $_POST['order_id']; ?></h2>

                        <h3>Client information:</h3>
                        <table class="ords_info">
                            <tr>
                                <th>Name</th>
                                <th>Phone number</th>
                                <th>E-mail</th>
                                <?php
                                if ($order_info[data][type] == 3) {
                                    ?>
                                    <th width="100">Table number</th>
                                    <?php
                                }
                                if ($order_info[data][type] == 1) {
                                    ?>
                                    <th>Delivery address</th>
                                    <?php
                                }
                                ?>
                            </tr>
                            <tr>
                                <td width='150'>
                                    <?php echo $order_info[data][first_name];
                                    if (!empty($order_info[data][last_name])) {
                                        echo " " . $order_info[data][last_name];
                                    } ?>
                                </td>
                                <td width='150'>
                                    <?php echo $order_info[data][phone]; ?>
                                </td>
                                <td width='250'>
                                    <?php echo $order_info[data][email]; ?>
                                </td>
                                <?php
                                if ($order_info[data][type] == 3) {
                                    ?>
                                    <td width="70"><?php echo $order_info[data][table_number]; ?></td>
                                    <?php
                                }
                                if ($order_info[data][type] == 1) {
                                    ?>
                                    <td width="250"><?php echo $order_info[data][shipment_city] . ", " . $order_info[data][shipment_street] . ", " . $order_info[data][shipment_hn]; ?></td>
                                    <?php
                                }
                                ?>
                            </tr>
                        </table>
                        <h3>Order information:</h3>
                        <table class="ords_info">
                            <tr>
                                <th>Products</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                            <?php
                            for ($k = 0; $k <= count($order_info[data][products]) - 1; $k++) {
                                ?>
                                <tr>
                                    <td width="150"><?php echo $order_info[data][products][$k][name] ?></td>
                                    <td width="70"><?php echo $order_info[data][products][$k][quantity] ?></td>
                                    <td width="80"><?php echo $order_info[data][products][$k][price] . " " . $order_info[data][currency][name]; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>

                        <?php
                    }
                }
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

function ordto_save_new_page_orders($page_num)
{
    $new_page_num = fopen(__DIR__ . '/pagination/page_num_ords.txt', 'w');
    fwrite($new_page_num, $page_num);
    fclose($new_page_num);
    echo file_get_contents(__DIR__ . '/pagination/page_num_ords.txt');
}

function ordto_save_order_status()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST['save_order_status'])) {

            $api_key = file_get_contents(__DIR__ . '/api_key.txt');
            $p = file_get_contents(__DIR__ . '/pagination/page_num_ords.txt');
            $json_orders_list = file_get_contents("https://cloud.ord.to/api/v1/orders?apiKey=$api_key&page=$p");
            $orders_list = json_decode($json_orders_list, true);

            $as_status_convert = ["New" => 1,
                "Rejected" => 4,
                "Returned" => 5,
                "To accept" => 6,
                "Preparing" => 7,
                "In delivery" => 8,
                "Delivered" => 9];

            for ($i = 0; $i <= count($orders_list[data]) - 1; ++$i) {
                $new_order_status = ['orderStatus' => $as_status_convert[$_POST['sel' . $i]]];

                $json_new_order_status = json_encode($new_order_status);

                $ch = curl_init('https://cloud.ord.to/api/v1/order/' . $orders_list[data][$i][id] . '/status?apiKey=' . $api_key);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_new_order_status);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($json_new_order_status))
                );
                curl_exec($ch);
            }

        }
    }
}

?>