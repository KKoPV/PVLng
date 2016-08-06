<?php
/**
 *
 */
return array(

    /**
     * Standard definition for all not explicit named channels
     *
     * Please mind, that the aggregation minutes must follow this rule:
     * - The "next" minutes aggregation MUST be a multiple of the previous like this:
     *   1 > 5 > 10 > 30 > 60 > 360 > 1440 or
     *   1 > 5 > 15 > 30 > 60 ...
     *   5 > 30 > 60 ...
     *
     * Only then works the further aggregation of formerly aggregated readings!
     */
    STANDARD => array(
         '7d' =>   1, // older than  7 days   -  one reading per   1 minutes
         '1m' =>   5, // older than  1 month  -  one reading per   5 minutes
         '3m' =>  10, // older than  3 month  -  one reading per  10 minutes
         '1y' =>  30, // older than  1 year   -  one reading per  30 minutes
         '5y' =>  60, // older than  5 years  -  one reading per  60 minutes
        '10y' => 120, // older than 10 years  -  one reading per 120 minutes
    ),

    /**
     * To disable standard and only work on some further defined channels,
     * set STANDARD to NULL
     * /
    STANDARD => NULL,

    '5cc4-464b-ca31-53d0-feea-0bf4-4878-d699' => array(
         '7d' =>   1, // older than  7 days   -  one reading per   1 minutes
         '1m' =>   5, // older than  1 month  -  one reading per   5 minutes
         '3m' =>  10, // older than  3 month  -  one reading per  10 minutes
         '1y' =>  30, // older than  1 year   -  one reading per  30 minutes
         '5y' =>  60, // older than  5 years  -  one reading per  60 minutes
        '10y' => 120, // older than 10 years  -  one reading per 120 minutes (1 day)
    ),

    /**
     * To specify different ranges for a channel, define a section with the GUID
     * from channel view as key
     */
    '0000-0000-0000-0000-0000-0000-0000-0000' => array(
        '1m'  =>   5, // older than 1 month  -  one reading per  5 minutes
        '6m'  =>  15, // older than 6 month  -  one reading per 15 minutes
        '1y'  =>  60, // older than 1 year   -  one reading per 60 minutes
    ),

    /**
     * To exclude a channel, set the data to a non-array, e.g. NULL
     */
    '0000-0000-0000-0000-0000-0000-0000-0000' => NULL,

);
