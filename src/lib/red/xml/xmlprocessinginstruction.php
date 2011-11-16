<?php

namespace red\xml
{
	use red\MBString;
	
	class XMLProcessingInstruction extends XMLText
	{
		protected $encoding = MBString::ENCODING_UTF8;
		protected $docType = null;
	}
}

#EOF