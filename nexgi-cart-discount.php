<?php
/**
 * Plugin Name: Nexgi Payment Gateway Type Discount
 * Plugin URI: https://www.nexgi.com
 * Description: You can add payment gateway type discount
 * Version: 6.1
 * Author: NexGen WordPress Development Team
 * Author URI: https://www.nexgi.com
 *
 */
add_action('woocommerce_cart_calculate_fees', 'nexgi_cart_discount_add_discount', 20, 1);

function nexgi_cart_discount_add_discount($cart_object) {

    if (is_admin() && !defined('DOING_AJAX'))
        return;

    $label_text = __("");

    $percent = 0;

    // Mention the payment method e.g. cod, bacs, cheque or paypal


    $cart_total = $cart_object->subtotal_ex_tax;

    $chosen_payment_method = WC()->session->get('chosen_payment_method'); //Get the selected payment method
    include_once __DIR__ . '/helper/nexgi_cart_dis_cls.php';
    $nexgi_cart_dis_obj = new nexgi_cart_dis_cls();
    $pg_settings = $nexgi_cart_dis_obj->get_pg_settings();
    $disType = $nexgi_cart_dis_obj->getOptionValue('dis_type_', $chosen_payment_method, $pg_settings);
    if ($disType != '' && $disType) {
        // Calculating percentage

        if ($disType == "flat") {
            $discount = $nexgi_cart_dis_obj->getOptionValue('dis_val_', $chosen_payment_method, $pg_settings);
        } else {
            $discount = number_format(($cart_total / 100) * $nexgi_cart_dis_obj->getOptionValue('dis_val_', $chosen_payment_method, $pg_settings), 2);
        }

        if ((int) $discount) {
            $label_text = $nexgi_cart_dis_obj->getOptionValue('dis_label_', $chosen_payment_method, $pg_settings);;
            $cart_object->add_fee($label_text, -$discount, false);
        }

// Adding the discount
    }
}

add_action('woocommerce_review_order_before_payment', 'nexgi_cart_discount_refresh_payment_method');

function nexgi_cart_discount_refresh_payment_method() {
// jQuery
    ?>
    <script type="text/javascript">
        (function ($) {
            $('form.checkout').on('change', 'input[name^="payment_method"]', function () {
                $('body').trigger('update_checkout');
            });
        })(jQuery);
    </script>
    <?php
}

function nexgi_cart_discount_admin_menu() {
    add_menu_page(
            __('NexGi Discount', 'nexgi_cart_discount'), __('PG Discount', 'nexgi_cart_discount'), 'manage_options', 'nexgi-cart-discount', 'nexgi_cart_discount_admin_page_contents', 'dashicons-schedule', 3
    );
}

add_action('admin_menu', 'nexgi_cart_discount_admin_menu');

function nexgi_cart_discount_admin_page_contents() {
    ?>
    <style>
        .each_payment_gateway .form-group {
        display: inline-block;
    width: 33%;
}
.each_payment_gateway{
    margin-top: 10px;
    background: #d9d9d9;
    padding: 9px 15px 19px 15px;
}
        </style>
    <div>
        <h1>
            <?php esc_html_e('NexGi Payment Gateway Discount', 'nexgi-cart-discount'); ?>
        </h1>
        <hr>
        <?php
        include_once __DIR__ . '/helper/form_helper.php';
        include_once __DIR__ . '/helper/nexgi_cart_dis_cls.php';
        $nexgi_cart_dis_obj = new nexgi_cart_dis_cls();
        $pg_settings = $nexgi_cart_dis_obj->get_pg_settings();
        $payment_gateway = $nexgi_cart_dis_obj->get_payment_gateways();


        $pg_list = array();

        if (isset($_POST['save_payment_gateway_options'])) {
            unset($_POST['save_payment_gateway_options']);
            update_option("nexgi_cart_dis_settings", json_encode($_POST));
            echo "Updated Successfully";
        }


        $dis_types = $nexgi_cart_dis_obj->get_dis_types();

        echo '<form method="POST" action="#">';

        foreach ($payment_gateway as $pg_code => $pg_values) {
            $pg_list[$pg_code] = $pg_values->method_title;
            ?>

            <div class="each_payment_gateway">
                <h2>
                    Setting for Payment Gateway <?php echo $pg_values->method_title ?> [<?php echo $pg_code ?>] 
                </h2>
                <div class="form-group">
                    <label>Discount Type</label>
                    <?php echo form_dropdown('dis_type_' . $pg_code, $dis_types, $nexgi_cart_dis_obj->getOptionValue('dis_type_', $pg_code, $pg_settings)) ?>
                </div>
                <div class="form-group">
                    <label>Discount value</label>
                    <?php echo form_input('dis_val_' . $pg_code, $nexgi_cart_dis_obj->getOptionValue('dis_val_', $pg_code, $pg_settings)) ?>
                </div>
                <div class="form-group">
                <label>Discount label</label>
                <?php echo form_input('dis_label_' . $pg_code, $nexgi_cart_dis_obj->getOptionValue('dis_label_', $pg_code, $pg_settings)) ?>
                </div>
            </div>
        

        <?php
    }
    ?>
        <br>   
    <input type="submit" class="button button-primary" name="save_payment_gateway_options">
    </form>
    </div>
    <?php
}
