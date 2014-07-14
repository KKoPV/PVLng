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
        $help = array(
            'methods'     => implode(', ', $route->getHttpMethods()),
            'since'       => 'r1',
            'description' => $route->getName(),
            'apikey'      => 0
        );
        if (isset($route->help)) $help = array_merge($help, $route->help);

        $pattern =  $route->getPattern();

        foreach ($route->getConditions() as $key=>$value) {
            if (strpos($pattern, ':'.$key) !== FALSE) $help['conditions'][$key] = $value;
        }

        $content[$api->request()->getRootUri() . $pattern] = $help;
    }

    ksort($content);

    $api->response->headers->set('Content-Type', 'application/json');
    $api->render($content);
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
        $help = array(
            'methods'     => '['.implode('|', $route->getHttpMethods()).']',
            'since'       => 'r1',
            'description' => $route->getName(),
            'apikey'      => 0,
            'payload'     => array()
        );
        if (isset($route->help)) $help = array_merge($help, $route->help);

        $pattern =  $route->getPattern();

        foreach ($route->getConditions() as $key=>$value) {
            if (strpos($pattern, ':'.$key) !== FALSE) $help['conditions'][$key] = $value;
        }

        $content[$api->request()->getRootUri() . $pattern] = $help;
    }

    ksort($content);
    $body = '<div style="float:right;padding:1em;border:dashed gray 1px">';

    foreach($content as $route=>$help) {
        $body .= '<a href="#'.urlencode($route).'">'.$route.'</a><br />';
    }
    $body .= '</div>'
          . '<h2>Routes for '.$api->request()->getRootUri().'</h2>';

    foreach($content as $route=>$help) {
        $body .= '<a name="'.urlencode($route).'"></a><h3>'.$help['methods'].' '.$route.'</h3>'
               . '<h4>'.$help['description'].'</h4>'
               . '<p>API key required: '.($help['description'] ? 'yes' : 'no').'</p>'
               . '<p>Available since: <tt>'.$help['since'].'</tt></p>';
        if (!empty($help['payload'])) {
            $body .= '<h4>Payload</h4>';
          foreach ((array) $help['payload'] as $key=>$value) $body .= '- <strong>'.htmlspecialchars($key).'</strong> : '.$value.'<br/>';
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
