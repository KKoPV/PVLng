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
        $childs = $this->getChilds(TRUE);
        if (empty($childs)) {
            // Add 1st child
            $child = parent::addChild($channel);
            // Adopt meter and icon
            $self = new \ORM\Channel($this->entity);
            $new  = $this->getRealChannel($channel);
            $self->setMeter($new->getMeter())->setIcon($new->getIcon())->update();
        } else {
            // Check if the new child have the same type and unit as the 1st (and any other) child
            $first = $childs[0];
            $next  = $this->getRealChannel($channel);
            if ($first->meter == $next->getMeter() AND $first->unit == $next->getUnit()) {
                // ok, add new child
                $child = parent::addChild($channel);
            } else {
                $meter = $first->meter ? 'meter' : 'sensor';
                \Messages::Error('"'.$this->name.'" accepts only '.$meter.' channels with unit '.$first->unit, 400);
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

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function getRealChannel( $channel ) {
        $channel = new \ORM\Channel($channel);
        if ($channel->getType() == 0) {
            // Is an alias, get real channel
            $guid = $channel->getChannel();
            $channel = new \ORM\Tree;
            $channel->filterByGuid($guid)->findOne();
        }
        return $channel;
    }
}
