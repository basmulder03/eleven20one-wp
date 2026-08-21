( function () {
	function pad( n ) {
		return String( n ).padStart( 2, '0' );
	}

	function formatDelta( ms ) {
		if ( ms <= 0 ) {
			return null;
		}
		var totalSeconds = Math.floor( ms / 1000 );
		var days = Math.floor( totalSeconds / 86400 );
		var hours = Math.floor( ( totalSeconds % 86400 ) / 3600 );
		var minutes = Math.floor( ( totalSeconds % 3600 ) / 60 );
		var seconds = totalSeconds % 60;
		return days + 'd ' + pad( hours ) + ':' + pad( minutes ) + ':' + pad( seconds );
	}

	function tick() {
		document.querySelectorAll( '.e120-countdown' ).forEach( function ( el ) {
			var showTs = parseInt( el.dataset.showTs, 10 );
			var saleTs = parseInt( el.dataset.saleTs, 10 );
			var now = Date.now();

			el.querySelectorAll( '.e120-countdown__timer' ).forEach( function ( timer ) {
				var target = timer.dataset.target === 'sale' ? saleTs : showTs;
				var clock = timer.querySelector( '.e120-countdown__clock' );
				var delta = formatDelta( target - now );
				clock.textContent = delta || 'Nu!';
			} );
		} );
	}

	tick();
	setInterval( tick, 1000 );
} )();
