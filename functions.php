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

		wp_enqueue_style(
			'lessavoirsinedits-dark-mode',
			get_stylesheet_directory_uri() . '/assets/css/dark-mode.css',
			array( 'lessavoirsinedits-brand' ),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_script(
			'lessavoirsinedits-dark-mode',
			get_stylesheet_directory_uri() . '/assets/js/dark-mode.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'lessavoirsinedits_enqueue_styles', 20 );

if ( ! function_exists( 'lessavoirsinedits_dark_mode_no_flash' ) ) {
	/**
	 * Applique le thème mémorisé (localStorage) avant le premier rendu,
	 * pour éviter un flash de la mauvaise couleur au chargement.
	 */
	function lessavoirsinedits_dark_mode_no_flash() {
		?>
		<script>
		( function () {
			try {
				var theme = localStorage.getItem( 'lsi-theme' );
				if ( theme === 'dark' || theme === 'light' ) {
					document.documentElement.setAttribute( 'data-theme', theme );
				}
			} catch ( e ) {}
		} )();
		</script>
		<?php
	}
}
add_action( 'wp_head', 'lessavoirsinedits_dark_mode_no_flash', 1 );

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
if ( ! function_exists( 'lessavoirsinedits_interest_mailto' ) ) {
	function lessavoirsinedits_interest_mailto( $product ) {
		if ( ! $product instanceof WC_Product ) {
			return '';
		}
		$subject = sprintf( __( 'Intérêt pour : %s', 'lessavoirsinedits' ), $product->get_name() );
		$body    = sprintf(
			/* translators: %s: product URL */
			__( "Bonjour,\n\nCe livre m'intéresse : %s\n\nMerci de me recontacter pour organiser la commande.", 'lessavoirsinedits' ),
			get_permalink( $product->get_id() )
		);
		return 'mailto:' . antispambot( get_option( 'admin_email' ) )
			. '?subject=' . rawurlencode( $subject )
			. '&body=' . rawurlencode( $body );
	}
}

add_filter( 'woocommerce_product_add_to_cart_url', function ( $url, $product ) {
	return lessavoirsinedits_interest_mailto( $product );
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
