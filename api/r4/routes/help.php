<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */

/**
 *
 */
$api->any('/help', function() use ($api) {
    $content = array();
    foreach ($api->router()->getNamedRoutes() as $route) {
        $help = array_merge(array(
            'methods'     => implode(', ', $route->getHttpMethods()),
            'since'       => 'r1',
            'description' => $route->getName(),
            'apikey'      => 0
        ), $route->help);

        $pattern =  $route->getPattern();

        foreach ($route->getConditions() as $key=>$value) {
            if (strpos($pattern, ':'.$key) !== FALSE) $help['conditions'][$key] = $value;
        }

        $key = $api->request()->getRootUri() . $pattern;

        $content[$key] = $help;
    }

    ksort($content);

    $api->response->headers->set('Content-Type', 'application/json');
    $api->render($content);
})->name('help')->help = array(
    'since'       => 'r1',
    'description' => 'This help, overview of valid calls',
);
