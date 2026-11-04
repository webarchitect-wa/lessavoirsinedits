<?php
/**
 * Title: Fiche produit - caractéristiques
 * Slug: lessavoirsinedits/product-specs
 * Categories: woocommerce
 * Inserter: false
 * Description: Statut de stock, tableau des caractéristiques et modes de retrait, à partir des champs produit Les Savoirs Inédits.
 *
 * @package LesSavoirsInedits
 */

$product = wc_get_product( get_the_ID() );
if ( ! $product ) {
	return;
}

$placeholder = __( 'À compléter', 'lessavoirsinedits' );

$categories = wc_get_product_category_list( $product->get_id() );
$categories = $categories ? wp_strip_all_tags( $categories ) : $placeholder;

$pages      = get_post_meta( $product->get_id(), '_lsi_pages', true );
$dimensions = get_post_meta( $product->get_id(), '_lsi_dimensions', true );
$isbn       = get_post_meta( $product->get_id(), '_lsi_isbn', true );
$parution   = get_post_meta( $product->get_id(), '_lsi_parution', true );
$diffusion  = get_post_meta( $product->get_id(), '_lsi_diffusion', true );

$rows = array(
	__( 'Collection', 'lessavoirsinedits' ) => $categories,
	__( 'Format', 'lessavoirsinedits' )     => $dimensions ? $dimensions : $placeholder,
	__( 'Pages', 'lessavoirsinedits' )      => $pages ? $pages : $placeholder,
	__( 'ISBN', 'lessavoirsinedits' )       => $isbn ? $isbn : $placeholder,
	__( 'Parution', 'lessavoirsinedits' )   => $parution ? $parution : $placeholder,
	__( 'Diffusion', 'lessavoirsinedits' )  => $diffusion ? $diffusion : $placeholder,
);

$in_stock = $product->is_in_stock();
?>
<div class="lsi-stock-status">
	<span class="lsi-stock-dot <?php echo $in_stock ? 'is-in-stock' : 'is-out-of-stock'; ?>"></span>
	<?php echo $in_stock
		? esc_html__( 'En stock · expédié sous 48 h', 'lessavoirsinedits' )
		: esc_html__( 'Rupture de stock', 'lessavoirsinedits' ); ?>
</div>

<table class="lsi-specs-table">
	<tbody>
	<?php foreach ( $rows as $label => $value ) : ?>
		<tr>
			<th><?php echo esc_html( $label ); ?></th>
			<td><?php echo esc_html( $value ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<div class="lsi-fulfillment-icons">
	<span>
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
		<?php esc_html_e( 'Expédition sous 48 h', 'lessavoirsinedits' ); ?>
	</span>
	<span>
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
		<?php esc_html_e( "Retrait possible à l'association", 'lessavoirsinedits' ); ?>
	</span>
	<span>
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
		<?php esc_html_e( 'Commandes groupées sur devis', 'lessavoirsinedits' ); ?>
	</span>
</div>
