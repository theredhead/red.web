<?php

namespace red\web\ui\controls
{
	class RangeRepeaterDatasource extends BaseControl implements IRepeaterDatasourceDelegate
	{
		// <editor-fold defaultstate="collapsed" desc="Property integer From">
		private $from = 0;

		/**
		 * @return integer
		 */
		public function getFrom()
		{
			return $this->from;
		}

		/**
		 * @param integer $newFrom
		 */
		public function setFrom($newFrom)
		{
			$this->from = (integer)$newFrom;
		}
		// </editor-fold>
		// <editor-fold defaultstate="collapsed" desc="Property integer To">
		private $to = 0;

		/**
		 * @return integer
		 */
		public function getTo()
		{
			return $this->to;
		}

		/**
		 * @param integer $newTo
		 */
		public function setTo($newTo)
		{
			$this->to = (integer)$newTo;
		}
		// </editor-fold>

		public function numberOfRowsInRepeater(Repeater $repeater)
		{
			return $this->getTo() - $this->getFrom();
		}

		public function objectValueForRepeaterAtRowIndex(Repeater $repeater, $rowIndex)
		{
			return $this->getFrom() + $rowIndex;
		}
		
		public function setAttribute($name, $value)
		{
			switch(strtolower($name))
			{
				case 'from' : 
					$this->setFrom($value);
					break;
				case 'to' : 
					$this->setTo($value);
					break;
				default:
					parent::setAttribute($name, $value);
					break;
			}
		}
		
		/**
		 * TODO: migrate this to an abstract baseclass that is invisible
		 * @return boolean
		 */
		public function isVisible()
		{
			return false;
		}
	}
}