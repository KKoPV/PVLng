// Put this snippet into your config/config.cron.php
// and fill in your s settings / GUIDs

// SNIP >>>

    array(

        /**
         * Common settings
         */
        'enabled' => TRUE, // (TRUE|FALSE|0) '0' runs only in test mode!
        'Name'    => 'A meaningful name for debugging here...'
        'Handler' => 'Wunderground', // DON'T change!
        'RunEach' => 5, // Minutes

        /**
         * Sign up for a API key: http://www.wunderground.com/weather/api/
         */
        'APIKey'   => '',

        /**
         * Your language code; english: EN, german: DL
         * http://www.wunderground.com/weather/api/d/docs?d=resources/country-to-iso-matching
         */
        'Language' => 'EN',

        'Channel' => array(
            /**
             * GUID of your 'Wunderground' group channel
             */
            'Actual'   => '',
            /**
             * GUID of your 'Sky cover forecast' channel
             */
            'Forecast' => ''
        )

    ),

// <<< SNAP
