<?php
/**
 * @author       Knut Kohl <github@knutkohl.de>
 * @copyright    2012-2014 Knut Kohl
 * @license      GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version      1.0.0
 */

/**
 *
 */
$api->any('/help', function() use ($api) {
    $content = array();

    foreach ($api->router()->getNamedRoutes() as $route) {
        $name    = $route->getName();
        $pattern = implode('|', $route->getHttpMethods()) . ' '
                 . $api->request()->getRootUri() . $route->getPattern();
        $help = $route->help;
        foreach ($route->getConditions() as $key=>$value) {
            if (strpos($pattern, ':'.$key) !== FALSE) $help['conditions'][$key] = $value;
        }
        $content[$pattern] = $help;
    }

    $api->response->headers->set('Content-Type', 'application/json');
    $api->render($content);
})->name('help')->help = array(
    'since'       => 'v1',
    'description' => 'This help, overview of valid calls',
);
