/*jslint white: true, browser: true, undef: true, nomen: true, eqeqeq: true, plusplus: false, bitwise: true, regexp: true, strict: true, newcap: true, immed: true, maxerr: 14 */
/*global window: false, REDIPS: true */

/* enable strict mode */
"use strict";


var redips_init,		// define redips_init variable
	toggle_animation,	// animation for shift option
	set_drop_option;	// function sets drop_option parameter

// redips initialization
redips_init = function () {
	// reference to the REDIPS.drag lib
	var rd = REDIPS.drag;
	// initialization
	rd.init();
	// set shift drop option
	rd.drop_option = 'switching';
	// enable animation on shifted elements
	rd.animation_shift = false;
};


// add onload event listener
if (window.addEventListener) {
	window.addEventListener('load', redips_init, false);
}
else if (window.attachEvent) {
	window.attachEvent('onload', redips_init);
}