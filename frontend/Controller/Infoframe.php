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
class Infoframe extends \Controller {

    /**
     *
     */
    public function Index_Action() {

        $dir   = APP_DIR . DS . 'View' . DS . 'Infoframe';
        $frame = $this->app->params->get('frame');

        $config = $dir . DS . $frame . '.php';
        if (!file_exists($config)) $this->app->halt(400, 'Missing settings in '.$config);

        foreach (array('png', 'jpg', 'jpeg', 'gif') as $ext) {
            $file = $dir . DS . $frame . '.' . $ext;
            if (file_exists($file)) break;
            $file = NULL;
        }
        if (!$file) $this->app->halt(400, 'Missing image: '.$frame.'.(png|jpg|jpeg|gif)');

        $im = imagecreatefromstring(file_get_contents($file));
        if (!$im) $this->app->halt(400, 'Can\'t read image from '.$file);

        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $border = 3;

        foreach (include $config as $id=>$item) {
            $item = array_merge(
                array(
                    'guid'   => NULL,
                    'start'  => '-5minutes',
                    'period' => 'last',
                    'font'   => 5,
                    'label'  => '<Label '.$id.'?'.'>',
                    'x'      => 0,
                    'y'      => 0,
                ),
                $item
            );

            if ($item['guid']) {
                $value = \Channel::byGUID($item['guid'])
                       ->read(array('start'=>$item['start'], 'period'=>$item['period']))
                       ->rewind()
                       ->current();
                $label = isset($value['data']) ? sprintf($item['label'], $value['data']) : '';
            } elseif ($item['label'] == '{DATETIME}') {
                $label = date($this->config->get('Locale.DateTime'));
            } elseif ($item['label'] == '{TIME}') {
                $label = date($this->config->get('Locale.TimeShort'));
            } else {
                $label = $item['label'];
            }

            if ($label) {
                $font = $item['font'];
                $x = $item['x'];
                $y = $item['y'];
                imagefilledrectangle($im, $x-$border, $y-$border,
                                     $x+imagefontwidth($font)*strlen($label)+$border,
                                     $y+imagefontheight($font)+$border, $white);
                imagestring($im, $font, $x, $y, $label, $black);
            }
        }

        header('Content-Type: image/png');
        imagepng($im);
        exit;
    }
}
