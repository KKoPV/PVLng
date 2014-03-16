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
$api->get('/views(/:language)', function( $language='en' ) use ($api) {
    I18N::setLanguage($language);

    $result = $api->request->get('select')
            ? array( array(
                'name'   => '--- ' . I18N::_('Select') . ' ---',
                'data'   => NULL,
                'public' => 1,
                'slug'   => NULL
              ))
            : array();

    $views = new ORM\View;

    $q = new DBQuery('pvlng_view');

    if ($api->request->get('sort_by_visibilty')) $q->order('public');

    $q->order('name');

    foreach ($api->db->queryRowsArray($q) as $view) {
        if ($api->APIKeyValid == 1 OR $view['public'] == 1) {
            $result[] = $view;
        }
    }

    $api->render($result);
})->name('get views')->help = array(
    'since'       => 'v3',
    'description' => 'Fetch chart view data via slug, provide for Top select entry the correct language',
);

/**
 *
 */
$api->put('/view', function() use ($api) {
    $request = json_decode($api->request->getBody(), TRUE);

    if (empty($request['name'])) return;

    $view = new ORM\View;
    $view->name = $request['name'];
    $view->data = json_encode(($_=&$request['data']?:''));
    $view->public = ($_=&$request['public']?:0);

    $slug = Slug::encode($request['name']);
    if ($view->public == 2) {
        $slug .= '-mobile';
    }
    $view->slug = $slug;

    if (!$view->replace()) {
        $api->stopAPI('Insert failed', 400);
    }

    $api->render(array(
        'name' => $view->name,
        'slug' => $view->slug
    ));
})->name('create view data')->help = array(
    'since'       => 'v3',
    'description' => 'Create chart view data, return slug',
);

/**
 *
 */
$api->get('/view/:slug', function( $slug ) use ($api) {
    $view = new ORM\View;
    $view->findBySlug($slug);

    if ($view->name == '') {
        $api->stopAPI('No chart variant found for: '.$slug, 404);
    }
    if ($view->public == 0 AND $api->APIKeyValid == 0) {
        // Private channel and no API key given
        $api->stopAPI('Private chart variant', 403);
    }

    $api->render(json_decode($view->data, TRUE));
})->name('get view data')->help = array(
    'since'       => 'v3',
    'description' => 'Fetch chart view data via slug',
);

/**
 * @ToDo
 */
$api->post('/view/:slug', function( $slug ) use ($api) {


})->name('update view data')->help = array(
    'since'       => 'v3',
    'description' => 'Update chart view data via slug',
);

/**
 *
 */
$api->delete('/view/:slug', function( $slug ) use ($api) {
    $view = new ORM\View;
    $view->findBySlug($slug);

    if ($view->name == '') {
        $api->stopAPI('No chart variant found for: '.$slug, 404);
    }

    $view->delete();
    $api->status(204);
})->conditions(array(
    'slug' => '[@\w\d-]+'
))->name('delete view data')->help = array(
    'since'       => 'v3',
    'description' => 'Detele chart view data by slug',
);
