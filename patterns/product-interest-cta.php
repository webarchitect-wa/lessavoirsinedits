<?php
/**
 * Title: Fiche produit - bouton d'intérêt
 * Slug: lessavoirsinedits/product-interest-cta
 * Categories: woocommerce
 * Inserter: false
 * Description: Remplace l'ajout au panier WooCommerce sur la fiche produit :
 * la boutique sert à manifester un intérêt, pas à payer en ligne.
 *
 * @package LesSavoirsInedits
 */

$product = wc_get_product( get_the_ID() );
if ( ! $product ) {
	return;
}
?>
<a class="lsi-interest-button" href="<?php echo esc_url( lessavoirsinedits_interest_mailto( $product ) ); ?>">
	<?php esc_html_e( 'Je suis intéressé·e', 'lessavoirsinedits' ); ?>
</a>
