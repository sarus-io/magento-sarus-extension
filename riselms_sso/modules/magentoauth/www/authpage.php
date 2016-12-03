<?php

define('MAGENTO_STORE_ID', 1);

/**
 * This page serves as a dummy login page.
 *
 * Note that we don't actually validate the user in this example. This page
 * just serves to make the example work out of the box.
 *
 * @package simpleSAMLphp
 */

if (!isset($_REQUEST['ReturnTo'])) {
	die('Missing ReturnTo parameter.');
}

$returnTo = SimpleSAML_Utilities::checkURLAllowed($_REQUEST['ReturnTo']);


/*
 * The following piece of code would never be found in a real authentication page. Its
 * purpose in this example is to make this example safer in the case where the
 * administrator of * the IdP leaves the exampleauth-module enabled in a production
 * environment.
 *
 * What we do here is to extract the $state-array identifier, and check that it belongs to
 * the exampleauth:External process.
 */

if (!preg_match('@State=(.*)@', $returnTo, $matches)) {
	die('Invalid ReturnTo URL for this example.');
}
$stateId = urldecode($matches[1]);

// sanitize the input
$sid = SimpleSAML_Utilities::parseStateID($stateId);
if (!is_null($sid['url'])) {
	SimpleSAML_Utilities::checkURLAllowed($sid['url']);
}

SimpleSAML_Auth_State::loadState($stateId, 'magentoauth:External');

/*
 * The loadState-function will not return if the second parameter does not
 * match the parameter passed to saveState, so by now we know that we arrived here
 * through the exampleauth:External authentication page.
 */



/*
 * Time to handle login responses.
 * Since this is a dummy example, we accept any data.
 */

$loginFailed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$login = (string)$_REQUEST['username'];
	$password = (string)$_REQUEST['password'];


	$config = SimpleSAML_Configuration::getOptionalConfig('authsources.php');
	$configitem = $config->getConfigItem('magento-auth');

	require_once $configitem->getValue('mage_path');


	/* Store or website code */
	$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

	/* Run store or run website */
	$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

	Mage::init($mageRunCode, $mageRunType, array());

	$customer = Mage::getModel("customer/customer")->setWebsiteId(MAGENTO_STORE_ID);

	$customer->loadByEmail($login);

	if(empty($customer)) {
		$errorMsg = Mage::helper('customer')->__('Invalid login or password.');
		$loginFailed = true;
	}elseif ($customer->getConfirmation() && $customer->isConfirmationRequired()) {
		$errorMsg = Mage::helper('customer')->__('This account is not confirmed.');
		$loginFailed = true;
	}elseif (!$customer->validatePassword($password)) {
		$errorMsg = Mage::helper('customer')->__('Invalid login or password.');
		$loginFailed = true;
	}else{

		if (!session_id()) {
			/* session_start not called before. Do it here. */
			session_start();
		}

		$data = $customer->getData();

		if ($billingAddress = $customer->getPrimaryBillingAddress()) {
			$data['address'] = $billingAddress->getData();
		} else {
			$data['address'] = array();
		}

		$_SESSION['uid'] = $data['entity_id'];
		$_SESSION['customer_info'] = $data;

		SimpleSAML_Utilities::redirectTrustedURL($returnTo);

	}

}


/*
 * If we get this far, we need to show the login page to the user.
 */
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Welcome to Amen University</title>
		<link rel="stylesheet" href="/skin/frontend/swarming/default/css/style-base-blessed1.css">
		<link rel="stylesheet" href="/skin/frontend/swarming/default/css/style-base-blessed2.css">
		<style>
<?php
// We'll hold our styles with a heredoc so we can minify them below
$styles=<<<STYLE
html,
body {
	height: 100%;
	background-color: #e7e7e7;
}

body {
	background: url('/skin/frontend/swarming/default/images/swirl-pattern-dark.jpg') repeat-x;
}

.login-form {
	position: relative;
	top: 25%;
	overflow: auto;

	margin: 0 auto;
	padding: 2em;
	max-width: 22em;

	background: #fff;
}
@media (min-width: 310px) {
	.login-form {
		border-radius: 5px;
	}
}
@media (max-height: 700px) {
	.login-form {
		top: 13%;
	}
}
@media (max-height: 550px) {
	.login-form {
		top: 3%;
	}
}
h1 {
	margin: 0 0 2em;
	text-transform: uppercase;
	text-align: center;
	font-size: 1.1em;
}
img {
	display: block;
	margin: 0 auto;
	max-width: 90%;
}

.alert {
	margin: 1em 0 -1.5em;
}

form {
	margin-top: 2.5em;
}
label {
	font-size: 0.9em;
	margin-bottom: 0;
}
.form-control {
	color: #404040;
}
#username {
	margin-bottom: 0.5em;
}
#password {
	margin-bottom: 0.5em;
}
a.forgot-password {
	display: block;
	margin-bottom: 2.5em;
	text-align: center;
}
button {
	width: 100%;
}
.support {
	color: #888;
	font-size: 0.9em;
	line-height: 1.6;
	margin-top: 2.5em;
	text-align: center;
}
.support a {
	color: #666;
}
.support a:hover,
.support a:focus {
	text-decoration: underline;
}
STYLE;

// Minify the CSS
$styles = str_replace(': ', ':', $styles);
$styles = str_replace(' {', '{', $styles);
$styles = str_replace(array("\r\n", "\r", "\n", "\t"), '', $styles);
echo $styles;
?>
		</style>
	</head>

	<body>

		<div class="login-form">
			<h1>Welcome to Amen University</h1>

			<img src="/skin/frontend/swarming/default/images/amen-university-logo.png">

			<?php if (isset($loginFailed) && isset($errorMsg) && $loginFailed): ?>
				<div class="alert alert-danger"><?php echo $errorMsg ?></div>
			<?php endif; ?>

			<form action="?" method="post">
				<div>
					<label for="username" class="control-label">Username</label>
					<input type="text" name="username" class="form-control" id="username" placeholder="your@email.com">
				</div>
				<div>
					<label for="password" class="control-label">Password</label>
					<input type="password" name="password" class="form-control" id="password">
				</div>

				<div>
					<a class="forgot-password" href="/customer/account/forgotpassword">Forgot your password?</a>
				</div>

				<?php if (isset($returnTo)): ?>
				<input type="hidden" name="ReturnTo" value="<?php echo htmlspecialchars($returnTo) ?>">
				<?php endif; ?>

				<div>
					<button type="submit" class="btn btn-primary">Sign In</button>
				</div>

				<div class="support">
					For Amen University Support contact:<br>
					<a href="tel:888-850-5622">888-850-5622</a><br>
					<a href="mailto:support@amenuniversity.com">support@amenuniversity.com</a>
				</div>
			</form>
		</div>
	</body>
</html>