<?php
/**
 * PVLng - PhotoVoltaic Logger new generation (https://pvlng.com/)
 *
 * @link       https://github.com/KKoPV/PVLng
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */

/**
 *
 */
$api->any(
    '/help',
    function () use ($api) {
        $version = preg_replace('~.*/(.*?)/routes~', '$1', __DIR__);

        foreach ($api->router()->getNamedRoutes() as $route) {
            $pattern = $route->getPattern();

            $help = array(
            'pattern'     => str_replace('latest', $version, $api->request()->getRootUri()) . $pattern,
            'methods'     => implode(', ', $route->getHttpMethods()),
            'since'       => 'r1',
            'description' => $route->getName(),
            'apikey'      => 0
                );
            if (isset($route->help)) {
                $help = array_merge($help, $route->help);
            }

            foreach ($route->getConditions() as $key => $value) {
                if (strpos($pattern, ':'.$key) !== false) {
                    $help['conditions'][$key] = $value;
                }
            }

            // Add methods to make array key unique
            $content[$api->request()->getRootUri() . $pattern . implode($route->getHttpMethods())] = $help;
        }

        ksort($content);

        // Preprend version
        array_unshift($content, array('version' => $version));

        $api->response->headers->set('Content-Type', 'application/json');
        // Skip keys and render only the values
        $api->render(array_values($content));
    }
)
->name('ANY /help')
->help = array(
    'since'       => 'r1',
    'description' => 'This help, overview of valid calls',
);

/**
 *
 */
$api->any(
    '/helphtml',
    function () use ($api) {
        $version = preg_replace('~.*/(.*?)/routes~', '$1', __DIR__);
        $content = array();

        foreach ($api->router()->getNamedRoutes() as $route) {
            $pattern = $route->getPattern();

            $help = array(
            'uri'         => str_replace('latest', $version, $api->request()->getRootUri()) . $pattern,
            'pattern'     => substr($pattern, 1),
            'methods'     => '['.implode('|', $route->getHttpMethods()).']',
            'since'       => 'r1',
            'description' => $route->getName(),
            'apikey'      => 0,
            'header'      => array(),
            'payload'     => array()
                );
            if (isset($route->help)) {
                $help = array_merge($help, $route->help);
            }

            foreach ($route->getConditions() as $key => $value) {
                if (strpos($pattern, ':'.$key) !== false) {
                    $help['conditions'][$key] = $value;
                }
            }

            $content[$api->request()->getRootUri().$pattern.implode($route->getHttpMethods())] = $help;
        }

        ksort($content);
        $body = '<div style="float:right;margin-left:1em;padding:1em;border:dashed gray 1px;font-size:small">'
          . '<strong>Routes</strong><br /><br />';

        foreach ($content as $route => $help) {
            $content[$route]['hash'] = $hash = substr(md5($route), -7);
            $body .= '<a href="#'.$hash.'">'.$help['pattern'].' <small>'.$help['methods'].'</small></a><br />';
        }
        $body .= '</div><a name="top"></a><h2>Version '.$version.'</h2>';

        foreach ($content as $help) {
            $body .= '<fieldset style="margin-bottom:1em;border:solid gray 1px">'
               .' <legend style="padding:0 .5em">'
               . '<a name="'.$help['hash'].'"></a><h3><tt>'.$help['methods'].' '.$help['uri'].'</tt>'
               . '<small><a style="margin-left:.5em" href="#top" title="back to top">^</a></small></h3>'
               . '</legend>'
               . '<p>'.$help['description'].'</p><ul>'
               . '<li>API key required: <strong>'.($help['description'] ? 'yes' : 'no').'</strong></li>'
               . '<li>Available since: <strong>'.$help['since'].'</strong></li></ul>';
            if (!empty($help['header'])) {
                $body .= '<h4>Header</h4><dl>';
                foreach ($help['header'] as $key => $value) {
                    $body .= '<dt><strong>'.htmlspecialchars($key).'</strong></dt><dd>'.$value.'</dd>';
                }
                $body .= '</dl>';
            }
            if (!empty($help['payload'])) {
                $body .= '<h4>Payload</h4><dl>';
                if (is_array($help['payload'])) {
                    foreach ($help['payload'] as $key => $value) {
                        $body .= '<dt><strong>'.htmlspecialchars($key).'</strong></dt><dd>'.$value.'</dd>';
                    }
                } else {
                    $body .= '<dt><strong>'.htmlspecialchars($help['payload']).'</strong></dt>';
                }
                $body .= '</dl>';
            }
            $body .= '</fieldset>';
        }

        // Force HTML content type
        $api->response->headers->set('Content-Type', 'text/html');
        $api->render(array(
            'title' => 'Routes for '.$api->request()->getRootUri(),
            'body'  => $body
        ));
    }
)
->name('ANY /helphtml')
->help = array(
    'since'       => 'r4',
    'description' => 'This help in HTML for browsers, overview of valid calls',
);

