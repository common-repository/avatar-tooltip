<?php if (!defined ('ABSPATH')) die ('No direct access allowed');

/**
 * Ajax calls
 *
 * Contains the hooks that manages ajax calls, e.g. via 'wp_ajax_'
 *
 * @package Avatar Tooltip
 * @since 1.0
 */



/**
 * Ajax request for user tooltip
 *
 * The function contains a lot of filter hooks to customise the content
 * of tooltip.
 * The function is pluggable, so you can rewrite at all: write your
 * 'axe_at_tooltip_content' function in a php file inside /mu-plugins folder.
 */
if ( ! function_exists('axe_at_tooltip_content') ) :

function axe_at_tooltip_content () {
	global $wp_version;
	check_ajax_referer( 'axe_at_content' );

	$axe_at_options = axe_at_get_options();
	if ( $axe_at_options['only_logged'] == 'yes' && ! is_user_logged_in() ) {
		header( "Content-Type: application/json" );
		$response =  json_encode( array( 'ttContent' => esc_js( __('error', AXE_AT_PLUGIN_DIR) ) ) );	
		die( $response );
	}
	
	$posted_user_id = $posted_md5email = false;
	$data = array();

	// Prepare user object
	$user = false;

	// Gravatar profile 
	$grav_name = '';
	$grav_profile = false;

	// Defaults
	$data['ttTitle'] = __('More info', AXE_AT_PLUGIN_DIR);
	$data['ttContent'] = __('No more info available', AXE_AT_PLUGIN_DIR);
		
	if ( isset( $_POST['uid'] ) ) {
		$posted = base64_decode( urldecode( $_POST['uid'] ) );
		if ( strpos( $posted, '|' ) !== false ) {

			// The posted user data 
			list( $posted_md5email, $posted_user_id ) = explode ( '|', $posted );

			// Check if passed md5 is equal to md5 of email of passed user ID
			$user = get_userdata( $posted_user_id );
			
			if ( empty( $user->user_email ) || md5( strtolower( trim( $user->user_email ) ) ) != $posted_md5email ) {
				// Ok, the email belongs to an existing user
				// otherwise we don't authorise info about user
				$user = false;
			}
		}
		
	} 	
	
	if ( $posted_md5email ) {


		/**
		 * User details (from blog)
		 * If an user exists
		 */
		 
		$content_user = '';
		if ( is_object($user) ) {

			// The user content
			$content_user = '<div class="container-section container-userinfo">';
			$content_user .= '<div class="content-title content-title-userinfo">'. sprintf( __('Info from %s', AXE_AT_PLUGIN_DIR), get_bloginfo('blogname') )  .'</div>';
			$content_user .= '<ul class="userinfo">';

			// User post archive
			if ( $user->ID ) {
				$content_user .= '<li><a href="'. esc_url( get_author_posts_url( $user->ID ) ) .'" title="'. esc_attr( sprintf( __('User posts on %s', AXE_AT_PLUGIN_DIR), get_bloginfo('blogname') ) ) .'">'. esc_html( __('Recent Posts') ) .'</a></li>';
			}
			
			// Primary blog (only on multisite)
			if ( is_multisite() && $user->ID && !empty($user->primary_blog) ) {
				$primary = (int)$user->primary_blog;
				if ( $primary > 1 ) {
					$primary_url = get_blogaddress_by_id($user->primary_blog);
					switch_to_blog( $primary );
					$content_user .= '<li><a href="'. esc_url( $primary_url ) .'" title="'. esc_attr( get_bloginfo('blogname') ).'">'. esc_html( __('User blog', AXE_AT_PLUGIN_DIR) ) .'</a></li>';
					restore_current_blog();
				}
			}

			// User website
			if ( $user->ID && !empty( $user->user_url ) ) {
				$content_user .= '<li><a href="'. esc_url($user->user_url) .'" target="_blank" title="'. esc_url($user->user_url) .'">'. esc_html(__('Website')) .'</a></li>';
			}

			$content_user .= '</ul>';
			$content_user .= '</div>'; // .container-userinfo

			// You can filter the content about user 
			$content_user = apply_filters( 'axe_avatar_tooltip_content_user', $content_user, $user ); // Hook
		}

		/**
		 * Gravatar profile
		 */
		$content_grav = '';
		$response = @file_get_contents('http://www.gravatar.com/' . $posted_md5email . '.php');
		$resp_body = !empty($response) ? unserialize( $response ) : false;
		//$content_grav .= '<pre>' . print_r( $response, true ) . '</pre>';
		
		if ( is_array( $resp_body ) && isset( $resp_body['entry'] ) ) {

			if ( isset($resp_body['entry'][0]) ) {
				//$content_grav .= '<pre>' . print_r( $resp_body['entry'][0], true ) . '</pre>';
				
				// Extract profile data!
				$grav_profile = $resp_body['entry'][0];

				//$content_grav .= '<pre>' . print_r( $grav_profile, true ) . '</pre>';
				
				$content_grav = '<div class="container-section container-gravatar">';
				$content_grav .= '<div class="content-title content-title-gravatar">'. __('More info', AXE_AT_PLUGIN_DIR)  .'</div>';


				// Link to profile
				if ( isset( $grav_profile['profileUrl'] ) ) {
					$link_grav_profile = '<a href="'. esc_url($grav_profile['profileUrl']) .'" title="'. esc_attr( __('view complete profile on Gravatar', AXE_AT_PLUGIN_DIR) ) .'" target="_blank" rel="nofollow">%s</a>';
				} else {
					$link_grav_profile = '%s';
				}

				// Thumb
				$content_grav .= sprintf( $link_grav_profile, '<img src="'. esc_url( $grav_profile['thumbnailUrl'] ) .'?s=80" class="gravatar-thumb" />');


				// Name, Location
				$content_grav .= '<ul class="gravatar-info">';
				$grav_name = ( !empty($grav_profile['displayName']) ) ? $grav_profile['displayName'] : $grav_profile['preferredUsername'];
				$content_grav .= '<li class="gravatar-info-user">'. sprintf( $link_grav_profile, $grav_name );
				if ( !empty($grav_profile['currentLocation']) ) {
					$content_grav .= ' <span class="gravatar-info-location"><small>/</small> '. $grav_profile['currentLocation'] .'</span>';
				}
				$content_grav .= '</li>';

				// Default tooltip title from gravatar name
				$data['ttTitle'] = $grav_name;
				
				if ( !empty($grav_profile['aboutMe']) ) {
					if ( version_compare ( $wp_version, '3.3', '>=' ) ) {
						$grav_aboutme = wp_trim_words( $grav_profile['aboutMe'], 25, ' <small>[...]</small>' );
					} else {
						$grav_aboutme = substr( $grav_profile['aboutMe'], 0, 120 ).'<small>[...]</small>';
					}
					$content_grav .= '<li class="gravatar-info-about">'. $grav_aboutme .'</li>';
				}					


				// List of urls
				if ( !empty($grav_profile['urls']) && is_array($grav_profile['urls']) ) {
					$content_grav_urls = '<ul class="gravatar-info-urls">';

					$grav_profile['urls'] = apply_filters( 'axe_avatar_tooltip_gravatar_urls', $grav_profile['urls'], $posted_md5email, $grav_profile ); // Hook
					
					foreach ( $grav_profile['urls'] as $k => $url ) {
						$content_grav_urls .= '<li><a href="'. $url['value'] .'" class="url" title="'. esc_attr($url['title']) .'" target="_blank" rel="nofollow">&nbsp;</a>';
						$content_grav_urls .= '</li>';
					}
					$content_grav_urls .= '</ul>';

					$content_grav_urls = apply_filters( 'axe_avatar_tooltip_content_gravatar_urls', $content_grav_urls, $posted_md5email, $grav_profile ); // Hook

					// Add to gravatar content
					$content_grav .= $content_grav_urls;
				}

				
				// List of Accounts
				if ( empty($grav_profile['accounts']) ) $grav_profile['accounts'] = array();
				
				// Add gravatar link
				if ( isset( $grav_profile['profileUrl'] ) ) {
					$account_grav = array(
	                    'domain' 	=> 'gravatar.com',
						'display' 	=> $grav_name,
						'url' 		=> $grav_profile['profileUrl'],
						'username' 	=> $grav_name,
						'verified' 	=> true,
						'shortname'	=> 'gravatar'
					);
					array_unshift( $grav_profile['accounts'], $account_grav );
				}
				
				if ( !empty($grav_profile['accounts']) && is_array($grav_profile['accounts']) ) {
					$content_grav_accounts = '<ul class="gravatar-info-accounts gravatar-info-services">';

					$grav_profile['accounts'] = apply_filters( 'axe_avatar_tooltip_gravatar_accounts', $grav_profile['accounts'], $posted_md5email, $grav_profile ); // Hook
					
					foreach ( $grav_profile['accounts'] as $k => $account ) {
						if ( $account['verified'] ) {
							$content_grav_accounts .= '<li><a href="'. $account['url'] .'" class="img accounts_'.$account['shortname'].'" title="'. esc_attr($account['shortname']) .'" target="_blank" rel="nofollow">&nbsp;</a>';
							//$content_grav_accounts .= $account['shortname'] . $account['url'] .
							$content_grav_accounts .= '</li>';
						}
					}
					$content_grav_accounts .= '</ul>';

					$content_grav_accounts = apply_filters( 'axe_avatar_tooltip_content_gravatar_accounts', $content_grav_accounts, $posted_md5email, $grav_profile ); // Hook

					// Add to gravatar content
					$content_grav .= $content_grav_accounts;
				}
							
				
				$content_grav .= '</ul>'; // .gravatar-info

				$content_grav .= '</div>'; // .container-gravatar
				
				// You can filter the content about gravatar
				$content_grav = apply_filters( 'axe_avatar_tooltip_content_gravatar', $content_grav, $posted_md5email, $grav_profile ); // Hook
			}
		}
		
		
		// Add custom text at beginning
		$content_before = apply_filters( 'axe_avatar_tooltip_content_before', '', $user, $posted_md5email, $grav_name, $grav_profile ); // Hook
				
		// Add custom text at bottom
		$content_after = apply_filters( 'axe_avatar_tooltip_content_after', '', $user, $posted_md5email, $grav_name, $grav_profile ); // Hook

		// Here the complete content!
		$tooltip_content = trim( $content_before . $content_user . $content_grav . $content_after );

		// The last opportunity to filter content
		$tooltip_content = $title = apply_filters( 'axe_avatar_tooltip_content', $tooltip_content, $user, $posted_md5email, $grav_name, $grav_profile ); // Hook

		// Override the default content only if not empty
		if ( $tooltip_content != "" ) $data['ttContent'] = $tooltip_content;
				

		/**
		 * Tooltip title, if not filled with gravatar name maybe use user name
		 */
		if ( is_object($user) ) {
			$title = esc_js( $user->display_name );
			$data['ttTitle'] = $title;
		}
		// You can filter the tooltip title
		$data['ttTitle'] = $title = apply_filters( 'axe_avatar_tooltip_title', $data['ttTitle'], $user, $posted_md5email, $grav_name ); // Hook
		
	} else {
		
		$data['ttTitle'] = esc_js( __('error', AXE_AT_PLUGIN_DIR) );
		$data['ttContent'] = esc_js( __('error', AXE_AT_PLUGIN_DIR) );
	}
	
	// response output
	header( "Content-Type: application/json" );
	$response =  json_encode( $data );	
	die( $response );
}

endif;

add_action('wp_ajax_nopriv_axe_at_get_tooltip_content', 'axe_at_tooltip_content');
add_action('wp_ajax_axe_at_get_tooltip_content', 'axe_at_tooltip_content');


/* EOF */
