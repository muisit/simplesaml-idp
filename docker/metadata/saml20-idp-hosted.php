<?php
/**
 * SAML 2.0 IdP configuration for SimpleSAMLphp.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-hosted
 */


// For compatibility with Shib SPs we need to put the scopes this IdP supports into the metadata.
// These first lines read the logins.json file and process the domain names from the SHO attribute 
// and add them as scopes to the metadata
$fname = realpath("/var/www/simplesamlphp/config/logins.json");
$well_known_logins=array();
if(file_exists($fname) && is_readable($fname) && strncmp($fname, "/var/www/simplesamlphp", strlen("/var/www/simplesamlphp")) == 0) {
    $well_known_logins = json_decode ( file_get_contents($fname), true);
    if(!is_array($well_known_logins)) {
        $well_known_logins = array();
    }
}

foreach($well_known_logins as $key => $val) {
  if ($key != "0") {
    $domains[] = $val["schacHomeOrganization"];
  }
}
$domains = array_unique($domains);


$metadata['__DYNAMIC:1__'] = array(
	/*
	 * The hostname of the server (VHOST) that will use this SAML entity.
	 *
	 * Can be '__DEFAULT__', to use this entry by default.
	 */
	'host' => '__DEFAULT__',

	// X.509 key and certificate. Relative to the cert directory.
	'privatekey' => 'idp.example.org.pem',
	'certificate' => 'idp.example.org.crt',

	/*
	 * Authentication source to use. Must be one that is configured in
	 * 'config/authsources.php'.
	 */
	'auth' => 'example-userpass',
	'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

	/*
	 * WARNING: SHA-1 is disallowed starting January the 1st, 2014.
	 *
	 * Uncomment the following option to start using SHA-256 for your signatures.
	 * Currently, SimpleSAMLphp defaults to SHA-1, which has been deprecated since
	 * 2011, and will be disallowed by NIST as of 2014. Please refer to the following
	 * document for more information:
	 *
	 * http://csrc.nist.gov/publications/nistpubs/800-131A/sp800-131A.pdf
	 *
	 * If you are uncertain about service providers supporting SHA-256 or other
	 * algorithms of the SHA-2 family, you can configure it individually in the
	 * SP-remote metadata set for those that support it. Once you are certain that
	 * all your configured SPs support SHA-2, you can safely remove the configuration
	 * options in the SP-remote metadata set and uncomment the following option.
	 *
	 * Please refer to the IdP hosted reference for more information.
	 */
	//'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

	// Scopes
	'scope' => $domains,
	
	/* Uncomment the following to use the uri NameFormat on attributes. */
	//'userid.attribute' => 'eduPersonPrincipalName',
	'authproc' => array(
	    2 => array(
	        'class' => 'saml:AttributeNameID',
	        'attribute' => 'uid',
	        'SPNameQualifier' => FALSE,
	        'Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified'
	    ),
		//40 => 'core:AttributeRealm',
		//100 => array('class' => 'core:AttributeMap', 'name2urn'),
	),

	/*
	 * Uncomment the following to specify the registration information in the
	 * exported metadata. Refer to:
     * http://docs.oasis-open.org/security/saml/Post2.0/saml-metadata-rpi/v1.0/cs01/saml-metadata-rpi-v1.0-cs01.html
	 * for more information.
	 */
	/*
	'RegistrationInfo' => array(
		'authority' => 'urn:mace:example.org',
		'instant' => '2008-01-17T11:28:03Z',
		'policies' => array(
			'en' => 'http://example.org/policy',
			'es' => 'http://example.org/politica',
		),
	),
	*/
);
