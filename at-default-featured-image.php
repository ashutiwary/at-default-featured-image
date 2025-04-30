<?php

/**
 * Plugin Name: AT Default Featured Image
 * Description: Adds a default featured image for posts without one
 * Version: 1.0.0
 * Author: Ashu Tiwary
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AT_DEFAULT_FEATURED_IMAGE_PATH', plugin_dir_path(__FILE__));
define('AT_DEFAULT_FEATURED_IMAGE_URL', plugin_dir_url(__FILE__));
define('AT_DEFAULT_IMAGE_PLACEHOLDER', plugin_dir_url(__FILE__) . 'assets/at-default-featured-image.png');

// Include required files
require_once AT_DEFAULT_FEATURED_IMAGE_PATH . 'includes/class-at-default-featured-image.php';
require_once AT_DEFAULT_FEATURED_IMAGE_PATH . 'admin/class-at-default-featured-image-admin.php';

// Initialize the plugin
function at_default_featured_image_init()
{
    new AT_Default_Featured_Image();
    if (is_admin()) {
        new AT_Default_Featured_Image_Admin();
    }
}
add_action('plugins_loaded', 'at_default_featured_image_init');

// Add settings link to plugins page
function at_default_featured_image_settings_link($links)
{
    $settings_link = '<a href="' . admin_url('options-general.php?page=at-default-featured-image') . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'at_default_featured_image_settings_link');

// Activation hook
register_activation_hook(__FILE__, 'at_default_featured_image_activate');
function at_default_featured_image_activate()
{
    // Set default image and enabled status on activation
    if (!get_option('at_default_featured_image')) {
        update_option('at_default_featured_image', AT_DEFAULT_IMAGE_PLACEHOLDER);
    }
    if (!get_option('at_default_featured_image_enabled')) {
        update_option('at_default_featured_image_enabled', 'yes');
    }
    add_option('at_default_featured_image_do_activation_redirect', true);
}

// Redirect after activation
add_action('admin_init', 'at_default_featured_image_redirect');
function at_default_featured_image_redirect()
{
    if (get_option('at_default_featured_image_do_activation_redirect', false)) {
        delete_option('at_default_featured_image_do_activation_redirect');
        if (!isset($_GET['activate-multi'])) {
            wp_safe_redirect(admin_url('options-general.php?page=at-default-featured-image'));
            exit;
        }
    }
}
