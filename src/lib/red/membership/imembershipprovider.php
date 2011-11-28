<?php

namespace red\membership
{
	/**
	 * Defines the minimum contract for a membership provider.
	 */
	interface IMembershipProvider
	{
		/**
		 * Get the application name. Consider this equal to "REALM" for http authentication
		 *
		 * @abstract
		 * @return string
		 */
		public function getApplicationName();

		/**
		 * Validate a login/password combination
		 *
		 * @abstract
		 * @param $login
		 * @param $password
		 * @param \red\membership\IMembershipMember $member
		 * @return boolean
		 */
		public function validateLogin($login, $password, IMembershipMember &$member=null);

		/**
		 * @abstract
		 * @param string $login
		 * @param string $validationQuestion
		 * @param string $validationAnswer
		 * @return void
		 */
		public function validateQuestionAndAnswer($login, $validationQuestion, $validationAnswer);

		/**
		 * @abstract
		 * @param string $password
		 * @return string
		 */
		public function encryptPassword($password);

		/**
		 * Change password for a user.
		 *
		 * @abstract
		 * @param \red\membership\IMembershipMember $member
		 * @param string $oldPassword
		 * @param string $newPassword
		 * @param string $validationQuestion
		 * @param string $validationAnswer
		 * @return void
		 */
		public function changePassword(IMembershipMember $member, $oldPassword, $newPassword, $validationQuestion, $validationAnswer);

		/**
		 * @abstract
		 * @param \red\membership\IMembershipMember $member
		 * @param string $validationQuestion
		 * @param string $validationAnswer
		 * @return void
		 */
		public function resetPassword(IMembershipMember $member, $validationQuestion, $validationAnswer);

		/**
		 * Lock a member
		 * 
		 * @abstract
		 * @param IMembershipMember $member
		 * @return void
		 */
		public function lockMember(IMembershipMember $member);

		/**
		 * Unlock a member
		 *
		 * @abstract
		 * @param IMembershipMember $member
		 * @return void
		 */
		public function unlockMember(IMembershipMember $member);

		/**
		 * @abstract
		 * @param string $validationQuestion
		 * @param string $validationAnswer
		 * @return void
		 */
		public function changePasswordQuestionAndAnswer($validationQuestion, $validationAnswer);

		/**
		 * Create a new member
		 *
		 * @abstract
		 * @param string $login
		 * @param string $password
		 * @param string $email
		 * @param string $validationQuestion
		 * @param string $validationAnswer
		 * @return \red\membership\IMembershipMember
		 */
		public function createMember($login, $password, $email, $validationQuestion, $validationAnswer);

		public function deleteMember(IMembershipMember $member);

		/**
		 * Get the currently logged in member, if any
		 *
		 * @abstract
		 * @return \red\membership\IMembershipMember|null
		 */
		public function getCurrentMember();

		
	}
}