/**
 *
 */
$api->any(
    '/help/apiary',
    function () use ($api) {
        $version = preg_replace('~.*/(.*?)/routes~', '$1', __DIR__);
        $sep = str_repeat('#', 80);

        $parameters = array(
            'guid'       =>   'Channel GUID',
            'timestamp'  => [ 'Timestamp', 'integer' ],
        );

        $content = array(
            'FORMAT: 1A',
            null,
            '# PVLng API '.$version,
        );

        $routes = array();
        foreach ($api->router()->getNamedRoutes() as $route) {
            $pattern = $route->getPattern();
            $group   = explode('/', $pattern);
            $pattern = str_replace('latest', $version, $api->request()->getRootUri()) . $pattern;
            $help = array(
                'since'       => 'r1',
                'description' => $route->getName(),
                'apikey'      => 0
            );
            if (isset($route->help)) {
                $help = array_merge($help, $route->help);
            }

            foreach ($route->getConditions() as $key => $value) {
                if (preg_match('~:'.$key.'([+(/]|$)~', $pattern)) {
                    $help['conditions'][$key] = $value;
                }
            }

            // Add methods to make array key unique
            foreach ($route->getHttpMethods() as $method) {
                if ($method != 'HEAD') {
                    $routes[trim($group[1], '(')][$pattern][$method] = $help;
                }
            }
        }

        ksort($routes);

        foreach ($routes as $group => $patterns) {
            $content[] = null;
            $content[] = $sep;
            $content[] = '# Group '.ucwords($group);
            $content[] = $sep;
            foreach ($patterns as $pattern => $methods) {
                $content[] = null;
                $content[] = $sep;
                $pattern = preg_replace_callback(
                '~:(\w+)([+]?)~',
                function ($p) {
                    return '{'.($p[2]?'+':'').$p[1].'}';
                },
                $pattern
                );
                $content[] = '## '.$pattern;
                foreach ($methods as $method => $route) {
                        $content[] = null;
                        $content[] = '### '.$route['description'].' ['.$method.']';

                    if (!empty($route['conditions'])) {
                        $content[] = '+ Parameters'."\n";

                        if (isset($route['parameters'])) {
                            $parameters = array_merge($parameters, $route['parameters']);
                        }

                        foreach ($route['conditions'] as $parameter => $regex) {
                            $param = isset($parameters[$parameter]) ? $parameters[$parameter] : ucfirst($parameter);
                            if (!is_array($param)) {
                                $param = [$param, 'string'];
                            }
                            $content[] = '    + '.$parameter .' ('.$param[1].') - '.$param[0];
                        }
                    }

                        $request = $headers = false;

                    if (!empty($route['payload'])) {
                        $content[] = '+ Request'."\n";
                        $request = true;
                        foreach ((array) $route['payload'] as $key => $desc) {
                            $content[] = '        '.$key.' - '.$desc;
                        }
                    }

                    if ($route['apikey']) {
                        if (!$request) {
                            $content[] = '+ Request'."\n";
                            $request = true;
                        }
                        if (!$headers) {
                            $content[] = '    + Headers'."\n";
                            $headers = true;
                        }
                        $content[] = '            X-PVLng-Key: Your API key';
                    }

                    if (!empty($route['header'])) {
                        if (!$request) {
                            $content[] = '+ Request';
                            $request = true;
                        }
                        if (!$headers) {
                            $content[] = '    + Headers'."\n";
                            $headers = true;
                        }
                        foreach ($route['header'] as $header => $desc) {
                            $content[] = '            '.$header.': '.$desc;
                        }
                    }

                    $content[] = '+ Response 200 (application/json)';
                }
            }
        }

        $api->response->headers->set('Content-Type', 'text/plain');

        $api->render(implode("\n", $content));
    }
)
->name('ANY /help/apiary')
->help = array(
    'since'       => 'r6',
    'description' => 'This help, overview of valid calls',
);
