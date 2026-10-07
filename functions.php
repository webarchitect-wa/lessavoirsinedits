<?php
/**
 * Les Savoirs Inédits - fonctions du thème enfant.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'lessavoirsinedits_enqueue_styles' ) ) {
	/**
	 * Charge le style du thème parent puis celui du thème enfant.
	 */
	function lessavoirsinedits_enqueue_styles() {
		wp_enqueue_style(
			'lessavoirsinedits-style',
			get_stylesheet_uri(),
			array( 'twentytwentyfive-style' ),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_style(
			'lessavoirsinedits-dark-mode',
			get_stylesheet_directory_uri() . '/assets/css/dark-mode.css',
			array( 'lessavoirsinedits-style' ),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_script(
			'lessavoirsinedits-dark-mode',
			get_stylesheet_directory_uri() . '/assets/js/dark-mode.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lessavoirsinedits_enqueue_styles', 20 );

if ( ! function_exists( 'lessavoirsinedits_dark_mode_no_flash' ) ) {
	/**
	 * Applique le thème mémorisé (localStorage) avant le premier rendu,
	 * pour éviter un flash de la mauvaise couleur au chargement.
	 */
	function lessavoirsinedits_dark_mode_no_flash() {
		?>
		<script>
		( function () {
			try {
				var theme = localStorage.getItem( 'lsi-theme' );
				if ( theme === 'dark' || theme === 'light' ) {
					document.documentElement.setAttribute( 'data-theme', theme );
				}
			} catch ( e ) {}
		} )();
		</script>
		<?php
	}
}
add_action( 'wp_head', 'lessavoirsinedits_dark_mode_no_flash', 1 );
