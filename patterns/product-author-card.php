<?php
/**
 * Title: Fiche produit - carte auteur·ice
 * Slug: lessavoirsinedits/product-author-card
 * Categories: woocommerce
 * Inserter: false
 * Description: Carte courte présentant l'auteur·ice du livre (première
 * personne rattachée au produit via la taxonomie lsi_author), avec lien
 * vers sa page.
 *
 * @package LesSavoirsInedits
 */

$authors = get_the_terms( get_the_ID(), 'lsi_author' );
if ( ! is_array( $authors ) || empty( $authors ) ) {
	return;
}
$author = $authors[0];
$role   = get_term_meta( $author->term_id, 'lsi_role', true );
$bio    = get_term_meta( $author->term_id, 'lsi_bio', true );
?>
<div class="lsi-author-card">
	<div class="lsi-author-avatar"><?php echo esc_html( lessavoirsinedits_author_initials( $author->name ) ); ?></div>
	<p class="lsi-author-name"><?php echo esc_html( $author->name ); ?></p>
	<?php if ( $role ) : ?>
		<p class="lsi-author-role"><?php echo esc_html( mb_strtoupper( $role ) ); ?></p>
	<?php endif; ?>
	<?php if ( $bio ) : ?>
		<p class="lsi-author-bio"><?php echo esc_html( $bio ); ?></p>
	<?php endif; ?>
	<p class="lsi-section-link"><a href="<?php echo esc_url( get_term_link( $author ) ); ?>"><?php esc_html_e( 'Voir ses titres', 'lessavoirsinedits' ); ?></a></p>
</div>
