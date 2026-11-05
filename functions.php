<?php
/**
 * Les Savoirs Inédits - fonctions du thème enfant.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'lessavoirsinedits_flush_rewrite_rules' ) ) {
	/**
	 * La taxonomie lsi_author ajoute de nouvelles règles de réécriture :
	 * sans ce flush, ses URLs (/auteur/...) renvoient un 404 tant que
	 * personne n'a resauvegardé les permaliens manuellement.
	 */
	function lessavoirsinedits_flush_rewrite_rules() {
		lessavoirsinedits_register_author_taxonomy();
		flush_rewrite_rules();
	}
}
add_action( 'after_switch_theme', 'lessavoirsinedits_flush_rewrite_rules' );

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
			'lessavoirsinedits-brand',
			get_stylesheet_directory_uri() . '/assets/css/brand.css',
			array( 'lessavoirsinedits-style' ),
			wp_get_theme()->get( 'Version' )
		);


	}
}
add_action( 'wp_enqueue_scripts', 'lessavoirsinedits_enqueue_styles', 20 );

/**
 * Champs complémentaires des livres (fiche produit) : sous-titre,
 * ISBN, pages, format/dimensions, parution, diffusion.
 * L'auteur·ice est géré·e via la taxonomie lsi_author (voir plus bas),
 * pas un champ texte.
 */
if ( ! function_exists( 'lessavoirsinedits_book_meta_fields' ) ) {
	function lessavoirsinedits_book_meta_fields() {
		return array(
			'_lsi_subtitle'   => __( 'Sous-titre', 'lessavoirsinedits' ),
			'_lsi_isbn'       => __( 'ISBN', 'lessavoirsinedits' ),
			'_lsi_pages'      => __( 'Nombre de pages', 'lessavoirsinedits' ),
			'_lsi_dimensions' => __( 'Format (dimensions, reliure)', 'lessavoirsinedits' ),
			'_lsi_parution'   => __( 'Date de parution', 'lessavoirsinedits' ),
			'_lsi_diffusion'  => __( 'Diffusion', 'lessavoirsinedits' ),
		);
	}
}

if ( ! function_exists( 'lessavoirsinedits_register_book_meta' ) ) {
	function lessavoirsinedits_register_book_meta() {
		foreach ( array_keys( lessavoirsinedits_book_meta_fields() ) as $key ) {
			register_post_meta( 'product', $key, array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
				'auth_callback' => function () {
					return current_user_can( 'edit_products' );
				},
			) );
		}
	}
}
add_action( 'init', 'lessavoirsinedits_register_book_meta' );

