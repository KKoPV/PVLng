<?php
/**
 * Default channel field settings
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2014 Knut Kohl
 * @license     MIT License (MIT) http://opensource.org/licenses/MIT
 * @version     1.0.0
 */
return array(

    /**
     * Possible keys:
     * - type        : (text|textarea|textextra|numeric|integer|select|sql), default text
     * - visible     : (TRUE|FALSE), default TRUE
     * - required    : (TRUE|FALSE), default FALSE
     * - readonly    : (TRUE|FALSE), default FALSE
     * - placeholder : Input placeholder, optional
     * - default     : Default value, works also for not visible attributes
     */

    'name' => array(
        'position'    => 10,
        'required'    => true
    ),

    'description' => array(
        'position'    => 20
    ),

    'serial' => array(
        'position'    => 30
    ),

    'channel' => array(
        'position'    => 40
    ),

    'resolution' => array(
        'position'    => 50,
        'type'        => 'numeric',
        'placeholder' => '0'.I18N::translate('TSEP').'000'.I18N::translate('DSEP').'00',
        'required'    => true,
        'default'     => 1
    ),

    'unit' => array(
        'position'    => 60,
        'type'        => 'textsmall',
    ),

    'decimals' => array(
        'position'    => 70,
        'type'        => 'integer',
        'placeholder' => '0',
        'default'     => 2
    ),

    'meter' => array(
        'position'    => 80,
        'type'        => 'boolean;1:yes;0:no',
        'default'     => 0
    ),

    'numeric' => array(
        'position'    => 90,
        'type'        => 'boolean;1:yes;0:no',
        'default'     => 1
    ),

    'offset' => array(
        'position'    => 100,
        'type'        => 'numeric',
        'placeholder' => '0'.I18N::translate('TSEP').'000'.I18N::translate('DSEP').'00',
    ),

    'adjust' => array(
        'position'    => 110,
        'type'        => 'boolean;1:yes;0:no',
        'default'     => 0
    ),

    'cost' => array(
        'position'    => 120,
        'type'        => 'numeric',
        'placeholder' => '0'.I18N::translate('TSEP').'000'.I18N::translate('DSEP').'00',
    ),

    'tariff' => array(
        'position'    => 130,
        'type'        => 'sql:'
                         // Add a blank 1st option (optional)
                       . 'X:'
                         // The SQL MUST return
                         // - as 1st field the key (saved as value)
                         // - as 2nd field the text to display
                         //
                         // Exclude tariffs without not yet maintained
                         // start date data sets
                       . 'SELECT `id`, `name`'
                       . '  FROM `pvlng_tariff`'
                       . '  JOIN `pvlng_tariff_time` USING(`id`)'
                       . ' GROUP BY `id` ORDER BY `name`'
    ),

    'threshold' => array(
        'position'    => 140,
        'type'        => 'numeric',
        'placeholder' => '0'.I18N::translate('TSEP').'000'.I18N::translate('DSEP').'00',
    ),

    'valid_from' => array(
        'position'    => 150,
        'type'        => 'numeric',
        'placeholder' => '0'.I18N::translate('TSEP').'000'.I18N::translate('DSEP').'00',
    ),

    'valid_to' => array(
        'position'    => 160,
        'type'        => 'numeric',
        'placeholder' => '0'.I18N::translate('TSEP').'000'.I18N::translate('DSEP').'00',
    ),

    // Channels which need "extra" have to show it and give them a proper type
    'extra' => array(
        'position'    => 500,
        'visible'     => false,
    ),

    'public' => array(
        'position'    => 900,
        'type'        => 'boolean;1:yes;0:no',
        'default'     => 1
    ),

    'icon' => array(
        'position'    => 905
    ),

    'tags' => array(
        'position'    => 907,
        'type'        => 'textarea',
    ),

    'comment' => array(
        'position'    => 910,
        'type'        => 'textarea',
    ),

);
