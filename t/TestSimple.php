<?php

/**
 *
 *  Class TestSimple - class for testing
 *
 *  @author:    Anton Morozov
 *  @email:     antonfin@mail.ua
 *  @copyright	(c) 2009 by Anton Morozov
 *  @version:   0.01
 *
 */

class TestSimple
{

    /**
     *  $version    - version number
     *      @var        string
     *      @access     public
     */
    static public $version = 0.01;

    /**
     *  Test number
     *      @var        int
     *      @access     public
     */
    static public $TESTS = 0;

    /**
     *  Run tests
     *      @var        bool
     *      @access     private
     */
    static private $RUN_FINISH = true;

    /**
     *  Count of test
     *      @var        int
     *      @access     public
     */
    public $count = 0;

    /**
     *  TestSimple objects properties
     *      ok      - number of ok tests
     *      fail    - number of failed tests
     *
     *      @var        int
     *      @access     public
     */
    public $ok     = 0;
    public $fail   = 0;

    /**
     *  TestSimple object properties
     *      fail_format     - error message format
     *      ok_format       - ok message format
     *  
     *  Detail:
     *      %C Fully qualified class name of the caller
     *      %F File (full path) where the logging event occurred
     *      %f File (name only) where the logging event occurred
     *      %L Line number within the file where the log statement was issued
     *      %M Method or function's name where the logging request was issued
     *      %N Test number
     *      %n Newline (OS-independent)
     *      %m The message to be logged
     *
     *      @var        string
     *      @access     public
     *
     */
    public $fail_format = "test\t%N\t fail (%m - %f:%L)%n";
    public $ok_format   = "test\t%N\t ok%n";

    /**
     *  Singleton
     *      @var        object
     *      @access     private
     */
    static private $TestSimple;

    /**
     *  Hide create object methos
     */
    private function __construct()  {}
    private function __clone()      {}

    /**
     *  TestSimple get_test_object()   - return test object
     *      @param      int         $count          - planed tests
     *      @return     object      $TestSimple     - TestSimple object
     *      @access     public
     */
    static public function get_test_object( $count = 0 ){
        if( ! self::$TestSimple ){ 
            self::$TestSimple = new self;
            self::$TestSimple->start( $count );
        }

        return self::$TestSimple;
    }

    /**
     *  void $this->start( int $count )     - set planed tests count
     *      @param      int         $count      - planed test count
     *      @return     void
     *      @access     public
     */
    public function start( $count ){ $this->count = $count; }

    /**
     *  void finish()   - finish test and analize planed tests
     *      @return     void
     *      @access     public
     */
    public function finish(){

        self::$RUN_FINISH = true;

        switch( true ){
            case $this->count == self::$TESTS && $this->count == $this->ok  : print "All tests ({$this->count}) was successfully finished\n"; break;
            case 0                              : print "Warn: you must set planed tests\n"; break;
            case $this->fail                    : print "Warn: {$this->fail} tests was failed\n"; break;
            case $this->count <> self::$TESTS   : print "Warn: planed tests count ({$this->count}) don't equal of all successfully tests {$this->ok}\n"; break;
            default                             : print "Error: check all planed and runed tests\n"; 
        }
        exit;
    }

    /**
     *  My destruct realisation (with planed tests analizer)
     */
    function __destruct(){ if( ! self::$RUN_FINISH ) $this->finish(); }

    /**
     *  void class_ok( string $class )  - check class existence 
     *      @param      string      $class      - class name
     *      @return     void
     *      @access     public
     */
    public function class_ok( $class ){
        
        ++self::$TESTS;
        if( class_exists( $class ) ){ 
            ++$this->ok;
            $this->_ok_msg( "class [$class] exists" );
        }
        else{ 
            ++$this->fail; 
            $this->_fail_msg( "class [$class] doesn't exist" );
        }

    }

    /**
     *  void ok( bool $status [, string $test_name ] )      - check element by true
     *      @param      bool        $status         - status true
     *      @param      string      $test_name      - test name
     *      @return     void
     *      @access     public
     */
    public function ok( $status, $test_name = '' ){

        ++self::$TESTS;
        if( (bool) $status ){ 
            ++$this->ok; $this->_ok_msg( $test_name );
        }
        else{ 
            ++$this->fail; $this->_fail_msg( $test_name );
        }
    }

