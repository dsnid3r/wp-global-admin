<?php
/**
 * Core User Role & Capabilities API
 *
 * @package WPGlobalAdmin
 * @since 1.0.0
 */

if ( ! function_exists( 'get_global_administrators' ) ) :

	/**
	 * Retrieves a list of global administrators.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of global administrator logins.
	 */
	function get_global_administrators() {
		if ( ! is_multinetwork() ) {
			return get_super_admins();
		}

		return get_global_option( 'global_administrators', array() );
	}

endif;

if ( ! function_exists( 'is_global_administrator' ) ) :

	/**
	 * Determines if a user is a global administrator.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id Optional. ID of a user. Default is the current user.
	 * @return bool True if the user is a global administrator, false otherwise.
	 */
	function is_global_administrator( $user_id = false ) {
		$user_id = (int) $user_id;

		if ( ! is_multinetwork() ) {
			return is_super_admin( $user_id );
		}

		if ( ! $user_id || get_current_user_id() === $user_id ) {
			$user = wp_get_current_user();
		} else {
			$user = get_userdata( $user_id );
		}

		if ( ! $user || ! $user->exists() ) {
			return false;
		}

		$global_admins = get_global_administrators();

		return is_array( $global_admins ) && in_array( $user->user_login, $global_admins, true );
	}

endif;

/**
 * Gets the list of capabilities only global administrators should have.
 *
 * @since 1.0.0
 * @access private
 *
 * @return array List of global capabilities.
 */
function _ga_get_global_primitive_capabilities() {
	$global_capabilities = array(
		'manage_global',
		'manage_global_users',
		'manage_global_themes',
		'manage_global_plugins',
		'manage_global_options',
	);

	/**
	 * Filters the capabilities that only global administrators should have in a multinetwork.
	 *
	 * @since 1.0.0
	 *
	 * @param array Array of global capabilities.
	 */
	$global_capabilities = apply_filters( 'global_admin_capabilities', $global_capabilities );

	return $global_capabilities;
}

/**
 * Filters the meta map process to handle global capabilities and make related changes.
 *
 * @since 1.0.0
 *
 * @param array  $caps    Returns the user's actual capabilities.
 * @param string $cap     Capability name.
 * @param int    $user_id The user ID.
 * @param array  $args    Adds the context to the cap. Typically the object ID.
 * @return array The mapped capabilities.
 */
function _ga_map_meta_cap( $caps, $cap, $user_id, $args ) {
	if ( ! is_multinetwork() ) {
		return $caps;
	}

	$global_capabilities = _ga_get_global_primitive_capabilities();

	switch ( $cap ) {
		case 'list_networks':
		case 'create_networks':
		case 'delete_networks':
			$caps = array( 'manage_networks' );
			break;
		case 'create_signups':
		case 'edit_signups':
		case 'activate_signup':
		case 'delete_signup':
		case 'edit_signup':
		case 'resend_signup':
			$caps = array( 'manage_signups' );
			break;
		case 'edit_user':
			if ( ! current_user_can( 'manage_global_users' ) && isset( $args[0] ) ) {
				$user = get_userdata( $args[0] );
				if ( $user->has_cap( 'manage_global_users' ) ) {
					$caps[] = 'do_not_allow';
				}
			}
			break;
	}

	// Hack: Add do_not_allow for mapped global capabilities so that super admins don't have it.
	if ( ! in_array( 'do_not_allow', $caps, true ) ) {
		foreach ( $caps as $mapped_cap ) {
			if ( in_array( $mapped_cap, $global_capabilities, true ) && ! is_global_administrator( $user_id ) ) {
				$caps[] = 'do_not_allow';
				break;
			}
		}
	}

	return $caps;
}
add_filter( 'map_meta_cap', '_ga_map_meta_cap', 10, 4 );
