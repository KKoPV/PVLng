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
use Core\Messages;
use ORM\Channel as ORMChannel;
use ORM\Tree as ORMTree;

/**
 *
 */
class Calculator extends Channel
{

    /**
     * Accept only childs of the same meter attribute and unit
     */
    public function addChild($channel)
    {
        $childs = $this->getChilds(true);
        if (empty($childs)) {
            // Add 1st child
            if ($child = parent::addChild($channel)) {
                // Adopt meter
                $self = new ORMChannel($this->entity);
                $self->setMeter($this->getRealChannel($channel)->getMeter())->update();
            }
        } else {
            // Check if the new child have the same type and unit as the 1st (and any other) child
            $first = $childs[0];
            $next  = $this->getRealChannel($channel);
            if ($first->meter == $next->getMeter() && $first->unit == $next->getUnit()) {
                // ok, add new child
                $child = parent::addChild($channel);
            } else {
                $meter = $first->meter ? 'meter' : 'sensor';
                Messages::error('"'.$this->name.'" accepts only '.$meter.' channels with unit '.$first->unit, 400);
                return;
            }
        }
        return $child;
    }

    /**
     *
     */
    public function read($request)
    {
        $this->beforeRead($request);

        $child = $this->getChild(1);

        // Get some properties from child
        $this->meter = $child->meter;

        // Simply pass-through
        return $this->afterRead($child->read($request));
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected function getRealChannel($channel)
    {
        $channel = new ORMChannel($channel);
        if ($channel->getType() == 0) {
            // Is an alias, get real channel
            $guid = $channel->getChannel();
            $channel = new ORMTree;
            $channel->filterByGuid($guid)->findOne();
        }
        return $channel;
    }
}
