<?php

/**
 *
 *  class Logger_tmpl   - template log class
 *
 *  @author	    Anton Morozov (antonfin@mail.ua)	
 *  @see        Logger.php for more detail 
 *  @copyright	(c) 2009 by Anton Morozov
 *  @version	0.01
 *
 */
class Logger_tmpl extends Logger_Common
{
    
    /**
     *  Type property
     *      @var        string
     *      @access     private 
     */
    private $type = '...';

    /**
     *  Logger_... object constructor
     *      @see    Logger class
     *
     */
    public function __construct(){}

    /**
     *  Hide clone constructor
     */
    private function __clone(){} 
     
    /**
     *  $this->write( int $level )    - write messages in the ...
     *      @param      int     $level  - log level 
     *      @return     void 
     *      @access     public
     */
    public function write( $level ){ /* PHP CODE FOR SAVE LOG MESSAGE */ }  

    /**
     *  $this->set_priv_config( array $config )  - validate and set configuration
     *      @param      array       $config         - configuration
     *      @return     void
     *      @access     public
     */
    public function set_priv_config( $config ){ /* PHP CODE FOR SET SPECIFIC CONFIGURATION */ }
        
}

?>
