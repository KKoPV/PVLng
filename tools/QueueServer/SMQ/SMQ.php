<?php
/**
 * Simple Memcache Queue
 *
 * Idea from http://dev.ionous.net/2009/01/memcached-lockless-queue.html
 */
namespace SMQ;

/**
 *
 */
class SMQ {

    /**
     *
     */
    public function __construct( $id='SMQ', $host='localhost', $port=11211 ) {
        $this->mc = new \Memcache;

        if (!$this->mc->addServer($host, $port, TRUE)) {
            throw new \Exception('Unable to connect to '.$host.':'.$port);
        }

        // Unique queue name
        $this->queue = $id;

        // Cache key for write pointer
        $this->write = $this->key('>');

        // Cache key for read pointer
        $this->read  = $this->key('<');
    }

    /**
     *
     * @return integer|bool Returns write pointer Id on success or FALSE on failure
     */
    public function push( $data ) {
        // Ignore empty data sets
        if ($data == '') return;

        // Make sure, write pointer is initialized
        $this->getWrite();

        // Increment pointer to get the Id to work with
        $id = $this->mc->increment($this->write);

        return $this->mc->add($this->key($id), $data) ? $id : FALSE;
    }

    /**
     *
     * @return mixed
     */
    public function pull() {
        // No outstanding queue entry: return
        if ($this->getRead() >= $this->getWrite()) return;

        // Increment pointer to get the Id to work with
        $id = $this->mc->increment($this->read);

        $key    = $this->key($id);
        $result = $this->mc->get($key);
        $this->mc->delete($key);

        return $result;
    }

    /**
     * For debugging only
     */
    public function _getIds() {
        return array(
            $this->mc->get($this->write),
            $this->mc->get($this->read)
        );
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * @var \Memcache Memcache instance
     */
    protected $mc;

    /**
     * @var string Write pointer name
     */
    protected $write;

    /**
     * @var string Read pointer name
     */
    protected $read;

    /**
     * Build key name from queue name and key name
     *
     * @return string
     */
    protected function key( $key ) {
        return $this->queue.':'.$key;
    }

    /**
     * Check write and read pointer
     *
     * @return int
     */
    protected function getWrite() {
        $value = $this->mc->get($this->write);
        if ($value === FALSE) $this->mc->set($this->write, 0);
        return +$value; // Make FALSE to 0
    }

    /**
     * Check write and read pointer
     *
     * @return int
     */
    protected function getRead() {
        $value = $this->mc->get($this->read);
        if ($value === FALSE) $this->mc->set($this->read, 0);
        return +$value; // Make FALSE to 0
    }
}
