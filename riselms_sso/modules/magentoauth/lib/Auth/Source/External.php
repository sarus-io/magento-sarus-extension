<?php

/**
 * Magento external authentication source.
 *
 * This class is an example authentication source which is designed to
 * hook into an external authentication system.
 *
 * To adapt this to your own web site, you should:
 * 1. Create your own module directory.
 * 2. Add a file "default-enable" to that directory.
 * 3. Copy this file and modules/magentoauth/www/resume.php to their corresponding
 *    location in the new module.
 * 4. Replace all occurrences of "magentoauth" in this file and in resume.php with the name of your module.
 * 5. Adapt the getUser()-function, the authenticate()-function and the logout()-function to your site.
 * 6. Add an entry in config/authsources.php referencing your module. E.g.:
 *        'myauth' => array(
 *            'magentoauth:External',
 *        ),
 *
 * @package simpleSAMLphp
 */
class sspmod_magentoauth_Auth_Source_External extends SimpleSAML_Auth_Source {

	/**
	 * Constructor for this authentication source.
	 *
	 * @param array $info  Information about this authentication source.
	 * @param array $config  Configuration.
	 */
	public function __construct($info, $config) {
		assert('is_array($info)');
		assert('is_array($config)');

		/* Call the parent constructor first, as required by the interface. */
		parent::__construct($info, $config);

		/* Do any other configuration we need here. */
	}


	/**
	 * Retrieve attributes for the user.
	 *
	 * @return array|NULL  The user's attributes, or NULL if the user isn't authenticated.
	 */
	private function getUser() {

		/*
		 * In this example we assume that the attributes are
		 * stored in the users PHP session, but this could be replaced
		 * with anything.
		 */

		if (!session_id()) {
			/* session_start not called before. Do it here. */
			session_start();
		}

		if (!isset($_SESSION['uid'])) {
			/* The user isn't authenticated. */
			return NULL;
		}

		/*
		 * Find the attributes for the user.
		 * Note that all attributes in simpleSAMLphp are multivalued, so we need
		 * to store them as arrays.
		 */

		$info = $_SESSION['customer_info'];

// [website_id] => 1
//     [entity_id] => 137
//     [entity_type_id] => 1
//     [attribute_set_id] => 0
//     [email] =>
//     [group_id] => 1
//     [increment_id] =>
//     [store_id] => 1
//     [created_at] => 2016-02-18T07:21:09-08:00
//     [updated_at] => 2016-02-18 15:21:09
//     [is_active] => 1
//     [disable_auto_group_change] => 0
//     [created_in] => English
//     [firstname] =>
//     [middlename] =>
//     [lastname] =>

		$attributes = array(
			'email' => array($info['email']),
			'first_name' => array($info['firstname']),
			'middle_name' => isset($info['middlename']) ? array($info['middlename']) : array(),
			'last_name' => array($info['lastname']),
			'address1' => isset($info['address']['street']) ? array($info['address']['street']) : array(),
			'address2' => array(),
			'city_locality' => isset($info['address']['city']) ? array($info['address']['city']) : array(),
			'state_region' => isset($info['address']['region']) ? array($info['address']['region']) : array(),
			'postal_code' => isset($info['address']['postcode']) ? array($info['address']['postcode']) : array(),
			'country' => isset($info['address']['country_id']) ? array($info['address']['country_id']) : array(),
		);

		return $attributes;
	}


