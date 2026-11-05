<?php
/**
 * Title: Footer
 * Slug: twentytwentyfive/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Pied de page Les Savoirs Inédits - 4 colonnes (logo/description, liens, infos, newsletter) + barre légale.
 *
 * @package LesSavoirsInedits
 */

?>
<!-- wp:group {"className":"lsi-site-footer","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group lsi-site-footer" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--40)">

	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide">

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"width":"180px","sizeSlug":"full","linkDestination":"custom","className":"lsi-site-logo lsi-footer-logo"} -->
			<figure class="wp-block-image size-full is-resized lsi-site-logo lsi-footer-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-lsi-header.png' ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" style="width:180px;height:auto"/></a></figure>
			<!-- /wp:image -->
			<!-- wp:paragraph {"fontSize":"small","style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
			<p class="has-small-font-size" style="margin-top:var(--wp--preset--spacing--30)"><?php esc_html_e( "Association d'édition. Nous publions des savoirs de terrain, rigoureux et accessibles au plus grand nombre.", 'lessavoirsinedits' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column lsi-footer-links">
			<!-- wp:heading {"level":4} -->
			<h4><?php esc_html_e( 'Le site', 'lessavoirsinedits' ); ?></h4>
			<!-- /wp:heading -->
			<!-- wp:navigation {"ref":null,"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Boutique', 'lessavoirsinedits' ); ?>","url":"<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Actu', 'lessavoirsinedits' ); ?>","url":"<?php echo esc_url( home_url( '/category/actualites/' ) ); ?>"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( "L'association", 'lessavoirsinedits' ); ?>","url":"<?php echo esc_url( home_url( '/qui-sommes-nous/' ) ); ?>"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Auteur·es', 'lessavoirsinedits' ); ?>","url":"<?php echo esc_url( home_url( '/auteur-es/' ) ); ?>"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Comité de lecture', 'lessavoirsinedits' ); ?>","url":"#"} /-->
			<!-- /wp:navigation -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column lsi-footer-links">
			<!-- wp:heading {"level":4} -->
			<h4><?php esc_html_e( 'Infos pratiques', 'lessavoirsinedits' ); ?></h4>
			<!-- /wp:heading -->
			<!-- wp:navigation {"ref":null,"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Où acheter nos livres', 'lessavoirsinedits' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Où consulter nos livres', 'lessavoirsinedits' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Livraison et retours', 'lessavoirsinedits' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Conditions générales de vente', 'lessavoirsinedits' ); ?>","url":"#"} /-->
				<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Nous contacter', 'lessavoirsinedits' ); ?>","url":"#"} /-->
			<!-- /wp:navigation -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":4} -->
			<h4><?php esc_html_e( 'La lettre des Savoirs', 'lessavoirsinedits' ); ?></h4>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size"><?php esc_html_e( 'Une lettre par trimestre : parutions, rencontres, appels à manuscrits.', 'lessavoirsinedits' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:html -->
			<form class="lsi-newsletter-form" method="post" action="#">
				<label for="lsi-newsletter-email" class="has-small-font-size"><?php esc_html_e( 'Adresse e-mail', 'lessavoirsinedits' ); ?></label>
				<input type="email" id="lsi-newsletter-email" name="email" placeholder="nom@exemple.fr" required>
				<button type="submit" class="wp-element-button"><?php esc_html_e( "S'inscrire", 'lessavoirsinedits' ); ?></button>
			</form>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->

	</div>
	<!-- /wp:columns -->

	<!-- wp:separator {"className":"lsi-footer-separator"} -->
	<hr class="wp-block-separator has-alpha-channel-opacity lsi-footer-separator"/>
	<!-- /wp:separator -->

	<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size"><?php echo esc_html( '© ' . gmdate( 'Y' ) . ' Les Savoirs Inédits · Association loi 1901' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:navigation {"ref":null,"overlayMenu":"never","className":"lsi-footer-legal-nav","layout":{"type":"flex","justifyContent":"right"}} -->
			<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Mentions légales', 'lessavoirsinedits' ); ?>","url":"#"} /-->
			<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Données personnelles', 'lessavoirsinedits' ); ?>","url":"#"} /-->
			<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Accessibilité : partiellement conforme', 'lessavoirsinedits' ); ?>","url":"#"} /-->
			<!-- wp:navigation-link {"label":"<?php esc_html_e( 'Plan du site', 'lessavoirsinedits' ); ?>","url":"#"} /-->
		<!-- /wp:navigation -->
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
