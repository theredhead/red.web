<?php
namespace red\text
{
	use \red\Obj;

	class MarkdownTransformer extends Obj implements ITextTransformer
	{
		/**
		 * Transform the text from $input and return the result
		 *
		 * @param string $input
		 * @return string
		 */
		public function transform($input)
		{
			//@todo: we might need a better way to link externals...
			require_once (dirname(REDWEB_BOOTSTRAP_FILE) . DIRECTORY_SEPARATOR .
			             implode(DIRECTORY_SEPARATOR, array('external', 'michelf.com', 'markdown.php')));

			$transformed = \MarkDown(''.$input);

			return $transformed;
		}
	}
}
#EOF