if ( ! function_exists( 'lessavoirsinedits_add_book_meta_box' ) ) {
	function lessavoirsinedits_add_book_meta_box() {
		add_meta_box(
			'lsi-book-details',
			__( 'Caractéristiques du livre', 'lessavoirsinedits' ),
			'lessavoirsinedits_render_book_meta_box',
			'product',
			'normal',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'lessavoirsinedits_add_book_meta_box' );

if ( ! function_exists( 'lessavoirsinedits_render_book_meta_box' ) ) {
	function lessavoirsinedits_render_book_meta_box( $post ) {
		wp_nonce_field( 'lsi_save_book_meta', 'lsi_book_meta_nonce' );
		echo '<table class="form-table">';
		foreach ( lessavoirsinedits_book_meta_fields() as $key => $label ) {
			$value = get_post_meta( $post->ID, $key, true );
			printf(
				'<tr><th><label for="%1$s">%2$s</label></th><td><input type="text" class="widefat" id="%1$s" name="%1$s" value="%3$s" /></td></tr>',
				esc_attr( $key ),
				esc_html( $label ),
				esc_attr( $value )
			);
		}
		echo '</table>';
	}
}

if ( ! function_exists( 'lessavoirsinedits_save_book_meta' ) ) {
	function lessavoirsinedits_save_book_meta( $post_id ) {
		if ( ! isset( $_POST['lsi_book_meta_nonce'] ) || ! wp_verify_nonce( $_POST['lsi_book_meta_nonce'], 'lsi_save_book_meta' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_product', $post_id ) ) {
			return;
		}
		foreach ( array_keys( lessavoirsinedits_book_meta_fields() ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}
	}
}
add_action( 'save_post_product', 'lessavoirsinedits_save_book_meta' );

/**
 * Taxonomie Auteur·ice : chaque terme est une personne autrice, rattachée
 * à un ou plusieurs livres (produits). Rôle et bio courte en term meta.
 */
if ( ! function_exists( 'lessavoirsinedits_register_author_taxonomy' ) ) {
	function lessavoirsinedits_register_author_taxonomy() {
		register_taxonomy( 'lsi_author', 'product', array(
			'labels' => array(
				'name'          => __( 'Auteur·es', 'lessavoirsinedits' ),
				'singular_name' => __( 'Auteur·ice', 'lessavoirsinedits' ),
				'search_items'  => __( 'Rechercher un·e auteur·e', 'lessavoirsinedits' ),
				'add_new_item'  => __( 'Ajouter un·e auteur·ice', 'lessavoirsinedits' ),
			),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'public'            => true,
			'rewrite'           => array( 'slug' => 'auteur' ),
		) );

		register_term_meta( 'lsi_author', 'lsi_role', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );

		register_term_meta( 'lsi_author', 'lsi_bio', array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		) );
	}
}
add_action( 'init', 'lessavoirsinedits_register_author_taxonomy' );

if ( ! function_exists( 'lessavoirsinedits_author_term_fields' ) ) {
	function lessavoirsinedits_author_term_fields( $term ) {
		$role = is_object( $term ) ? get_term_meta( $term->term_id, 'lsi_role', true ) : '';
		$bio  = is_object( $term ) ? get_term_meta( $term->term_id, 'lsi_bio', true ) : '';
		?>
		<div class="form-field">
			<label for="lsi_role"><?php esc_html_e( 'Rôle / pratique', 'lessavoirsinedits' ); ?></label>
			<input type="text" name="lsi_role" id="lsi_role" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'Ex. : Assistante sociale scolaire', 'lessavoirsinedits' ); ?>" />
		</div>
		<div class="form-field">
			<label for="lsi_bio"><?php esc_html_e( 'Bio courte', 'lessavoirsinedits' ); ?></label>
			<textarea name="lsi_bio" id="lsi_bio" rows="3"><?php echo esc_textarea( $bio ); ?></textarea>
		</div>
		<?php
	}
}
add_action( 'lsi_author_add_form_fields', 'lessavoirsinedits_author_term_fields' );
add_action( 'lsi_author_edit_form_fields', function ( $term ) {
	echo '<tr class="form-field"><th colspan="2">';
	lessavoirsinedits_author_term_fields( $term );
	echo '</th></tr>';
} );

if ( ! function_exists( 'lessavoirsinedits_save_author_term_meta' ) ) {
	function lessavoirsinedits_save_author_term_meta( $term_id ) {
		if ( isset( $_POST['lsi_role'] ) ) {
			update_term_meta( $term_id, 'lsi_role', sanitize_text_field( wp_unslash( $_POST['lsi_role'] ) ) );
		}
		if ( isset( $_POST['lsi_bio'] ) ) {
			update_term_meta( $term_id, 'lsi_bio', sanitize_textarea_field( wp_unslash( $_POST['lsi_bio'] ) ) );
		}
	}
}
add_action( 'created_lsi_author', 'lessavoirsinedits_save_author_term_meta' );
add_action( 'edited_lsi_author', 'lessavoirsinedits_save_author_term_meta' );

if ( ! function_exists( 'lessavoirsinedits_author_initials' ) ) {
	/**
	 * Initiales d'un·e auteur·ice à partir de son nom (ex. "Françoise Sigonneau" -> "FS").
	 */
	function lessavoirsinedits_author_initials( $name ) {
		$parts    = preg_split( '/\s+/', trim( $name ) );
		$initials = '';
		if ( ! empty( $parts ) ) {
			$initials .= mb_substr( $parts[0], 0, 1 );
			if ( count( $parts ) > 1 ) {
				$initials .= mb_substr( end( $parts ), 0, 1 );
			}
		}
		return mb_strtoupper( $initials );
	}
}

/**
 * Pas de paiement en ligne : la boutique sert à manifester son intérêt
 * pour un livre, pas à l'acheter. On remplace partout le lien/bouton
 * "Ajouter au panier" de WooCommerce par un mailto pré-rempli, et on
 * coupe l'ajout au panier en AJAX pour que le clic ouvre bien le mail
 * au lieu d'ajouter silencieusement au panier.
 */
/**
 * URL du bouton « Je suis intéressé·e » : la page de demande de commande,
 * avec le livre pré-sélectionné (?produit=ID).
 */
if ( ! function_exists( 'lessavoirsinedits_interest_url' ) ) {
	function lessavoirsinedits_interest_url( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}
		$page = get_page_by_path( 'demande-de-commande' );
		if ( ! $page ) {
			return get_permalink( $product->get_id() );
		}
		return add_query_arg( 'produit', $product->get_id(), get_permalink( $page ) );
	}
}

/**
 * Formulaire de demande de commande (shortcode [lsi_commande_form]).
 */
add_shortcode( 'lsi_commande_form', function () {
	$status    = isset( $_GET['commande'] ) ? sanitize_key( wp_unslash( $_GET['commande'] ) ) : '';
	$preselect = isset( $_GET['produit'] ) ? absint( $_GET['produit'] ) : 0;
	$products  = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );

	ob_start();
	if ( 'ok' === $status ) {
		echo '<p class="lsi-form-notice lsi-form-ok">' . esc_html__( 'Merci ! Votre demande a bien été envoyée. Nous revenons vers vous pour organiser la commande.', 'lessavoirsinedits' ) . '</p>';
	} elseif ( 'erreur' === $status ) {
		echo '<p class="lsi-form-notice lsi-form-error">' . esc_html__( 'Le formulaire est incomplet ou n’a pas pu être envoyé. Merci de vérifier les champs obligatoires.', 'lessavoirsinedits' ) . '</p>';
	}
	?>
	<form class="lsi-commande-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="lsi_commande">
		<?php wp_nonce_field( 'lsi_commande', 'lsi_commande_nonce' ); ?>
		<p class="lsi-hp" aria-hidden="true" style="position:absolute;left:-9999px;">
			<label>Ne pas remplir : <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
		</p>

		<p>
			<label for="lsi-nom"><?php esc_html_e( 'Nom et prénom', 'lessavoirsinedits' ); ?> *</label>
			<input id="lsi-nom" type="text" name="nom" required autocomplete="name">
		</p>
		<p>
			<label for="lsi-email"><?php esc_html_e( 'Adresse email', 'lessavoirsinedits' ); ?> *</label>
			<input id="lsi-email" type="email" name="email" required autocomplete="email">
		</p>
		<p>
			<label for="lsi-tel"><?php esc_html_e( 'Téléphone (facultatif)', 'lessavoirsinedits' ); ?></label>
			<input id="lsi-tel" type="tel" name="telephone" autocomplete="tel">
		</p>
		<p>
			<label for="lsi-produit"><?php esc_html_e( 'Livre', 'lessavoirsinedits' ); ?> *</label>
			<select id="lsi-produit" name="produit" required>
				<option value=""><?php esc_html_e( 'Choisir un livre', 'lessavoirsinedits' ); ?></option>
				<?php foreach ( $products as $product ) : ?>
					<option value="<?php echo esc_attr( $product->get_id() ); ?>" <?php selected( $preselect, $product->get_id() ); ?>>
						<?php echo esc_html( $product->get_name() ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="lsi-quantite"><?php esc_html_e( 'Quantité', 'lessavoirsinedits' ); ?></label>
			<input id="lsi-quantite" type="number" name="quantite" min="1" value="1">
		</p>
		<p>
			<label for="lsi-mode"><?php esc_html_e( 'Envoi ou retrait', 'lessavoirsinedits' ); ?> *</label>
			<select id="lsi-mode" name="mode" required>
				<option value=""><?php esc_html_e( 'Choisir', 'lessavoirsinedits' ); ?></option>
				<option value="envoi"><?php esc_html_e( 'Envoi', 'lessavoirsinedits' ); ?></option>
				<option value="retrait"><?php esc_html_e( 'Retrait', 'lessavoirsinedits' ); ?></option>
			</select>
		</p>
		<p>
			<label for="lsi-message"><?php esc_html_e( 'Message (facultatif)', 'lessavoirsinedits' ); ?></label>
			<textarea id="lsi-message" name="message" rows="4"></textarea>
		</p>
		<p class="lsi-form-consent">
			<label>
				<input type="checkbox" name="consent" value="1" required>
				<?php esc_html_e( 'J’accepte que mes données servent uniquement à traiter ma demande. Elles ne sont ni revendues ni partagées.', 'lessavoirsinedits' ); ?> *
			</label>
		</p>
		<p>
			<button type="submit" class="lsi-interest-button"><?php esc_html_e( 'Envoyer ma demande', 'lessavoirsinedits' ); ?></button>
		</p>
	</form>
	<?php
	return ob_get_clean();
} );

/**
 * Traitement du formulaire : vérifications, puis envoi d'un email à l'admin.
 */
function lessavoirsinedits_handle_commande() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/demande-de-commande/' );
	$back = remove_query_arg( 'commande', $back );

	$fail = function () use ( $back ) {
		wp_safe_redirect( add_query_arg( 'commande', 'erreur', $back ) );
		exit;
	};

	if ( ! isset( $_POST['lsi_commande_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lsi_commande_nonce'] ) ), 'lsi_commande' ) ) {
		$fail();
	}
	if ( ! empty( $_POST['website'] ) ) { // champ piège rempli : c'est un robot
		$fail();
	}

	$nom     = sanitize_text_field( wp_unslash( $_POST['nom'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$tel     = sanitize_text_field( wp_unslash( $_POST['telephone'] ?? '' ) );
	$product = wc_get_product( absint( $_POST['produit'] ?? 0 ) );
	$qty     = max( 1, absint( $_POST['quantite'] ?? 1 ) );
	$mode    = sanitize_key( wp_unslash( $_POST['mode'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	if ( '' === $nom || ! is_email( $email ) || ! $product || ! in_array( $mode, array( 'envoi', 'retrait' ), true ) || empty( $_POST['consent'] ) ) {
		$fail();
	}

	$body = implode( "\n", array(
		'Nom : ' . $nom,
		'Email : ' . $email,
		'Téléphone : ' . ( $tel ? $tel : '-' ),
		'Livre : ' . $product->get_name() . ' (' . get_permalink( $product->get_id() ) . ')',
		'Quantité : ' . $qty,
		'Mode : ' . ( 'envoi' === $mode ? 'Envoi' : 'Retrait' ),
		'',
		'Message :',
		$message ? $message : '-',
	) );

	$sent = wp_mail(
		get_option( 'admin_email' ),
		'Demande de commande : ' . $product->get_name(),
		$body,
		array( 'Reply-To: ' . $nom . ' <' . $email . '>' )
	);

	if ( ! $sent ) {
		$fail();
	}
	wp_safe_redirect( add_query_arg( 'commande', 'ok', $back ) );
	exit;
}
add_action( 'admin_post_nopriv_lsi_commande', 'lessavoirsinedits_handle_commande' );
add_action( 'admin_post_lsi_commande', 'lessavoirsinedits_handle_commande' );

add_filter( 'woocommerce_product_add_to_cart_url', function ( $url, $product ) {
	return lessavoirsinedits_interest_url( $product );
}, 10, 2 );

add_filter( 'woocommerce_product_add_to_cart_text', function ( $text, $product ) {
	return __( 'Je suis intéressé·e', 'lessavoirsinedits' );
}, 10, 2 );

add_filter( 'woocommerce_product_single_add_to_cart_text', function ( $text, $product ) {
	return __( 'Je suis intéressé·e', 'lessavoirsinedits' );
}, 10, 2 );

add_action( 'init', function () {
	if ( 'no' !== get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
		update_option( 'woocommerce_enable_ajax_add_to_cart', 'no' );
	}
} );

/*
 * WooCommerce accroche automatiquement un mini-panier (et un second lien
 * "compte") à tout bloc core/navigation (API Hooked Blocks). Pas de panier
 * ici, et le compte client est déjà placé explicitement dans le header :
 * on retire ces deux types de la liste des blocs à accrocher. (Les
 * supprimer via le filtre hooked_block_{type} en renvoyant null déclenche
 * un warning PHP côté cœur WordPress lors du rendu des patterns, d'où
 * cette approche en amont à la place.)
 */
add_filter( 'hooked_block_types', function ( $hooked_block_types, $relative_position, $anchor_block_type ) {
	if ( 'core/navigation' === $anchor_block_type ) {
		$hooked_block_types = array_diff( $hooked_block_types, array( 'woocommerce/mini-cart', 'woocommerce/customer-account' ) );
	}
	return $hooked_block_types;
}, 20, 3 );

/**
 * Le bloc woocommerce/product-filter-checkbox-list (utilisé imbriqué dans
 * les filtres Collections/Format/Disponibilité) ne charge pas toujours sa
 * propre feuille de style quand il est inséré "à la main" dans un gabarit
 * plutôt que via l'éditeur : sans elle, les cases à cocher retombent sur le
 * rendu natif du navigateur (bordure de fieldset, case grise). On force
 * son chargement.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_shop() && ! is_product_category() ) {
		return;
	}
	$rel_path = 'plugins/woocommerce/assets/client/blocks/woocommerce/product-filter-checkbox-list-style.css';
	if ( file_exists( WP_CONTENT_DIR . '/' . $rel_path ) ) {
		wp_enqueue_style(
			'lessavoirsinedits-product-filter-checkbox-list',
			content_url( $rel_path ),
			array(),
			WC()->version
		);
	}
}, 30 );

/**
 * Liste admin des produits : la colonne « Auteur·es » (taxonomie lsi_author)
 * reçoit une largeur fixe et un retour à la ligne, sinon WordPress la compresse
 * au point de rendre son titre illisible.
 */
add_action( 'admin_head-edit.php', function () {
	global $typenow;
	if ( 'product' !== $typenow ) {
		return;
	}
	?>
	<style>
		.wp-list-table th.column-taxonomy-lsi_author,
		.wp-list-table td.column-taxonomy-lsi_author {
			width: 170px;
			white-space: normal;
		}
	</style>
	<?php
} );

/**
 * Les boutons écrits en chemin relatif à la racine (href="/boutique/") ne
 * fonctionnent que si le site est à la racine du domaine. On les rattache à
 * l'adresse réelle du site, ce qui marche aussi en sous-dossier (XAMPP).
 */
add_filter( 'render_block_core/button', 'lessavoirsinedits_site_relative_hrefs' );
add_filter( 'render_block_core/paragraph', 'lessavoirsinedits_site_relative_hrefs' );

function lessavoirsinedits_site_relative_hrefs( $block_content ) {
	$base = untrailingslashit( home_url( '/' ) );
	return preg_replace( '#href="/(?!/)#', 'href="' . $base . '/', $block_content );
}

/*
 * Proposition de manuscrit (shortcode [lsi_manuscrit_form]).
 * Même principe que la demande de commande : vérifications, email à l'admin,
 * aucune donnée stockée. Le texte n'est pas joint (taille des fichiers) :
 * le formulaire demande un résumé et un lien facultatif vers le manuscrit.
 */
add_shortcode( 'lsi_manuscrit_form', function () {
	$status = isset( $_GET['manuscrit'] ) ? sanitize_key( wp_unslash( $_GET['manuscrit'] ) ) : '';

	ob_start();
	if ( 'ok' === $status ) {
		echo '<p class="lsi-form-notice lsi-form-ok">' . esc_html__( 'Merci pour votre proposition. Notre comité de lecture l’examinera et nous vous répondrons.', 'lessavoirsinedits' ) . '</p>';
	} elseif ( 'erreur' === $status ) {
		echo '<p class="lsi-form-notice lsi-form-error">' . esc_html__( 'Le formulaire est incomplet ou n’a pas pu être envoyé. Merci de vérifier les champs obligatoires.', 'lessavoirsinedits' ) . '</p>';
	}
	?>
	<form class="lsi-commande-form lsi-manuscrit-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="lsi_manuscrit">
		<?php wp_nonce_field( 'lsi_manuscrit', 'lsi_manuscrit_nonce' ); ?>
		<p class="lsi-hp" aria-hidden="true" style="position:absolute;left:-9999px;">
			<label>Ne pas remplir : <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
		</p>

		<p>
			<label for="lsi-m-nom"><?php esc_html_e( 'Nom et prénom', 'lessavoirsinedits' ); ?> *</label>
			<input id="lsi-m-nom" type="text" name="nom" required autocomplete="name">
		</p>
		<p>
			<label for="lsi-m-email"><?php esc_html_e( 'Adresse email', 'lessavoirsinedits' ); ?> *</label>
			<input id="lsi-m-email" type="email" name="email" required autocomplete="email">
		</p>
		<p>
			<label for="lsi-m-tel"><?php esc_html_e( 'Téléphone (facultatif)', 'lessavoirsinedits' ); ?></label>
			<input id="lsi-m-tel" type="tel" name="telephone" autocomplete="tel">
		</p>
		<p>
			<label for="lsi-m-profil"><?php esc_html_e( 'Votre pratique', 'lessavoirsinedits' ); ?> *</label>
			<select id="lsi-m-profil" name="profil" required>
				<option value=""><?php esc_html_e( 'Choisir', 'lessavoirsinedits' ); ?></option>
				<option value="professionnel"><?php esc_html_e( 'Professionnel·le', 'lessavoirsinedits' ); ?></option>
				<option value="benevole"><?php esc_html_e( 'Bénévole', 'lessavoirsinedits' ); ?></option>
				<option value="autre"><?php esc_html_e( 'Autre', 'lessavoirsinedits' ); ?></option>
			</select>
		</p>
		<p>
			<label for="lsi-m-titre"><?php esc_html_e( 'Titre de travail du manuscrit', 'lessavoirsinedits' ); ?> *</label>
			<input id="lsi-m-titre" type="text" name="titre" required>
		</p>
		<p>
			<label for="lsi-m-resume"><?php esc_html_e( 'Résumé de l’ouvrage', 'lessavoirsinedits' ); ?> *</label>
			<textarea id="lsi-m-resume" name="resume" rows="5" required></textarea>
		</p>
		<p>
			<label for="lsi-m-pages"><?php esc_html_e( 'Nombre de pages environ', 'lessavoirsinedits' ); ?></label>
			<input id="lsi-m-pages" type="number" name="pages" min="1">
		</p>
		<p>
			<label for="lsi-m-lien"><?php esc_html_e( 'Lien vers le manuscrit (facultatif)', 'lessavoirsinedits' ); ?></label>
			<input id="lsi-m-lien" type="url" name="lien" placeholder="https://">
		</p>
		<p>
			<label for="lsi-m-message"><?php esc_html_e( 'Message (facultatif)', 'lessavoirsinedits' ); ?></label>
			<textarea id="lsi-m-message" name="message" rows="3"></textarea>
		</p>
		<p class="lsi-form-consent">
			<label>
				<input type="checkbox" name="consent" value="1" required>
				<?php esc_html_e( 'J’accepte que mes données servent uniquement à examiner ma proposition. Elles ne sont ni revendues ni partagées.', 'lessavoirsinedits' ); ?> *
			</label>
		</p>
		<p>
			<button type="submit" class="lsi-interest-button"><?php esc_html_e( 'Envoyer ma proposition', 'lessavoirsinedits' ); ?></button>
		</p>
	</form>
	<?php
	return ob_get_clean();
} );

function lessavoirsinedits_handle_manuscrit() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/proposer-un-manuscrit/' );
	$back = remove_query_arg( 'manuscrit', $back );
	$fail = function () use ( $back ) {
		wp_safe_redirect( add_query_arg( 'manuscrit', 'erreur', $back ) );
		exit;
	};

	if ( ! isset( $_POST['lsi_manuscrit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lsi_manuscrit_nonce'] ) ), 'lsi_manuscrit' ) ) {
		$fail();
	}
	if ( ! empty( $_POST['website'] ) ) {
		$fail();
	}

	$nom     = sanitize_text_field( wp_unslash( $_POST['nom'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$tel     = sanitize_text_field( wp_unslash( $_POST['telephone'] ?? '' ) );
	$profil  = sanitize_key( wp_unslash( $_POST['profil'] ?? '' ) );
	$titre   = sanitize_text_field( wp_unslash( $_POST['titre'] ?? '' ) );
	$resume  = sanitize_textarea_field( wp_unslash( $_POST['resume'] ?? '' ) );
	$pages   = absint( $_POST['pages'] ?? 0 );
	$lien    = esc_url_raw( wp_unslash( $_POST['lien'] ?? '' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );

	if ( '' === $nom || ! is_email( $email ) || '' === $titre || '' === $resume || ! in_array( $profil, array( 'professionnel', 'benevole', 'autre' ), true ) || empty( $_POST['consent'] ) ) {
		$fail();
	}

	$profils = array( 'professionnel' => 'Professionnel·le', 'benevole' => 'Bénévole', 'autre' => 'Autre' );
	$body    = implode( "\n", array(
		'Nom : ' . $nom,
		'Email : ' . $email,
		'Téléphone : ' . ( $tel ? $tel : '-' ),
		'Pratique : ' . $profils[ $profil ],
		'Titre de travail : ' . $titre,
		'Pages (environ) : ' . ( $pages ? $pages : '-' ),
		'Lien vers le manuscrit : ' . ( $lien ? $lien : '-' ),
		'',
		'Résumé :',
		$resume,
		'',
		'Message :',
		$message ? $message : '-',
	) );

	$sent = wp_mail(
		get_option( 'admin_email' ),
		'Proposition de manuscrit : ' . $titre,
		$body,
		array( 'Reply-To: ' . $nom . ' <' . $email . '>' )
	);
	if ( ! $sent ) {
		$fail();
	}
	wp_safe_redirect( add_query_arg( 'manuscrit', 'ok', $back ) );
	exit;
}
add_action( 'admin_post_nopriv_lsi_manuscrit', 'lessavoirsinedits_handle_manuscrit' );
add_action( 'admin_post_lsi_manuscrit', 'lessavoirsinedits_handle_manuscrit' );
