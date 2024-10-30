<?php
/**
 * Plugin Name: Lana Email Logger
 * Plugin URI: https://lana.codes/product/lana-email-logger/
 * Description: Logs all emails sent by WordPress.
 * Version: 1.1.0
 * Author: Lana Codes
 * Author URI: https://lana.codes/
 * Text Domain: lana-email-logger
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();
define( 'LANA_EMAIL_LOGGER_VERSION', '1.1.0' );
define( 'LANA_EMAIL_LOGGER_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'LANA_EMAIL_LOGGER_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Includes
 * classes
 */
require_once LANA_EMAIL_LOGGER_DIR_PATH . '/includes/class-lana-email.php';

/**
 * Language
 * load
 */
load_plugin_textdomain( 'lana-email-logger', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Add plugin action links
 *
 * @param $links
 *
 * @return mixed
 */
function lana_email_logger_add_plugin_action_links( $links ) {

	$settings_url = esc_url( admin_url( 'admin.php?page=lana-email-logger-settings.php' ) );

	/** add settings link */
	$settings_link = sprintf( '<a href="%s">%s</a>', $settings_url, __( 'Settings', 'lana-email-logger' ) );
	array_unshift( $links, $settings_link );

	return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'lana_email_logger_add_plugin_action_links' );

/**
 * Lana Email Logger - Settings page
 * load admin scripts
 *
 * @param $hook
 */
function lana_email_logger_settings_admin_scripts( $hook ) {

	if ( 'lana-email-logger_page_lana-email-logger-settings' != $hook ) {
		return;
	}

	/** lana email logger settings admin js */
	wp_register_script( 'lana-email-logger-settings-admin', LANA_EMAIL_LOGGER_DIR_URL . '/assets/js/lana-email-logger-settings-admin.js', array( 'jquery' ), LANA_EMAIL_LOGGER_VERSION, true );
	wp_enqueue_script( 'lana-email-logger-settings-admin' );
}

add_action( 'admin_enqueue_scripts', 'lana_email_logger_settings_admin_scripts' );

/**
 * Lana Email Logger
 * load admin styles
 */
function lana_email_logger_admin_styles() {

	/** lana email logger admin */
	wp_register_style( 'lana-email-logger-admin', LANA_EMAIL_LOGGER_DIR_URL . '/assets/css/lana-email-logger-admin.css', array(), LANA_EMAIL_LOGGER_VERSION );
	wp_enqueue_style( 'lana-email-logger-admin' );
}

add_action( 'admin_enqueue_scripts', 'lana_email_logger_admin_styles' );

/**
 * Install Lana Email Logger
 * - create email log table
 */
function lana_email_logger_install() {
	lana_email_logger_create_email_logs_table();
}

register_activation_hook( __FILE__, 'lana_email_logger_install' );

/**
 * Create email logs table
 */
function lana_email_logger_create_email_logs_table() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_name      = $wpdb->prefix . 'lana_email_logger_logs';

	/** create table */
	$wpdb->query( "CREATE TABLE IF NOT EXISTS " . $table_name . " (
	  id bigint(20) NOT NULL auto_increment,
	  user_id bigint(20) DEFAULT NULL,
	  username varchar(255) DEFAULT NULL,
	  user_ip varchar(255) NOT NULL,
	  user_agent varchar(255) NOT NULL,
	  email_to text NOT NULL,
	  subject text NOT NULL,
	  message text NOT NULL,
	  headers text NOT NULL,
	  date datetime DEFAULT NULL,
	  PRIMARY KEY (id)
	) " . $charset_collate . ";" );
}

/**
 * Add Lana Email Logger
 * custom wp roles
 */
function lana_email_logger_custom_wp_roles() {

	/**
	 * Administrator
	 * role
	 */
	$administrator_role = get_role( 'administrator' );

	if ( is_a( $administrator_role, 'WP_Role' ) ) {
		$administrator_role->add_cap( 'manage_lana_email_logger_logs' );
	}
}

add_action( 'admin_init', 'lana_email_logger_custom_wp_roles' );

