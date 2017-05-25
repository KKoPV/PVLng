<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Frontend\Controller;

/**
 *
 */
use Frontend\Controller;
use ORM\ChannelType as ORMChannelType;
use I18N;

/**
 *
 */
class Type extends Controller
{
    /**
     *
     */
    public function indexAction()
    {
        $this->view->SubTitle = I18N::translate('ChannelTypes');

        $ORMType = new ORMChannelType;
        $ORMType->filter('id', ['min' => 1])->find();

        $types = [];

        foreach ($ORMType->asAssoc() as $type) {
            $type['description'] = I18N::translate($type['description']);
            $types[] = $type;
        }

        $this->view->Types = $types;
    }
}
