<?php
define('TWENTYELEVEN_XILI_VER', '1.8.0'); // as style.css
/**
 * twentyeleven for xili functions - + exemple de deux menus ajoutés
 * 120117 - 1.0.2 - compatible with options multiple locations (pro version)
 * 120227 - 1.1 - fixes issues with options of parent-theme page
 * 120416 - 1.2 - fixes WP 3.3 and WP 3.4 compat
 * 130504 - 2.0 - based on multilingual class since 2.8.8+
 * 140212 - 2.1 - updated for permalinks class since XL 2.10 and TwentyEleven v. 1.7
 *
 * 140510 - 1.8.0 - new versioning to follow parent version 1.8 (wp 3.9.1) - and XL version 2.12+
 */
function twentyeleven_xilidev_setup () {

	$theme_domain = 'twentyeleven';

	$minimum_xl_version = '2.11.9';

	load_theme_textdomain( $theme_domain, get_stylesheet_directory() . '/langs' ); // now use .mo of child

	$xl_required_version = false;

	if ( class_exists('xili_language') ) { // if temporary disabled

		$xl_required_version = version_compare ( XILILANGUAGE_VER, $minimum_xl_version, '>' );

		global $xili_language;

		$xili_language_includes_folder = $xili_language->plugin_path .'xili-includes';

		$xili_functionsfolder = get_stylesheet_directory() . '/functions-xili' ;

		if ( file_exists( $xili_functionsfolder . '/multilingual-classes.php') ) {
			require_once ( $xili_functionsfolder . '/multilingual-classes.php' ); // xili-options

		} elseif ( file_exists( $xili_language_includes_folder . '/theme-multilingual-classes.php') ) {

			require_once ( $xili_language_includes_folder . '/theme-multilingual-classes.php' ); // ref xili-options based in plugin
		}

		if ( file_exists( $xili_functionsfolder . '/multilingual-functions.php') ) {
			require_once ( $xili_functionsfolder . '/multilingual-functions.php' );
		}



		global $xili_language_theme_options ; // used on both side

		$xili_args = array (
			'customize_clone_widget_containers' => true, // comment or set to true to clone widget containers
			'settings_name' => 'xili_twentyeleven_theme_options', // name of array saved in options table
			'theme_name' => 'Twenty Eleven',
			'theme_domain' => $theme_domain,
			'child_version' => TWENTYELEVEN_XILI_VER
		);

		if ( is_admin() ) {

		// Admin args dedicaced to this theme

			$xili_admin_args = array_merge ( $xili_args, array (
				'customize_adds' => true, // add settings in customize page
				'customize_addmenu' => false, // done by 2013
				'capability' => 'edit_theme_options',
				'authoring_options_admin' => false
			) );
			if ( class_exists ( 'xili_language_theme_options_admin' ) ) {
				$xili_language_theme_options = new xili_language_theme_options_admin ( $xili_admin_args );
				$class_ok = true ;
			} else {
				$class_ok = false ;
			}


		} else { // visitors side - frontend

			if ( class_exists ( 'xili_language_theme_options' ) ) {
				$xili_language_theme_options = new xili_language_theme_options ( $xili_args );
				$class_ok = true ;
			} else {
				$class_ok = false ;
			}
		}

		// new ways to add parameters in authoring propagation
		add_theme_support('xiliml-authoring-rules', array (
			'post_content' => array('default' => '1',
				'data' => 'post',
				'hidden' => '',
				'name' => 'Post Content',
				/* translators: added in child functions by xili */
				'description' => __('Will copy content in the future translated post', 'twentyeleven')
		),
			'post_parent' => array('default' => '1',
				'data' => 'post',
				'name' => 'Post Parent',
				'hidden' => '1',
				/* translators: added in child functions by xili */
				'description' => __('Will copy translated parent id (if original has parent and translated parent)!', 'twentyeleven')
		))
		); //

		$xili_theme_options = get_theme_xili_options() ;
		// to collect checked value in xili-options of theme
		if ( file_exists( $xili_functionsfolder . '/multilingual-permalinks.php') && $xili_language->is_permalink && isset ( $xili_theme_options['perma_ok'] ) && $xili_theme_options['perma_ok'] ) {
			require_once ( $xili_functionsfolder . '/multilingual-permalinks.php' ); // require subscribing premium services
		}
	}

	// errors and installation informations

	if ( ! class_exists( 'xili_language' ) ) {

		$msg = '
		<div class="error">'.
			/* translators: added in child functions by xili */
			'<p>' . sprintf ( __('The %s child theme requires xili-language plugin installed and activated', 'twentyeleven' ), get_option( 'current_theme' ) ).'</p>
		</div>';

	} elseif ( $class_ok === false ) {

		$msg = '
		<div class="error">'.
			/* translators: added in child functions by xili */
			'<p>' . sprintf ( __('The %s child theme requires <em>xili_language_theme_options</em> class to set multilingual features.', 'twentyeleven' ), get_option( 'current_theme' ) ).'</p>
		</div>';

	} elseif ( $xl_required_version ) {

		$msg = '
		<div class="updated">'.
			/* translators: added in child functions by xili */
			'<p>' . sprintf ( __('The %s child theme was successfully activated with xili-language.', 'twentyeleven' ), get_option( 'current_theme' ) ).'</p>
		</div>';

	} else {

		$msg = '
		<div class="error">'.
			/* translators: added in child functions by xili */
			'<p>' . sprintf ( __('The %1$s child theme requires xili-language version %2$s+', 'twentyeleven' ), get_option( 'current_theme' ), $minimum_xl_version ).'</p>
		</div>';
	}
	// after activation and in themes list
	if ( isset( $_GET['activated'] ) || ( ! isset( $_GET['activated'] ) && ( ! $xl_required_version || ! $class_ok ) ) )
		add_action( 'admin_notices', $c = create_function( '', 'echo "' . addcslashes( $msg, '"' ) . '";' ) );

	// end errors...

}

