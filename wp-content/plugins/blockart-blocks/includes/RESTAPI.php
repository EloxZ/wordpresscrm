<?php
/**
 * Register REST routes.
 *
 * @since 1.0.0
 * @package BlockArt
 */

namespace BlockArt;

use Exception;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

/**
 * REST_API class.
 *
 * @since 1.0.0
 */
final class RESTAPI {

	/**
	 * The namespace of this controller's route.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected static $namespace = 'blockart/v1';

	/**
	 * REST API endpoint.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected static $api_endpoint = 'https://wpblockart.com/wp-json/blockart-library/v1';

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {

		blockart_doing_it_wrong( __FUNCTION__, __( 'You cannot clone this class.', 'blockart' ), '1.0.0' );
	}

	/**
	 * Init.
	 */
	public static function init() {
		self::init_hooks();
	}

	/**
	 * REST_API constructor.
	 *
	 * @since 1.0.0
	 */
	private static function init_hooks() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Register REST routes.
	 *
	 * @since 1.0.0
	 */
	public static function register_routes() {

		register_rest_route(
			self::$namespace,
			'/save_block_css/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( __CLASS__, 'save_block_css' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/get_block_content/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( __CLASS__, 'get_block_content' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/save_reusable_block_css/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( __CLASS__, 'append_block_css' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/get_section/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => function( $request ) {
					return self::get_designs( $request );
				},
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/get_single_section/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => function( $request ) {
					return self::get_content( $request );
				},
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/get_template/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => function( $request ) {
					return self::get_designs( $request, 'templates' );
				},
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/get_single_template/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => function( $request ) {
					return self::get_content( $request, 'single-template' );
				},
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);

