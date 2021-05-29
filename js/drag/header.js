/*jslint white: true, browser: true, undef: true, nomen: true, eqeqeq: true, plusplus: false, bitwise: true, regexp: true, strict: true, newcap: true, immed: true, maxerr: 14 */
/*global window: false, REDIPS: true */

/* enable strict mode */
"use strict";

// define header_init and default redips_url variable
var header_init,
	redips_url = redips_url || '/javascript/drag-and-drop-table-content/';

// add onload event listener
if (window.addEventListener) {
	window.addEventListener('load', header_init, false);
}
else if (window.attachEvent) {
	window.attachEvent('onload', header_init);
}