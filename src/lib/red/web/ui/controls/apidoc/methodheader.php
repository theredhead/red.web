<?php

namespace red\web\ui\controls\apidoc
{
	use \Reflector;
	use \ReflectionClass;
	use \ReflectionMethod;
	use \ReflectionParameter;
	use \ReflectionProperty;
	use red\web\ui\controls\TemplateControl;
	use red\web\ui\controls\BindableControl;
	use red\web\ui\controls\Repeater;
	use red\web\ui\controls\IBindable;
	use red\web\ui\controls\IRepeaterDatasourceDelegate;

	class MethodHeader extends TemplateControl implements IRepeaterDatasourceDelegate, IBindable
	{
		private $datasource = null;
		/**
		 * @return \ReflectionClass
		 */
		protected function getDatasource()
		{
			return $this->datasource;
		}
		protected function setDatasource(\ReflectionMethod $newDatasource)
		{
			$this->datasource = $newDatasource;
		}
		
		/**
		 * @var \red\web\ui\controls\StaticText
		 */
		protected $txtMethodName;
		
		/**
		 * @var Repeater
		 */
		protected $rptParameters;
		
		
		private $isBound = false;
		public function bind($dataItem)
		{
			assert($dataItem instanceof ReflectionMethod);
			$this->setDatasource($dataItem);
			// $this->txtMethodName->setText($dataItem->getName());
//			$this->rptParameters->bind($this);
			
			if (! $dataItem->isUserDefined())
			{
				$dlMemberDetails = $this->getElementsByClassName('member-details')->first();
				$dlMemberDetails->clear();
			}
			
			$this->isBound = true;
		}
		
		/**
		 * Will hold the parameters for the method being documented
		 *
		 * @var array
		 */
		private $parameters = null;
		
		protected function assertReflectionDataGathered()
		{
			if (! $this->isBound())
			{
				static::fail('Have not been databound yet.');
			}
			assert(is_array($this->parameters) or $this->parameters = $this->getDatasource()->getParameters());
		}		
		
		/**
		 *
		 * @param Repeater $repeater
		 * @return integer
		 */
		public function numberOfRowsInRepeater(Repeater $repeater)
		{
			$this->assertReflectionDataGathered();
			$result = null;

			if ($repeater->isSameInstance($this->rptParameters))
			{
				$result = count($this->parameters);
			}
			else
			{
				static::fail('Unknown repeater.');
			}
			return $result;
		}

		/**
		 *
		 * @param Repeater $repeater
		 * @param type $rowIndex
		 * @return ReflectionMethod
		 */
		public function objectValueForRepeaterAtRowIndex(Repeater $repeater, $rowIndex)
		{
			$this->assertReflectionDataGathered();
			$result = null;

			if ($repeater->isSameInstance($this->rptParameters))
			{
				$result = isset($this->parameters[$rowIndex])
						? $this->parameters[$rowIndex]
						: null;
			}
			else
			{
				static::fail('Unknown repeater.');
			}
			return $result;
		}

		
		public function canBindTo($dataItem)
		{
//			return $dataItem instanceof Documentor;
			return $dataItem instanceof \ReflectionMethod;
		}


		public function isBound()
		{
			return $this->isBound;
		}
	}
}

#EOF