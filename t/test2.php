<?php
/**
 *  test2.php   - Test Logger class
 *      
 *      Test base methods of Logger class
 *
 *          This script testing:
 *              @debug
 */

//  Load Logger and class for testing
require_once ('../lib/Logger.php');
require_once ('TestSimple.php');

$class      = 'Logger';
$version    = '0.01';

//  config
$config = array(
    'use'       => array('LOGGER'),
    'common'    => array(),
    'appenders' => array(
            'LOGGER'    => array( 'type' => 'stdout', 'log_format' => '%f:%m' )
        )
    );
$cur_appender = 'LOGGER';
$file = basename( __FILE__ );
$level = 'debug';

$T = TestSimple::get_test_object( 84 ) or die( "Can't create test object" );
$T->class_ok( $class );

//////////////////////  Classes' methods    //////////////////

//  Check get_version method

$T->can_ok( $class, 'debug' );
//  need for testig
$T->can_ok( $class, 'get' );

$LOG = Logger::get_logger( $config );
//  testing log_format  "%f:%m"
//
$l = $LOG->debug( null )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$msg = "NULL\n";
$var = '$VAR1 = ';
$text = $file . ':' . $var . $msg;
$T->is( $l, $text, "Is \"" . $text );

$l = $LOG->debug( true )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->is( $l, $file . ":\$VAR1 = TRUE\n", "Is \"" . $file . ':$VAR1 = TRUE"' );

$l = $LOG->debug( false )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->is( $l, $file . ":\$VAR1 = FALSE\n", "Is \"" . $file . ':$VAR1 = FALSE"' );

$l = $LOG->debug('')->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->is( $l, $file . ":\$VAR1 = \"\"\n", "Is \"" . $file . ':$VAR1 = """' );

$l = $LOG->debug()->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->is( $l, $file . ":\n", "Is \"" . $file . ':"' );

$l = $LOG->debug( array() )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->is( $l, $file . ":\$VAR1 = array (\n)\n", "Is \"" . $file . ':array ()"' );

$l = $LOG->debug( array('Anton Morozov') )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->is( $l, $file . ":\$VAR1 = array (\n  0 => 'Anton Morozov',\n)\n", "Is \"" . $file . ':array ()"' );

$l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$text = $file . ":\$VAR1 = array (\n  0 => 'Anton Morozov',\n  1 => 'antonfin@mail.ua',\n  2 => 1985,\n)\n";
$T->is( $l, $text, "Is: $text" );

$l = $LOG->debug( array( 'fullname' => 'Anton Morozov', 'email' => 'antonfin@mail.ua', 'age' => 1985) )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$text = $file . ":\$VAR1 = array (\n  'fullname' => 'Anton Morozov',\n  'email' => 'antonfin@mail.ua',\n  'age' => 1985,\n)\n";
$T->is( $l, $text, "Is: $text" );

$l = $LOG->debug( array( 
    'fullname' => 'Anton Morozov',
    'email' => 'antonfin@mail.ua',
    'age' => 1985, 
    'log' => array( 'debug', 'info', 'warn', 'error', 'fatal') 
) )->get( $cur_appender );

$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$text = $file . ":\$VAR1 = array (\n  'fullname' => 'Anton Morozov',\n  'email' => 'antonfin@mail.ua',\n  'age' => 1985,\n" .
    "  'log' => \n  array (\n    0 => 'debug',\n    1 => 'info',\n    2 => 'warn',\n    3 => 'error',\n    4 => 'fatal',\n  ),\n)\n";

$T->is( $l, $text, "Is: $text" );

/////////////////////////           n - params
//
$l = $LOG->debug( 'Anton Morozov', 'antonfin@mail.ua', 1985 )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$text = $file . ":\$VAR1 = Anton Morozov\n\$VAR2 = antonfin@mail.ua\n\$VAR3 = 1985\n";
$T->is( $l, $text, "Is: $text" );

//////////////
$l = $LOG->debug( 'Anton Morozov', 'antonfin@mail.ua', 1985, array( 'debug', 'fatal' ) )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$text = $file . ":\$VAR1 = Anton Morozov\n\$VAR2 = antonfin@mail.ua\n\$VAR3 = 1985\n\$VAR4 = array (\n  0 => 'debug',\n  1 => 'fatal',\n)\n";
$T->is( $l, $text, "Is: $text" );

//////////////      Test loggers for objects
$O = new ForTestClass();
$l = $LOG->debug( $O )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$text = $file . ":\$VAR1 = ForTestClass::__set_state(array(
   'p1' => 0,
   'p2' => 'Test',
   'p3' => '',
))\n"; 
$T->is( $l, $text, "Is: $text" );

