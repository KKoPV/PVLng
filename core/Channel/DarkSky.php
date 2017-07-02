<?php
/**
 * Accept JSON data from Dark Sky API
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2017 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 */
namespace Channel;

/**
 *
 */
class DarkSky extends JSON
{
    /**
     * Overwrite and call multiple times parent::write()
     */
    public function write($request, $timestamp = null)
    {
        $ok = 0;

        if (isset($request['hourly']) && isset($request['hourly']['data'])) {
            foreach ($request['hourly']['data'] as $data) {
                $timestamp = $data['time'];
                unset($data['time']);
                $ok += parent::write($data, $timestamp);
            }
        }

        return $ok;
    }
}
