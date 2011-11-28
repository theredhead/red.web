<?php
namespace red\text
{
	interface ITextTransformer
	{
		/**
		 * Transform the text from $input and return the result
		 *
		 * @abstract
		 * @param string $input
		 * @return string
		 */
		public function transform($input);
	}
}
#EOF