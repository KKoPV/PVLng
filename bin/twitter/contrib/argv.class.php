<?php
/**
 *
 */
class argv {

  /**
   *
   */
  public $HelpValid = 'Valid values';

  /**
   *
   */
  public $HelpRequired = 'This parameter is required';

  /**
   *
   */
  public $HelpDefault = 'default value';

  /**
   *
   */
  public $MsgRequired = 'ERROR: Parameter "%1$s" is required';

  /**
   *
   */
  public $MsgUnknownParameter = 'ERROR: Unknown parameter: %1$s';

  /**
   *
   */
  public $MsgInvalidRange = 'ERROR: Invalid value for parameter "%1$s": %2$s';

  /**
   *
   */
  public $MsgInvalid = 'ERROR: Parameter "%1$s": %2$s';

  /**
   *
   */
  public function __construct( $argv, $description='' ) {
    $this->self = array_shift($argv);
    $this->argv = $argv;
    $this->description = $description;
    $this->maxLong = 0;
    $this->params = array();
  }

  /**
   *
   */
  public function add( $short, $help, $long='', $required=FALSE, $valid=array()) {
    $required = (bool) $required;
    $this->args[$short] = array(
      'help'     => $help,
      'long'     => $long,
      'required' => (bool) $required,
      'valid'    => (array) $valid,
      // Set 1st entry in valid array as default if not required
      'value'    => (!$required AND !empty($valid)) ? reset($valid) : NULL,
      'validate' => array(),
    );
    $l = strlen($long);
    if ($l > $this->maxLong) $this->maxLong = $l;
    return $this;
  }

  /**
   *
   */
  public function strict( $strict=TRUE ) {
    $this->Strict = $strict;
    return $this;
  }

  /**
   *
   */
  public function validate( $short, $callback ) {
    if (isset($this->args[$short])) {
      $this->args[$short]['validate'][] = $callback;
      return $this;
    }
    throw new Exception('Parameter "' . $short . '" is not defined yet!');
  }

  /**
   *
   * @param int $exitOnError Set to 0 .. 255
   *                         If <> 0:
   *                         - Exit on error and
   *                         - Set return code
   */
  public function run( $exitOnError=1 ) {
    if ($this->ran) return;
    $ok = TRUE;

    foreach ($this->argv as $arg) {
    
      if (substr($arg, 0, 1) == '-') {
        // Named parameter
        if (substr($arg, 0, 2) == '--') {
          // Long parameter
          $arg = substr($arg, 2);
          if (strpos($arg, '=')) {
            list($arg, $value) = explode('=', $arg, 2);
          } else {
            $value = '';
          }
          foreach ($this->args as $key=>$data) {
            if ($data['long'] == $arg) {
              $arg = $key;
              // Handle as flag
              if ($value == '') $value = TRUE;
              break;
            }
          }
        } else {
          // Short parameter
          // From 3rd pos.: value
          $value = substr($arg, 2);
          // From 2nd pos: parameter
          $arg = substr($arg, 1, 1);
          // Handle as flag
          if ($value == '') $value = TRUE;
        }

        // Strict?
        if ($this->Strict AND !isset($this->args[$arg])){
          $this->errors[] = sprintf($this->MsgUnknownParameter, $arg);
          $ok = FALSE;
          continue;
        }

        // Check value
        if (!empty($this->args[$arg]['valid']) AND
            !in_array($value, $this->args[$arg]['valid'])) {
          $this->errors[] = sprintf($this->MsgInvalidRange, $arg, $value);
          $ok = FALSE;
        }

        // Set value, if ok
        if ($ok AND isset($this->args[$arg])) {
          $this->args[$arg]['value'] = $value;
        }
      } else {
        // Other parameter
        $this->params[] = $arg;
      }
    };

    foreach ($this->args as $arg=>$data) {
      // Check missing required parameters
      if ($data['required'] AND $data['value'] == '') {
        $this->errors[] = sprintf($this->MsgRequired, $arg);
        $ok = FALSE;
      }
      // Validate by methods defined in a subclass!
      $method = 'Validate_' . $arg;
      if (method_exists($this, $method) AND $err = $this->$method($data['value'])) {
        foreach ((array)$err as $msg) {
          $this->errors[] = sprintf($this->MsgInvalid, $arg, $msg);
        }
        $ok = FALSE;
      }
    }

    // Show help
    if (isset($this->args['h']) AND $this->args['h']['value'] == 'h') {
      die($this->helpText());
    }

    if (!$ok AND $exitOnError) {
      $this->help();
      exit($exitOnError);
    }

    $this->ran = TRUE;
    return $ok;
  }

  /**
   *
   */
  public function __get( $name ) {
    // Add. parameters
    if ($name == 'params') {
      return $this->params;
    }

    // Short parameter
    if (isset($this->args[$name])) {
      return $this->args[$name]['value'];
    }

    // Long parameter
    foreach ($this->args as $data) {
      if ($data['long'] == $name) {
        return $data['value'];
      }
    }
  }

  /**
   *
   */
  public function getAll() {
    $return = array();
    foreach ($this->args as $arg=>$data) {
      $return[$arg] = $data['value'];
    }
    $return['params'] = $this->params;
    return $return;
  }

  /**
   *
   */
  public function help() {
    echo $this->helpText();
  }

  /**
   *
   */
  public function helpText() {
    $indent = $this->maxLong + 11;
    // Usage...
    $help = array('');

    // Description
    if ($this->description) {
      $help[] = $this->description;
      $help[] = '';
    }

    $h = 'Usage: ' . $this->self;
    foreach ($this->args as $arg=>$data) if ($data['required']) $h .= ' -'.$arg.' ...';
    $help[] = $h.' [options] ...';

    // Errors
    if (!empty($this->errors)) {
      $help[] = '';
      $help = array_merge($help, $this->errors);
    }
    $help[] = '';
    foreach ($this->args as $short=>$data) {
      $h = '  -' . $short;
      // long parameter?
      if (!empty($data['long'])) $h .= ', --' . $data['long'];
      // pad until indent len
      $h .= str_pad(' ', $indent-strlen($h));
      // wordwrap help text
      $helptext = $data['help'];
      if (!$data['required'] AND !empty($data['valid'])) {
        $helptext .= ', ' . $this->HelpDefault . ': ' . reset($data['valid']);
      }
      $helptext = explode("\n", wordwrap($helptext, 79-$indent));
      // add 1st line to actual line
      $help[] = $h . array_shift($helptext);
      foreach ($helptext as $line) {
        // add additional lines
      	$help[] = str_repeat(' ', $indent) . $line;
      } ;
      // vaild values
      if (!empty($data['valid'])) {
      	$help[] = str_repeat(' ', $indent)
                . $this->HelpValid . ': ' . implode(', ',$data['valid']);
      }
      // required value?
      if ($data['required']) {
        $help[] = str_repeat(' ', $indent) . $this->HelpRequired;
      }
    }
    $help[] = '';

    return implode(PHP_EOL, $help);
  }

  // -------------------------------------------------------------------------
  // PROTECTED
  // -------------------------------------------------------------------------

  /**
   *
   */
  protected $args = array();

  /**
   *
   */
  protected $params;

  /**
   *
   */
  protected $self;

  // -------------------------------------------------------------------------
  // PRIVATE
  // -------------------------------------------------------------------------

  /**
   *
   */
  private $argv;

  /**
   *
   */
  private $maxLong = 0;

  /**
   *
   */
  private $Strict = FALSE;

  /**
   *
   */
  private $ran = FALSE;

  /**
   *
   */
  private $errors = array();

}
