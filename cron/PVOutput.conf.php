// Put this snippet into your config/config.cron.php
// and fill in your GUIDs

// SNIP >>>

    array(

        /**
         * Common settings
         */
        'enabled' => TRUE,
        'Name'    => 'A meaningful name for debugging here...'
        'Handler' => 'PVOutput', // DON'T change!
        'RunEach' => 5, // From system settings (5|10|15) minutes

        /**
         * PVOutput API key and System Id
         * Settings > Registered Systems
         * required
         */
        'APIKey'   => '',
        'SystemId' => 0000,

        'Channels' => array(

            // v1 - Energy Generation, watt hours
            1 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v2 - Power Generation, watts
            2 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v3 - Energy Consumption, watt hours
            3 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v4 - Power Consumption, watts
            4 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v5 - Temperature, celsius
            5 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v6 - Voltage, volts
            6 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            /**
             * Values 7 .. 12 are available in donation mode
             * http://pvoutput.org/help.html#donations
             */

            // v7 - Extended Value 1
            7 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v8 - Extended Value 2
            8 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v9 - Extended Value 3
            9 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v10 - Extended Value 4
            10 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v11 - Extended Value 5
            11 => array(
                'GUID'   => '',
                'factor' => 1,
            ),

            // v12 - Extended Value 6
            12 => array(
                'GUID'   => '',
                'factor' => 1,
            )
        )

    ),

// <<< SNAP
