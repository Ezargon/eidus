<?php
/**
 * SAML 2.0 remote IdP metadata for SimpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote 
 */

/*
 * Guest IdP. allows users to sign up and register. Great for testing!
 */
$metadata['https://ssopre.us.es'] = array (
  'entityid' => 'https://ssopre.us.es',
  'contacts' => 
  array (
  ),
  'metadata-set' => 'saml20-idp-remote',
  'SingleSignOnService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://ssopre.us.es/SAML2/SSOService.php',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://ssopre.us.es/SAML2/SSOService.php',
    ),
  ),
  'SingleLogoutService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://ssopre.us.es/SAML2/SLOService.php',
    ),
  ),
  'ArtifactResolutionService' => 
  array (
  ),
  'keys' => 
  array (
    0 => 
    array (
      'encryption' => false,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIICqzCCAhQCCQCWGGa9VGkNSjANBgkqhkiG9w0BAQsFADCBmTELMAkGA1UEBhMC
RVMxEDAOBgNVBAgMB1NldmlsbGExEDAOBgNVBAcMB1NldmlsbGExHzAdBgNVBAoM
FlVuaXZlcnNpZGFkIGRlIFNldmlsbGExDDAKBgNVBAsMA1NTTzEVMBMGA1UEAwwM
c3NvcHJlLnVzLmVzMSAwHgYJKoZIhvcNAQkBFhFzb3BvcnRlLWdpcEB1cy5lczAe
Fw0xNTA5MjIwNTQ3MjlaFw0xODA5MjEwNTQ3MjlaMIGZMQswCQYDVQQGEwJFUzEQ
MA4GA1UECAwHU2V2aWxsYTEQMA4GA1UEBwwHU2V2aWxsYTEfMB0GA1UECgwWVW5p
dmVyc2lkYWQgZGUgU2V2aWxsYTEMMAoGA1UECwwDU1NPMRUwEwYDVQQDDAxzc29w
cmUudXMuZXMxIDAeBgkqhkiG9w0BCQEWEXNvcG9ydGUtZ2lwQHVzLmVzMIGfMA0G
CSqGSIb3DQEBAQUAA4GNADCBiQKBgQCiHDS+CdWfloiFYBPILeMhzc9C+9ebKYnV
mXfJOxCOjoz0Y7oIx9ypgfXlDn4ul6wlwnutU4Lj9RaaE2pLUWDg5WGqS6ZYtrBj
0NWk7I2H0f7jlv6qPkRkHy3fOoqdunxLVUyDFqFqPwVAwyfizbFkcJ7Tetqd3PLM
58AoO4LslwIDAQABMA0GCSqGSIb3DQEBCwUAA4GBAHnjIqeaNGkh4SdkCzZTkiWa
vKNZKNeQft5WiATuFv64tb9a/6c5JfM9/xSZtY7CwoZfUQwPaPxtNiUBCJEVIk4v
D2Is5vLrx/gMWCyfQeNDKFEXu4o9rdzukLBbrkhJlL2qWNAUg1Ss/B1UqATYZDPW
yK6BTKgV5vmX+piPR9UE
',
    ),
    1 => 
    array (
      'encryption' => true,
      'signing' => false,
      'type' => 'X509Certificate',
      'X509Certificate' => 'MIICqzCCAhQCCQCWGGa9VGkNSjANBgkqhkiG9w0BAQsFADCBmTELMAkGA1UEBhMC
RVMxEDAOBgNVBAgMB1NldmlsbGExEDAOBgNVBAcMB1NldmlsbGExHzAdBgNVBAoM
FlVuaXZlcnNpZGFkIGRlIFNldmlsbGExDDAKBgNVBAsMA1NTTzEVMBMGA1UEAwwM
c3NvcHJlLnVzLmVzMSAwHgYJKoZIhvcNAQkBFhFzb3BvcnRlLWdpcEB1cy5lczAe
Fw0xNTA5MjIwNTQ3MjlaFw0xODA5MjEwNTQ3MjlaMIGZMQswCQYDVQQGEwJFUzEQ
MA4GA1UECAwHU2V2aWxsYTEQMA4GA1UEBwwHU2V2aWxsYTEfMB0GA1UECgwWVW5p
dmVyc2lkYWQgZGUgU2V2aWxsYTEMMAoGA1UECwwDU1NPMRUwEwYDVQQDDAxzc29w
cmUudXMuZXMxIDAeBgkqhkiG9w0BCQEWEXNvcG9ydGUtZ2lwQHVzLmVzMIGfMA0G
CSqGSIb3DQEBAQUAA4GNADCBiQKBgQCiHDS+CdWfloiFYBPILeMhzc9C+9ebKYnV
mXfJOxCOjoz0Y7oIx9ypgfXlDn4ul6wlwnutU4Lj9RaaE2pLUWDg5WGqS6ZYtrBj
0NWk7I2H0f7jlv6qPkRkHy3fOoqdunxLVUyDFqFqPwVAwyfizbFkcJ7Tetqd3PLM
58AoO4LslwIDAQABMA0GCSqGSIb3DQEBCwUAA4GBAHnjIqeaNGkh4SdkCzZTkiWa
vKNZKNeQft5WiATuFv64tb9a/6c5JfM9/xSZtY7CwoZfUQwPaPxtNiUBCJEVIk4v
D2Is5vLrx/gMWCyfQeNDKFEXu4o9rdzukLBbrkhJlL2qWNAUg1Ss/B1UqATYZDPW
yK6BTKgV5vmX+piPR9UE
',
    ),
  ),
);
