<?php
/**
 *
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */

namespace Core;

/**
 *
 */
class Language
{

    /**
     *
     */
    public function add($pos, $code, $label, $icon = null)
    {
        while (isset($this->lang[$pos])) {
            $pos++;
        }
        $this->lang[$pos] = array(
            'CODE'     => $code,
            'ICON'     => $icon ?: $code,
            'LABEL'    => $label
        );
    }

    /**
     *
     */
    public function get()
    {
        ksort($this->lang);
        return $this->lang;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     *
     */
    protected $lang = array();
}
