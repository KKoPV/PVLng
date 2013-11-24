<?php
/**
 * Place for custom routes definitions
 *
 * Available Route Middleware:
 *
 */
$app->get('/your/route/:name', function($name) use ($app) {

	// Build your response, best as array for the different content types
	$response = array('Called route: /your/route/'.$name);

	// Render the response
	$app->render($response);

});