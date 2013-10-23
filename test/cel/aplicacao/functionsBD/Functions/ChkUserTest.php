<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2013-10-23 at 12:19:48.
 */

require_once dirname(__FILE__) . '/../../ChkUser.php';
class ChkUserTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ChkUser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ChkUser;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers ChkUser::checkUserAuthentication
     * @todo   Implement testCheckUserAuthentication().
     */
    public function testCheckUserAuthentication() {
        $real = $this->object->checkUserAuthentication($url);
        $expectedNull = NULL;
        $expectedValid = exit; 
        $this->assertEquals($expectedNull, $real);
        $this->assertEquals($expectedValid, $real);
    }

    /**
     * @covers ChkUser::simple_query
     * @todo   Implement testSimple_query().
     */
    public function testSimple_query() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
