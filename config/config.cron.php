<?php

return [

    /**
     * For handler templates see cron/<handler>.config.php
     */
    [

        /**
         * Common settings
         */
        'enabled' => false, // (true|false|0) '0' runs only in test mode!
        'Name'    => 'SMA Webbox',
        'Handler' => 'Webbox', // DON'T change!
        'RunEach' => 1, // Minutes
        // Handler specific
        'Password'  => 'b6ks8g36m4xw',
        'Equipment' => [
            'WRTP2S89:2110211279' => '28b9-b390-e697-26c7-06e0-3791-8116-b9fc',
            'WRTP2889:2110276164' => 'aed0-2170-d4f0-63ea-ecfd-e58b-be49-314a',
            'SENS0500:7703'       => 'e6ad-a14d-63ef-8e6f-e036-bc94-6a36-b431',
        ]
    ],

    [
        'enabled' => true,
        'Name'    => 'Blockhaus',
        'Handler' => 'PVOutput',
        'RunEach' => 5, // Minutes
        // Handler specific
        'APIKey'   => 'b6321c98e6088928600a85258d31fa0305c22e1a',
        'SystemId' => 5241,
        'Channels' => [

            // v1 - Energy Generation, watt hours
            1 => [
                'GUID'   => '8797-e6b4-9560-7e75-744f-e3de-4bfe-ba4b',
                'factor' => 1,
            ],
            // v2 - Power Generation, watts
            2 => [
                'GUID'   => 'fc0d-af98-ab12-cf7b-021d-dac2-9284-b925',
                'factor' => 1,
            ],
            // v3 - Energy Consumption, watt hours
            3 => [
                'GUID'   => '1f0c-8bd8-8b82-cef4-f3bd-fd57-22be-3eb0',
                'factor' => 1000,
            ],
            // v4 - Power Consumption, watts
            4 => [
                'GUID'   => 'bf87-5e5e-ffc4-32d9-7dfd-58fd-a5ea-4242',
                'factor' => 1,
            ],
            // v5 - Temperature, celsius
            5 => [
                'GUID'   => '2ee2-1dd6-f45d-a6b4-1394-d8e3-66c9-a75b',
                'factor' => 1,
            ],
            // v6 - Voltage, volts
            6 => [
                'GUID'   => 'e377-49a8-99c0-a91d-3575-0d22-d87c-873f',
                'factor' => 1,
            ],
            /**
             * Values 7 .. 12 are available in donation mode
             * http://pvoutput.org/help.html#donations
             */
            // v7 - Extended Value 1
            // Irradiation
            7 => [
                'GUID'   => '1e1d-9e34-9e32-96aa-fa6a-7faa-f807-ab45',
                'factor' => 1,
            ],
            // v8 - Extended Value 2
            // Module temperature
            8 => [
                'GUID'   => 'a28c-e02f-fc9c-99a5-01c7-9162-8ae7-cd40',
                'factor' => 1,
            ],
            // v9 - Extended Value 3
            // Outside temperature
            9 => [
                'GUID'   => 'e78d-9435-1f6c-bd36-23de-182f-2e8e-ebf3',
                'factor' => 1,
            ],
            // v10 - Extended Value 4
            // Inverter temperature
            10 => [
                'GUID'   => '2ee2-1dd6-f45d-a6b4-1394-d8e3-66c9-a75b',
                'factor' => 1,
            ],
            // v11 - Extended Value 5
            11 => [
                'GUID'   => '',
                'factor' => 1,
            ],
            // v12 - Extended Value 6
            12 => [
                'GUID'   => '',
                'factor' => 1,
            ]
        ]
    ],

    // -------------------------------------------------------------------------
    [
        'enabled' => true,
        'Name'    => 'Carport',
        'Handler' => 'PVOutput',
        'RunEach' => 5, // Minutes
        // Handler specific
        'APIKey'   => 'b6321c98e6088928600a85258d31fa0305c22e1a',
        'SystemId' => 7367,
        'Channels' => [

            // v1 - Energy Generation, watt hours
            1 => [
                'GUID'   => '95a7-4af4-e338-92ed-2288-a5f8-ab8d-cbff',
                'factor' => 1,
            ],
            // v2 - Power Generation, watts
            2 => [
                'GUID'   => '1f4c-582c-c0d6-f454-8f43-fa89-2370-8468',
                'factor' => 1,
            ],
            // v5 - Temperature, celsius
            5 => [
                'GUID'   => '762f-c2af-39c5-3c01-9c42-c7d9-65a1-98d1',
                'factor' => 1,
            ],
            // v6 - Voltage, volts
            6 => [
                'GUID'   => '1166-89bd-97d7-b886-b7fc-acfb-e4d5-6c10',
                'factor' => 1,
            ],
            // v7 - Extended Value 1
            // Outside temperature
            7 => [
                'GUID'   => 'e78d-9435-1f6c-bd36-23de-182f-2e8e-ebf3',
                'factor' => 1,
            ],
            // v8 - Extended Value 2
            // Inverter temperature
            8 => [
                'GUID'   => '762f-c2af-39c5-3c01-9c42-c7d9-65a1-98d1',
                'factor' => 1,
            ],
            // v9 - Extended Value 3
            // Inside temperature
            9 => [
                'GUID'   => 'b00e-6b15-29a1-e8a3-41f9-529b-8d68-3bdb',
                'factor' => 1,
            ],
        ]
    ],

    [
        'enabled' => true,
        'Name'    => 'Wetter',
        'Handler' => 'Wunderground',
        'RunEach' => 20, // Minutes
        // Handler specific
        'Language' => 'DL',
        'Channel'  => '440c-1e94-b327-dc26-8c52-f78f-3fae-4741'
    ],
];
