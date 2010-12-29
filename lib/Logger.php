<?php
/**
 *  Class Logger - utils for loged other information about system
 *
 *          Logger --------<> Logger_Common   
 *                                ^   
 *                               /|\
 *                              /_|_\
 *                                |
 *      ----------------------------------------------------------
 *      |              |               |             |           |
 *  Logger_stdout  Logger_file   Logger_syslog   Logger_mail   Logger_...
 *
 *
 *  @author:    Anton Morozov
 *  @email:     antonfin@mail.ua
 *  @copyright	(c) 2009-2010 by Anton Morozov
 *  @version:   0.90_32b
 *
 */

/**
 *  Logger type constants
 */
define( 'DEBUG',   4 );
define( 'INFO',    3 );
define( 'WARN',    2 );
define( 'ERROR',   1 );
define( 'FATAL',   0 );

/**
 *  Define appendix root (or alternativ root)
 */
define( 'LOGGER_APP_ROOT', dirname( __FILE__ ) );

/**
 *  Define correct line feed
 */
if( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ){ define( 'LOGGER_LINE_FEED', "\r\n" ); } 
else                                                { define( 'LOGGER_LINE_FEED', "\n" );   }

/*******************        Error Messages      ************************/
/**
 *
 */
define( 'ERROR_BAD_CONF',     'Error. Bad config' );
define( 'ERROR_COMMON_CONF',  'Error. Bad common config. Must be Array' );
define( 'ERROR_APP_CONF',     'Error. Bad appenders config. Must exist and been Array. Appender name: ' );
define( 'ERROR_APP_EX',       'Error. Appender does not exists. Appender name: ' );
define( 'ERROR_CLASS_EX',     'Error. Class does not exist. Class: ' );
/**
 *  Load LogCommon class
 */
require_once( LOGGER_APP_ROOT . '/Logger/Common.php' );

/**
 *
 *      Main Logger methods - builder log object. This object have meny simple objects. This objects 
 *      generate and save log messages. Logger object it's Singleton, it's mine, that if you will planed
 *
 *      Class's methods:
 *          @var        $version            - version number
 *          @method     get_version         - return version number
 *          @method     get_logger          - create (if object doesn't exist) and return Logger 
 *                                          object
 *          @method     
 *      Object's methods:
 *          @method     log                 -   
 *          @method     dumper               |
 *          @method     info                 |      messages for create and save log messages
 *          @method     warn                 |      
 *          @method     error                |
 *          @method     fatal               -
 *          @method     get                 - return last logged messege
 *          @method     appender_list       - return appenders list
 *          @method     set_config          - set new configuration
 *          @method     get_config          - return current configuration
 *
 */
class Logger
{
    
    /**
     *  $version    - version number
     *      @var        string
     *      @access     public
     */
    static public $version = '0.90_32b';
    
    /**
     *  $LOGGER  - log object (singleton)
     *      @var        array 
     *      @access     private
     */
    static private $LOGGER;
    
    /**
     *  Configuration 
     *      @var        array
     *      @access     protected
     */
    protected $config = array();

    /**
     *  List with appenders objects
     *      @var        array
     *      @access     protected
     */
    protected $appenders = array();

    /**
     *  string Logger::get_version()    - return current version 
     *      @return     string      $version        - version number
     *      @access     public
     *      @type       class's method
     */
    static public function get_version(){
        return self::$version;
    }

    /**
     *  Hide object constructors
     */
    private function __construct()  {}
    private function __clone()      {}

    /**
     *  Logger Object = Logger::get_logger( [ array $config ] )   - return logger object
     *      @param      array       $config             - logger config: not obligatory
     *      @return     object      $LOGGER
     *      @access     public
     *      @type       class's method
     *          
     */
    static public function get_logger( $config = null ){
        
        if( ! self::$LOGGER ) self::$LOGGER = new self;
        if( $config         ) self::$LOGGER->set_config( $config );

        return self::$LOGGER;
    }

    /**
     *  void $log->set_config( array $config )  - set new logger configuration
     *      @param      array       $config     - logger config
     *      @access     public
     */
    public function set_config( array $config ){

        $this->config = array_merge( $this->config, $config );

        if( ! isset( $this->config['common'] ) ){ $this->config['common'] = array();     }  // common must be array
        if( ! $this->_validate_config()        ){ throw new Exception( ERROR_BAD_CONF ); }

        $this->appenders = array();

        foreach( $this->config['use'] as $appender ){
            
            $class = 'Logger_' . $this->config['appenders'][ $appender ]['type'];
            //----------        require <class> if <class> is absent 
            if( ! class_exists( $class ) ){ 
                $file =  LOGGER_APP_ROOT . '/' . __CLASS__ . '/' . $this->config['appenders'][ $appender ]['type'] . '.php';
                if( is_file( $file ) )  require( $file );
                else                    throw new Exception( ERROR_CLASS_EX . $class );
            }
            
            $appender_config = array_merge( $this->config['common'], $this->config['appenders'][ $appender ] ); 
            $this->appenders[ $appender ] = Logger_Common::appender( $class, $appender, $appender_config );

        }
    }