    /**
     *  void is( mix $got, mix $expected, string $test_name )   - check got and expected by equal
     *      @param      mix         $got        - first element
     *      @param      mix         $expected   - second element (must be equal by first element)
     *      @param      string      $test_name  - test name
     *      @return     void
     *      @access     public
     */
    public function is( $got, $expected, $test_name = '' ){
        
        ++self::$TESTS;
        if( $got === $expected ){ 
            ++$this->ok; $this->_ok_msg( $test_name );
        }
        else{ 
            ++$this->fail; $this->_fail_msg( $test_name );
        }
    }

    /**
     *  void isnt( mix $got, mix $expected, string $test_name )   - check got and expected by not equal
     *      @param      mix         $got        - first element
     *      @param      mix         $expected   - second element (must be not equal by first element)
     *      @param      string      $test_name  - test name
     *      @return     void
     *      @access     public
     */
    public function isnt( $got, $expected, $test_name = '' ){
        
        ++self::$TESTS;
        if( $got !== $expected ){ 
            ++$this->ok; $this->_ok_msg( $test_name );
        }
        else{ 
            ++$this->fail; $this->_fail_msg( $test_name );
        }
    }

    /**
     *  void like( mix $got, mix $expected, string $test_name )     - check, then got must be like expected
     *      @param      mix         $got        - first element
     *      @param      mix         $expected   - second element (must be like first element)
     *      @param      string      $test_name  - test name
     *      @return     void
     *      @access     public
     *
     */
    public function like( $got, $expected, $test_name ){
         
        ++self::$TESTS;
        if( ! preg_match( "/^\/.+\/$/", $got ) ) $expected = "/$expected/"; 
        if( preg_match( $expected, $got ) ){ 
            ++$this->ok; $this->_ok_msg( $test_name );
        }
        else{ 
            ++$this->fail; $this->_fail_msg( $test_name );
        }

    }

    /**
     *  void unlike( mix $got, mix $expected, string $test_name )   - check, then got must be not like expected
     *      @param      mix         $got        - first element
     *      @param      mix         $expected   - second element (must be not like first element)
     *      @param      string      $test_name  - test name
     *      @return     void
     *      @access     public
     *
     */
    public function unlike( $got, $expected, $test_name ){
    
        ++self::$TESTS;
        if( ! preg_match( $expected, $got ) ){ 
            ++$this->ok; $this->_ok_msg( $test_name );
        }
        else{ 
            ++$this->fail; $this->_fail_msg( $test_name );
        }

    }

    /**
     *  
     *  void unlike( mix $got, string $operator, mix $expected, string $test_name )   - check both 
     *  element usage operator
     *      @param      mix         $got        - first element
     *      @param      string      $operator   - operator
     *      @param      mix         $expected   - second element (must be not equal by first element)
     *      @param      string      $test_name  - test name
     *      @return     void
     *      @access     public
     *
     */
    public function cmp_ok( $got, $operator, $expected, $test_name ){
    
        ++self::$TESTS;

        $result = false;
        switch( $operator ){
            case '=='   : $result = $expected ==  $got; break;
            case '!='   : $result = $expected !=  $got; break;
            case '<>'   : $result = $expected !=  $got; break;
            case '>'    : $result = $expected >   $got; break;
            case '<'    : $result = $expected <   $got; break;
            case '<='   : $result = $expected <=  $got; break;
            case '>='   : $result = $expected >=  $got; break;
            case '==='  : $result = $expected === $got; break;
            case '!=='  : $result = $expected !== $got; break;
            default : $this->_fail_msg( "Operator $operator does not exist" );
        }

        if( $result ){ 
            ++$this->ok; $this->_ok_msg( $test_name );
        }
        else{ 
            ++$this->fail; $this->_fail_msg( $test_name );
        }

    }

    /**
     *  void can_ok( mix $mix, string $method )    - check methos exists in the object
     *      @param      mix         $mix        - object or class
     *      @param      string      $method     - method name
     *      @return     void
     *      @access     public
     *
     */
    public function can_ok( $mix, $method ){
    
        ++self::$TESTS;
        
        if( method_exists( $mix, $method ) ){ 
            ++$this->ok; $this->_ok_msg( "Method [$method] exists" );
        }
        else{ 
            ++$this->fail; $this->_fail_msg( "Method [$method] doesn't exist" );
        }

    }
    
