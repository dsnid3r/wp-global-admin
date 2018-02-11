<?php
/**
 * Multisite users administration panel.
 *
 * @package WPGlobalAdmin
 * @since 1.0.0
 */

/** Load WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

if ( ! is_multinetwork() ) {
	wp_die( __( 'Multinetwork support is not enabled.', 'wp-global-admin' ) );
}

if ( ! current_user_can( 'manage_global_users' ) ) {
	wp_die( __( 'You do not have permission to access this page.' ), 403 );
}

if ( isset( $_GET['action'] ) ) {
	//TODO: process user update and redirect back
}

require_once ABSPATH . 'wp-admin/includes/class-wp-ms-users-list-table.php';
require_once dirname( dirname( __FILE__ ) ) . '/includes/class-wp-ga-users-list-table.php';

$wp_list_table = new WP_GA_Users_List_Table( array( 'screen' => get_current_screen() ) );
$pagenum = $wp_list_table->get_pagenum();
$wp_list_table->prepare_items();
$total_pages = $wp_list_table->get_pagination_arg( 'total_pages' );

if ( $pagenum > $total_pages && $total_pages > 0 ) {
	wp_redirect( add_query_arg( 'paged', $total_pages ) );
	exit;
}
$title = __( 'Users' );
$parent_file = 'users.php';

add_screen_option( 'per_page' );

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __( 'Overview', 'wp-global-admin' ),
	'content' =>
		'<p>' . __( 'This table shows all users across all networks and all sites.', 'wp-global-admin' ) . '</p>' .
		'<p>' . __( 'Hover over any user on the list to make the edit links appear. The Edit link on the left will take you to their Edit User profile page; the Edit link on the right by any site name goes to an Edit Site screen for that site.', 'wp-global-admin' ) . '</p>' .
		'<p>' . __( 'You can also go to the user&#8217;s profile page by clicking on the individual username.', 'wp-global-admin' ) . '</p>' .
		'<p>' . __( 'You can sort the table by clicking on any of the table headings and switch between list and excerpt views by using the icons above the users list.', 'wp-global-admin' ) . '</p>' .
		'<p>' . __( 'The bulk action will permanently delete selected users, or mark/unmark those selected as spam. Spam users will have posts removed and will be unable to sign up again with the same email addresses.', 'wp-global-admin' ) . '</p>' .
		'<p>' . __( 'You can make an existing user an additional global admin by going to the Edit User profile page and checking the box to grant that privilege.', 'wp-global-admin' ) . '</p>'
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:', 'wp-global-admin' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://github.com/felixarntz/wp-global-admin/wiki/Global-Admin-Users-Screen" target="_blank">Documentation on Network Users</a>', 'wp-global-admin' ) . '</p>'
);

get_current_screen()->set_screen_reader_content( array(
	'heading_views'      => __( 'Filter users list' ),
	'heading_pagination' => __( 'Users list navigation' ),
	'heading_list'       => __( 'Users list' ),
) );

require_once( ABSPATH . 'wp-admin/admin-header.php' );

if ( isset( $_REQUEST['updated'] ) && $_REQUEST['updated'] == 'true' && ! empty( $_REQUEST['action'] ) ) {
	?>
	<div id="message" class="updated notice is-dismissible"><p>
		<?php
		switch ( $_REQUEST['action'] ) {
			case 'delete':
				_e( 'User deleted.' );
			break;
			case 'all_spam':
				_e( 'Users marked as spam.' );
			break;
			case 'all_notspam':
				_e( 'Users removed from spam.' );
			break;
			case 'all_delete':
				_e( 'Users deleted.' );
			break;
			case 'add':
				_e( 'User added.' );
			break;
		}
		?>
	</p></div>
	<?php
}
	?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Users' ); ?></h1>

	<?php if ( current_user_can( 'create_users' ) ) : ?>
		<a href="<?php echo global_admin_url( 'user-new.php' ); ?>" class="page-title-action"><?php echo esc_html_x( 'Add New', 'user' ); ?></a>
	<?php endif; ?>

	<?php
	if ( strlen( $usersearch ) ) {
		/* translators: %s: search keywords */
		printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', esc_html( $usersearch ) );
	}
	?>

	<hr class="wp-header-end">

	<?php $wp_list_table->views(); ?>

	<form method="get" class="search-form">
		<?php $wp_list_table->search_box( __( 'Search Users' ), 'all-user' ); ?>
	</form>

	<form id="form-user-list" action="users.php?action=allusers" method="post">
		<?php $wp_list_table->display(); ?>
	</form>
</div>

<?php require_once( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
