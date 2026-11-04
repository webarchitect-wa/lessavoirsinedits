<?php
/**
 * Title: Fiche produit - sous-titre et auteur·ice
 * Slug: lessavoirsinedits/product-subtitle-author
 * Categories: woocommerce
 * Inserter: false
 *
 * @package LesSavoirsInedits
 */

$subtitle = get_post_meta( get_the_ID(), '_lsi_subtitle', true );
$authors  = get_the_terms( get_the_ID(), 'lsi_author' );
$authors  = is_array( $authors ) ? $authors : array();

if ( ! $subtitle && empty( $authors ) ) {
	return;
}
?>
<?php if ( $subtitle ) : ?>
	<p class="lsi-product-subtitle"><?php echo esc_html( $subtitle ); ?></p>
<?php endif; ?>
<?php if ( ! empty( $authors ) ) : ?>
	<p class="lsi-product-author">
		<?php foreach ( $authors as $i => $author ) : ?>
			<?php echo $i > 0 ? ', ' : ''; ?><a href="<?php echo esc_url( get_term_link( $author ) ); ?>"><?php echo esc_html( $author->name ); ?></a>
		<?php endforeach; ?>
	</p>
<?php endif; ?>
