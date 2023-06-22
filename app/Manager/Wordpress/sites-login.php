<?php
/**
 * Plugin Name:     Sites login
 * Plugin URI:      https://github.com/uspdev/sites/
 * Description:     Use WP-CLI to generate a token login URL for any user.
 * Author:          Masaki K Neto
 * Text Domain:     sites-login
 * Domain Path:     /languages
 * Version:         0.1.0
 * Adapted from:    https://github.com/danielbachhuber/one-time-login
 * Tested:          5.7 - 6.2
 *
 * @package         Sites_Login
 */

/* Exit if accessed directly */
if (!defined('ABSPATH')) {
    return;
}

/**
 * Generate sites one-time tokens using WP CLI.
 *
 * ## OPTIONS
 *
 * <user>
 * : user login for the user
 *
 * [--expiry=<minutes>]
 * : Delete existing token after $expiry minutes from creation (default: 1)
 *
 * ## EXAMPLES
 *
 *     # Generate one-time login URLs.
 *     $ wp user sites-login testuser
 *     http://wpdev.test/wp-login.php?qsites_login_token=ebe62e3
 *
 * @param array $args
 * @param array $assoc_args
 */
function sites_login_wp_cli_command($args, $assoc_args)
{
    $user = get_user_by('login', $args[0]);
    if (!$user instanceof WP_User) {
        wp_die('Invalid user login: ' . $args[0]);
    }

    $expiry = (int) (WP_CLI\Utils\get_flag_value($assoc_args, 'expiry') ?? 1);
    $expiry = ($expiry >= 1) ? $expiry : 1;

    $login_url = sites_login_generate_tokens($user, $expiry);
    WP_CLI::log($login_url);
}

if (class_exists('WP_CLI')) {
    WP_CLI::add_command('user sites-login', 'sites_login_wp_cli_command');
}

/**
 * Generate a token login URL(s) for any user.
 *
 * @param WP_User $user Wordpress user
 * @param int $expiry Delete existing token after $expiry minutes from creation
 *
 * @return array
 */
function sites_login_generate_tokens($user, $expiry)
{

    $tokens = sites_login_get_tokens();
    $new_token = [sha1(wp_generate_password()), $user->ID];
    array_push($tokens, $new_token);
    update_option('sites_login_token', $tokens, false);

    // schedule a cleanup hook
    wp_schedule_single_event(time() + ($expiry * 60), 'sites_login_cleanup_expired_tokens', [$new_token[0]]);

    $login_url = add_query_arg(['sites_login_token' => $new_token[0]], wp_login_url());
    return $login_url;
}

/**
 * Fetch sanitized sites_login_token option
 */
function sites_login_get_tokens()
{
    $tokens = get_option('sites_login_token', []);
    return is_string($tokens) ? [] : $tokens;
}

/**
 * Handle cleanup process for expired one-time login tokens.
 *
 * @param array $expired_token_hash
 */
function sites_login_cleanup_expired_tokens($expired_token_hash)
{
    $tokens = sites_login_get_tokens();
    foreach ($tokens as $i => $token) {
        if ($token[0] == $expired_token_hash) {
            unset($tokens[$i]);
            break;
        }
    }
    update_option('sites_login_token', $tokens, false);
}

add_action('sites_login_cleanup_expired_tokens', 'sites_login_cleanup_expired_tokens', 10, 1);

/**
 * Log a request in as a user if the token is valid.
 */
function sites_login_handle_token()
{
    global $pagenow;

    if ('wp-login.php' !== $pagenow || empty($_GET['sites_login_token'])) {
        return;
    }

    if (is_user_logged_in()) {
        $error = sprintf(__('Invalid sites login token, but you are logged in as \'%1$s\'. <a href="%2$s">Go to the dashboard instead</a>?', 'sites-login'), wp_get_current_user()->user_login, admin_url());
    } else {
        $error = sprintf(__('Invalid sites login token. <a href="%s">Try signing in instead</a>?', 'sites-login'), wp_login_url());
    }

    $tokens = sites_login_get_tokens();
    $is_valid = false;

    // lets remove the used token
    foreach ($tokens as $i => $token) {
        if (hash_equals($token[0], $_GET['sites_login_token'])) {
            $is_valid = true;
            $user = get_user_by('id', $token[1]);
            unset($tokens[$i]);
            break;
        }
    }

    if (!$is_valid) {
        wp_die($error);
    }

    do_action('sites_login_logged_in', $user);
    update_option('sites_login_token', $tokens, false);
    wp_set_auth_cookie($user->ID, true, is_ssl());
    do_action('sites_login_after_auth_cookie_set', $user);
    wp_safe_redirect(admin_url());
    exit;
}

add_action('init', 'sites_login_handle_token');
