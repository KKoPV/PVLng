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
    foreach ($api->router()->getNamedRoutes() as $route) {
        $pattern = $route->getPattern();

        $help = array(
            'pattern'     => $api->request()->getRootUri() . $pattern,
            'methods'     => implode(', ', $route->getHttpMethods()),
            'since'       => 'r1',
            'description' => $route->getName(),
            'apikey'      => 0
        );
        if (isset($route->help)) $help = array_merge($help, $route->help);

        foreach ($route->getConditions() as $key=>$value) {
            if (strpos($pattern, ':'.$key) !== FALSE) $help['conditions'][$key] = $value;
        }

        // Add methods to make array key unique
        $content[$api->request()->getRootUri() . $pattern . implode($route->getHttpMethods())] = $help;
    }

    ksort($content);

    // Preprend version
    array_unshift($content, array('version' => 'r4'));

    $api->response->headers->set('Content-Type', 'application/json');
    // Skip keys and render only the values
    $api->render(array_values($content));
})->name('help')->help = array(
    'since'       => 'r1',
    'description' => 'This help, overview of valid calls',
);

/**
 *
 */
$api->any('/helphtml', function() use ($api) {
    $content = array();

#    $body = '';

    foreach ($api->router()->getNamedRoutes() as $route) {
        $pattern = $route->getPattern();

        $help = array(
            'pattern'     => $api->request()->getRootUri() . $pattern,
            'methods'     => '['.implode('|', $route->getHttpMethods()).']',
            'since'       => 'r1',
            'description' => $route->getName(),
            'apikey'      => 0,
            'payload'     => array()
        );
        if (isset($route->help)) $help = array_merge($help, $route->help);

        foreach ($route->getConditions() as $key=>$value) {
            if (strpos($pattern, ':'.$key) !== FALSE) $help['conditions'][$key] = $value;
        }

        $content[$api->request()->getRootUri().$pattern.implode($route->getHttpMethods())] = $help;
    }

    ksort($content);
    $body = '<div style="float:right;padding:1em;border:dashed gray 1px">';

    foreach($content as $route=>$help) {
        $content[$route]['hash'] = $hash = substr(md5($route), -7);
        $body .= '<a href="#'.$hash.'">'.$help['pattern'].' <small>'.$help['methods'].'</small></a><br />';
    }
    $body .= '</div>'
           . '<h1>Version ' . 'r4' . '</h1>'
           . '<h2>Routes for '.$api->request()->getRootUri().'</h2>';

    foreach($content as $help) {
        $body .= '<a name="'.$help['hash'].'"></a><h3>'.$help['methods'].' '.$help['pattern'].'</h3>'
               . '<h4>'.$help['description'].'</h4>'
               . '<p>API key required: '.($help['description'] ? 'yes' : 'no').'</p>'
               . '<p>Available since: <tt>'.$help['since'].'</tt></p>';
        if (!empty($help['payload'])) {
            $body .= '<h4>Payload</h4>';
            if (is_array($help['payload'])) {
                foreach ($help['payload'] as $key=>$value) {
                    $body .= '- <strong>'.htmlspecialchars($key).'</strong> : '.$value.'<br/>';
                }
            } else {
                $body .= '<strong>'.htmlspecialchars($help['payload']).'</strong>';
            }
        }
    }

    // Force HTML content type
    $api->response->headers->set('Content-Type', 'text/html');
    $api->render(array(
        'title' => 'Routes for '.$api->request()->getRootUri(),
        'body'  => $body
    ));
})->name('help html')->help = array(
    'since'       => 'r4',
    'description' => 'This help in HTML for browsers, overview of valid calls',
);
