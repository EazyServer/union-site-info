<?php
/*
Plugin Name: Union Site Info
Plugin URI:
Description: WordPress dashboard widget displaying the main site info.
Version:     1.1
Author:      Union Digital
Author URI:  http://www.union.co.uk/
Text Domain: site-info-dashboard-widget
*/



/**
 * Security check
 *
 * Prevent direct access to the file.
 *
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/**
 * Site Info Dashboard Widget
 *
 * Register new WordPress dashboard widget.
 *
 * @since 1.0
 */
class Site_Info_Dashboard_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		add_action( 'plugins_loaded',     array( $this, 'load_textdomain' )      );

	}

	/**
	 * Plugin textdomain
	 *
	 * Load plugin textdomain.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function load_textdomain() {

		load_plugin_textdomain( 'site-info-dashboard-widget' ); 

	}

	/**
	 * Add Dashboard Widget
	 *
	 * Registers the "Site info" dashboard widget.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function add_dashboard_widget() {

		wp_add_dashboard_widget(
			'site_info_dashboard_widget',
			__( 'Site Info', 'site-info-dashboard-widget' ),
			array( $this, 'render_dashboard_widget' )
		);

	}

	/**
	 * Render Dashboard Widget
	 *
	 * Creates the widget content with all the data.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function render_dashboard_widget() {

		global $wpdb;

		$info = array(
			__( 'Site Name',              'site-info-dashboard-widget') => get_bloginfo( 'name' ),
			__( 'Site Tagline',           'site-info-dashboard-widget') => get_bloginfo( 'description' ),
			__( 'Site URL',               'site-info-dashboard-widget') => get_site_url(),
			__( 'Home URL',               'site-info-dashboard-widget') => get_home_url(),
			__( 'Admin Email',            'site-info-dashboard-widget') => get_bloginfo( 'admin_email' ),
			__( 'Admin Language',         'site-info-dashboard-widget') => get_bloginfo( 'language' ),
			__( 'Text Direction',         'site-info-dashboard-widget') => get_bloginfo( 'text_direction' ),
			__( 'PHP Version',            'site-info-dashboard-widget') => PHP_VERSION,
			__( 'MySQL Version',          'site-info-dashboard-widget') => $wpdb->db_version(),
			__( 'Web Server Info',        'site-info-dashboard-widget') => esc_html( $_SERVER['SERVER_SOFTWARE'] ),
			__( 'WordPress Version',      'site-info-dashboard-widget') => get_bloginfo( 'version' ),
			__( 'WordPress Memory Limit', 'site-info-dashboard-widget') => size_format( WP_MEMORY_LIMIT ),
			'remote_end_point' => get_site_url().'/wp-admin/admin-ajax.php?action=get_site_info&key='.urlencode(NONCE_KEY),
		);

		echo '<table>';
		foreach ( $info as $key => $value ) {
			echo '<tr><td><strong>' . $key . ' :</strong></td><td>' . $value . '</td></tr>';
		}
		echo '</table>';

	}

}

new Site_Info_Dashboard_Widget;

add_action( 'wp_ajax_nopriv_get_site_info', 'get_site_info' );
add_action( 'wp_ajax_get_site_info', 'get_site_info' );

function get_site_info() {

	if($_GET['key'] == NONCE_KEY)
	{

		global $wpdb;

		$plugins = get_plugins();

		foreach ( $plugins as $key=>$plugin ) {
			$plugins[$key]['isActive'] = is_plugin_active($key);
		}


		$info =  json_encode(array(
			__( 'SiteName',              'site-info-dashboard-widget') => get_bloginfo( 'name' ),
			__( 'SiteTagline',           'site-info-dashboard-widget') => get_bloginfo( 'description' ),
			__( 'SiteURL',               'site-info-dashboard-widget') => get_site_url(),
			__( 'HomeURL',               'site-info-dashboard-widget') => get_home_url(),
			__( 'AdminEmail',            'site-info-dashboard-widget') => get_bloginfo( 'admin_email' ),
			__( 'AdminLanguage',         'site-info-dashboard-widget') => get_bloginfo( 'language' ),
			__( 'TextDirection',         'site-info-dashboard-widget') => get_bloginfo( 'text_direction' ),
			__( 'PHPVersion',            'site-info-dashboard-widget') => PHP_VERSION,
			__( 'MySQLVersion',          'site-info-dashboard-widget') => $wpdb->db_version(),
			__( 'WebServerInfo',        'site-info-dashboard-widget') => esc_html( $_SERVER['SERVER_SOFTWARE'] ),
			__( 'WordPressVersion',      'site-info-dashboard-widget') => get_bloginfo( 'version' ),
			__( 'WordPressMemory Limit', 'site-info-dashboard-widget') => size_format( WP_MEMORY_LIMIT ),
			'Plugins' => $plugins
		));

		echo $info;
	}

	exit(0);
}
