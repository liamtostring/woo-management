<?php
/*
Plugin Name: WooCommerce Order Management
Description: Display recent WooCommerce orders and allow editing and printing of billing and shipping details.
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
        echo '<th>Order ID</th>';
        echo '<th>Date</th>';
        echo '<th>Total</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($orders as $order) {
            $order_id = $order->get_id();
            $order_date = $order->get_date_created();
            $order_total = wc_price($order->get_total());

            echo '<tr>';
            echo '<td>' . $order_id . '</td>';
            echo '<td>' . $order_date->date_i18n('F j, Y H:i:s') . '</td>';
            echo '<td>' . $order_total . '</td>';
            echo '<td>';
            echo '<a href="' . admin_url('post.php?post=' . $order_id . '&action=edit') . '">Edit</a> | ';
            echo '<a href="#" class="print-order" data-order-id="' . $order_id . '">Print</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No recent orders found.</p>';
    }

    // Inline CSS for demonstration
    echo '<style>';
    echo '.widefat { width: 100%; margin-top: 20px; }';
    echo 'table.widefat thead th { background-color: #f0f0f0; }';
    echo 'table.widefat tbody tr:nth-child(odd) { background-color: #f9f9f9; }';
    echo '</style>';

    // Inline JavaScript for demonstration
    echo '<script>';
    echo 'jQuery(document).ready(function($) {';
    echo '    $(".print-order").on("click", function(e) {';
    echo '        e.preventDefault();';
    echo '        var orderId = $(this).data("order-id");';
    echo '        // Add your code here to handle printing of order details';
    echo '    });';
    echo '});';
    echo '</script>';

    echo '</div>';
}

// Hook into WordPress to enqueue CSS and JavaScript files
function enqueue_order_management_scripts() {
    // This function is left empty as we are using inline CSS and JavaScript
}
add_action('admin_enqueue_scripts', 'enqueue_order_management_scripts');
?>
