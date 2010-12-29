<?php
/**
 *
 *  Class Logger_Common - common packege for all log types
 *
 *  @author:    Anton Morozov
 *  @email:     anton@antonfin.kiev.ua
 *  @copyright	(c) 2009 by Anton Morozov
 *  @version:   0.01
 *
 */

abstract class Logger_Common
{
    /*
     *  Standart trace indent
     */

    const STANDART_LOGGER_TRACE = 2;
    /**
     *  List objects
     *      @var        array
     *      @access     public
     */
    static public $LOGS = array();

    /**
     *  $level2id   - hash, where level type are keys and logger type id are values
     *      @var        array
     *      @access     private
     */
    static public $level2id = array(
        'debug' => DEBUG,
        'info'  => INFO,
        'warn'  => WARN,
        'error' => ERROR,
        'fatal' => FATAL
    );

    /**
     *  $id2level   - hash, where keys - level id and values - level name 
     *      @var        array
     *      @access     private
     */
    static public $id2level = array( 
        DEBUG   => 'debug', 
        INFO    => 'info', 
        WARN    => 'warn', 
        ERROR   => 'error', 
        FATAL   => 'fatal' 
    );

    /**
     *  $space  - space for dumper
     *      @var        int
     *      @access     protected
     */
    protected $space = 2;

    /**
     *  $log_format     - log format, allows for a very flexible format in "printf"-style. The 
     *                  format string can contain a number of placeholders which will be replaced 
     *  by the logging engine when it's time to log the message:
     *
     *      %C Fully qualified package (or class) name of the caller
     *      %d Current date in yyyy/MM/dd hh:mm:ss format
     *      %t timer in ss.ssss format
     *      %F File (full path) where the logging event occurred
     *      %f File (name only) where the logging event occurred
     *      %H Hostname
     *      %L Line number within the file where the log statement was issued
     *      %m The message to be logged
     *      %m{chomp} The message to be logged, stripped off a trailing newline
     *      %M Method or subroutine where the logging request was issued
     *      %n Newline (OS-independent)
     *      %p Priority of the logging event
     *      %P pid of the current process
     *      %T A stack trace of functions called
     *      %% A literal percent (%) sign
     *
     */
    protected $log_format = '%d %p - %f:%M:%L - %m';

    /**
     *  $default_level      - default level (used for log method if level doesn't set)
     *      @var        int
     *      @access     protected
     */
    protected $default_level = DEBUG;

    /**
     *  $min_log_level      - minimal level for show
     *      @var        int
     *      @access     protected
     */
    protected $min_log_level = DEBUG;

    /**
     *  $add_var    - added Var"N" - text before each elements 
     *      @var        bool
     *      @access     protected
     */
    protected $add_var = true;

    /**
     *  $last_log   - the last logger message
     *      @var        string
     *      @access     public
     */
    public $last_log;

    /**
     *  $trace_depth - when we parse trace for search where was call logger, you my define ignore 
     *  trace depth. This property was effective if you want use wrappers. For example:
     *      <code>
     *          Logger::get_logger( array( 
     *                  'use'       => array( 'LOGGER' ),
     *                  'common'    => array( 'trace_depth' => 1, 'add_var' => false ),
     *                  'appenders' => array(
     *                      'LOGGER' => array( 'type' => 'stdout', log_format => '%M:%m' )
     *                  )
     *              ) );
     *
     *          function l_debug(){
     *              Logger::get_logger()->debug( func_get_args() );
     *          }
     *
     *          function l_info(){
     *              Logger::get_logger()->info( func_get_args() );
     *          }
     *
     *          function l_warn(){
     *              Logger::get_logger()->warn( func_get_args() );
     *          }
     *
     *          function l_error(){
     *              Logger::get_logger()->error( func_get_args() );
     *          }
     *
     *          function l_fatal(){
     *              Logger::get_logger()->fatal( func_get_args() );
     *          }
     *
     *          function logger(){
     *              Logger::get_logger()->log( func_get_args() );
     *          }
     *
     *          function some_function(){
     *              // do some think
     *              logger( 'Test', 'AAA' );
     *              // in stdout was write: some_function:array( 0 => 'Test', 1 => 'AAA' )', but not: 'logger:array( 0 => 'Test', 1 => 'AAA' )
     *          }
     *
     *      </code>
     *
     *      @var        int
     *      @access     protected
     */
    protected $trace_depth = self::STANDART_LOGGER_TRACE;

