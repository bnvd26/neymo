require("../scss/app.scss");

// any CSS you import will output into a single css file (app.css in this case)
import "../css/app.css";
const $ = require('jquery');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

console.log("Hello Webpack Encore! Edit me in assets/js/app.js");

$(document).ready(function () {
	$(".modal-trigger").click(function () {
		$(".modal").modal();
		url = $(this).attr("data-target");
		$.get(url, function (data) {
			$(".modal-body").html(data);
		});
	});
});
