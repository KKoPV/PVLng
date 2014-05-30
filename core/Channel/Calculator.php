<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     1.0.0
 */
namespace Channel;

/**
 *
 */
class Calculator extends Channel {

    /**
     * Accept only childs of the same meter attribute and unit
     */
    public function addChild( $channel ) {
        $childs = $this->getChilds();
        if (empty($childs)) {
            // Add 1st child
            $child = parent::addChild($channel);
            // Adopt icon
            $self = new \ORM\Channel($this->entity);
            $new  = new \ORM\Channel($channel);
            if ($new->getType() == 0) {
                // Is an alias, get real channel
                $guid = $new->getChannel();
                $new = new \ORM\Tree;
                $new->filterByGuid($guid)->findOne();
            }
            $self->setMeter($new->getMeter())->setIcon($new->getIcon())->update();
        } else {
            // Check if the new child have the same type as the 1st (and any other) child
            $first = new \ORM\ChannelView($childs[0]->entity);
            $next  = new \ORM\ChannelView($channel);
            if ($first->getMeter() == $next->getMeter() AND $first->getUnit() == $next->getUnit()) {
                // ok, add new child
                $child = parent::addChild($channel);
            } else {
                $meter = $first->getMeter() ? 'meter' : 'sensor';
                \Messages::Error('"'.$this->name.'" accepts only '.meter.' channels with unit '.$first->getUnit(), 400);
                return;
            }
        }
        return $child;
    }

    /**
     *
     */
    public function read( $request ) {

        $this->before_read($request);

        $child = $this->getChild(1);

        // Get some properties from child
        $this->meter = $child->meter;

        // Simply pass-through
        return $this->after_read($child->read($request));
    }

}