    /**
     *  $place  - class or script name (base file name ( basename(__FILE__) ) or full file 
     *          path ( __FILE__ )) where logger will be work
     *      @var        string
     *      @access     protected
     */
    protected $place = NULL;

    /**
     *  Abstract methods: set_priv_config and write. This methods must be in eche classes.
     */
    abstract public function set_priv_config( $config );
    abstract public function write( $level );

    /**
     *  Object = Logger_Common::appender( string $class, string $appender_name, array $config )   - 
     *      return Log object for appender list. If object doesn't exist create new object, else 
     *      update older object
     *
     *      @param      string      $class      - logger class
     *      @param      string      $name       - appender name
     *      @param      array       $config     - configuration
     *      @return     object      $log        - some log object
     *      @access     public
     *
     */
    static public function appender( $class, $name, $config ){

        if( ! isset(self::$LOGS[ $name ]) or ! self::$LOGS[ $name ] ) {
            self::$LOGS[ $name ] = new $class();
        }

        self::$LOGS[ $name ]->set_config( $config );

        return self::$LOGS[ $name ];
    }

    /**
     *  void destroy()      - destoy all objects
     *      @access     public
     */
    static public function destroy(){ self::$LOGS = array(); }

    /**
     *  void $this->set_config( array $config )    - set config
     *      @param      array       $config         - configuration parameters
     */
    public function set_config( $config ){

        if( isset( $config['log_format']    ) ) $this->log_format    = (string) $config['log_format'];
        if( isset( $config['add_var']       ) ) $this->add_var       = (bool)   $config['add_var'];
        if( isset( $config['space']         ) ) $this->space         = (int) $config['space'];
        if( isset( $config['default_level'] ) ) $this->default_level = (int) $config['default_level'];
        if( isset( $config['min_log_level'] ) ) $this->min_log_level = self::$level2id[ strtolower( $config['min_log_level'] ) ];
        if( isset( $config['trace_depth']   ) ) $this->trace_depth   = self::STANDART_LOGGER_TRACE + (int) $config['trace_depth'];

        // place fetures - find log only in the spetial place: files, classes of file paths.
        if( isset( $config['place_regexp']  ) ) $this->place = $config['place_regexp'];
        if( isset( $config['place']         ) ) $this->place = '/^' . preg_quote( $config['place'], '/' ) . '$/';

        $this->set_priv_config( $config );

    }
    
    /**
     *  void logger( array $args ) - ananimus log method, if you doesn't want use debug, info, warn, error or fatal 
     *                          methods, or you want use default log level
     *      @param      array       $args       - vars for loged
     *      @return     void
     *      @access     public
     */
    public function logger( $args ){
        switch( strtolower( $args[0] ) ){
            case 'debug'    : array_shift( $args ); $this->log( DEBUG, $args ); break; 
            case 'info'     : array_shift( $args ); $this->log( INFO,  $args ); break;
            case 'warn'     : array_shift( $args ); $this->log( WARN,  $args ); break;
            case 'error'    : array_shift( $args ); $this->log( ERROR, $args ); break;
            case 'fatal'    : array_shift( $args ); $this->log( FATAL, $args ); break;
            default         : $this->log( $this->default_level, $args );
        }
    }