/**
 * Lana Email Logger
 * add admin page
 */
function lana_email_logger_admin_menu() {
	global $lana_email_logger_logs_page;

	/** Lana Email Logger page */
	$lana_email_logger_logs_page = add_menu_page( __( 'Lana Email Logger', 'lana-email-logger' ), __( 'Lana Email Logger', 'lana-email-logger' ), 'manage_options', 'lana-email-logger.php', 'lana_email_logger_logs_page', 'dashicons-email-alt', 82 );

	/** add screen options */
	add_action( 'load-' . $lana_email_logger_logs_page, 'lana_email_logger_logs_page_screen_options' );

	/** Settings page */
	add_submenu_page( 'lana-email-logger.php', __( 'Settings', 'lana-email-logger' ), __( 'Settings', 'lana-email-logger' ), 'manage_options', 'lana-email-logger-settings.php', 'lana_email_logger_settings_page' );

	/** call register settings function */
	add_action( 'admin_init', 'lana_email_logger_register_settings' );
}

add_action( 'admin_menu', 'lana_email_logger_admin_menu' );

/**
 * Lana Email Logger
 * add view lana email submenu page
 */
function lana_email_logger_admin_menu_view_lana_email() {
	global $pagenow;
	global $lana_email_view_page;

	if ( 'admin.php' != $pagenow ) {
		return;
	}

	if ( ! isset( $_GET['page'] ) ) {
		return;
	}

	if ( 'lana-email-view.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
		return;
	}

	/** Lana Email Logger page */
	add_submenu_page( 'lana-email-logger.php', __( 'Lana Email Logger', 'lana-email-logger' ), __( 'Lana Email Logger', 'lana-email-logger' ), 'manage_options', 'lana-email-logger.php' );

	/** View Email page */
	$lana_email_view_page = add_submenu_page( 'lana-email-logger.php', __( 'View Email', 'lana-email-logger' ), __( 'View Email', 'lana-email-logger' ), 'manage_options', 'lana-email-view.php', 'lana_email_logger_view_email_page' );
	lana_email_logger_add_lana_email_view_page_args();
}

add_action( 'admin_menu', 'lana_email_logger_admin_menu_view_lana_email', 1 );

/**
 * Lana Email Logger
 * add id query arg for submenu page
 */
function lana_email_logger_add_lana_email_view_page_args() {
	global $submenu;

	$page_slug   = 'lana-email-view.php';
	$parent_slug = 'lana-email-logger.php';
	$position    = lana_emails_manager_search_submenu_position( $page_slug, $parent_slug );

	/** check position */
	if ( ! is_numeric( $position ) ) {
		return;
	}

	/** check submenu */
	if ( $submenu[ $parent_slug ][ $position ][2] != $page_slug ) {
		return;
	}

	/** add args */
	$submenu[ $parent_slug ][ $position ][2] = add_query_arg( array(
		'page' => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
		'id'   => absint( wp_unslash( $_GET['id'] ) ),
	), 'admin.php' );
}

add_action( 'admin_menu', 'lana_email_logger_add_lana_email_view_page_args' );

/**
 * Lana Email Logger
 * change parent file in lana email view
 *
 * @param $parent_file
 *
 * @return string
 */
function lana_email_logger_lana_email_view_parent_file( $parent_file ) {
	global $pagenow;

	if ( 'admin.php' != $pagenow ) {
		return $parent_file;
	}

	if ( ! isset( $_GET['page'] ) ) {
		return $parent_file;
	}

	if ( 'lana-email-view.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
		return $parent_file;
	}

	return 'lana-email-logger.php';
}

add_filter( 'parent_file', 'lana_email_logger_lana_email_view_parent_file' );

/**
 * Lana Email Logger
 * change submenu file in lana email view
 *
 * @param $submenu_file
 *
 * @return string
 */
