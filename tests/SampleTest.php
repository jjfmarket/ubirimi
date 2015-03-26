<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/25/2015
 * Time: 8:22 PM
 */

require("Sample.php");

class SampleTest extends PHPUnit_Framework_TestCase {

    private $testValue = 'a test value';
    public function testVar() {
        $sample = new Sample();
        $var = $sample->getVar();
        $this->assertTrue($var == $this->testValue);
    }
}
