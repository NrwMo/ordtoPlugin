<?php
/**
 * Plugin Name:       Ordering System - ord.to
 * Description:       Integrate menu to your website with ord.to!
 * Version:           1.1.0
 * Requires at least: 5.6
 * Requires PHP:      7.1
 * Author:            Getreve Ltd
 * Text Domain:       ordto
 * Domain Path:       /lang
 */

if (!defined('ABSPATH')) {
    die;
}

require_once __DIR__ . '/includes/ords.php';
require_once __DIR__ . '/includes/items_view.php';
require_once __DIR__ . '/includes/prods.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/menu_or_widget.php';

function ortdo_register_assets()
{
    wp_register_style('ordto_style', plugins_url('admin/css/style.css', __FILE__));
}

function ordto_enqueue_assets($hook)
{
    if ($hook != ('toplevel_page_orders' || 'toplevel_page_products' || 'toplevel_page_config')) {
        return;
    }
    wp_enqueue_style('ordto_style');
}

function ordto_show_new_items()
{
    $title = 'Ordering system configuration';
    if (current_user_can('manage_options')) {
        add_menu_page(
            esc_html__($title),
            esc_html__('Ord.to'),
            'manage_options',
            'config',
            'ordto_add_config',
            'dashicons-clipboard',
            3
        );

        add_submenu_page(
            'config',
            esc_html__($title),
            esc_html__('Configuration', 'ord_sys'),
            'manage_options',
            'config',
            'ordto_add_config'
        );

        add_submenu_page(
            'config',
            esc_html__('Products'),
            esc_html__('Products', 'ord_sys'),
            'manage_options',
            'products',
            'ordto_view_products'
        );

        add_submenu_page(
            'config',
            esc_html__('Orders'),
            esc_html__('Orders', 'ord_sys'),
            'manage_options',
            'orders',
            'ordto_view_orders'
        );
    }
}

if (is_admin()) {
    add_action('admin_enqueue_scripts', 'ortdo_register_assets');
    add_action('admin_enqueue_scripts', 'ordto_enqueue_assets');
    add_action('admin_menu', 'ordto_show_new_items');
}

if (!is_admin()) {
    ordto_view_public();
}