function lana_email_logger_lana_email_view_submenu_file( $submenu_file ) {
	global $pagenow;

	if ( 'admin.php' != $pagenow ) {
		return $submenu_file;
	}

	if ( ! isset( $_GET['page'] ) ) {
		return $submenu_file;
	}

	if ( 'lana-email-view.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
		return $submenu_file;
	}

	return add_query_arg( array(
		'page' => sanitize_file_name( wp_unslash( $_GET['page'] ) ),
		'id'   => absint( wp_unslash( $_GET['id'] ) ),
	), 'admin.php' );
}

add_filter( 'submenu_file', 'lana_email_logger_lana_email_view_submenu_file' );

/**
 * Lana Email Logger
 * change admin title in lana email view
 *
 * @param $admin_title
 *
 * @return string
 */
function lana_email_logger_lana_email_view_admin_title( $admin_title ) {
	global $pagenow;

	if ( 'admin.php' != $pagenow ) {
		return $admin_title;
	}

	if ( ! isset( $_GET['page'] ) ) {
		return $admin_title;
	}

	if ( 'lana-email-view.php' != sanitize_file_name( wp_unslash( $_GET['page'] ) ) ) {
		return $admin_title;
	}

	if ( ! isset( $_GET['id'] ) ) {
		return $admin_title;
	}

	/** change admin title */
	$admin_title = __( 'View Email', 'lana-email-logger' ) . $admin_title;

	return $admin_title;
}

add_filter( 'admin_title', 'lana_email_logger_lana_email_view_admin_title' );

/**
 * Lana Email Logger
 * search submenu position
 *
 * @param $page_slug
 * @param $parent_slug
 *
 * @return int|null
 */
function lana_emails_manager_search_submenu_position( $page_slug, $parent_slug ) {
	global $submenu;

	if ( ! isset( $submenu[ $parent_slug ] ) ) {
		return null;
	}

	foreach ( $submenu[ $parent_slug ] as $i => $item ) {
		if ( $page_slug == $item[2] ) {
			return $i;
		}
	}

	return null;
}

/**
 * Lana Email Logger
 * logs page screen options - add per page option
 */
function lana_email_logger_logs_page_screen_options() {
	global $lana_email_logger_logs_page;

	$screen = get_current_screen();

	if ( $screen->id != $lana_email_logger_logs_page ) {
		return;
	}

	$args = array(
		'label'   => __( 'Logs per page', 'lana-email-logger' ),
		'default' => 25,
		'option'  => 'lana_email_logger_logs_per_page',
	);
	add_screen_option( 'per_page', $args );
}

/**
 * Lana Email Logger
 * logs page - set screen options
 *
 * @param $screen_value
 * @param $option
 * @param $value
 *
 * @return mixed
 */
function lana_email_logger_logs_page_set_screen_option( $screen_value, $option, $value ) {

	if ( 'lana_email_logger_logs_per_page' == $option ) {
		$screen_value = $value;
	}

	if ( 'lana_email_logger_login_logs_per_page' == $option ) {
		$screen_value = $value;
	}

	return $screen_value;
}

add_filter( 'set-screen-option', 'lana_email_logger_logs_page_set_screen_option', 10, 3 );

/**
 * Register settings
 */
function lana_email_logger_register_settings() {
	register_setting( 'lana-email-logger-settings-group', 'lana_email_logger_cleanup_by_amount' );
	register_setting( 'lana-email-logger-settings-group', 'lana_email_logger_cleanup_amount' );
	register_setting( 'lana-email-logger-settings-group', 'lana_email_logger_cleanup_by_time' );
	register_setting( 'lana-email-logger-settings-group', 'lana_email_logger_cleanup_time' );
}

/**
 * Lana Email Logger
 * logs page
 */
