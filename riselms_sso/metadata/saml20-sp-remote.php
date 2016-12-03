<?php
/**
 * SAML 2.0 remote SP metadata for simpleSAMLphp.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-sp-remote
 */

/*
 * Example simpleSAMLphp SAML 2.0 SP
 */

// SAML 2.0 SP Metadata

$domains = [
	'riselms.dev',
	'demo.riselms.com',
	'stage.riselms.com',
	'riselms.com',
];

foreach ($domains as $domain) {
	// Commented out -- used for testing on basetheme staging
	// $metadata['https://brainmd.'.$domain] = array(
	// 	'AssertionConsumerService' => 'https://brainmd.'.$domain.'/sso/saml/assert',
	// 	'SingleLogoutService' => 'https://brainmd.'.$domain.'/sso/saml/logout',
	// );
	$metadata['https://amenuniversity.'.$domain] = array(
		'AssertionConsumerService' => 'https://amenuniversity.'.$domain.'/sso/saml/assert',
		'SingleLogoutService' => 'https://amenuniversity.'.$domain.'/sso/saml/logout',
	);
}

$metadata['https://amenuniversity.com'] = array(
	'AssertionConsumerService' => 'https://amenuniversity.com/sso/saml/assert',
	'SingleLogoutService' => 'https://amenuniversity.com/sso/saml/logout',
);

$metadata['https://www.amenuniversity.com'] = array(
	'AssertionConsumerService' => 'https://www.amenuniversity.com/sso/saml/assert',
	'SingleLogoutService' => 'https://www.amenuniversity.com/sso/saml/logout',
);

/*
 * This example shows an example config that works with Google Apps for education.
 * What is important is that you have an attribute in your IdP that maps to the local part of the email address
 * at Google Apps. In example, if your google account is foo.com, and you have a user that has an email john@foo.com, then you
 * must set the simplesaml.nameidattribute to be the name of an attribute that for this user has the value of 'john'.
 */
// $metadata['google.com'] = array(
// 	'AssertionConsumerService' => 'https://www.google.com/a/g.feide.no/acs',
// 	'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
// 	'simplesaml.nameidattribute' => 'uid',
// 	'simplesaml.attributes' => FALSE,
// );
