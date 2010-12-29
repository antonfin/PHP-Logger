<?php

/**
 *
 *  class Logger_mail - log class send message by email
 *
 *  @author	    Anton Morozov (antonfin@mail.ua)	
 *  @see        Logger.php for more detail 
 *  @copyright	(c) 2009 by Anton Morozov
 *  @version	0.01
 *
 */

/*
 *  Error message
 */
define( 'ERRROR_SEND_MESSAGE_FOR_MAIL', "Error. Can't send message" );

class Logger_mail extends Logger_Common
{
    
    /**
     *  Type property
     *      @var        string
     *      @access     private 
     */
    private $type = 'mail';

    /**
     *  Receiver, or receivers of the mail.
     *      @var        string
     *      @access     private
     */
    private $to;

    /**
     *  Subject of the email to be sent.
     *      @var        string
     *      @access     private
     */
    private $subject;

    /**
     *  Headers String to be inserted at the end of the email header.
     *      @var        string
     *      @access     private
     */
    private $headers;

    /**
     *  Logger_mail object constructor
     *      @see    Logger class
     *
     */
    public function __construct(){}

    /**
     *  Hide clone constructor
     */
    private function __clone(){} 
     
    /**
     *  $this->write( int $level )    - write messages in the email and sent it.
     *      @param      int     $level  - log level 
     *      @return     void 
     *      @access     public
     */
    public function write( $level ){ 
        mail( $this->to, $this->subject, $this->last_log, $this->headers ) or die( ERRROR_SEND_MESSAGE_FOR_MAIL );
    }

    /**
     *  $this->set_priv_config( array $config )  - validate and set configuration
     *      @param      array       $config         - configuration
     *      @return     void
     *      @access     public
     */
    public function set_priv_config( $config ){
        if( isset( $config['subject'] ) ) $this->subject = $config['subject']; 
        if( isset( $config['to']      ) ) $this->to      = $config['to'];
        if( $config['headers']          ) $this->headers = $config['headers'];
    }
        
}

?>