	/**
	 * Log in using an external authentication helper.
	 *
	 * @param array &$state  Information about the current authentication.
	 */
	public function authenticate(&$state) {
		assert('is_array($state)');

		$attributes = $this->getUser();
		if ($attributes !== NULL) {
			/*
			 * The user is already authenticated.
			 *
			 * Add the users attributes to the $state-array, and return control
			 * to the authentication process.
			 */
			$state['Attributes'] = $attributes;
			return;
		}

		/*
		 * The user isn't authenticated. We therefore need to
		 * send the user to the login page.
		 */

		/*
		 * First we add the identifier of this authentication source
		 * to the state array, so that we know where to resume.
		 */
		$state['magentoauth:AuthID'] = $this->authId;


		/*
		 * We need to save the $state-array, so that we can resume the
		 * login process after authentication.
		 *
		 * Note the second parameter to the saveState-function. This is a
		 * unique identifier for where the state was saved, and must be used
		 * again when we retrieve the state.
		 *
		 * The reason for it is to prevent
		 * attacks where the user takes a $state-array saved in one location
		 * and restores it in another location, and thus bypasses steps in
		 * the authentication process.
		 */
		$stateId = SimpleSAML_Auth_State::saveState($state, 'magentoauth:External');

		/*
		 * Now we generate a URL the user should return to after authentication.
		 * We assume that whatever authentication page we send the user to has an
		 * option to return the user to a specific page afterwards.
		 */
		$returnTo = SimpleSAML_Module::getModuleURL('magentoauth/resume.php', array(
			'State' => $stateId,
		));

		/*
		 * Get the URL of the authentication page.
		 *
		 * Here we use the getModuleURL function again, since the authentication page
		 * is also part of this module, but in a real example, this would likely be
		 * the absolute URL of the login page for the site.
		 */
		$authPage = SimpleSAML_Module::getModuleURL('magentoauth/authpage.php');

		/*
		 * The redirect to the authentication page.
		 *
		 * Note the 'ReturnTo' parameter. This must most likely be replaced with
		 * the real name of the parameter for the login page.
		 */
		SimpleSAML_Utilities::redirectTrustedURL($authPage, array(
			'ReturnTo' => $returnTo,
		));

		/*
		 * The redirect function never returns, so we never get this far.
		 */
		assert('FALSE');
	}


	/**
	 * Resume authentication process.
	 *
	 * This function resumes the authentication process after the user has
	 * entered his or her credentials.
	 *
	 * @param array &$state  The authentication state.
	 */
	public static function resume() {

		/*
		 * First we need to restore the $state-array. We should have the identifier for
		 * it in the 'State' request parameter.
		 */
		if (!isset($_REQUEST['State'])) {
			throw new SimpleSAML_Error_BadRequest('Missing "State" parameter.');
		}
		$stateId = (string)$_REQUEST['State'];

		// sanitize the input
		$sid = SimpleSAML_Utilities::parseStateID($stateId);
		if (!is_null($sid['url'])) {
			SimpleSAML_Utilities::checkURLAllowed($sid['url']);
		}

		/*
		 * Once again, note the second parameter to the loadState function. This must
		 * match the string we used in the saveState-call above.
		 */
		$state = SimpleSAML_Auth_State::loadState($stateId, 'magentoauth:External');

		/*
		 * Now we have the $state-array, and can use it to locate the authentication
		 * source.
		 */
		$source = SimpleSAML_Auth_Source::getById($state['magentoauth:AuthID']);
		if ($source === NULL) {
			/*
			 * The only way this should fail is if we remove or rename the authentication source
			 * while the user is at the login page.
			 */
			throw new SimpleSAML_Error_Exception('Could not find authentication source with id ' . $state[self::AUTHID]);
		}

		/*
		 * Make sure that we haven't switched the source type while the
		 * user was at the authentication page. This can only happen if we
		 * change config/authsources.php while an user is logging in.
		 */
		if (! ($source instanceof self)) {
			throw new SimpleSAML_Error_Exception('Authentication source type changed.');
		}


		/*
		 * OK, now we know that our current state is sane. Time to actually log the user in.
		 *
		 * First we check that the user is acutally logged in, and didn't simply skip the login page.
		 */
		$attributes = $source->getUser();
		if ($attributes === NULL) {
			/*
			 * The user isn't authenticated.
			 *
			 * Here we simply throw an exception, but we could also redirect the user back to the
			 * login page.
			 */
			throw new SimpleSAML_Error_Exception('User not authenticated after login page.');
		}

		/*
		 * So, we have a valid user. Time to resume the authentication process where we
		 * paused it in the authenticate()-function above.
		 */

		$state['Attributes'] = $attributes;
		SimpleSAML_Auth_Source::completeAuth($state);

		/*
		 * The completeAuth-function never returns, so we never get this far.
		 */
		assert('FALSE');
	}


	/**
	 * This function is called when the user start a logout operation, for example
	 * by logging out of a SP that supports single logout.
	 *
	 * @param array &$state  The logout state array.
	 */
	public function logout(&$state) {
		assert('is_array($state)');

		if (!session_id()) {
			/* session_start not called before. Do it here. */
			session_start();
		}

		/*
		 * In this example we simply remove the 'uid' from the session.
		 */
		unset($_SESSION['uid']);

		/*
		 * If we need to do a redirect to a different page, we could do this
		 * here, but in this example we don't need to do this.
		 */
	}

}
