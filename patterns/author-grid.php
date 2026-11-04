<?php
/**
 * Title: Auteur·es - grille
 * Slug: lessavoirsinedits/author-grid
 * Categories: text
 * Inserter: false
 * Description: Liste de toutes les personnes auteur·ices (taxonomie
 * lsi_author), filtrable par collection et recherche via les paramètres
 * GET posés par le pattern author-filters.
 *
 * @package LesSavoirsInedits
 */

$current_cat = isset( $_GET['collection'] ) ? sanitize_title( wp_unslash( $_GET['collection'] ) ) : '';
$current_q   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

$authors = get_terms( array(
	'taxonomy'   => 'lsi_author',
	'hide_empty' => true,
) );
$authors = is_wp_error( $authors ) ? array() : $authors;

if ( '' !== $current_cat ) {
	$authors = array_filter( $authors, function ( $author ) use ( $current_cat ) {
		$products = get_posts( array(
			'post_type'      => 'product',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => array(
				'relation' => 'AND',
				array( 'taxonomy' => 'lsi_author', 'field' => 'term_id', 'terms' => $author->term_id ),
				array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $current_cat ),
			),
		) );
		return ! empty( $products );
	} );
}

if ( '' !== $current_q ) {
	$authors = array_filter( $authors, function ( $author ) use ( $current_q ) {
		return false !== stripos( $author->name, $current_q );
	} );
}
?>
<?php if ( empty( $authors ) ) : ?>
	<p><?php esc_html_e( 'Aucun·e auteur·ice ne correspond à cette recherche.', 'lessavoirsinedits' ); ?></p>
<?php else : ?>
	<div class="lsi-author-grid">
		<?php foreach ( $authors as $author ) : ?>
			<?php $role = get_term_meta( $author->term_id, 'lsi_role', true ); ?>
			<?php $bio  = get_term_meta( $author->term_id, 'lsi_bio', true ); ?>
			<div class="lsi-author-card">
				<div class="lsi-author-avatar"><?php echo esc_html( lessavoirsinedits_author_initials( $author->name ) ); ?></div>
				<p class="lsi-author-name"><?php echo esc_html( $author->name ); ?></p>
				<?php if ( $role ) : ?>
					<p class="lsi-author-role"><?php echo esc_html( mb_strtoupper( $role ) ); ?></p>
				<?php endif; ?>
				<?php if ( $bio ) : ?>
					<p class="lsi-author-bio"><?php echo esc_html( wp_trim_words( $bio, 20 ) ); ?></p>
				<?php endif; ?>
				<p class="lsi-section-link"><a href="<?php echo esc_url( get_term_link( $author ) ); ?>"><?php esc_html_e( 'Voir ses titres', 'lessavoirsinedits' ); ?></a></p>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
