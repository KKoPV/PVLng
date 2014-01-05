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
    public function Index_Action() {
        // Switch layout
        $this->Layout = 'content';

        $this->app->ContentType('text/javascript');

        // Compress in any case!
        $this->config->set('View.Verbose', FALSE);

        $data = array();
        $time1 = $time2 = time();
        $max = -PHP_INT_MAX;

        try {

            $guid = $this->app->request->get('guid');

            if ($guid == '') throw new \Exception('Missing channel GUID!');

            $channel = \Channel::byGUID($guid);

            foreach ($channel->read(array('period'=>'10i')) as $row) {
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
            $this->view->Type    = $this->boolParam('area', FALSE) ? 'areaspline' : 'spline';
            $this->view->Time1   = date('H:i', $time1);
            $this->view->Time2   = date('H:i', $time2);
            $this->view->Data    = json_encode($data);
            $this->view->Max     = round($max, $channel->decimals);
            // Aplly content direct
            $this->view->Content = $this->view->render('chart.tpl');

        } catch (\Exception $e) {
            $this->view->Error = $e->getMessage();

            // Switch off compression for help text ...
            $this->config->set('View.Verbose', TRUE);

            $this->view->Content = "/*\n\n".$this->view->render('help.tpl')."\n\n*/";

            // ... and re-enable
            $this->config->set('View.Verbose', FALSE);
        }
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function strParam( $name, $default ) {
        $value = $this->app->request->get($name);
        return !is_null($value) ? $value : $default;
    }

    /**
     *
     */
    protected function intParam( $name, $default ) {
        $value = $this->app->request->get($name);
        return $value != '' ? (int) $value : (int) $default;
    }

    /**
     *
     */
    protected function boolParam( $name, $default ) {
        $value = strtolower(trim($this->app->request->get($name)));
        return $value != ''
             ? (preg_match('~^(?:true|on|yes|1)$~', $value) === 1)
             : $default;
    }
}
