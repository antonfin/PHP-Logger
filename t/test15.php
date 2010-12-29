<?php
/**
 *  NOT AUTOMATICALY TEST. CHECK COLOR FOR TERM
 */

$config = array(
    'use' => array('LOGGER'),
    'appenders' => array(
        'LOGGER' => array(
            'type' => 'stdout',
            'color' => true,
            'colors' => array(
                'debug' => 'yellow',
                'info'  => 'green',
                'warn'  => 'purple',
                'error' => 'cyan',
                'fatal' => 'red',
            )
        ),
    )
);

require_once ('TestSimple.php');

// init test object
$T = TestSimple::get_test_object() or die( "Can't create test object" );
$T->fail_format = "test\t%N\t fail - %f:%L%nMessage: %m%n";
$T->start( 10 );

$class = 'Logger';
require_once ('../lib/Logger.php');

echo "!!!Check color 2!!!\n";

try{
    $T->can_ok( $class, "get_logger" );

    $LOGGER = Logger::get_logger();

    $T->ok( $LOGGER, "Logger is undefined" );
    $T->isa_ok( $LOGGER, $class );
    $T->can_ok( $class, "set_config" );
    $T->can_ok( $LOGGER, "set_config" );

    $LOGGER->set_config( $config );
}
catch( Exception $e ){
    print "Exception: " . $e->getMessage();
}

$LOGGER->debug("Must be yellow");
$T->ok( $LOGGER->get('LOGGER'), "Message was not returned" );

$LOGGER->info("Must be green");

$T->ok( $LOGGER->get(), "Message was not returned" );
$LOGGER->warn("Must be purple");
$T->ok( $LOGGER->get(), "Message was not returned" );

$LOGGER->error("Must be cyan");
$T->ok( $LOGGER->get(), "Message was not returned" );

$LOGGER->fatal("Must be red");
$T->ok( $LOGGER->get(), "Message was not returned" );

//  End of test
$T->finish();

?>