    /**
     *  $this->_validate_config()    - check config
     *      @return     bool        $result     - true if successfully or false otherwise
     *      @access     private
     */
    private function _validate_config(){

        if( !( $this->config['use'] and is_array( $this->config['use'] )
            and $this->config['appenders'] and is_array( $this->config['appenders'] ) ) ) 
            return false;

        if( isset( $this->config['common'] ) && ! is_array( $this->config['common'] ) ){
            throw new Exception( ERROR_COMMON_CONF );
        }
        
        foreach( $this->config['use'] as $appender ){

            if( ! isset( $this->config['appenders'][ $appender ] )
                or ! $this->config['appenders'][ $appender ] 
                or ! is_array( $this->config['appenders'][ $appender ] )
            ) throw new Exception( ERROR_APP_CONF . $appender );
        }

        return true;
    }
    
    /**
     *  array get_config()   - return current configuration
     *      @return     array       $congig         - config params
     *      @access     public
     */
    public function get_config(){ return $this->config; }

    /*
     *  array appender_list()     - return appender list 
     *      @return     array      $appenders   - return appenders name 
     *      @access     public
     */
    public function appender_list(){ return $this->config['use']; }
    
    /**
     *  string get( [ string $appender_name ] )     - return string of last log (this method mast return 
     *                                              empty string if last log was apsent, because log 
     *                                              level was very hieght)
     *      @param      string      $appender_name  - appender name (if doesn't exist, usege 
     *                                              first appender forn the list)
     *      @return     string      $last_log       - last log
     */
    public function get( $appender_name = '' ){ return $this->_appender( $appender_name )->last_log; }

    /**
     *  object _appender( [ string $appender_name ] )   - return appender object, if appender name 
     *                                                  not getting, return first object, it's suit, 
     *                                                  when you use only one appender
     *      @param      string      $appender_name      - appender name
     *      @return     object      $LOG                - appender object
     *      @access     private 
     */
    private function _appender( $appender_name = '' ){
        
        if( $appender_name && isset( $this->appenders[ $appender_name ] ) ){
            return $this->appenders[ $appender_name ];
        }
        elseif ( $appender_name && !isset( $this->appenders[ $appender_name ] ) ){
            die( ERROR_APP_EX . $appender_name );
        }
        else{
            $appenders = array_values( $this->appenders );
            return $appenders[0];
        }
    }

    /**
     *  void destroy()       - destroy objects
     *      @access     public
     */
    public function destroy(){
        Logger_Common::destroy();
        self::$LOGGER = null;
        unset( $this );
    }

    /**
     *  void $LOGGER->log( mix $arg1, mix $arg2, ..., mix $argN )   - log message, you may get 
     *                                                              level like first parameters, 
     *  for example: $LOGGER->log( 'debug', $arg1, $arg2, ..., $argN )
     *      @param      mix         $arg    - data for loging
     *      @return     Logger      $LOG    - Logger object
     *      @access     public
     */
    public function log(){
        foreach( $this->appenders as $appender )
            $appender->logger( func_get_args() );

        return $this;
    }

    /**
     *  void $LOGGER->debug( mix $arg1, mix $arg2, ..., mix $argN ) - debug level log message
     *      #param      mix         $arg    - data for loging
     *      @return     Logger      $LOG    - Logger object
     *      @access     public
     */
    public function debug(){
        $args = func_get_args();
        foreach( $this->appenders as $appender )
            $appender->log( DEBUG, $args );

        return $this;
    }

    /**
     *  void $LOGGER->info( mix $arg1, mix $arg2, ..., mix $argN ) - info level log message
     *      @param      mix         $arg    - data for loging
     *      @return     Logger      $LOG    - Logger object
     *      @access     public
     */
    public function info(){
        $args = func_get_args();
        foreach( $this->appenders as $appender )
            $appender->log( INFO, $args );

        return $this;
    }

    /**
     *  void $LOGGER->warn( mix $arg1, mix $arg2, ..., mix $argN ) - warn level log message
     *      @param      mix         $arg    - data for loging
     *      @return     Logger      $LOG    - Logger object
     *      @access     public
     */
    public function warn(){
        $args = func_get_args();
        foreach( $this->appenders as $appender )
            $appender->log( WARN, $args );

        return $this;
    }

    /**
     *  void $LOGGER->error( mix $arg1, mix $arg2, ..., mix $argN ) - error level log message
     *      @param      mix         $arg    - data for loging
     *      @return     Logger      $LOG    - Logger object
     *      @access     public
     */
    public function error(){
        $args = func_get_args();
        foreach( $this->appenders as $appender )
            $appender->log( ERROR, $args );

        return $this;
    }

    /**
     *  void $LOGGER->fatal( mix $arg1, mix $arg2, ..., mix $argN ) - fatal level log message
     *      @param      mix         $arg    - data for loging
     *      @return     Logger      $LOG    - Logger object
     *      @access     public
     */
    public function fatal(){
        $args = func_get_args();
        foreach( $this->appenders as $appender )
            $appender->log( FATAL, $args );

        return $this;
    }

}

?>
