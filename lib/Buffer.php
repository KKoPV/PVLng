<?php
/**
 * Memory buffer for results
 *
 * @author     Knut Kohl <github@knutkohl.de>
 * @copyright  2012-2016 Knut Kohl
 * @license    MIT License (MIT) http://opensource.org/licenses/MIT
 */
class Buffer implements Iterator, Countable
{

    /**
     * Use PHPs internal temp stream, use file for data greater 5 MB
     */
    public function __construct($data = array(), $size = 5)
    {
        $this->fh = fopen('php://temp/maxmemory:'.(1024 * 1024 * $size), 'w+');
        $this->rowCount = 0;
        foreach ($data as $key => $row) {
            $this->write($row, $key);
        }
        $this->rewind();
    }

    /**
     *
     */
    public function __destruct()
    {
        // Not yet closed
        if (is_resource($this->fh)) {
            fclose($this->fh);
        }
    }

    /**
     * Countable
     */
    public function count()
    {
        return $this->rowCount;
    }

    /**
     * Iterator
     */
    public function rewind()
    {
        rewind($this->fh);
        // NOT part of Iterator interface
        return $this->next();
    }

    /**
     * Iterator
     */
    public function valid()
    {
        return !empty($this->data);
    }

    /**
     * Iterator
     */
    public function key()
    {
        return $this->id;
    }

    /**
     * Iterator
     */
    public function current()
    {
        return $this->data;
    }

    /**
     * Iterator
     */
    public function next()
    {
        if ($data = $this->decode(fgets($this->fh))) {
            list($this->id, $this->data) = $data;
        } else {
            $this->id   = null;
            $this->data = array();
        }
        // NOT part of Iterator interface
        return $this;
    }

    /**
     *
     */
    public function write(array $data, $id = null)
    {
        // Skip empty data sets
        if (empty($data)) {
            return 0;
        }

        $this->rowCount++;

        return fwrite($this->fh, $this->encode($data, $id) . PHP_EOL);
    }

    /**
     *
     */
    public function size()
    {
        return fstat($this->fh)['size'];
    }

    /**
     *
     */
    public function last()
    {
        rewind($this->fh);

        $data = false;

        // Read all raw rows and remember last valid one
        while ($_ = fgets($this->fh)) {
            $data = $_;
        }

        if ($data = $this->decode($data)) {
            $data = $data[1];
        }

        return $data;
    }

    /**
     *
     */
    public function append(Buffer $buffer)
    {
        foreach ($buffer as $id => $row) {
            $this->write($row, $id);
        }
        return $this;
    }

    /**
     *
     */
    public function asArray()
    {
        $result = array();
        foreach ($this as $id => $row) {
            $result[$id] = $row;
        }
        return $result;
    }

    /**
     *
     */
    public function close()
    {
        fclose($this->fh);
        unset($this);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Separators for encoding/decoding row data
     */
    const SEP1 = "\x01";
    const SEP2 = "\x02";
    const NL   = "\x03";

    /**
     *
     */
    protected $fh;

    /**
     *
     */
    protected $id;

    /**
     *
     */
    protected $data;

    /**
     *
     */
    protected $rowCount;

    /**
     *
     */
    protected function encode($data, $id)
    {
        $keys   = implode(self::SEP2, array_keys($data));
        $values = implode(self::SEP2, array_values($data));
        // Mask newlines
        $values = str_replace(PHP_EOL, self::NL, $values);

        return $id . self::SEP1 . $keys . self::SEP1 . $values;
    }

    /**
     *
     */
    protected function decode($data)
    {
        if (!$data) {
            return;
        }

        list($id, $keys, $values) = explode(self::SEP1, trim($data));

        $keys = explode(self::SEP2, $keys);

        // Restore newlines
        $values = str_replace(self::NL, PHP_EOL, $values);
        $values = explode(self::SEP2, $values);

        return array($id, array_combine($keys, $values));
    }
}
