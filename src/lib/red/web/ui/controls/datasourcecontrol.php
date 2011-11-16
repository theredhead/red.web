<?php

namespace red\web\ui\controls
{
	/**
	 * Abstract base class to allow for easy declarative bindings to data in pages.
	 * 
	 * You'll still have to implement whatever datasource interface required for your
	 * view control but you van "design" subclasses of DatasourceControl onto webpages
	 */
	abstract class DatasourceControl extends BaseControl
	{
		/**
		 * Always returns false so there is no useless div on the page when rendered.
		 * 
		 * @return boolean
		 */
		final public function isVisible()
		{
			return false;
		}
	}
}

#EOF
