<?php

namespace Fabrico\Test\Core;

use Fabrico\Core\Annotation;
use Fabrico\Test\Test;

class AnnotationTest extends Test
{
    /**
     * @annotation true
     * @test hi
     * @set
     * @array 1
     * @array 2
     * @array 3
     */
    public function testParser()
    {
        ob_start();
        var_dump(Annotation::parse(__CLASS__, __FUNCTION__));
        $dump = ob_get_clean();

        $expected = <<<TEXT
array(4) {
  'annotation' =>
  bool(true)
  'test' =>
  string(2) "hi"
  'set' =>
  bool(true)
  'array' =>
  array(3) {
    [0] =>
    int(1)
    [1] =>
    int(2)
    [2] =>
    int(3)
  }
}
TEXT;

        $this->assertEquals(trim($expected), trim($dump));
    }

    public function testStringTrueReturnBooleanTrue()
    {
        $this->assertTrue(Annotation::convertStringValue('true'));
    }

    public function testStringFalseReturnBooleanFalse()
    {
        $this->assertFalse(Annotation::convertStringValue('false'));
    }

    public function testStringIntegersReturnRealIntegers()
    {
        $this->assertTrue(is_int(Annotation::convertStringValue('23')));
        $this->assertEquals(23, Annotation::convertStringValue('23'));
    }

    public function testStringDoublesReturnRealDoubleds()
    {
        $this->assertTrue(is_double(Annotation::convertStringValue('23.4')));
        $this->assertEquals(23.4, Annotation::convertStringValue('23.4'));
    }

    public function testRegularStringsAreReturnedAsRegularStrings()
    {
        $val = 'hj54324%^#$';
        $this->assertTrue(is_string(Annotation::convertStringValue($val)));
        $this->assertEquals($val, Annotation::convertStringValue($val));
    }
}
