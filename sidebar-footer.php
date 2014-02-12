<?php
/**
 * The Footer widget areas.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
 
if ( class_exists('xili_language') ) { // if temporary disabled 
	$options = get_theme_xili_options();
	$curlang_suffix = ( the_curlang() == 'en_us' || the_curlang() == "" ) ? '' : '_'.the_curlang()  ; 
} else {
	$curlang_suffix = '';
}
$curlang_suffix3 = ( $curlang_suffix != '' && !isset( $options['sidebar_'.'sidebar-3'] ) ) ? '' : $curlang_suffix ; //display default  - no clone 
$curlang_suffix4 = ( $curlang_suffix != '' && !isset( $options['sidebar_'.'sidebar-4'] ) ) ? '' : $curlang_suffix ;
$curlang_suffix5 = ( $curlang_suffix != '' && !isset( $options['sidebar_'.'sidebar-5'] ) ) ? '' : $curlang_suffix ; 
?>

<?php
	/* The footer widget area is triggered if any of the areas
	 * have widgets. So let's check that first.
	 *
	 * If none of the sidebars have widgets, then let's bail early.
	 */
	if (   ! is_active_sidebar( 'sidebar-3' . $curlang_suffix3 )
		&& ! is_active_sidebar( 'sidebar-4' . $curlang_suffix4 )
		&& ! is_active_sidebar( 'sidebar-5' . $curlang_suffix5  )
	)
		return;
	// If we get this far, we have widgets. Let do this.
?>
<div id="supplementary" <?php twentyeleven_footer_sidebar_class(); ?>>
	<?php if ( is_active_sidebar( 'sidebar-3' . $curlang_suffix3  ) ) : ?>
	<div id="first" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-3' . $curlang_suffix3 ); ?>
	</div><!-- #first .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-4' . $curlang_suffix4 ) ) : ?>
	<div id="second" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-4' . $curlang_suffix4 ); ?>
	</div><!-- #second .widget-area -->
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'sidebar-5' . $curlang_suffix5 ) ) : ?>
	<div id="third" class="widget-area" role="complementary">
		<?php dynamic_sidebar( 'sidebar-5' . $curlang_suffix5 ); ?>
	</div><!-- #third .widget-area -->
	<?php endif; ?>
</div><!-- #supplementary -->