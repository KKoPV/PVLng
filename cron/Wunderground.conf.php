// Put this snippet into your config/config.cron.php
// and fill in your s settings / GUIDs

// SNIP >>>

    array(

        /**
         * Common settings
         */
        'enabled' => TRUE,
        'Name'    => 'A meaningful name for debugging here...'
        'Handler' => 'Wunderground', // DON'T change!
        'RunEach' => 5, // Minutes

        /**
         * Sign up for a API key: http://www.wunderground.com/weather/api/
         */
        'APIKey'   => '',

        /**
         * Your language code; e.g. english: EN, german: DL
         * http://www.wunderground.com/weather/api/d/docs?d=resources/country-to-iso-matching
         */
        'Language' => 'EN',

        /**
         * GUID of your Wunderground group channel
         */
        'Channel' => ''

    ),

// <<< SNAP