    /**
     *  void isa_ok( object $object, string $class )    - check object's class
     *      @param      object      $object     - object
     *      @param      string      $class      - class name
     *      @return     void
     *      @access     public
     *
     */
    public function isa_ok( $object, $class ){
    
        ++self::$TESTS;
        if( get_class( $object ) == $class ){ 
            ++$this->ok; $this->_ok_msg( "Object belongs to class [$class]" );
        }
        else{ 
            ++$this->fail; $this->_fail_msg( "Object doesn't belong to class [$class]" );
        }

    }

    /**
     * void eq_array( array $got, array $expected, string $test_name )  - checks if two arrays are equivalent.
     *      @param      array       $got        - first element
     *      @param      array       $expected   - second element (must be not equal by first element)
     *      @param      string      $test_name  - test name
     *      @return     void
     *      @access     public
     *      @todo       now don't work
     *
     */
    public function eq_array( $got, $expected, $test_name ){
    
        ++self::$TESTS;

        $result;
        if( is_array( $got ) && is_array( $expected ) ){
            if( $this->_eq_array( ksort( $got ), ksort( $expected ) ) ){
                ++$this->ok; $this->_ok_msg( $test_name );
                return;
            }
        }
        else{
            $this->_fail_msg( "First and second elements must be arrays" );
        }

        ++$this->fail; $this->_fail_msg( $test_name );
    }

    
    /**************************************************************************/
    //                                  UTILS                                 //
    /**************************************************************************/

    /**
     *  bool _eq_array( array $got, $expected )
     *      @param      array       $got        - first array
     *      @param      array       $expected   - second array (must be not equal by first element)
     *      @return     bool        $result     - if equivalent true, otherwise false
     *      @todo       now don't work
     *
     */
    private function _eq_array( $got, $expected ){
        if ( count( $got ) == count( $expected ) && !array_diff( $got, $expected ) ){
            foreach ( $got as $key => $val ) {
                if ( $val != $expected[ $key ] ) return false;
            }
            
            return true;
        }
        
        return false;
    }

    /**
     *  void _fail_msg( string $msg )   - show fail message
     *      @param      string      $msg        - test name
     *      @return     void
     *      @access     private
     */
    private function _fail_msg( $msg ){
        print $this->_parse_msg( $this->fail_format, $msg );
    }
   
    /**
     *  void _ok_msg( string $msg )   - show okey message
     *      @param      string      $msg        - test name
     *      @return     void
     *      @access     private
     */
    private function _ok_msg( $msg ){
        print $this->_parse_msg( $this->ok_format, $msg );
    }

    /**
     *  string _parse_msg( string $string, string $msg )    - parse message
     *      @param      string      $string         - base message format
     *      @param      string      $msg            - message
     *      @return     string      $string         - parsed message
     *      @access     private
     */
    private function _parse_msg( $string, $msg ){

        $trace  = $this->_smart_trace();
        $string = preg_replace( "/%C/", $trace['class'],          $string );
        $string = preg_replace( "/%F/", $trace['file'],           $string );
        $string = preg_replace( "/%f/", basename($trace['file']), $string );
        $string = preg_replace( "/%L/", $trace['line'],           $string );
        $string = preg_replace( "/%m/", $msg,                     $string );
        $string = preg_replace( "/%N/", self::$TESTS,             $string );
        $string = preg_replace( "/%M/", $trace['function'],       $string );
        $string = preg_replace( "/%n/", "\n",                     $string );  //  TODO!!! check OS

        return $string;

    }

    /**
     *  array _smart_trace()    - parse backtrace and find class, file, function's name and line, where was 
     *                          called method
     *      @return     $trace      - array with main loggers haracteristis:
     *                              'class'         - class,    where call logger
     *                              'file'          - file,     -- "" -- 
     *                              'function'      - function, -- "" --
     *                              'line'          - line,     -- "" --
     */
    private function _smart_trace(){
        
        $backtrace      = debug_backtrace();
        $my_trace       = array( 
            'class'     => '', 'file'   => '',
            'function'  => '', 'line'   => '',
        );

        $reverse_trace = array_reverse( $backtrace ); 
        foreach( $reverse_trace as $trace ){

            $my_trace['class']      = isset($trace['class']) ? $trace['class'] : '';
            $my_trace['file']       = $trace['file'];
            $my_trace['function']   = $trace['function'];
            $my_trace['line']       = $trace['line'];

            if( $my_trace['class'] == __CLASS__ ) return $my_trace;
        }
        
        return $my_trace;
    }

}


?>