function lana_email_logger_logs_page() {
	global $wpdb;

	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	require_once LANA_EMAIL_LOGGER_DIR_PATH . '/includes/class-lana-email-logger-logs-list-table.php';

	$lana_email_logger_logs_list_table = new Lana_Email_Logger_Logs_List_Table();

	/** manage actions */
	$action = $lana_email_logger_logs_list_table->current_action();

	if ( $action ) {

		/** delete logs */
		if ( 'delete_logs' == $action ) {

			if ( ! current_user_can( 'manage_lana_email_logger_logs' ) ) {
				wp_die( __( 'Sorry, you are not allowed to delete logs.', 'lana-email-logger' ) );
			}

			check_admin_referer( 'bulk-lana_email_logger_logs' );

			$table_name = $wpdb->prefix . 'lana_email_logger_logs';
			$wpdb->query( "TRUNCATE TABLE " . $table_name . ";" );
		}
	}

	/** prepare items */
	$lana_email_logger_logs_list_table->prepare_items();
	?>
    <div class="wrap">
        <h2>
			<?php _e( 'Email Logs', 'lana-email-logger' ); ?>
        </h2>
        <br/>

        <form id="lana-email-logger-logs-form" method="post">
			<?php $lana_email_logger_logs_list_table->display(); ?>
        </form>
    </div>
	<?php
}

/**
 * Lana Email Logger
 * settings page
 */
