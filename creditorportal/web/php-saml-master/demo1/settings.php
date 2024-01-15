<?php

    $spBaseUrl = 'http://eb.benefitz.in/php-saml-master'; //or http://<your_domain>

    $settingsInfo = array (
        'sp' => array (
            'entityId' => $spBaseUrl.'/demo1/metadata.php',
            'assertionConsumerService' => array (
                'url' => $spBaseUrl.'/demo1/index.php?acs',
            ),
            'singleLogoutService' => array (
                'url' => $spBaseUrl.'/demo1/index.php?sls',
            ),
            'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
        ),
        'idp' => array (
            'entityId' => 'https://app.onelogin.com/saml/metadata/dbe6c717-aadf-4504-a5b8-0ac24558c1b2',
            'singleSignOnService' => array (
                'url' => 'https://sahil2.onelogin.com/trust/saml2/http-post/sso/dbe6c717-aadf-4504-a5b8-0ac24558c1b2',
            ),
            'singleLogoutService' => array (
                'url' => 'https://sahil2.onelogin.com/trust/saml2/http-redirect/slo/1641745',
            ),
            'x509cert' => '-----BEGIN CERTIFICATE-----
MIID2DCCAsCgAwIBAgIULcIbMfpyZP1xeJTwI2zTtZoepP0wDQYJKoZIhvcNAQEF
BQAwRDEPMA0GA1UECgwGc2FoaWwyMRUwEwYDVQQLDAxPbmVMb2dpbiBJZFAxGjAY
BgNVBAMMEU9uZUxvZ2luIEFjY291bnQgMB4XDTIyMDExMzE0MzgyMFoXDTI3MDEx
MzE0MzgyMFowRDEPMA0GA1UECgwGc2FoaWwyMRUwEwYDVQQLDAxPbmVMb2dpbiBJ
ZFAxGjAYBgNVBAMMEU9uZUxvZ2luIEFjY291bnQgMIIBIjANBgkqhkiG9w0BAQEF
AAOCAQ8AMIIBCgKCAQEAwCgQfA3bhmaewqvIwidTVkn/2yl2z24b4bkKNGTC/bYk
TQCtgUCiKzBdLKpjwpREZwAMakOBycvrv5elHuNR60XQxOjRCFonzKo7DGgG9h66
LUndX2uKl5TQQuZgEcNZbrDfn0S26jKUZPYfc6WPSxJ6XRB4k5MSzJSMUHWF2aVy
eP4gzxiph0ClsdnIQYPnc4kNNtppZZQHDjSmKEpHMTyPigu3rwxXoHU47dHygRGI
oyJCM5wL1GDIG/2f8wuOAfE4FAv+HAuCRuW2lyrmeZyGkyBDMapnw7wmAUi9pufa
gjMbRSyL//cCNrInxQgIKpVBxCtj+xSo4ycr5v3uDwIDAQABo4HBMIG+MAwGA1Ud
EwEB/wQCMAAwHQYDVR0OBBYEFJr1QWHT0Vn0MJraRZjbchpIksYYMH8GA1UdIwR4
MHaAFJr1QWHT0Vn0MJraRZjbchpIksYYoUikRjBEMQ8wDQYDVQQKDAZzYWhpbDIx
FTATBgNVBAsMDE9uZUxvZ2luIElkUDEaMBgGA1UEAwwRT25lTG9naW4gQWNjb3Vu
dCCCFC3CGzH6cmT9cXiU8CNs07WaHqT9MA4GA1UdDwEB/wQEAwIHgDANBgkqhkiG
9w0BAQUFAAOCAQEAez91WZUvCk77DLTNigZHFfOIDo14rRMcnBHPCtddkG1ZLRBJ
dsNRRjEud1CUnm9kIAePg+hhK4W+AUU8dNk6VmQfDowsT0NpZ9WvU8Ti2eDZUZe0
ZAoC/mIcqGQbN7iAPyZ2nnC0+09uhYBLPXDptMgkw+exD20KUgxHk/pTbWL0Lw+W
in2NZHwuGC4P4irAvAqe9P2iGaXkZAKMjtswiITr/3khjLS5+fupCl3h47tDVz/V
GnRiN0UQoeWhtZdJDGoS9HOm97Z3qTLp/55+NRTwvTuWJCWRWLKhEdLyUjAi/HLU
GaiDf3lCQldLr21qYnnGIK3rLf7QshJNE1F1DA==
-----END CERTIFICATE-----
',
        ),
    );