/* actions and filters*/
add_action( 'after_setup_theme', 'twentyeleven_xilidev_setup', 11 );
add_action( 'wp_head', 'special_head' );

define('XILI_CATS_ALL','0');



/**
 * define when search form is completed by radio buttons to sub-select language when searching
 *
 */
function special_head() {

	if ( is_search() ) {
		add_filter('get_search_form', 'my_langs_in_search_form_2011', 10, 1); // in multilingual-functions.php
	}

	$xili_theme_options = get_theme_xili_options() ;

	if ( !isset( $xili_theme_options['no_flags'] ) || $xili_theme_options['no_flags'] != '1' ) {
		twentyeleven_flags_style();
	}
}

/**
 * condition to filter adjacent links
 * @since 1.1.4
 *
 */

function is_xili_adjacent_filterable() {

	if ( is_search () ) { // for multilingual search
		return false;
	}
	return true;
}

/**
 * dynamic style for flag depending current list
 *
 * @since 1.0.2 - add #access
 *
 */
function twentyeleven_flags_style () {

	if ( class_exists('xili_language') ) {
		global $xili_language ;
		$language_xili_settings = get_option('xili_language_settings');
		if ( !is_array( $language_xili_settings['langs_ids_array'] ) ) {
			$xili_language->get_lang_slug_ids(); // update array when no lang_perma 110830 thanks to Pierre
			update_option( 'xili_language_settings', $xili_language->xili_settings );
			$language_xili_settings = get_option('xili_language_settings');
		}

		$language_slugs_list = array_keys ( $language_xili_settings['langs_ids_array'] ) ;

		?>
		<style type="text/css">
		<?php

		$path = get_stylesheet_directory_uri();

		$ulmenus = array();
		foreach ( $language_slugs_list as $slug ) {
			echo "#access ul.menu li.lang-{$slug} a {background: transparent url('{$path}/images/flags/{$slug}.png') no-repeat center 16px; padding:0 !important;}\n";
			echo "#access li.lang-{$slug}:hover > a {background: #efefef url('{$path}/images/flags/{$slug}.png') no-repeat center 16px !important;}\n";
			$ulmenus[] = "#access ul.menu li.lang-{$slug}";
		}
			echo implode (', ', $ulmenus ) . " {text-indent:-9999px; width:24px; }\n";
		?>
		</style>
		<?php

	}
}

/**
 *
 *
 */
function single_lang_dir($post_id) {
	$langdir = ((function_exists('get_cur_post_lang_dir')) ? get_cur_post_lang_dir($post_id) : array());
	if ( isset($langdir['direction']) ) return $langdir['direction'];
}

function twentyeleven_xili_credits () {
	/* translators: added in child functions by xili */
	printf( __("Multilingual child theme of twentyeleven by %s", 'twentyeleven' ),"<a href=\"http://dev.xiligroup.com\">dev.xiligroup</a> - " );
}

add_action ('twentyeleven_credits','twentyeleven_xili_credits');

/**
 * to avoid display of old xiliml_the_other_posts in singular
 * @since 1.1
 */
function xiliml_new_list() {
	if ( class_exists('xili_language') ) {
		global $xili_language;

		$xili_theme_options = get_theme_xili_options() ; // see below

		if ( $xili_theme_options['linked_posts'] == 'show_linked' ) {
			if (is_page() && is_front_page() ) {
				return false;
			} else {
				return true;
			}
		}

		if ( is_active_widget ( false, false, 'xili_language_widgets' ) ) {

			$xili_widgets = get_option('widget_xili_language_widgets', array());
			foreach ( $xili_widgets as $key => $arrprop ) {
				if ( $key != '_multiwidget' ) {
					if ( $arrprop['theoption'] == 'typeonenew' ) {	// widget with option for singular
						if ( is_active_widget( false, 'xili_language_widgets-'.$key, 'xili_language_widgets' ) ) return false ;
					}
				}
			}
		}

		if ( XILILANGUAGE_VER > '2.0.0' && isset($xili_language -> xili_settings['navmenu_check_options']['primary']) && in_array ('navmenu-1', $xili_language -> xili_settings['navmenu_check_options']['primary']) ) return false ;

	}
	return true ;

}



?>