function lana_email_logger_settings_page() {
	?>
    <div class="wrap">
        <h2><?php _e( 'Lana Email Logger Settings', 'lana-email-logger' ); ?></h2>

		<?php settings_errors(); ?>

        <hr/>
        <a href="<?php echo esc_url( 'https://lana.codes/' ); ?>" target="_blank">
            <img src="<?php echo esc_url( LANA_EMAIL_LOGGER_DIR_URL . '/assets/img/plugin-header.png' ); ?>"
                 alt="<?php esc_attr_e( 'Lana Codes', 'lana-email-logger' ); ?>"/>
        </a>
        <hr/>

        <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php settings_fields( 'lana-email-logger-settings-group' ); ?>

            <h2 class="title"><?php _e( 'Cleanup Settings', 'lana-email-logger' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="lana-email-logger-cleanup-by-amount">
							<?php _e( 'Cleanup by Amount', 'lana-email-logger' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="lana_email_logger_cleanup_by_amount" id="lana-email-logger-cleanup-by-amount"
                                data-tr-target=".cleanup-amount">
                            <option value="0"
								<?php selected( get_option( 'lana_email_logger_cleanup_by_amount', false ), false ); ?>>
								<?php _e( 'Disabled', 'lana-email-logger' ); ?>
                            </option>
                            <option value="1"
								<?php selected( get_option( 'lana_email_logger_cleanup_by_amount', false ), true ); ?>>
								<?php _e( 'Enabled', 'lana-email-logger' ); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr class="cleanup cleanup-amount">
                    <th scope="row">
                        <label for="lana-email-logger-cleanup-amount">
							<?php _e( 'Cleanup Amount', 'lana-email-logger' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" name="lana_email_logger_cleanup_amount"
                               id="lana-email-logger-cleanup-amount"
                               value="<?php echo esc_attr( get_option( 'lana_email_logger_cleanup_amount' ) ); ?>">
                        <p class="description">
							<?php _e( 'Deletes emails that exceed the set value.', 'lana-email-logger' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="lana-email-logger-cleanup-by-time">
							<?php _e( 'Cleanup by Time', 'lana-email-logger' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="lana_email_logger_cleanup_by_time" id="lana-email-logger-cleanup-by-time"
                                data-tr-target=".cleanup-time">
                            <option value="0"
								<?php selected( get_option( 'lana_email_logger_cleanup_by_time', false ), false ); ?>>
								<?php _e( 'Disabled', 'lana-email-logger' ); ?>
                            </option>
                            <option value="1"
								<?php selected( get_option( 'lana_email_logger_cleanup_by_time', false ), true ); ?>>
								<?php _e( 'Enabled', 'lana-email-logger' ); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr class="cleanup cleanup-time">
                    <th scope="row">
                        <label for="lana-email-logger-cleanup-time">
							<?php _e( 'Cleanup Time', 'lana-email-logger' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" name="lana_email_logger_cleanup_time"
                               id="lana-email-logger-cleanup-time"
                               value="<?php echo esc_attr( get_option( 'lana_email_logger_cleanup_time' ) ); ?>">
                        <p class="description">
							<?php _e( 'Deletes emails that are older than the set days.', 'lana-email-logger' ); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary"
                       value="<?php esc_attr_e( 'Save Changes', 'lana-email-logger' ); ?>"/>
            </p>

        </form>
    </div>
	<?php
}

/**
 * Lana Email Logger
 * view email
 */
function lana_email_logger_view_email_page() {

	global $lana_email;

	if ( ! isset( $_GET['id'] ) ) {
		wp_die( __( 'The id is not set.', 'lana-email-logger' ) );
	}

	$id = intval( $_GET['id'] );

	$lana_email = Lana_Email::find( $id );

	include_once LANA_EMAIL_LOGGER_DIR_PATH . '/views/lana-email-view.php';
}

/**
 * Lana Email Logger
 * view email
 * add headers to postbox 1
 */
function lana_email_logger_view_email_add_headers_to_postbox_1() {
	include_once LANA_EMAIL_LOGGER_DIR_PATH . '/views/lana-email-view-headers.php';
}

add_action( 'lana_email_logger_email_view_postbox_1', 'lana_email_logger_view_email_add_headers_to_postbox_1', 1 );

/**
 * Lana Email Logger
 * view email
 * add info to postbox 1
 */
function lana_email_logger_view_email_add_info_to_postbox_1() {
	include_once LANA_EMAIL_LOGGER_DIR_PATH . '/views/lana-email-view-info.php';
}

add_action( 'lana_email_logger_email_view_postbox_1', 'lana_email_logger_view_email_add_info_to_postbox_1', 2 );

/**
 * Lana Email Logger
 * view email
 * add date to postbox 1
 */
function lana_email_logger_view_email_add_date_to_postbox_1() {
	include_once LANA_EMAIL_LOGGER_DIR_PATH . '/views/lana-email-view-date.php';
}

add_action( 'lana_email_logger_email_view_postbox_1', 'lana_email_logger_view_email_add_date_to_postbox_1', 3 );

/**
 * Lana Email Logger
 * save wp_mail
 *
 * @param $mail
 *
 * @return mixed
 */
function lana_email_logger_save_wp_mail( $mail ) {

	global $wpdb;

	$email_to = sanitize_email( $mail['to'] );
	$subject  = sanitize_text_field( $mail['subject'] );
	$message  = wp_strip_all_tags( $mail['message'] );
	$headers  = $mail['headers'];

	/**
	 * Set
	 * email to
	 */
	if ( is_array( $email_to ) ) {
		$email_to = implode( '\n', $email_to );
	}

	/**
	 * Set
	 * headers
	 */
	if ( is_array( $headers ) ) {
		$headers = implode( '\n', $headers );
	}

	$headers = str_replace( '\r\n', '\n', $headers );

	/**
	 * Save
	 * in wpdb
	 */
	$wpdb->hide_errors();

	/** @var WP_User $user */
	$user     = wp_get_current_user();
	$user_id  = $user->ID;
	$username = $user->user_login;

	/** @var Lana_Email $lana_email */
	$lana_email = new Lana_Email( array(
		'user_id'  => $user_id,
		'username' => $username,
		'email_to' => $email_to,
		'subject'  => $subject,
		'message'  => $message,
		'headers'  => $headers,
		'date'     => current_time( 'mysql' ),
	) );
	$lana_email->save();

	return $mail;
}

add_filter( 'wp_mail', 'lana_email_logger_save_wp_mail' );

/**
 * Get user IP
 * @return mixed
 */
function lana_email_logger_get_user_ip() {

	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];

	if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
		$ip = $client;
	} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
		$ip = $forward;
	} else {
		$ip = $remote;
	}

	return $ip;
}

/**
 * Get user agent
 * @return mixed
 */
function lana_email_logger_get_user_agent() {

	if ( ! isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return '';
	}

	if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		return '';
	}

	return $_SERVER['HTTP_USER_AGENT'];
}

/**
 * Lana Email logger
 * update cleanup option - add schedule event
 *
 * @param $option
 */
function lana_email_logger_update_cleanup_option_add_schedule_event( $option ) {

	$cleanup_by_amount_update_options = array(
		'lana_email_logger_cleanup_by_amount',
		'lana_email_logger_cleanup_amount',
	);

	$cleanup_by_time_update_options = array(
		'lana_email_logger_cleanup_by_time',
		'lana_email_logger_cleanup_time',
	);

	if ( in_array( $option, $cleanup_by_amount_update_options ) ) {
		lana_email_logger_cleanup_by_amount_schedule_event();
	}

	if ( in_array( $option, $cleanup_by_time_update_options ) ) {
		lana_email_logger_cleanup_by_time_schedule_event();
	}
}

add_action( 'added_option', 'lana_email_logger_update_cleanup_option_add_schedule_event' );
add_action( 'updated_option', 'lana_email_logger_update_cleanup_option_add_schedule_event' );

/**
 * Lana Email logger
 * cleanup by amount - create a scheduled event
 */
function lana_email_logger_cleanup_by_amount_schedule_event() {
	if ( ! get_option( 'lana_email_logger_cleanup_by_amount', false ) ) {
		wp_clear_scheduled_hook( 'lana_email_logger_cleanup_by_amount' );

		return;
	}

	if ( ! wp_next_scheduled( 'lana_email_logger_cleanup_by_amount' ) ) {
		wp_schedule_event( time(), 'hourly', 'lana_email_logger_cleanup_by_amount' );
	}
}

add_action( 'plugins_loaded', 'lana_email_logger_cleanup_by_amount_schedule_event' );

/**
 * Lana Email logger
 * cleanup by time - create a scheduled event
 */
function lana_email_logger_cleanup_by_time_schedule_event() {
	if ( ! get_option( 'lana_email_logger_cleanup_by_time', false ) ) {
		wp_clear_scheduled_hook( 'lana_email_logger_cleanup_by_time' );

		return;
	}

	if ( ! wp_next_scheduled( 'lana_email_logger_cleanup_by_time' ) ) {
		wp_schedule_event( time(), 'hourly', 'lana_email_logger_cleanup_by_time' );
	}
}

add_action( 'plugins_loaded', 'lana_email_logger_cleanup_by_time_schedule_event' );

/**
 * Lana Email logger
 * cleanup emails by amount
 */
function lana_email_logger_cleanup_emails_by_amount() {
	global $wpdb;

	/** check by amount */
	if ( ! get_option( 'lana_email_logger_cleanup_by_amount', false ) ) {
		return;
	}

	$cleanup_amount = absint( get_option( 'lana_email_logger_cleanup_amount' ) );

	/** check amount */
	if ( $cleanup_amount <= 0 ) {
		return;
	}

	$table_name = $wpdb->prefix . 'lana_email_logger_logs';

	/** delete query */
	$wpdb->query( "DELETE lana_email_logger_logs FROM " . $table_name . " AS lana_email_logger_logs
						JOIN ( 
						    SELECT id FROM " . $table_name . " ORDER BY id DESC LIMIT 1 OFFSET " . $cleanup_amount . "
						) AS lana_email_logger_logs_limit ON lana_email_logger_logs.id <= lana_email_logger_logs_limit.id;" );
}

add_action( 'lana_email_logger_cleanup_by_amount', 'lana_email_logger_cleanup_emails_by_amount' );

/**
 * Lana Email logger
 * cleanup emails by time
 */
function lana_email_logger_cleanup_emails_by_time() {
	global $wpdb;

	/** check by time */
	if ( ! get_option( 'lana_email_logger_cleanup_by_time', false ) ) {
		return;
	}

	$cleanup_time = absint( get_option( 'lana_email_logger_cleanup_time' ) );

	/** check time */
	if ( $cleanup_time <= 0 ) {
		return;
	}

	$table_name = $wpdb->prefix . 'lana_email_logger_logs';

	/** delete query */
	$wpdb->query( "DELETE FROM " . $table_name . " WHERE DATEDIFF( NOW(), date ) >= " . $cleanup_time . ";" );
}

add_action( 'lana_email_logger_cleanup_by_time', 'lana_email_logger_cleanup_emails_by_time' );