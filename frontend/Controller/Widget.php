<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
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
    public function before() {
        parent::before();

        $this->app->ContentType('text/javascript');

        // Switch layout
        $this->Layout = 'widget';

        // Compress in any case!
        $this->config->set('View.Verbose', FALSE);
    }

    /**
     *
     */
    public function Inc_Action() {
        // Aplly content direct
        $this->view->Content = $this->view->render('inc.js');
    }

    /**
     *
     */
    public function Chart_Action() {

        $data = array();
        $time1 = $time2 = time();
        $max = -PHP_INT_MAX;

        try {

            $guid = $this->app->request->get('guid');

            if ($guid == '') throw new \Exception('Missing channel GUID!');

            $channel = \Channel::byGUID($guid);

            $period = $this->intParam('period', 0);
            if ($period != 0) $period .= 'i';

            foreach ($channel->read(array('period'=>$period)) as $row) {
                $data[] = array( $row['timestamp']*1000, +$row['data'] );
                $time1  = min($row['timestamp'], $time1);
                $max    = max($row['data'], $max);
            }
            // Last row
            if (isset($row)) $time2 = $row['timestamp'];

            $this->view->GUID    = $channel->guid;
            $this->view->Unit    = $channel->unit;
            $this->view->Width   = $this->intParam('width', 320);
            $this->view->Height  = $this->intParam('height', 200);
            $this->view->Color   = $this->strParam('color', '#2F7ED8');
            $this->view->Labels  = $this->boolParam('labels', TRUE);
            $this->view->Area    = $this->boolParam('area', FALSE);
            $this->view->Time1   = date('H:i', $time1);
            $this->view->Time2   = date('H:i', $time2);
            $this->view->Data    = json_encode($data);
            $this->view->Max     = round($max, $channel->decimals);
            // Aplly content direct
            $this->view->Content = $this->view->render('chart.js');

        } catch (\Exception $e) {
            $this->view->Content = $e->getMessage();
        }
    }
}
