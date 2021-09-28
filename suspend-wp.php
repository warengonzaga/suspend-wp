<?php
/**
* Plugin Name: Suspend WP
* Plugin URI: https://github.com/warengonzaga/suspend-wp
* Description: A WordPress plugin to suspend WordPress sites automagically. Simple and lightweight, no annoying ads and fancy settings.
* Version: 1.0.0
* Author: Waren Gonzaga
* Author URI: https://warengonzaga.com
* License: GPL-3.0+
* Text Domain: suspend-wp
*
* @package suspend-wp
* @copyright Copyright (c) 2021, Waren Gonzaga
* @license GPL3
*/

/**
* Suspend WP
*
* Displays the suspended message page for anyone who's not logged in.
* Client with unpaid invoices will automatically be suspended.
*
* @return void
*/

// prevent direct access
defined( 'ABSPATH' ) or die( "Restricted Access!" );

class SuspendWP {
    // suspendwp methods

    public function register() {
        // load the plugin
        add_action( 'wp_loaded', array( $this, 'suspend_wp' ) );
        
        // register admin styles and scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqeueu' ) );
    }

    public function activate() {
        // methods on activate
        require_once plugins_url(__FILE__) . 'includes/suspendwp-plugin-activate.php';
        SuspendWPActivate::activate();
    }

    public function deactivate() {
        // methods on deactivate
        require_once plugins_url(__FILE__) . 'includes/suspendwp-plugin-deactivate.php';
        SuspendWPDeactivate::deactivate();
    }

    protected function admin_enqueue() {
        // enqueue plugin admin styles and scripts
        wp_enqueue_style( 'suspendwp_admin_styles', plugins_url( 'assets/css/admin.css', __FILE__ ) );
        wp_enqueue_script( 'suspendwp_admin_scripts', plugins_url( 'assets/js/admin.js', __FILE__ ) );
    }

    private function suspend_wp() {
        // main method of suspendwp

        global $pagenow;

        if ( $pagenow !== 'wp-login.php' && ! current_user_can( 'manage_options' ) && ! is_admin() ) {
            
            header( $_SERVER["SERVER_PROTOCOL"] . ' 503 Service Temporarily Unavailable', true, 503 );
            header( 'Content-Type: text/html; charset=utf-8' );

            if ( file_exists( plugin_dir_path( __FILE__ ) . 'views/suspended-view.php' ) ) {
                require_once( plugin_dir_path( __FILE__ ) . 'views/suspended-view.php' );
            }

            die();
        }

    }
}

if ( class_exists( 'SuspendWP' )) {
    $suspendWP = new SuspendWP();
    $suspendWP->register();
}

// activation of plugin
register_activation_hook( __FILE__, array( $suspendWP, 'activate' ) );

// deactivation of plugin
register_deactivation_hook( __FILE__, array( $suspendWP, 'deactivate' ) );