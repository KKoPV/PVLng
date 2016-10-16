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
$api->get(
    '/views(/:language)',
    function($language='en') use ($api)
{
    $result = array();

    if ($api->request->get('select')) {
        I18N::setLanguage($language);
        $result[] = array(
            'name'   => '--- ' . __('Select') . ' ---',
            'data'   => null,
            'public' => 1,
            'slug'   => null
        );
    }

    if ($api->request->get('empty')) {
        $result[] = array(
            'name'   => null,
            'data'   => null,
            'public' => 1,
            'slug'   => null
        );
    }

    $noData = $api->request->get('no_data');
    $type   = $api->request->get('type');

    $views = new ORM\View;

    if ($api->request->get('sort_by_visibilty')) {
        $views->order('public');
    }

    if ($type = $api->request->get('type')) {
        $views->filterByPublic($type);
    }

    foreach ($views->order('name')->find() as $view) {
        if ($api->APIKeyValid == 1 || $view->getPublic() == 1) {
            $data = $view->asAssoc();
            if ($noData) unset($data['data']);
            $result[] = $data;
        }
    }

    $api->render($result);
})->name('get views')->help = array(
    'since'       => 'r3',
    'description' => 'Fetch all charts',
);

/**
 *
 */
$api->put(
    '/view',
    $APIkeyRequired,
    function() use ($api)
{
    $request = json_decode($api->request->getBody(), TRUE);

    if (empty($request['name'])) return;

    $name   = $request['name'];
    $public = $_=&$request['public'] ?: 0;
    $data   = $_=&$request['data'] ?: '';

    $view = new ORM\View;
    $view->setName($name)
         ->setData(json_encode($data))
         ->setPublic($public);

    if (!$view->replace()) {
        $api->stopAPI('Insert failed', 400);
    }

    // Reload generated slug
    $view->filterByName($name)->filterByPublic($public)->findOne();
    $api->render(array(
        'name' => $view->getName(),
        'slug' => $view->getSlug()
    ));
})->name('create view data')->help = array(
    'since'       => 'r3',
    'description' => 'Create chart view data, return slug',
);

/**
 *
 */
$api->get(
    '/view/:slug',
    function($slug) use ($api)
{
    $view = new ORM\View;
    $view->filterBySlug($slug)->findOne();

    if ($view->getName() == '') {
        $api->stopAPI('No chart found for: '.$slug, 404);
    }

    if ($view->getPublic() == 0 AND $api->APIKeyValid == 0) {
        // Private channel and no API key given
        $api->stopAPI('Private chart variant', 403);
    }

    $api->render(json_decode($view->getData(), TRUE));
})->conditions(array(
    'slug' => '[\w\d-]+'
))->name('get view data')->help = array(
    'since'       => 'r3',
    'description' => 'Fetch chart view data via slug',
);

/**
 *
 */
$api->delete(
    '/view/:slug',
    $APIkeyRequired,
    function($slug) use ($api)
{
    $view = new ORM\View;
    $view->filterBySlug($slug)->findOne();

    if ($view->getName() == '') {
        $api->stopAPI('No chart found for: '.$slug, 404);
    }

    $view->delete();
    $api->status(204);
})->conditions(array(
    'slug' => '[\w\d-]+'
))->name('delete view data')->help = array(
    'since'       => 'r3',
    'description' => 'Detele chart view data by slug',
    'apikey'      => TRUE,
);
