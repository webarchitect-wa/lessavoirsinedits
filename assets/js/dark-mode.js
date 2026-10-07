( function () {
	var root = document.documentElement;
	var STORAGE_KEY = 'lsi-theme';
	var button;

	function systemPrefersDark() {
		return window.matchMedia( '(prefers-color-scheme: dark)' ).matches;
	}

	function currentTheme() {
		var attr = root.getAttribute( 'data-theme' );
		if ( attr === 'dark' || attr === 'light' ) {
			return attr;
		}
		return systemPrefersDark() ? 'dark' : 'light';
	}

	function updateButton( theme ) {
		if ( ! button ) {
			return;
		}
		button.textContent = theme === 'dark' ? '☀️' : '🌙';
		button.setAttribute( 'aria-pressed', theme === 'dark' ? 'true' : 'false' );
		button.setAttribute(
			'aria-label',
			theme === 'dark' ? 'Activer le mode clair' : 'Activer le mode sombre'
		);
	}

	function applyTheme( theme ) {
		root.setAttribute( 'data-theme', theme );
		updateButton( theme );
	}

	function toggleTheme() {
		var next = currentTheme() === 'dark' ? 'light' : 'dark';
		try {
			localStorage.setItem( STORAGE_KEY, next );
		} catch ( e ) {}
		applyTheme( next );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		button = document.createElement( 'button' );
		button.type = 'button';
		button.className = 'lsi-dark-mode-toggle';
		button.addEventListener( 'click', toggleTheme );
		document.body.appendChild( button );
		updateButton( currentTheme() );
	} );
} )();
