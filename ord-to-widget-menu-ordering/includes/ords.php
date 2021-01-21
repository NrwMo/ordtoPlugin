<?php

function ordto_orders_view()
{
    if (file_exists(__DIR__ . '/api_key.yaml')) {
        if (!empty(file_get_contents(__DIR__ . '/api_key.yaml'))) {
            $api_key = file_get_contents(__DIR__ . '/api_key.yaml');
            $json_orders_list = file_get_contents('https://cloud.ord.to/api/v1/orders?apiKey=' . $api_key . '&page=1');
            $orders_list = json_decode($json_orders_list, true);

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
            ?>

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

                for ($i = 0; $i <= count($orders_list[data]) - 1; ++$i) {

                    $json_order_info = file_get_contents('https://cloud.ord.to/api/v1/order/' . $orders_list[data][$i][id] . '?apiKey=' . $api_key);
                    $order_info = json_decode($json_order_info, true);

                    ?>
                    <tr>
                        <td width="80">
                            <form method="post"><input class="order_number" type="submit" name="order_id"
                                                       value="<?php echo "#" . $order_info[data][number]; ?>">
                            </form>
                        </td>
                        <td width="230"><?php $date = $orders_list[data][$i][order_date];
                            echo date("F j, Y, g:i a", strtotime($date)); ?></td>
                        <td width="100"> <?php echo $orders_list[data][$i][price] . " " . $orders_list[data][$i][currency][name]; ?></td>
                        <td width="100"><?php echo $as_status[$orders_list[data][$i][status]]; ?></td>
                        <td width="100"><?php for ($j = 0; $j <= count($order_type[data]); $j++) {
                                if ($order_type[data][$j][id] == $order_info[data][type]) {
                                    echo $order_type[data][$j][name];
                                }
                            } ?></td>
                        <td width="200"><?php echo $as_payment_status[$order_info[data][payment_status]]; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                for ($i = 0; $i <= count($orders_list[data]) - 1; ++$i) {
                    $json_order_info = file_get_contents('https://cloud.ord.to/api/v1/order/' . $orders_list[data][$i][id] . '?apiKey=' . $api_key);
                    $order_info = json_decode($json_order_info, true);

                    if ("#{$order_info[data][number]}" == $_POST['order_id']) {
                        ?>
                        <hr>
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
        } else echo 'Specify your API key in the Configuration tab!';
    } else echo 'Specify your API key in the Configuration tab!';
}


function ordto_order_info_arr()
{
    $api_key = file_get_contents(__DIR__ . '/api_key.yaml');
    $json_orders_list = file_get_contents('https://cloud.ord.to/api/v1/orders?apiKey=' . $api_key . '&page=1');
    $orders_list = json_decode($json_orders_list, true);

    for ($i = 0; $i <= count($orders_list[data]) - 1; ++$i) {
        $json_order_info = file_get_contents('https://cloud.ord.to/api/v1/order/' . $orders_list[data][$i][id] . '?apiKey=' . $api_key);
        $order_info = json_decode($json_order_info, true);
        echo "<pre>";
        print_r($order_info);
        echo "</pre>";
    }
}

function ordto_orders_list_arr()
{
    $api_key = file_get_contents(__DIR__ . '/api_key.yaml');
    $json_orders_list = file_get_contents('https://cloud.ord.to/api/v1/orders?apiKey=' . $api_key . '&page=1');
    $orders_list = json_decode($json_orders_list, true);

    echo "<pre>";
    print_r($orders_list);
    echo "</pre>";
}

?>