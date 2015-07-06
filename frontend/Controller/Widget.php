<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Controller;

/**
 *
 */
class Widget extends \Controller {

    /**
     *
     */
    public function Index_Action() {

        $app  = $this->app;
        $view = $this->view;

        $app->showStats = FALSE;
        $app->ContentType('text/javascript');

        // Compress in any case!
        $this->config->set('View.Verbose', FALSE);
        // Don't use layout, generate raw data
        $this->Layout = FALSE;

        // init parameter was provided, can be empty
        if ($app->request->get('init') !== NULL) {
            // Don't use a layout, render direct
            $app->render('init.js');
            $app->stop();
        }

        $view->UID     = mt_rand(100000, 999999);
        $view->Width   = $this->intParam('width', 320);
        $view->Height  = $this->intParam('height', 200);
        $view->Color   = $this->strParam('color', '#2F7ED8');
        $view->Link    = $this->strParam('link');
        // Need boolean parameters as integers, make numeric with +...
        $view->Labels  = +$this->boolParam('labels', TRUE);
        $view->Area    = +$this->boolParam('area', FALSE);

        // Real chart data
        $data = array();
        $time1 = $time2 = time();
        $max = -PHP_INT_MAX;

        try {

            // Throws Exception for empty/not existsing channel
            $channel = \Channel::byGUID($app->request->get('guid'));

            $request = array('period' => $this->intParam('period', 1).'i');

            foreach ($channel->read($request) as $row) {
                $data[] = array( $row['timestamp']*1000, +$row['data'] );
                $time1  = min($row['timestamp'], $time1);
                $max    = max($row['data'], $max);
            }
            // Last row
            if (isset($row)) $time2 = $row['timestamp'];

            if ($time1 == $time2) {
                // before 1st reading of day saved, at least 12 hours,
                // but max. until midnight
                $time2 = min($time2 + 12*60*60, strtotime('24:00'));
            }

            $view->Unit    = $channel->unit;
            $view->Time1   = date('H:i', $time1);
            $view->Time2   = date('H:i', $time2);
            $view->Data    = json_encode($data);
            $view->Max     = round($max, $channel->decimals);

        } catch (\Exception $e) {
          $view->error = $e->getMessage();
        }

        // Don't use a layout, render direct
        $app->render('chart.js');

    }
}
