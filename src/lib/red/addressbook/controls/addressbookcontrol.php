<?php

namespace red\addressbook\controls
{
	use \red\EventArgument;
	use \red\web\ui\controls\Repeater;
	use \red\web\ui\controls\TemplateControl;
	use red\web\ui\controls\Button;
	use \red\web\ui\IThemable;

	/**
	 * TemplateControl
	 */
	class AddressbookControl extends TemplateControl implements IThemable
	{
		/**
		 * @var PeopleDatasource
		 */
		protected $datasource = null;
		/**
		 * @var Repeater
		 */
		protected $cardList = null;
		
		/**
		 * @var CardDetailView
		 */
		protected $cardDetail = null;
		
		/**
		 * @var Button
		 */
		protected $btnEdit = null;
		/**
		 * @var Button
		 */
		protected $btnDone = null;
		/**
		 * @var Button
		 */
		protected $btnShare = null;
		
		/**
		 * gets called when the control is finished loading from the template 
		 */
		protected function templateLoaded()
		{
			parent::templateLoaded();
			$this->cardList->registerEventListener(
					  Repeater::EV_SELECTEDINDEX_CHANGED
					, 'onCardList_SelectedIndexChangerd', $this);

			$this->btnEdit->registerEventListener(
					  Button::EV_CLICKED
					, 'onBtnEdit_Clicked', $this);

			$this->btnDone->registerEventListener(
					  Button::EV_CLICKED
					, 'onBtnDone_Clicked', $this);
		}
		
		private function onCardList_SelectedIndexChangerd(Repeater $sender, EventArgument $argument)
		{
			$card = $this->datasource->objectValueForRepeaterAtRowIndex($sender, $sender->getSelectedIndex());
			$this->cardDetail->bind($card);
		}
		
		private function onBtnEdit_Clicked(Button $sender, EventArgument $argument)
		{
			$this->cardDetail->setEditable(true);
		}

		private function onBtnDone_Clicked(Button $sender, EventArgument $argument)
		{
			$this->cardDetail->setEditable(false);
		}
		
		public function preRender()
		{
			if (!$this->cardDetail->isBound() && $this->cardList->getSelectedIndex() !== null)
			{
				$card = $this->datasource->objectValueForRepeaterAtRowIndex(
						$this->cardList, 
						$this->cardList->getSelectedIndex());

				$this->cardDetail->bind($card);
			}

			$editable = $this->cardDetail->isEditable();
			$this->btnEdit->setVisible(!$editable);
			$this->btnDone->setVisible($editable);

			parent::preRender();
		}

		/**
		 * get an array of resource types to try and register.
		 *
		 * array should hold filename extensions to register as values
		 *
		 * example:
		 *  return array('css', 'js');
		 *
		 *
		 *
		 * @return array
		 */
		static public function getThemeResourceTypes()
		{
			return array('css');
		}
	}
}