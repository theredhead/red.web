<?php

namespace tests
{
	use \red\Convert;

	class TestBool
	{
		private $value;

		public function __construct($value)
		{
			$this->value = $value;
		}

		public function booleanValue()
		{
			return (bool)$this->value;
		}
	}

	class ConvertTests implements \red\ITestable
	{
		/**
		 * @test Test boolean conversions.
		 */
		public function testConvertToBoolean()
		{
			assert(Convert::toBoolean(1) === true);
			assert(Convert::toBoolean(0) === false);
			assert(Convert::toBoolean(2) === false);
			assert(Convert::toBoolean('yes') === true);
			assert(Convert::toBoolean('on') === true);
			assert(Convert::toBoolean('true') === true);
			assert(Convert::toBoolean('1') === true);

			assert(Convert::toBoolean(new TestBool(1)) === true);
			assert(Convert::toBoolean(new TestBool(2)) === true);
			assert(Convert::toBoolean(new TestBool(0)) === false);
			assert(Convert::toBoolean(new TestBool(true)) === true);
			assert(Convert::toBoolean(new TestBool(false)) === false);
			assert(Convert::toBoolean(new \stdClass()) === false);
			assert(Convert::toBoolean(array()) === false);
			assert(Convert::toBoolean(array(1)) === false);
			assert(Convert::toBoolean(array(true)) === false);
		}
	}
}