    /**
     *  void log( int $level, array $args )     - loged message with concete log level 
     *      @param      int         $level      - log level
     *      @param      array       $args       - vars for loged
     *      @return     void
     *      @access     public
     *
     */
    public function log( $level, $args ){

        // return if level more then minimal log level 
        if( $level > $this->min_log_level ){ $this->last_log = ''; return; }
        $msg    = $this->_dump( $args );
        $trace  = $this->_smart_trace();
        
        // if log only spectial place (class or file (file path))
        if ( $this->place 
            && ! preg_grep( $this->place, array($trace['class'], $trace['file'], $trace['filepath']) ) ) 
        { 
            $this->last_log = ''; return; 
        }

        $server = '';
        if ( isset ( $_SERVER['SERVER_NAME'] ) ) $server = $_SERVER['SERVER_NAME'];   //  TODO!!! rewrite without $_SERVER!!!

        $string = $this->log_format;
        $string = preg_replace( '/%C/',        $trace['class'],           $string );
        $string = preg_replace( '/%d/',        date('Y/m/d H:i:s'),       $string );  //  TODO!!! think about dinamic datatime format
        $string = preg_replace( '/%t/',        preg_replace( '/^\d.(\d{4})\d+ \d+(\d{2})$/', '\\2.\\1', microtime() ), $string );
        $string = preg_replace( '/%F/',        $trace['filepath'],        $string );
        $string = preg_replace( '/%f/',        $trace['file'],            $string );
        $string = preg_replace( '/%H/',        $server,                   $string );
        $string = preg_replace( '/%L/',        $trace['line'],            $string );
        $string = preg_replace( '/%m\{(\d+)\}/e', 'substr( $msg, 0, $1 ) . LOGGER_LINE_FEED', $string );
        $string = preg_replace( '/%m/',        $msg,                      $string );
        $string = preg_replace( '/%M/',        $trace['function'],        $string );
        $string = preg_replace( '/%p/',        self::$id2level[ $level ], $string );
        $string = preg_replace( '/%P/',        getmypid(),                $string );
        $string = preg_replace( '/%T/',        var_export( $trace['backtrace'], true),  $string );
        $string = preg_replace( '/%%/',        '%',                       $string );
        $string = preg_replace( '/%n/',        LOGGER_LINE_FEED,          $string );

        $this->last_log = $string;      // TODO remove $string var
        $this->write( $level );
        
    }

    /**
     *  string _dump( array $args )     - dump data into string
     *      @param      array       $args       - data for log
     *      @return     string      $string     - logger in string format
     *      @access     private
     */
    private function _dump( $args ){

        if( ! $args ) return LOGGER_LINE_FEED;

        $string = ''; 
        $number = 0;

        foreach( $args as $var ){

            if( $this->add_var ) $string .= '$VAR' . ++$number . ' = '; 

            switch ( true ){
                case $var === null              : $string .= 'NULL' . LOGGER_LINE_FEED; break;
                case $var === false             : $string .= 'FALSE'. LOGGER_LINE_FEED; break;
                case $var === true              : $string .= 'TRUE' . LOGGER_LINE_FEED; break;
                case is_string( $var ) && !$var : $string .= '""'   . LOGGER_LINE_FEED; break;
                case is_string( $var ) or is_int( $var ) or is_double( $var ) : $string .= $var . LOGGER_LINE_FEED; break;
                default                         : $string .= var_export( $var, true ) . LOGGER_LINE_FEED;
            }
        }

        if( $this->space != 2 ) $string = preg_replace( '/^\s+/m', str_repeat( ' ', $this->space ), $string );
        return $string;
    }

    /**
     *  array _smart_trace()    - parse backtrace and find class, file, func. and line, where was 
     *                          called method
     *      @return     $trace      - array with main loggers haracteristis:
     *                              'backtrace'     - all backtrace
     *                              'class'         - class,    where call logger
     *                              'file'          - file,     -- "" -- 
     *                              'function'      - function, -- "" --
     *                              'line'          - line,     -- "" --
     */
    private function _smart_trace(){
        
        $depth      = $this->trace_depth;
        $backtrace  = debug_backtrace();

        if( $backtrace[ self::STANDART_LOGGER_TRACE ]['class'] == __CLASS__ ) 
            ++$depth;  // if logger was call with "log" method 

        return array(
            'backtrace' => $backtrace,
            'class'     => isset( $backtrace[ $depth + 1 ]['class']    ) ? $backtrace[ $depth + 1 ]['class']    : '',
            'function'  => isset( $backtrace[ $depth + 1 ]['function'] ) ? $backtrace[ $depth + 1 ]['function'] : '',
            'filepath'  => $backtrace[ $depth ]['file'],
            'file'      => basename( $backtrace[ $depth ]['file'] ),
            'line'      => $backtrace[ $depth ]['line']
        );
    }

    /*
     *  string to_string( string $msg )    - transmormed message to one string message
     *      @param      string      $msg    - message
     *      @return     string      $_msg   - transmormed message
     *      @access     public
     */
    public function to_string( $msg ){
        return preg_replace( '/\s+/', ' ', preg_replace( "/LOGGER_LINE_FEED/", '\\n', $msg ) );
    }
}

?>
