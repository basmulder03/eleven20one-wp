( function () {
	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}
	if ( ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	var targets = document.querySelectorAll(
		'main.wp-block-group > .wp-block-group, .wp-block-post-template > li, .e120-countdown, .e120-shows-list__item'
	);

	if ( ! targets.length ) {
		return;
	}

	targets.forEach( function ( el ) {
		el.classList.add( 'e120-reveal' );
	} );

	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-revealed' );
					observer.unobserve( entry.target );
				}
			} );
		},
		{ threshold: 0.15, rootMargin: '0px 0px -60px 0px' }
	);

	targets.forEach( function ( el ) {
		observer.observe( el );
	} );
} )();
