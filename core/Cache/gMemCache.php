<?php
/**
 * A purely implementation of a MemCache client in PHP
 *
 * http://www.phpclasses.org/package/4094-PHP-memcache-client-in-pure-PHP.html
 *
 * Adjusted for PHP 5:
 *
 * @b CHANGED
 * - define -> const internal
 * - var -> private
 * - removed constructor, made connect() compatible to memcache
 *
 * @b NEW
 * - delete()
 * - flush()
 * - increment()
 * - decrement()
 *
 * @ingroup        Cache
 * @author         Cesar D. Rodas (saddor@cesarodas.com)
 * @author         Knut Kohl <knutkohl@users.sourceforge.net>
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version        1.0.0
 */

/* *************************************************************************
   ** gMemCache                                                            *
   ** Cesar D. Rodas (saddor@cesarodas.com)                                *
   *************************************************************************
   ** A purely implementation of a MemCache client in php.                 *
   ** With this class you could connect to a memcache server, store,       *
   ** get vars without download nothing more than this class.              *
   *************************************************************************
   ** Bugs Report at:                                                      *
   **         http://www.phclasses.org/gmemcache (in forums)               *
   *************************************************************************
   ** If you are a window$ user you get a port of memcache here            *
   ** http://jehiah.com/projects/memcached-win32                           *
   *************************************************************************
   ** The author disclaims the copyright of this project                   *
   ** You are legaly free to do what you want with this code               *
   ************************************************************************* */

class gMemCache
{

    const DISCONNECTED  = 0x00;
    const CONNECTED     = 0xF0;

    const IS_STRING     = 0x02;
    const IS_ARRAY      = 0x04;
    const IS_COMPRESSED = 0x08;

    const EOL           = "\r\n";

    /**
     * Connect to a memcache server.
     * On fail return FALSE.
     */
    public function connect($host = '127.0.0.1', $port = 11211)
    {
        if ($this->status == self::CONNECTED) {
            return false;
        }

        $this->host = $host;
        $this->port = $port;

        $this->status = self::DISCONNECTED;
        if ($this->host == '' || $this->port == '') {
            return false;
        }

        $this->socket = @fsockopen($this->host, $this->port);
        if ($this->socket !== false) {
            stream_set_timeout($this->socket, 2);
            $this->status = self::CONNECTED;
        }
        return ($this->status == self::CONNECTED);
    }

    /**
     *
     */
    public function isConnected()
    {
        return ($this->status == self::CONNECTED);
    }

    /**
     * Read the content from of $name from memcache
     * On fail return FALSE.
     */
    public function get($name)
    {
        if ($this->status != self::CONNECTED) {
            return false;
        }

        return $this->fetch('get "'.$name.'"');
    }

    /**
     * Set the var $name with the content $value
     * into the $lifetime seconds (forever=0; max = 2592000 [30 days])
     * Also can compress variables, for reduce network overhead.
     *
     * On fail return FALSE.
     */
    public function set($name, $value, $lifetime = 0, $compress = false)
    {
        if ($this->status != self::CONNECTED) {
            return false;
        }

        $magic = $this->getVarType($value);

        if ($magic == self::IS_ARRAY) {
            $value = serialize($value);
        }

        if ($compress) {
            $magic |= self::IS_COMPRESSED;
            $value = gzcompress($value);
        }

        $value = 'set "'.$name.'" '.$magic.' 0 '.strlen($value).' '.self::EOL
               . $value;

        return (trim($this->fetch($value)) == 'STORED');
    }

    /**
     *
     */
    public function delete($name)
    {
        if ($this->status != self::CONNECTED) {
            return false;
        }

        return (trim($this->fetch('delete "'.$name.'"')) == 'DELETED');
    }

    /**
     *
     */
    public function flush()
    {
        if ($this->status != self::CONNECTED) {
            return false;
        }

        return (trim($this->fetch('flush_all')) == 'OK');
    }

    /**
     *
     */
    public function increment($name, $value = 1)
    {
        if ($this->status != self::CONNECTED) {
            return false;
        }

        $ret = trim($this->fetch('incr '.$name.' '.$value));

        return ($ret != 'NOT_FOUND') ? $ret : null;
    }

    /**
     *
     */
    public function decrement($name, $value = 1)
    {
        if ($this->status != self::CONNECTED) {
            return false;
        }

        $ret = trim($this->fetch('decr '.$name.' '.$value));

        return ($ret != 'NOT_FOUND') ? $ret : null;
    }

    /**
     *
     */
    public function getStats()
    {
        if ($this->status != self::CONNECTED) {
            return false;
        }

        fwrite($this->socket, 'stats'.self::EOL);
        $buf = '';
        while ($c = fread($this->socket, 32)) {
            $buf .= $c;
        }

        $info = array();
        foreach (explode(self::EOL, $buf) as $value) {
            $value = explode(' ', $value, 3);
            if ($value[0] == 'STAT') {
                $info[$value[1]] = $value[2];
            }
        }

        return $info;
    }

    /**
     * Disconnect from a memcache server.
     * On fail return FALSE.
     */
    public function close()
    {
        if ($this->status != self::CONNECTED) {
            return false;
        }

        fclose($this->socket);
    }

    // -----------------------------------------------------------------------
    // PROTECTED
    // -----------------------------------------------------------------------

    protected $host;
    protected $port;
    protected $status;
    protected $socket;

    /**
     * This method return the type of the var.
     * The possible results are self::IS_ARRAY (need serialize)
     * or self::IS_STRING (do not need)
     */
    protected function getVarType(&$var)
    {
        switch (gettype($var)) {
            case 'array':
            case 'object':
                return self::IS_ARRAY;
            default:
                return self::IS_STRING;
        }
    }

    /**
     *
     */
    protected function fetch($data)
    {

        fwrite($this->socket, $data.self::EOL);

        $buf = '';
        while ($c = fread($this->socket, 2048)) {
            $buf .= $c;
            if (substr($c, -5, 3) == 'END') {
                break;
            }
        }
        $lines = explode(self::EOL, $buf, 2);

        if ($lines[0] == 'END') {
            return;
        }

        $parts = explode(' ', $lines[0]);
        if (count($parts) < 4) {
            return $lines[0];
        }

        $value = substr($lines[1], 0, $parts[3]);

        if ($parts[2] & self::IS_COMPRESSED) {
            $value = gzuncompress($value);
        }
        if ($parts[2] & self::IS_ARRAY) {
            $value = unserialize($value);
        }

        return $value;
    }
}
