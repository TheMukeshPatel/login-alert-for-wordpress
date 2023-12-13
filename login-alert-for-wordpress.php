<?php
/**
 * Plugin Name: Login Alert for WordPress
 * Plugin URI: https://mpateldigital.com/
 * Description: Sends Email notifications to users upon login.
 * Version: 0.0.2
 * Author: Mukesh Patel
 * Author URI: https://mpateldigital.com/
 * License: GPLv2 or later 
 * Text Domain: login-alert-for-wordpress
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/* Register activation hook. */
register_activation_hook(__FILE__, 'login_alert_for_wordpress_activation_hook');
/**
 * Runs only when the plugin is activated.
 * @since 0.0.1
 */
function login_alert_for_wordpress_activation_hook()
{
    // Check nonce for security
    check_admin_referer('login-alert-activation');

    /* Create transient data */
    set_transient('login-alert-for-wordpress-activation-notice', true, 5);
}

add_action('admin_notices', 'login_alert_for_wordpress_notice');

/* Add admin notice */
function login_alert_for_wordpress_notice()
{
    /* Check for transient, if available display notice */
    if (get_transient('login-alert-for-wordpress-activation-notice')) {
        ?>
        <style>
            div#message.updated {
                display: none;
            }
        </style>
        <div class="updated notice is-dismissible">
            <p>
                <?php echo esc_html__('😊 Thank you for using Notify User on Login. Plugin is started working. You do not need to configure anything', 'login_alert_for_wordpress'); ?>
            </p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient('login-alert-for-wordpress-activation-notice');
    }
}

// Support links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'login_alert_for_wordpress_add_action_links');
function login_alert_for_wordpress_add_action_links($links)
{
    $plugin_shortcuts = array(
        '<a rel="noopener" title="Hire for Technical Support" href="https://mpateldigital.net/contact/" target="_blank" style="color: #d42e06;font-weight: 500;">' . __('Hire Me', 'login_alert_for_wordpress') . '</a>',
        '<a rel="noopener" title="Show your support" href="https://ko-fi.com/mukeshpatel" target="_blank" style="color:#080;">' . __('Buy developer a coffee', 'login_alert_for_wordpress') . '</a>'
    );
    return array_merge($links, $plugin_shortcuts);
}

/**
 * Login Alert for WordPress Plugin Main Work
 */

add_action('wp_login', 'login_alert_for_wordpress_email_notification', 10, 2);

function login_alert_for_wordpress_email_notification($user_login, $user)
{
    // Get user email (sanitize and validate)
    $user_email = is_email(sanitize_email($user->user_email)) ? sanitize_email($user->user_email) : '';

    // Get user IP address
    $user_ip = esc_attr($_SERVER['REMOTE_ADDR']);

    // Get user device
    $user_agent = esc_attr($_SERVER['HTTP_USER_AGENT']);

    // Subject and message of the email
    $subject = 'You have successfully logged in to ' . get_bloginfo('name');
    $message = 'Hi ' . sanitize_text_field($user_login) . ',' . "\r\n" . 'This is a notification that you have successfully logged in to ' . get_bloginfo('name') . ' on ' . date('Y-m-d H:i:s') . "\r\n" . "\r\n" . 'IP Address: ' .  $user_ip . "\r\n" . 'Device: ' . $user_agent;

    // Send email using wp_mail() function
    wp_mail($user_email, $subject, $message);
}