		register_rest_route(
			self::$namespace,
			'/get_widget_block/',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( __CLASS__, 'get_widget_block' ),
				'permission_callback' => array( __CLASS__, 'permission_check' ),
			)
		);
	}

	/**
	 * Create files.
	 *
	 * @since 1.0.0
	 * @param string $filename Filename.
	 * @param string $content Content.
	 * @throws Exception Exception.
	 * @return void|WP_REST_Response
	 */
	public static function create_files( $filename = '', $content = '' ) {

		try {
			global $wp_filesystem;

			$upload_dir_url = wp_upload_dir();
			$dir            = trailingslashit( $upload_dir_url['basedir'] ) . 'blockart/';

			if ( ! $wp_filesystem ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			WP_Filesystem( false, $upload_dir_url['basedir'], true );

			if ( ! $wp_filesystem->is_dir( $dir ) ) {
				$wp_filesystem->mkdir( $dir );
			}

			if ( ! $wp_filesystem->put_contents( $dir . $filename, $content ) ) {
				throw new Exception( __( 'Permission failed to save file!', 'blockart' ) );
			}
		} catch ( Exception $e ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $e->getMessage(),
				)
			);
		}
	}

	/**
	 * Save block CSS in post meta and on a file.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Data about the request.
	 * @return WP_REST_Response
	 * @throws Exception Exception.
	 */
	public static function save_block_css( $request ) {

		$params         = $request->get_params();
		$post_id        = (int) sanitize_text_field( $params['postId'] );
		$filename       = "blockart-css-{$post_id}.css";
		$upload_dir_url = wp_upload_dir();
		$dir            = trailingslashit( $upload_dir_url['basedir'] ) . 'blockart/';

		if ( $params['has_block'] ) {
			self::create_files( $filename, $params['blockCss'] );
			update_post_meta( $post_id, '_blockart_active', 'yes' );
			update_post_meta( $post_id, '_blockart_css', $params['blockCss'] );
		} else {
			delete_post_meta( $post_id, '_blockart_active' );

			if ( file_exists( $dir . $filename ) ) {
				unlink( $dir . $filename );
			}

			delete_post_meta( $post_id, '_blockart_css' );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Data retrieved successfully.', 'blockart' ),
			)
		);
	}

	/**
	 * Get post content.
	 *
	 * To handle the reusable blocks as WordPress treats reusable block as a separate post type.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Data about the request.
	 * @return WP_REST_Response
	 */
	public static function get_block_content( $request ) {

		$post = $request->get_params();

		if ( isset( $post['postId'] ) ) {
			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => get_post( $post['postId'] )->post_content,
					'message' => __( 'Data retrieved successfully.', 'blockart' ),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => __( 'Failed to retrieve data!', 'blockart' ),
			)
		);
	}

	/**
	 * Append/Create css of the reusable blocks.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Data about the request.
	 * @throws Exception Exception.
	 * @return WP_REST_Response
	 */
	public static function append_block_css( $request ) {

		$post    = $request->get_params();
		$css     = $post['css'];
		$post_id = (int) sanitize_text_field( $post['postId'] );

		if ( $post_id ) {
			global $wp_filesystem;

			if ( ! $wp_filesystem ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			$filename = "blockart-css-{$post_id}.css";
			$dir      = trailingslashit( wp_upload_dir()['basedir'] ) . 'blockart/';

			if ( file_exists( $dir . $filename ) ) {

				$get_data = get_post_meta( $post_id, '_blockart_css', true );

				if ( false === strpos( $get_data, $css ) ) {
					// phpcs:disable
					$file = fopen( $dir . $filename, 'a' );
					fwrite( $file, $css );
					fclose( $file );
					// phpcs:enable

					update_post_meta( $post_id, '_blockart_css', $get_data . $css );
				}
			} else {
				self::create_files( $filename, $css );
				update_post_meta( $post_id, '_blockart_active', 'yes' );
				update_post_meta( $post_id, '_blockart_css', $css );
			}

			return new WP_REST_Response(
				array(
					'success' => true,
					'message' => __( 'Data retrieved successfully.', 'blockart' ),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => __( 'Failed to retrieve data!', 'blockart' ),
			)
		);
	}

	/**
	 * Fetch pre-made designs using self-hosted api endpoint.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Data about the request.
	 * @param string $type Type of data to fetch.
	 * @return WP_REST_Response
	 */
	private static function get_designs( $request, $type = 'sections' ) {

		$param = $request->get_params();

		$remote_data = wp_remote_post(
			self::$api_endpoint . "/{$type}",
			array(
				'method'  => 'POST',
				'timeout' => 120,
				'body'    => array(
					'starter_pack' => isset( $param['starter_pack'] ) && (bool) $param['starter_pack'],
				),
			)
		);

		if ( is_wp_error( $remote_data ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Failed to retrieve data!', 'blockart' ),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Data retrieved successfully.', 'blockart' ),
				'data'    => $remote_data['body'],
			)
		);
	}

	/**
	 * Fetch content using self-hosted api endpoint.
	 *
	 * @since 1.0.0
	 * @param WP_REST_Request $request Data about the request.
	 * @param string $type Type of data to fetch.
	 * @return WP_REST_Response
	 */
	private static function get_content( $request, $type = 'single-section' ) {

		$params      = $request->get_params();
		$remote_data = wp_remote_post(
			self::$api_endpoint . "/{$type}",
			array(
				'method'  => 'POST',
				'timeout' => 120,
				'body'    => array(
					'id' => $params['id'],
				),
			)
		);

		if ( is_wp_error( $remote_data ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => __( 'Failed to retrieve data!', 'blockart' ),
				)
			);
		}

		$data              = json_decode( $remote_data['body'] );
		$processed_content = ImportHelper::process_content( $data->content );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Data retrieved successfully.', 'blockart' ),
				'data'    => $processed_content,
			)
		);
	}

	/**
	 * Get widget block.
	 *
	 * @return WP_REST_Response
	 */
	public static function get_widget_block() {

		$widget_block = '';

		foreach ( get_option( 'widget_block' ) as $block_content ) {
			if ( isset( $block_content['content'] ) ) {
				$widget_block .= $block_content['content'];
			}
		}

		if ( ! empty( $widget_block ) ) {
			return new WP_REST_Response(
				array(
					'success' => true,
					'data'    => $widget_block,
					'message' => __( 'Data retrieved successfully.', 'blockart' ),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'success' => false,
				'message' => __( 'Failed to retrieve data!', 'blockart' ),
			)
		);
	}

	/**
	 * Permission check callback.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public static function permission_check() {
		return current_user_can( 'edit_posts' );
	}
}
