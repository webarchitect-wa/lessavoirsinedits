<?php
/**
 * Title: Bannière auteur·ice
 * Slug: lessavoirsinedits/author-banner
 * Categories: text
 * Inserter: false
 * Description: Avatar, nom, rôle et bio de l'auteur·ice, utilisés en haut
 * de l'archive taxonomy-lsi_author.html.
 *
 * @package LesSavoirsInedits
 */

$term = get_queried_object();
if ( ! $term instanceof WP_Term ) {
	return;
}

$role = get_term_meta( $term->term_id, 'lsi_role', true );
$bio  = get_term_meta( $term->term_id, 'lsi_bio', true );
?>
<div class="lsi-author-banner">
	<div class="lsi-author-avatar lsi-author-avatar-lg"><?php echo esc_html( lessavoirsinedits_author_initials( $term->name ) ); ?></div>
	<div>
		<h1><?php echo esc_html( $term->name ); ?></h1>
		<?php if ( $role ) : ?>
			<p class="lsi-author-role"><?php echo esc_html( mb_strtoupper( $role ) ); ?></p>
		<?php endif; ?>
		<?php if ( $bio ) : ?>
			<p class="lsi-author-banner-bio"><?php echo esc_html( $bio ); ?></p>
		<?php endif; ?>
	</div>
</div>
