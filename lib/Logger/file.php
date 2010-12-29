<?php

/**
 *  class Logger_file   - log module for save all log messages in the file
 *
 *  @author	    Anton Morozov (antonfin@mail.ua)	
 *  @package    Log2file
 *  @see        Logger.php for more detail 
 *  @copyright	(c) 2009 by Anton Morozov
 *  @version	0.01
 *
 */

/*
 *  Error messages
 */
define( 'ERROR_CANT_OPEN_FILE',     'Error. Can\'t open file: ' );
define( 'ERROR_CANT_CLOSE_FILE',    'Error. Can\'t close file: ' );
define( 'ERROR_FILE_PARAM_EX',      'Error. Appender config with type file, must has parameter filepath' );
define( 'ERROR_FILE_EX',            'Error. File or path does not exist and script can not create new file. File path: ' );
define( 'ERROR_PERMISSION_DENIED',  'Error. Permission denied. File does not writeble: ' );
define( 'ERROR_LOCK_FORMAT',        'Error. Check lock format' );

class Logger_file extends Logger_Common
{
    
    /**
     *  Type property
     *      @var        string
     *      @access     private 
     */
    private $type = 'file';

    /** 
     *  Logger file
     *      @var        string
     *      @access     private
     */
    private $filepath;
    
    /** 
     *  Lock file mode - default <code>null</code>. If Lock mode <code>null</code>, then file doesn't lock. You may get any 
     *  lock mode. For example: <code>LOCK_SH</code>, or <code>LOCK_EX</code>, or <code>LOCK_EX | LOCK_NB</code>, etc.
     *  If you decided include locking, remember: "if file was locking, your script will be wait, 
     *  when some process unlock file".
     *      @var        int
     *      @access     private
     */
    private $lock;

    /**
     *  Logger_file object constructor
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

        $fh = fopen( $this->filepath, 'a' ) or die( ERROR_CANT_OPEN_FILE . $this->filepath );
        if( ! isset( $this->lock ) or $this->_lock( $fh, $this->lock ) ){
            fwrite( $fh, $this->last_log ); 
            if( ! isset( $this->lock ) ) $this->_lock( $fh, LOCK_UN );
        }
        fclose( $fh ) or die( ERROR_CANT_CLOSE_FILE . $this->filepath );
    }

    /**
     *  $this->set_priv_config( array $config )  - validate and set configuration
     *      @param      array       $config         - configuration
     *      @return     void
     *      @access     public
     */
    public function set_priv_config( $config ){

        if( ! $config['filepath'] )  throw new Exception( ERROR_FILE_PARAM_EX );

        // create file if it doesn't exist
        if ( ! is_file( $config['filepath'] ) 
            && is_dir( dirname( $config['filepath'] ) ) 
            && $fh = @fopen( $config['filepath'], 'w' ) 
        ) fclose( $fh );

        if( ! is_file( $config['filepath'] )      ) throw new Exception( ERROR_FILE_EX . $config['filepath'] );
        if( ! is_writeable( $config['filepath'] ) ) throw new Exception( ERROR_PERMISSION_DENIED . $config['filepath'] );

        if( isset( $config['lock'] ) 
            && $config['lock'] != LOCK_SH 
            && $config['lock'] != LOCK_EX 
            && $config['lock'] != LOCK_SH | LOCK_NB
            && $config['lock'] != LOCK_EX | LOCK_NB
        ){
            throw new Exception( ERROR_LOCK_FORMAT );
        }

        $this->filepath = $config['filepath'];
        if ( isset( $config['lock'] ) ) $this->lock = $config['lock'];
    }

    /**
     *  bool $this->_lock( int $fh, int $mode ) - lock or unlock file
     *      @param      int         $fh         - file handler
     *      @param      int         $mode       - lock mode LOCK_SH or LOCK_EX or LOCK_UN
     *      @return     bool        $result     - operation result
     */
    private function _lock( $fh, $mode ){
        if( stristr( php_uname(), 'Windows 9' ) ) return true;
        return flock( $fh, $mode );
    }
}

?>
