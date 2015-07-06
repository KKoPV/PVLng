<?php
/**
 * Magic class with array access for measuring data
 *
 * Copyright (c) 2015 PV-Log.com, Top50-Solar
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
namespace PVLog\Classes\Json;

/**
 * Magic class to handle the measuring data, the data are accessable like an array
 *
 * @implements  ArrayAccess
 * @implements  Countable
 * @implements  Iterator
 *
 * @author   Knut Kohl <kohl@top50-solar.de>
 * @license  http://opensource.org/licenses/MIT MIT License (MIT)
 * @version  PVLog JSON 1.1
 * @since    2015-03-14
 * @since    v1.0.0
 */
class Set extends Json implements \ArrayAccess, \Countable, \Iterator {

    // -----------------------------------------------------------------------
    // PUBLIC
    // -----------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @internal
     * @param array $data Data to build from
     */
    public function __construct( $data=array() ) {
        is_array($data) || $data = array('midnight' => $data);
        foreach ($data as $key=>$value) {
            $this[$key] = $value;
        }
    }

    /**
     * Add measuring value
     *
     * @param  string  $name Timestamp or datetime
     * @param  object  $data Measuring value
     * @return self    For fluid interface
     */
    public function add( $name, $data ) {
        $this[$name] = $data;
        return $this;
    }

    /**
     * Add measuring value
     *
     * @param  array|numeric $data Measuring value
     * @return self For fluid interface
     */
    public function set( $name, $data=NULL ) {
        if (is_array($name) && is_null($data)) {
            foreach ($name as $datetime=>$value) {
                $this[$datetime] = $value;
            }
        } else {
            $this->clear();
            $this[$name] = $data;
        }

        return $this;
    }

    /**
     * Count of data records
     *
     * @internal
     * @implements \Countable
     * @return integer
     */
    public function clear() {
        $this->data = array();
        return $this;
    }

    /**
     * Count of data records
     *
     * @internal
     * @implements \Countable
     * @return integer
     */
    public function count() {
        return count($this->data);
    }

    /**
     * Interface ArrayAccess
     *
     * @internal
     * @implements \ArrayAccess
     * @throws InvalidArgumentException
     * @param  string|integer $datetime Timestamp
     * @param  float $value Value
     */
    public function offsetSet( $datetime, $value ) {
        if (is_null($datetime)) {
            throw new \InvalidArgumentException(
                'Can\'t add a value without date time/timestamp to '.
                __NAMESPACE__.'\\'.__CLASS__
            );
        } else {
            $this->data[Helper::asTimestamp($datetime)] = +$value;
        }
    }

    /**
     * Interface ArrayAccess
     *
     * @internal
     * @implements \ArrayAccess
     * @param  string|integer $datetime Timestamp
     */
    public function offsetExists( $datetime ) {
        return isset($this->data[Helper::asTimestamp($datetime)]);
    }

    /**
     * Interface ArrayAccess
     *
     * @internal
     * @implements \ArrayAccess
     * @param  string|integer $datetime Timestamp
     */
    public function offsetUnset( $datetime ) {
        unset($this->data[Helper::asTimestamp($datetime)]);
    }

    /**
     * Interface ArrayAccess
     *
     * @internal
     * @implements \ArrayAccess
     * @param  string|integer $datetime Timestamp
     */
    public function offsetGet( $datetime ) {
        $datetime = Helper::asTimestamp($datetime);
        return array_key_exists($datetime, $this->data)
             ? $this->data[$datetime]
             : NULL;
    }

    /**
     * Before the first iteration of the loop, Iterator::rewind() is called
     *
     * @internal
     * @implements \Iterator
     */
    function rewind() {
        $this->position = 0;
        // Prepare keys
        ksort($this->data);
        $this->keys = array_keys($this->data);
    }

    /**
     * @internal
     * @implements \Iterator
     */
    function current() {
        return $this->data[$this->keys[$this->position]];
    }

    /**
     * @internal
     * @implements \Iterator
     */
    function key() {
        return $this->keys[$this->position];
    }

    /**
     * @internal
     * @implements \Iterator
     */
    function next() {
        ++$this->position;
    }

    /**
     * @internal
     * @implements \Iterator
     */
    function valid() {
        if (isset($this->keys[$this->position])) {
            return TRUE;
        }
        // Free keys memory
        $this->keys = NULL;
        return FALSE;
    }

    /**
     * Return last data entry
     *
     * @return numeric
     */
    function last() {
        $data = array_values($this->data);
        return array_pop($data);
    }

    /**
     * Return last data entry
     *
     * @return numeric
     */
    function sort() {
        ksort($this->data);
        return $this;
    }

    /**
     * Interpolate missing data
     *
     * @internal
     * @return self For fluid interface
     */
    public function interpolate() {
        // Skip for empty data set
        if (!count($this->data)) return;

        // Work on a copy of original data
        $data = array();

        // Recalc timestamps to PV-Log timestamps
        foreach ($this->data as $timestamp=>$value) {
            // Into PV-Log timestamp
            $data[floor($timestamp / 300) * 300] = $value;
        }

        ksort($data);

        // Interpolate data
        $timestamps = array_keys($data);
        $last = array_shift($timestamps);

        // Prepare new data array, with 1st data set as is
        $this->data = array($last => $data[$last]);

        // Apply all other data sets
        foreach ($timestamps as $timestamp) {
            $diff = $timestamp - $last;

            if ($diff > 300) {
                // Calculate delta between
                $delta = ($data[$timestamp] - $data[$last]) / $diff * 300;
                // Remember base value to apply deltas to
                $base = $data[$last];

                // Interpolate values between
                $i = 1;
                while ($last < $timestamp) {
                    $last += 300;
                    $this->data[$last] = $base + $delta * $i++;
                }
            }

            $this->data[$timestamp] = $data[$timestamp];
            $last = $timestamp;
        }

        return $this;
    }

    /*
     * Overloaded
     */
    public function asArray( $flags=0 ) {
        // Work on a copy of data
        $data = array();

        if ($flags & self::DATETIME) {
            // Transform timestamps to datetimes
            // Buffer datetime format :-)
            $format = Helper::getDateFormat();
            foreach ($this->data as $timestamp=>$value) {
                $data[date($format, $timestamp)] = $value;
            }
        } else {
            // Return data as is
            $data = $this->data;
        }

        if (!($flags & self::INTERNAL)) {
            // Remove leading 0 values
            foreach ($data as $timestamp=>$value) {
                // Break loop on 1st non 0 value
                if ($value) break;
                unset($data[$timestamp]);
            }

            // Remove trailing 0 values
            // Reverse array, it is easier to delete leading data
            foreach (array_reverse($data, TRUE) as $timestamp=>$value) {
                // Break loop on 1st non 0 value
                if ($value) break;
                unset($data[$timestamp]);
            }
        }

        return $data;
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * @internal
     * @var int Array position for itterator
     */
    protected $position;

    /**
     * @internal
     * @var array Remember timestamp positions
     */
    protected $keys;

}
