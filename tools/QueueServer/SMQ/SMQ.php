<?php
/**
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
    public function __construct( $id='SMQ' ) {
        $this->mc = new \Memcache;
        $this->queue = $id;
        $this->write = $this->key('!');
        $this->read  = $this->key('?');
    }

    /**
     *
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function addServer( $host, $port, $persistent=TRUE, $weight=100 ) {
        if (!$this->mc->addServer($host, $port, $persistent, $weight)) {
            throw new Exception('Unable to connect to '.$host.':'.$port);
        }

        if (count($this->mc->getExtendedStats()) > 1) return;

        // Added 1st correct server, init pointers
        if ($this->mc->get($this->write) === FALSE) {
            // Init aqctual write pointer with 0 if not yet exists
            $this->mc->set($this->write, 0);
        }

        if ($this->mc->get($this->read) === FALSE) {
            // Init last read pointer with 0 if not yet exists, 1st add() will create Id 1!
            $this->mc->set($this->read, 0);
        }
    }

    /**
     *
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function push( $data ) {
        // Ignore empty data sets
        if ($data == '')  return;

        // Increment pointer 1st to get the iId to work with
        $id = $this->mc->increment($this->write);

        return $this->mc->add($this->key($id), $data) ? $id : FALSE;
    }

    /**
     *
     */
    public function pull() {
        // Not outstanding queue entry
        if ($this->mc->get($this->read) >= $this->mc->get($this->write)) return;

        // Increment pointer 1st to get the iId to work with
        $id = $this->mc->increment($this->read);

        $key    = $this->key($id);
        $result = $this->mc->get($key);
        $this->mc->delete($key);

        return $result;
    }

    /**
     * For debugging only
     */
    public function getIds() {
        return array(
            $this->mc->get($this->write),
            $this->mc->get($this->read)
        );
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    /**
     * Memcache instance
     */
    protected $mc;

    /**
     *
     */
    protected $write;

    /**
     *
     */
    protected $read;

    /**
     *
     */
    protected function key( $key ) {
        return $this->queue.':'.$key;
    }

}
