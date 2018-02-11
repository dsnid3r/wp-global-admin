<?php
/**
 * Build Global Administration Menu.
 *
 * @package WPGlobalAdmin
 * @since 1.0.0
 */

/* translators: Global menu item */
$menu[2] = array( __( 'Dashboard' ), 'manage_global', 'index.php', '', 'menu-top menu-top-first menu-icon-dashboard', 'menu-dashboard', 'dashicons-dashboard' );

$menu[4] = array( '', 'read', 'separator1', '', 'wp-menu-separator' );

$menu[10] = array( __( 'Users' ), 'manage_global_users', 'users.php', '', 'menu-top menu-icon-users', 'menu-users', 'dashicons-admin-users' );

$submenu['users.php'][5]  = array( __( 'All Users' ), 'manage_global_users', 'users.php' );
$submenu['users.php'][10] = array( _x( 'Add New', 'user' ), 'create_users', 'user-new.php' );

$menu[25] = array( __( 'Settings' ), 'manage_global_options', 'settings.php', '', 'menu-top menu-icon-settings', 'menu-settings', 'dashicons-admin-settings' );

$menu[99] = array( '', 'exist', 'separator-last', '', 'wp-menu-separator' );

// This would be needed if it was in Core.
//require_once( ABSPATH . 'wp-admin/includes/menu.php' );
