<?php
/*
Plugin Name: WooCommerce Order Management
Description: Display recent WooCommerce orders and allow printing of order details.
Version: 1.0
Author: Your Name
*/

// Add a menu item to the WordPress admin dashboard
function add_order_management_menu_item() {
    add_menu_page(
        'Order Management',
        'Order Management',
        'manage_options',
        'order-management',
        'display_order_management_page'
    );
}
add_action('admin_menu', 'add_order_management_menu_item');

// Display the order management page
function display_order_management_page() {
    // Get recent WooCommerce orders
    $orders = wc_get_orders(array(
        'limit' => 10, // You can adjust the number of orders to display
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    echo '<div class="wrap">';
    echo '<h2>Recent WooCommerce Orders</h2>';

    if (!empty($orders)) {
        echo '<table class="widefat">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Order Number</th>';
        echo '<th>Date</th>';
        echo '<th>Total</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($orders as $order) {
            $order_number = $order->get_order_number();
            $order_date = $order->get_date_created();
            $order_total = wc_price($order->get_total());

            echo '<tr>';
            echo '<td>' . $order_number . '</td>';
            echo '<td>' . $order_date->date_i18n('F j, Y H:i:s') . '</td>';
            echo '<td>' . $order_total . '</td>';
            echo '<td>';
            echo '<a href="#" class="print-order" data-order-id="' . $order->get_id() . '">Print</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No recent orders found.</p>';
    }

    // Inline JavaScript for printing functionality
    echo '<script>';
    echo 'jQuery(document).ready(function($) {';
    echo '    $(".print-order").on("click", function(e) {';
    echo '        e.preventDefault();';
    echo '        var orderId = $(this).data("order-id");';
    echo '        var printUrl = "' . admin_url('admin-ajax.php?action=print_order_details&order_id=') . '" + orderId;';
    echo '        var printWindow = window.open(printUrl, "_blank");';
    echo '        printWindow.onload = function() {';
    echo '            printWindow.print();';
    echo '        };';
    echo '    });';
    echo '});';
    echo '</script>';

    echo '</div>';
}

// Add an AJAX action for printing order details
add_action('wp_ajax_print_order_details', 'print_order_details_callback');
add_action('wp_ajax_nopriv_print_order_details', 'print_order_details_callback');

// Callback function to handle printing order details
function print_order_details_callback() {
    if (isset($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']);
        $order = wc_get_order($order_id);

        if ($order) {
            // Get the order number
            $order_number = $order->get_order_number();

            // Get billing information
            $billing_info = $order->get_address('billing');

            // Get custom fields under shipping
            $shipping_fields = array(
                'shipping_birlos' => $order->get_meta('shipping_birlos', true),
                'shipping_notaverde' => $order->get_meta('shipping_notaverde', true),
                'shipping_marca' => $order->get_meta('shipping_marca', true),
                'shipping_ano' => $order->get_meta('shipping_ano', true),
                'shipping_placas' => $order->get_meta('shipping_placas', true),
                'shipping_trabajo' => $order->get_meta('shipping_trabajo', true),
                'shipping_fechaentrega' => $order->get_meta('shipping_fechaentrega', true),
                'shipping_formapago' => $order->get_meta('shipping_formapago', true),
            );

            // Create a readable output
            $output = "Order Number: $order_number\n\n";
            $output .= "Billing Information:\n";
            foreach ($billing_info as $key => $value) {
                $output .= ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
            }
            $output .= "\nShipping Custom Fields:\n";
            foreach ($shipping_fields as $key => $value) {
                $output .= ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
            }

            // Output the readable format for printing
            header('Content-Type: text/plain');
            echo $output;
            exit;
        } else {
            echo 'Order not found.';
        }
    } else {
        echo 'Order ID not provided.';
    }

    wp_die();
}

?>
