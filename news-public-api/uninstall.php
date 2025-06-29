<?php
/**
 * Uninstall script for the News Public API plugin.
 *
 * This file is executed when the plugin is uninstalled.
 * It removes the custom database table created by the plugin.
 */
// Prevent direct access to this file
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}


global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}news_api_data" );