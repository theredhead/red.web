<?php

namespace red\membership
{
	/**
	 * Defines the minimum contract for membership users.
	 */
	interface IMembershipMember
	{
		/**
		 * @abstract
		 * @return string
		 */
		public function getDisplayName();

		/**
		 * @abstract
		 * @return string
		 */
		public function getEmail();
	}
}