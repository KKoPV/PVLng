<?php /* // AOP // */
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
class Settings extends \Controller {

    /**
     *
     */
    public function IndexPOST_Action() {
        if (!($data = $this->app->request->post('d'))) return;

        /// \Yryie::Debug($data);
        $tbl = new \ORM\Settings;
        $ok  = TRUE;

        // Handle password change separately
        $p1 = $data['p1'];  $p2 = $data['p2'];  unset($data['p1'], $data['p2']);

        $pwChanged = FALSE;

        if ($p1 != '' OR $p2 != '') {
            if ($p1 == $p2) {
                $data['core--Password'] = (new \PasswordHash)->HashPassword($p1);
                $pwChanged = TRUE;
            } else {
                \Messages::Error(__('PasswordsNotEqual'));
                $ok = FALSE;
            }
        }

        foreach ($data as $var=>$value) {
            list($scope, $name, $key) = explode('-', $var);
            if (!$tbl->checkValueType($scope, $name, $key, $value)) {
                \Messages::Error('Invalid value for'."\n".'"'.$name.': '.$tbl->getDescription() . '"');
                $ok = FALSE;
                continue;
            }
            $tbl->reset()
                ->filterByScopeNameKey($scope, $name, $key)
                ->findOne()
                ->setValue($value)
                ->update();
        }

        if ($ok) {
            \Messages::Success(__('DataSaved'));
            if ($pwChanged) {
                \Messages::Info(__('PleaseRelogin'));
                $this->app->redirect('/logout');
            }
            $this->app->redirect('/settings');
        }
    }

    /**
     *
     */
    public function Index_Action() {
        $this->view->SubTitle = __('Settings');
        $tbl = new \ORM\Settings;

        $data = array();
        $last = NULL;
        foreach ($tbl->order('scope')->order('name')->order('order')->find()->asAssoc() as $row) {
            $data[$row['scope']][$row['name']]['name'] = $row['name'];
            $row['var'] = $row['scope'].'-'.$row['name'].'-'.$row['key'];
            if ($row['type'] == 'bool') {
                // Transform to option
                $row['type'] = 'option';
                $row['data'] = '0:'.__('No').';1:'.__('Yes');
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
            if ($last !== $row['name']) {
                $last = $row['name'];
                $i = 1;
            }
            $row['class'] = ($i++ % 2) ? 'odd' : 'even';
            $row['value'] = htmlspecialchars($row['value']);
            $data[$row['scope']][$row['name']]['key'][] = $row;
        }
        /// \Yryie::Debug($data);
        $this->view->Core       = $data['core'];
        $this->view->Controller = $data['controller'];
        $this->view->Model      = $data['model'];
    }
}
