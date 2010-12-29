<?php

/*
 *  Define syslog vars.
 */
define_syslog_variables();

/*
 *  Error messages
 */
define( 'ERROR_CANT_OPEN_SYSLOG',   "Error. Can't open syslog"      );
define( 'ERROR_CANT_CLOSE_SYSLOG',  "Error. Can't close syslog"     );
define( 'ERROR_CANT_WRITE_SYSLOG',  "Error. Can't write to syslog"  );

/**
 *  class Logger_syslog   - log module for save all log messages to syslog 
 *
 *  @author	    Anton Morozov (antonfin@mail.ua)	
 *  @package    Logger_syslog
 *  @see        Logger.php for more detail 
 *  @copyright	(c) 2009 by Anton Morozov
 *  @version	0.01
 *
 */

class Logger_syslog extends Logger_Common
{
    
    /**
     *  $id2syslog   - hash, where keys - level id and values - syslog level 
     *      @var        array
     *      @access     private
     */
    static private $id2syslog = array( 
        DEBUG   => LOG_DEBUG, 
        INFO    => LOG_INFO,
        WARN    => LOG_WARNING,
        ERROR   => LOG_ERR,
        FATAL   => LOG_CRIT
    );

    /**
     *  Type property
     *      @var        string
     *      @access     private 
     */
    private $type = 'syslog';
    
    /**
     *  Add some test before messages. Default - "Logger"
     *      @var        string
     *      @access     private
     */
    private $ident = 'PHP Logger';

    /**
     *  Syslog level. Default - local0.
     *      @var        int
     *      @access     private
     *      @see        php.net     - openlog function - facilities constants
     *      
     */
    private $facility = LOG_LOCAL0;

    /**
     *  The option argument is used to indicate what logging options will be used when 
     *  generating a log message. 
     *      @var        int
     *      @access     private
     *      @see        php.net     - openlog function - options constants
     */
    private $openlog_opt = LOG_ODELAY;

    /**
     *  Logger_syslog object constructor
     *      @see    Logger class
     *
     */
    public function __construct(){}

    /**
     *  Hide clone constructor
     */
    private function __clone(){} 
    
    /**
     *  $this->_write( int $level )
     *      @param      int     $level  - log level 
     *      @return     void 
     *      @access     public
     */
    public function write( $level ){
        openlog( $this->ident, $this->openlog_opt, $this->facility ) or die( ERROR_CANT_OPEN_SYSLOG  );

        $this->to_string( &$this->last_log );

        syslog( self::$id2syslog[ $level ], $this->last_log )        or die( ERROR_CANT_WRITE_SYSLOG );
        closelog()                                                   or die( ERROR_CANT_CLOSE_SYSLOG );
    }

    /**
     *  $this->set_priv_config( array $config )  - validate and set configuration
     *      @param      array       $config         - configuration
     *      @return     void
     *      @access     public
     */
    public function set_priv_config( $config ){

        if( isset($config['facility'])    && $config['facility'] ){ 
            $this->facility    = $config['facility'];
        }
        
        if( isset($config['openlog_opt']) && $config['openlog_opt'] ){ 
            $this->openlog_opt = $config['openlog_opt']; 
        }
            
        if( isset($config['ident']) ){ $this->ident = $config['ident']; }
    }
}

?>
