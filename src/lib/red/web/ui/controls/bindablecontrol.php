<?php

namespace red\web\ui\controls
{
	abstract class BindableControl extends BaseControl implements IBindable
	{
		abstract protected function buildControl();

		// <editor-fold defaultstate="collapsed" desc="Default implementation">
		
		// <editor-fold defaultstate="collapsed" desc="Property mixed Datasource">
		private $datasource = null;

		/**
		 * @return mixed
		 */
		public function getDatasource()
		{
			if($this->datasource === null)
			{				
				// $this->datasource = $this->findFirst(array($this, 'canBindTo'));
			}
			if($this->datasource === null)
			{
				// static::fail('There is no datasource');
			}
			return $this->datasource;
		}

		/**
		 * @param mixed $newDatasource
		 */
		public function setDatasource($newDatasource)
		{
			$this->canBindTo($newDatasource) or $this->fail('Cannot bind to %s', typeid($newDatasource));

			$this->datasource = $newDatasource;
		}
		// </editor-fold>

		protected $isBuilt = false;
		protected $isBound = false;
		
		public function getRequiresDataBinding()
		{
			return true;
		}

		public function bind($dataItem)
		{
			$this->canBindTo($dataItem) or $this->fail('Cannot bind to %s', typeid($dataItem));
			$this->setDatasource($dataItem);
			$this->buildControl();
			$this->isBuilt = true;
			$this->isBound = true;
		}

		public function isBound()
		{
			return $this->isBound;
		}
		
		public function preRender()
		{
			if (!$this->isBound && $this->getRequiresDataBinding())
			{
				static::fail('Not databound');
			}
			if (! $this->isBuilt)
			{
				$this->buildControl();
			}
			parent::preRender();
		}
		
		// </editor-fold>
	}
}

#EOF