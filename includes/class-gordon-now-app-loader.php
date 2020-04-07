<?php

use \Firebase\JWT\JWT;

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://gfs.com
 * @since      1.0.0
 *
 * @package    Gordon_Now_App
 * @subpackage Gordon_Now_App/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Gordon_Now_App
 * @subpackage Gordon_Now_App/includes
 * @author     Chuck Zimmerman <chuck.zimmerman@gfs.com>
 */
class Gordon_Now_App_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		/**
		 * Provide GNA Auth Cookies
		 */

		function authenticate_user_generate_tokens( WP_REST_Request $request ) {
			$the_user = wp_authenticate($request['username'], $request['password']);
			if( is_wp_error( $the_user ) ) {
				// TODO: Replace with a better 401 response
				// return new WP_REST_Response(null, 401);
				return $the_user;
			} else {
				$user_id = $the_user->{'ID'};
				$user_email = $the_user->{'user_email'};
				// TODO: Short duration cookie for example only
				$expiration = time() + 580;
				// TODO: Change scheme to secure auth (https) later
				// This is the auth cookie used in wp-admin / wp-content that shows what you have access to. I think.
				$auth_cookie = wp_generate_auth_cookie( $user_id, $expiration, 'auth', $token = '' );
				// This is the front-end cookie that tells wordpress you have a valid logged in user session
				$logged_in_cookie = wp_generate_auth_cookie( $user_id, $expiration, 'logged_in', $token = '' );
				// The cookie names have hashed identifiers using WP constants
				$wordpress_cookie_exp = 'wordpress_cookie_expiration_unix';
				$wordpress_cookie = 'wordpress_' . COOKIEHASH;
				$wordpress_logged_in_cookie = 'wordpress_logged_in_' . COOKIEHASH;
				// Call for JWT using user email
				$generated_jwt = 'jwt';
				$jwt = generate_gna_jwt($user_email);
				// Populating the object we return with the cookies and their cookie names
				$wp_auth_object = new stdClass();
				$wp_auth_object->$generated_jwt = $jwt;
				$wp_auth_object->$wordpress_cookie_exp = $expiration;
				$wp_auth_object->$wordpress_cookie = $auth_cookie;
				$wp_auth_object->$wordpress_logged_in_cookie = $logged_in_cookie;
				return $wp_auth_object;
			}
		}
	
		add_action( 'rest_api_init', function () {
			register_rest_route( 'gordon-now-app/v1', '/authenticate-user', array(
			  'methods' => 'POST',
			  'callback' => 'authenticate_user_generate_tokens',
			) );
		} );

		/**
		 * Endpoint to provide JWT
		 */

		function generate_gna_jwt( $user_email ) {
			if (defined('GNA_PRIVATE_KEY') && defined('GNA_AUTH_AUD')) {
				$key = GNA_PRIVATE_KEY;
				$publicKey = GNA_PUBLIC_KEY;
				$aud = GNA_AUTH_AUD;
				$payload = array(
					"iss" => site_url(),
					"aud" => $aud,
					"iat" => time(),
					"nbf" => time(),
					// TODO: Set expiration time for JWT
					"exp" => time() + 580,
					"sub" => $user_email
				);
	
				/**
				 * IMPORTANT:
				 * You must specify supported algorithms for your application. See
				 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
				 * for a list of spec-compliant algorithms.
				 */
				$jwt = JWT::encode($payload, $key, 'RS256');
				$decoded = JWT::decode($jwt, $publicKey, array('RS256'));
				return $jwt;
			} else {
				wp_die('The Gordon Now App Integration plugin requires GNA_PRIVATE_KEY & GNA_AUTH_AUD');
				return;
			}
		}

		// Validate user is still logged in via cookies

		function validate_cookies( WP_REST_Request $request ) {
			return(wp_validate_auth_cookie($request['auth_cookie'], 'auth') && wp_validate_auth_cookie($request['logged_in_cookie'], 'logged_in'));
		}

		add_action( 'rest_api_init', function () {
			register_rest_route( 'gordon-now-app/v1', '/validate-cookies', array(
			  'methods' => 'POST',
			  'callback' => 'validate_cookies',
			) );
		} );

	}

}
