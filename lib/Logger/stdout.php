<?php

/**
 *
 *  class Logger_stdout - log class for print all messages in the stdout
 *
 *  @author	    Anton Morozov (antonfin@mail.ua)	
 *  @see        Logger.php for more detail 
 *  @copyright	(c) 2009 by Anton Morozov
 *  @version	0.01
 *
 */
class Logger_stdout extends Logger_Common
{

    /**
     *  Constant with default color code
     */
    const DEFAULT_COLOR = "\033[0;32m";
    
    /**
     *  Constant with reset code
     */
    const RESET_COLOR = "\033[0m";

    /**
     *  Colors list with codes. If you need more colors, write me (Anton Morozov <antonfin@mail.ua>)!
     *      @var        array
     *      @access     private
     */
    static private $COLOR_CODES = array(
        'black'     => "\033[0;30m",
        'red'       => "\033[0;31m",
        'green'     => "\033[0;32m",
        'yellow'    => "\033[0;33m",
        'blue'      => "\033[0;34m",
        'purple'    => "\033[0;35m",
        'cyan'      => "\033[0;36m",
        'white'     => "\033[0;37m",
    );
    
    /**
     *  Default colors list
     *      @var        array
     *      @access     private 
     */
    static private $default_colors = array(
        'debug' => 'green',
        'info'  => 'white',
        'warn'  => 'blue',
        'error' => 'red',
        'fatal' => 'yellow',
    );

    /**
     *  Type property
     *      @var        string
     *      @access     private 
     */
    private $type = 'stdout';

    /**
     *  Mark test for other colors
     *      @var        bool
     *      @access     private 
     */
    private $color = false;

    /**
     *  Color codes for curent appender
     *      @var        array
     *      @access     private 
     */
    private $appender_colors = array();

    /**
     *  Logger_stdout object constructor
     *      @see    Logger class
     *
     */
    public function __construct(){}

    /**
     *  Hide clone constructor
     */
    private function __clone(){} 
     
    /**
     *  $this->write( int $level )    - write messages in the stdout
     *      @param      int     $level  - log level 
     *      @return     void 
     *      @access     public
     */
    public function write( $level ){ 
        if ( $this->color ) $this->_cwrite( $level );
        else echo $this->last_log;
    }

    /**
     *  $this->_cwrite( int $level )    - write messages in the stdout with mark test other colors
     *      @param      int     $level  - log level 
     *      @return     void 
     *      @access     private
     */
    private function _cwrite( $level ){ echo $this->appender_colors[ $level ] . $this->last_log . self::RESET_COLOR; }


    /**
     *  $this->set_priv_config( array $config )  - validate and set configuration
     *      @param      array       $config         - configuration
     *      @return     void
     *      @access     public
     */
    public function set_priv_config( $config ){
        if ( isset( $config['color'] ) ) {
            $this->color = true;
            if ( ! $config['colors'] ) { $config['colors'] = self::$default_colors; }

            foreach ( parent::$level2id as $name => $l ) {
                $this->appender_colors[ $l ] = self::$COLOR_CODES[ strtolower( $config['colors'][ strtolower( $name ) ] ) ];
                if ( !$this->appender_colors[ $l ] ) $this->appender_colors[ $l ] = self::DEFAULT_COLOR;
            }
        }
    }
}

?>
