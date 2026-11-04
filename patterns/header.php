<?php
/**
 * Title: Header
 * Slug: twentytwentyfive/header
 * Categories: header
 * Block Types: core/template-part/header
 * Description: En-tête Les Savoirs Inédits - logo (ligne blanche) puis bandeau dégradé avec navigation, recherche et compte (pas de panier : la boutique ne vend pas en ligne).
 *
 * @package LesSavoirsInedits
 */

?>
<!-- wp:group {"tagName":"header","align":"full","className":"lsi-site-header","layout":{"type":"constrained"}} -->
<header class="wp-block-group alignfull lsi-site-header">

	<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide" style="padding-top:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20)">
		<!-- wp:site-logo {"width":170,"shouldSyncIcon":false,"className":"lsi-site-logo"} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"lsi-header-nav-band","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull lsi-header-nav-band">
		<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group alignwide">

			<!-- wp:navigation {"ref":27,"className":"lsi-header-nav","overlayBackgroundColor":"base","overlayTextColor":"contrast","layout":{"type":"flex","justifyContent":"left","flexWrap":"wrap"}} /-->

			<!-- wp:group {"className":"lsi-header-actions","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
			<div class="wp-block-group lsi-header-actions">
				<!-- wp:html -->
				<a class="lsi-header-search-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" onclick="var q=prompt('<?php echo esc_js( __( 'Rechercher', 'lessavoirsinedits' ) ); ?>'); if(q){window.location='<?php echo esc_js( home_url( '/' ) ); ?>?s='+encodeURIComponent(q);} return false;">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
					<?php esc_html_e( 'Rechercher', 'lessavoirsinedits' ); ?>
				</a>
				<!-- /wp:html -->

				<!-- wp:woocommerce/customer-account {"displayStyle":"icon_and_text","iconClass":"wc-block-customer-account__account-icon"} /-->
			</div>
			<!-- /wp:group -->

		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

</header>
<!-- /wp:group -->
