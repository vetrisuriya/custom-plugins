<?php

/**
 * Plugin Name:       News Public API
 * Plugin URI:        https://wordpress.vetrisuriya.in/
 * Description:       Public API Plugin for News
 * Version:           1.0.0
 * Requires at least: 6.8
 * Requires PHP:      8.0
 * Author:            Vetri Suriya
 * Author URI:        https://vetrisuriya.in/
 * Text Domain:       news-plugin-api
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants for plugin path and URL
define('PLUGIN_PATH', plugin_dir_path(str_replace("\\", '/', __FILE__)));
define('PLUGIN_URL', plugin_dir_url(str_replace("\\", '/', __FILE__)));

// Define all other constants
require_once(PLUGIN_PATH."includes/utils.php");

// Require necessary classes
require_once(PLUGIN_CLASS_PATH."class-api.php");




/**
 * Activate the plugin
 * This function will be called when the plugin is activated.
 * It will create a database table to store API data.
 * It will also fetch data from the API and store it in the database.
 */
register_activation_hook(__FILE__, 'plugin_activate');
function plugin_activate() {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $create_tb_query = "CREATE TABLE {$wpdb->prefix}news_api_data (
        api_id int(11) NOT NULL AUTO_INCREMENT,
        api_url varchar(1000) NOT NULL,
        api_count int(11) NOT NULL,
        api_value text NOT NULL,
        api_created_at datetime DEFAULT CURRENT_TIMESTAMP,
        api_updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (api_id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($create_tb_query);


    // Initialize the API Class and fetch data from the API
    $fetch_data_from_api_cls = new News\PublicApi\Class\Class_Api();
    $fetch_data_from_api_cls->fetch_data_from_api();
}


/**
 * Enqueue styles and scripts for the plugin
 * This function will enqueue the necessary styles and scripts for the plugin.
 * It will be called on both the admin and front-end pages.
 * Make sure to include the styles and scripts in the plugin's assets directory.
 * Usage: This function will be automatically called by WordPress when the admin or front-end pages are loaded.
 * It will include the styles from 'css/news-main-style.css' located in the plugin's assets directory.
 */
function news_plugin_enqueue_scripts() {
    // Enqueue custom styles and scripts for the plugin
    wp_enqueue_style('news-plugin-style', PLUGIN_ASSETS_URL . 'css/news-main-style.css', array(), '1.0.0');
}
add_action( 'admin_enqueue_scripts', 'news_plugin_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'news_plugin_enqueue_scripts' );



/**
 * Add a new menu item in the WordPress admin dashboard
 * This function will create a new menu item called "News" in the admin dashboard.
 * It will only be visible to users with the 'manage_options' capability.
 */
add_action('admin_menu', 'add_new_menu');
function add_new_menu() {
    // Check if we're in admin
    if (!is_admin()) return;
    
    // Check if user has capability
    if (!current_user_can('manage_options')) return;

    add_menu_page(
        'News API',
        'News API',
        'manage_options',
        'all-datas.php',
        'all_datas',
        PLUGIN_ASSETS_URL.'news-fav.png',
        25 
    );
}
function all_datas() {

    include 'templates/news-content.php';

    ?>
        <div class="news-content-wrapper">
            <h3 class="news-content-title">News API Data</h3>
            <div class="notice notice-info">
                <p>Shortcode: <input type="text" value="[news_api]" readonly></p>
                <p><a href="?page=all-datas.php&action=refresh">Refresh Data</a></p>
            </div>
            <?php include 'templates/news-table-content.php'; ?>
        </div>
    <?php
}


/**
 * Shortcode to display the API data
 * Usage: [api]
 * This shortcode can be used in posts or pages to display the news data.
 * It will include the necessary template files to render the data.
 * Make sure to include this shortcode in your post or page content.
 * Example: [news_api]
 */
add_shortcode('news_api', 'shortcode_datas');
function shortcode_datas() {

    include 'templates/news-content.php';

    ?>
        <div class="news-content-wrapper">
            <h3 class="news-content-title">Public News Data</h3>
            <?php include 'templates/news-table-content.php'; ?>
        </div>
    <?php
}



/**
 * Check if the action is 'refresh' and fetch data from the API
 * This will be triggered when the user clicks the "Refresh Data" link in the admin menu.
 * It will initialize the API class and call the method to fetch data from the API.
 * This is useful for updating the data in the database with the latest information from the API.
 * Usage: ?page=all-datas.php&action=refresh
 */
if (isset($_GET['action']) && $_GET['action'] == 'refresh') {
    
    // Initialize the API Class and fetch data from the API
    $fetch_data_from_api_cls = new News\PublicApi\Class\Class_Api();
    $fetch_data_from_api_cls->fetch_data_from_api();
}