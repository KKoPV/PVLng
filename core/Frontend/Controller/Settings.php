<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
namespace Frontend\Controller;

/**
 *
 */
use Frontend\Controller;
use Core\Messages;
use ORM\Settings as ORMSettings;
use BabelKitMySQLi;
use I18N;
use PasswordHash;
use Yryie;

/**
 *
 */
class Settings extends Controller
{
    /**
     *
     */
    public function indexPostAction()
    {
        if (!($data = $this->app->request->post('d'))) {
            return;
        }

        /// Yryie::Debug($data);
        $tbl = new ORMSettings;
        $ok  = true;

        // Handle password change separately
        $p1 = $data['p1'];
        $p2 = $data['p2'];
        unset($data['p1'], $data['p2']);

        $pwChanged = false;

        if ($p1 != '' || $p2 != '') {
            if ($p1 == $p2) {
                $data['core--Password'] = (new PasswordHash)->HashPassword($p1);
                $pwChanged = true;
            } else {
                Messages::error(I18N::translate('PasswordsNotEqual'));
                $ok = false;
            }
        }

        foreach ($data as $var => $value) {
            list($scope, $name, $key) = explode('-', $var);
            if (!$tbl->checkValueType($scope, $name, $key, $value)) {
                Messages::error('Invalid value for'."\n".'"'.$name.': '.$tbl->getDescription() . '"');
                $ok = false;
                continue;
            }
            $tbl->reset()
                ->filterByScopeNameKey($scope, $name, $key)
                ->findOne()
                ->setValue($value)
                ->update();
        }

        if ($ok) {
            Messages::success(I18N::translate('DataSaved'));
            if ($pwChanged) {
                Messages::info(I18N::translate('PleaseRelogin'));
                $this->app->redirect('/logout');
            }
            $this->app->redirect('/settings');
        }
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->view->SubTitle = I18N::translate('Settings');

        $t = BabelKitMySQLi::getInstance()
             ->full_set_assoc('settings', $this->app->Language);

        $ORMSettings = new ORMSettings;
        $ORMSettings->order('scope,name,order')->find();

        $data = array();
        $last = null;

        foreach ($ORMSettings->asAssoc() as $row) {
            $data[$row['scope']][$row['name']]['name'] = $row['name'];

            if ($row['type'] == 'bool') {
                // Transform to option
                $row['type'] = 'option';
                $row['data'] = '0:'.I18N::translate('No').';1:'.I18N::translate('Yes');
            }
            if ($row['type'] == 'option') {
                preg_match_all('~([^:;]+):([^:;]+)~i', $row['data'], $matches, PREG_SET_ORDER);
                $row['data'] = array();
                foreach ($matches as $option) {
                    $row['data'][] = array(
                        'VALUE'    => trim($option[1]),
                        'TEXT'     => trim($option[2]),
                        'SELECTED' => (trim($option[1]) == $row['value'])
                    );
                }
            }

            $row['var']   = $row['scope'].'-'.$row['name'].'-'.$row['key'];
            $row['value'] = htmlspecialchars($row['value']);

            // Translate
            $k = $row['scope'].'_'.$row['name'].'_'.$row['key'];
            $row['description'] = isset($t[$k][0]) ? $t[$k][0] : $k;

            if ($last !== $row['name']) {
                $last = $row['name'];
                $i = 1;
            }
            $row['class'] = ($i++ % 2) ? 'odd' : 'even';

            $data[$row['scope']][$row['name']]['key'][] = $row;
        }

        /// Yryie::Debug($data);
        $this->view->Core       = $data['core'];
        $this->view->Controller = $data['controller'];
        $this->view->Model      = $data['model'];
    }
}
