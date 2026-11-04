<?php
/**
 * Title: Auteur·es - filtres (collections + recherche)
 * Slug: lessavoirsinedits/author-filters
 * Categories: text
 * Inserter: false
 * Description: Pastilles de filtre par collection (catégories produit
 * réellement utilisées par des auteur·ices) + recherche par nom, en GET
 * pour rester simple et fonctionner sans JS.
 *
 * @package LesSavoirsInedits
 */

$current_cat = isset( $_GET['collection'] ) ? sanitize_title( wp_unslash( $_GET['collection'] ) ) : '';
$current_q   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
$page_url    = get_permalink();

$categories = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
) );
$categories = is_wp_error( $categories ) ? array() : $categories;
?>
<div class="lsi-author-filters">
	<div class="lsi-filter-pills">
		<a href="<?php echo esc_url( add_query_arg( array_filter( array( 'q' => $current_q ) ), $page_url ) ); ?>" class="lsi-pill <?php echo '' === $current_cat ? 'is-active' : ''; ?>">
			<?php esc_html_e( 'Toutes et tous', 'lessavoirsinedits' ); ?>
		</a>
		<?php foreach ( $categories as $cat ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array_filter( array( 'collection' => $cat->slug, 'q' => $current_q ) ), $page_url ) ); ?>" class="lsi-pill <?php echo $current_cat === $cat->slug ? 'is-active' : ''; ?>">
				<?php echo esc_html( $cat->name ); ?>
			</a>
		<?php endforeach; ?>
	</div>
	<form class="lsi-author-search" method="get" action="<?php echo esc_url( $page_url ); ?>">
		<?php if ( $current_cat ) : ?>
			<input type="hidden" name="collection" value="<?php echo esc_attr( $current_cat ); ?>" />
		<?php endif; ?>
		<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
		<input type="text" name="q" value="<?php echo esc_attr( $current_q ); ?>" placeholder="<?php esc_attr_e( 'Rechercher un·e auteur·e', 'lessavoirsinedits' ); ?>" />
	</form>
</div>
