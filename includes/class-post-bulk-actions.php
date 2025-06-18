<?php
/**
 * Post Bulk Actions Class
 *
 * @package Post_Bulk_Actions
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
class Post_Bulk_Actions {
	/**
	 * Initialize the plugin.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'init', array( $this, 'register_bulk_actions_for_post_types' ) );
		add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
	}

	/**
	 * Load plugin textdomain for translations
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'post-bulk-actions', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Enable options for all post types
	 */
	public function register_bulk_actions_for_post_types() {
		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach ( $post_types as $post_type ) {
			add_filter( "bulk_actions-edit-{$post_type}", array( $this, 'add_bulk_actions_options' ) );
			add_filter( "handle_bulk_actions-edit-{$post_type}", array( $this, 'handle_bulk_actions' ), 10, 3 );
		}
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'edit.php' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			'pba-admin-js',
			PBA_PLUGIN_URL . 'assets/admin.js',
			array( 'jquery' ),
			PBA_VERSION,
			true
		);

		// Pass data to JavaScript.
		wp_localize_script(
			'pba-admin-js',
			'pba_ajax',
			array(
				'admin_url' => admin_url(),
				'edit_url'  => admin_url( 'post.php?post=%d&action=edit' ),
				'nonce'     => wp_create_nonce( 'pba_bulk_action_nonce' ),
			)
		);
	}

	/**
	 * Add custom bulk action options.
	 *
	 * @param array $bulk_actions Existing bulk actions.
	 * @return array Modified bulk actions.
	 */
	public function add_bulk_actions_options( $bulk_actions ) {
		$bulk_actions['bulk-edit-tabs'] = __( 'Open in Edit Tabs', 'post-bulk-actions' );
		$bulk_actions['bulk-view-tabs'] = __( 'Open in View Tabs', 'post-bulk-actions' );
		return $bulk_actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * @param string $sendback Redirect URL.
	 * @param string $doaction Action being performed.
	 * @param array  $post_ids Array of post IDs.
	 * @return string Modified redirect URL.
	 */
	public function handle_bulk_actions( $sendback, $doaction, $post_ids ) {
		// Security check.
		$nonce = isset( $_REQUEST['_wpnonce'] ) ? wp_unslash( $_REQUEST['_wpnonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'bulk-posts' ) ) {
			return $sendback;
		}

		if ( 'bulk-edit-tabs' === $doaction ) {
			if ( ! current_user_can( 'edit_posts' ) ) {
				$sendback = add_query_arg( 'pba_error', 'no_permission', $sendback );
				return $sendback;
			}

			$valid_post_ids = array();
			foreach ( $post_ids as $post_id ) {
				if ( current_user_can( 'edit_post', $post_id ) ) {
					$valid_post_ids[] = intval( $post_id );
				}
			}

			if ( empty( $valid_post_ids ) ) {
				$sendback = add_query_arg( 'pba_error', 'no_valid_posts', $sendback );
				return $sendback;
			}

			$sendback = add_query_arg(
				array(
					'pba_action'    => $doaction,
					'pba_post_ids'  => implode( ',', $valid_post_ids ),
					'pba_processed' => count( $valid_post_ids ),
				),
				$sendback
			);
		}

		if ( 'bulk-view-tabs' === $doaction ) {
			$post_urls = array();
			foreach ( $post_ids as $post_id ) {
				$post_id = intval( $post_id );
				if ( 'publish' === get_post_status( $post_id ) ) {
					$permalink = get_permalink( $post_id );
					if ( $permalink ) {
						$post_urls[] = esc_url( $permalink );
					}
				}
			}

			if ( empty( $post_urls ) ) {
				$sendback = add_query_arg( 'pba_error', 'no_valid_urls', $sendback );
				return $sendback;
			}

			$sendback = add_query_arg(
				array(
					'pba_action'    => $doaction,
					'pba_post_urls' => implode( ',', $post_urls ),
					'pba_processed' => count( $post_urls ),
				),
				$sendback
			);
		}

		return $sendback;
	}

	/**
	 * Show admin notices
	 */
	public function show_admin_notices() {
		if ( ! isset( $_GET['pba_error'] ) ) {
			return;
		}

		$error = sanitize_text_field( wp_unslash( isset( $_GET['pba_error'] ) ) );
		$message = '';

		switch ( $error ) {
			case 'no_permission':
				$message = __( 'You do not have permission to edit posts.', 'post-bulk-actions' );
				break;
			case 'no_valid_posts':
				$message = __( 'No valid posts found to process.', 'post-bulk-actions' );
				break;
			case 'no_valid_urls':
				$message = __( 'No valid URLs found to process.', 'post-bulk-actions' );
				break;
		}

		if ( $message ) {
			printf(
				'<div class="notice notice-error is-dismissible"><p>%s</p></div>',
				esc_html( $message )
			);
		}
	}
}