$l = $LOG->debug( $O, 0, 'tester' )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$text = $file . ":\$VAR1 = ForTestClass::__set_state(array(
   'p1' => 0,
   'p2' => 'Test',
   'p3' => '',
))
\$VAR2 = 0
\$VAR3 = tester\n"; 
$T->is( $l, $text, "Is: $text" );

/////////////////////////////////////////////////////////////////////
//          Doesn't add "VAR"
$config['appenders']['LOGGER'] = array( 'log_format' => '%f:%m', 'add_var' => false, 'type' => 'stdout' );
$LOG = Logger::get_logger( $config );
//  testing log_format  "%f:%m"
//
$l = $LOG->debug( null )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$msg = "NULL\n";
$text = $file . ':' . $msg;
$T->is( $l, $text, "Is \"" . $text );

$l = $LOG->debug( true )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$T->is( $l, $file . ":TRUE\n", "Is \"" . $file . ':TRUE"' );

$l = $LOG->debug( 'Anton Morozov', 'antonfin@mail.ua', 1985 )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );

$text = $file . ":Anton Morozov\nantonfin@mail.ua\n1985\n";
$T->is( $l, $text, "Is: $text" );

/////////////       new format %p: %f:%m
$config['appenders']['LOGGER'] = array( 'log_format' => '%p: %f:%m', 'add_var' => true, 'type' => 'stdout' );
$LOG = Logger::get_logger( $config );


$l = $LOG->debug( null )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ': ' . $file . ":\$VAR1 = NULL\n";
$T->is( $l, $text, "Is \"" . $text );

$l = $LOG->debug( true )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ': ' . $file . ":\$VAR1 = TRUE\n";
$T->is( $l, $text, "Is \"" . $text );

$l = $LOG->debug( 'Anton Morozov', 'antonfin@mail.ua', 1985 )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ': ' . $file . ":\$VAR1 = Anton Morozov\n\$VAR2 = antonfin@mail.ua\n\$VAR3 = 1985\n";
$T->is( $l, $text, "Is: $text" );

$l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ': ' . $file . ":\$VAR1 = array (\n  0 => 'Anton Morozov',\n  1 => 'antonfin@mail.ua',\n  2 => 1985,\n)\n";
$T->is( $l, $text, "Is: $text" );

/////////////       new format %p - %F:%m
$config['appenders']['LOGGER'] = array( 'log_format' => '%p - %F:%m', 'add_var' => true, 'type' => 'stdout' );
$LOG = Logger::get_logger( $config );

$l = $LOG->debug( null )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ' - ' . __FILE__ . ":\$VAR1 = NULL\n";
$T->is( $l, $text, "Is \"" . $text );

$l = $LOG->debug( true )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ' - ' . __FILE__ . ":\$VAR1 = TRUE\n";
$T->is( $l, $text, "Is \"" . $text );

$l = $LOG->debug( 'Anton Morozov', 'antonfin@mail.ua', 1985 )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ' - ' . __FILE__ . ":\$VAR1 = Anton Morozov\n\$VAR2 = antonfin@mail.ua\n\$VAR3 = 1985\n";
$T->is( $l, $text, "Is: $text" );

$l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ' - ' . __FILE__ . ":\$VAR1 = array (\n  0 => 'Anton Morozov',\n  1 => 'antonfin@mail.ua',\n  2 => 1985,\n)\n";
$T->is( $l, $text, "Is: $text" );


/////////////       new format %p - %f:%m{10}
$config['appenders']['LOGGER'] = array( 'log_format' => '%p - %f:%m{21}', 'add_var' => true, 'type' => 'stdout' );
$LOG = Logger::get_logger( $config );

$l = $LOG->debug( 'Anton Morozov', 'antonfin@mail.ua', 1985 )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ' - ' . $file . ":\$VAR1 = Anton Morozov\n";
$T->is( $l, $text, "Is: $text" );

$l = $LOG->debug( array('Anton Morozov', 'antonfin@mail.ua', 1985) )->get( $cur_appender );
$T->ok( $l, "Is true" );
$T->ok( is_string( $l ), "Is string" );
$text = $level . ' - ' . $file . ":\$VAR1 = array (\n  0 =\n";
$T->is( $l, $text, "Is: $text" );


$T->finish();


///////////////////////         FOR TESTING         /////////////////////

class ForTestClass
{
    public $p1 = 0;
    public $p2 = 'Test';
    public $p3 = '';

    function __construct(){ return $this; }

}

