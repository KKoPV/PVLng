<?php
/**
 *
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2014 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 * @version    1.0.0
 */
namespace Channel;

/**
 *
 */
class Channel extends \Channel {

    /**
     * Run additional code before existing data presented to user
     */
    public static function beforeEdit(\ORM\Channel $channel, Array &$fields)
    {
        if ($channel->type == 51) {
            // Precalcutaion of meter values for power sensor
            // is only avalable on channel creation
            $fields['extra']['READONLY'] = true;
        }
    }

}
