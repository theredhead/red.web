<?php

namespace tests
{
	use \red\MBString;

	class MBStringTests implements \red\ITestable
	{
		/**
		 * @test Make sure that MBString can be directly compared to a native php string
		 *
		 * @return void
		 */
		public function testPhpStringEqualityResult()
		{
			$string = MBString::withString('The quick brown fox jumps over the lazy dog.');
			assert($string == 'The quick brown fox jumps over the lazy dog.');
		}

		/**
		 * @test This method tests the \red\MBString::replace method.
		 *
		 * @return void
		 */
		public function testReplace()
		{
			$string = MBString::withString('The quick brown fox jumps over the lazy dog.');
			$result = $string
						->replace('fox', 'tortoise')
						->replace('dog', 'hare')
						->replace('jumps over', 'outwits')
						->replace('quick', 'slow')
						->replace('brown', 'crafty')
						->replace('lazy', 'posh');
			assert($result != $string);
			assert($result == 'The slow crafty tortoise outwits the posh hare.');
		}

		/**
		 * @test Makes sure that substring indexing is 0 based and returns the correct characters.
		 *
		 * @return void
		 */
		public function testSubstring()
		{
			$string = MBString::withString('abcdefghijklmnopqrstuvwxyz');
			assert($string->substring(0,3) == "abc");
			assert($string->substring(10,10) == "klmnopqrst");
			assert($string->substring(23,3) == "xyz");
		}

		/**
		 * @test Make sure we can append strings to strings
		 * 
		 * @return void
		 */
		public function testAppend()
		{
			$string = MBString::withString('1,2,3,4,5');
			assert($string->append(',6,7,8,9,0') == '1,2,3,4,5,6,7,8,9,0');
		}

		/**
		 * @test Make sure we can append strings to strings
		 *
		 * @return void
		 */
		public function testPrepend()
		{
			$string = MBString::withString(',6,7,8,9,0');
			assert($string->prepend('1,2,3,4,5') == '1,2,3,4,5,6,7,8,9,0');
		}

		/**
		 * @test Make sure that toLower returns a lowercased version of the string.
		 * 
		 * @return void
		 */
		public function testCaseConversionToLowerCase()
		{
			$string = MBString::withString('FOO, BAR, BAZ');
			assert($string->toLower() == 'foo, bar, baz');
		}

		/**
		 * @test Make sure that toUpper returns an uppercased version of the string.
		 *
		 * @return void
		 */
		public function testCaseConversionToUpperCase()
		{
			$string = MBString::withString('foo, bar, baz');
			assert($string->toUpper() == 'FOO, BAR, BAZ');
		}

		/**
		 * @test Make sure indexOf is zero based and works
		 * 
		 * @return void
		 */
		public function testIndexOf()
		{
			$string = MBString::withString('Hello, World!');
			assert($string->indexOf(', ') == 5);
			assert($string->indexOf('l', 5) == 10);
		}

		/**
		 * @test Make sure the default encoding is UTF-8
		 * 
		 * @return void
		 */
		public function testThatDefaultEncodingIsUTF8()
		{
			$string = MBString::withString('foo');
			assert($string->getEncoding() == MBString::ENCODING_UTF8);
		}

		/**
		 * @test
		 * 
		 * @return void
		 */
		public function testMultipleTestFailures()
		{
			assert(false);
			assert(0 > 1);
		}
	}
}