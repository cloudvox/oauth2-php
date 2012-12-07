<?php
namespace OAuth2\Exception;

/**
 * @category OAuth2
 * @package  OAuth2
 *
 */
use OAuth2\Exception\ServerException;

/**
 * @category OAuth2
 * @package  OAuth2
 * Send an error header with the given realm and an error, if provided.
 * Suitable for the bearer token type.
 *
 * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-bearer-04#section-2.4
 *
 * @ingroup oauth2_error
 */
class AuthenticateException extends ServerException {

    protected $header;

    /**
     *
     * @param $http_status_code
     * HTTP status code message as predefined.
     * @param $error
     * The "error" attribute is used to provide the client with the reason
     * why the access request was declined.
     * @param $error_description
     * (optional) The "error_description" attribute provides a human-readable text
     * containing additional information, used to assist in the understanding
     * and resolution of the error occurred.
     * @param $scope
     * A space-delimited list of scope values indicating the required scope
     * of the access token for accessing the requested resource.
     */
    public function __construct($httpCode, $tokenType, $realm, $error, $error_description = NULL, $scope = NULL) {
        $scope = (array) $scope;
        parent::__construct($httpCode, $error, $error_description);
        if ($scope) {
            $this->errorData['scope'] = implode(' ',$scope) ;
        }
        // Build header
        $this->header = sprintf('WWW-Authenticate: %s realm="%s"', ucwords($tokenType), $realm);
        foreach ( $this->errorData as $key => $value ) {
            $this->header .= sprintf(', %s="%s"',$key,$value);
        }
    }

    /**
     * Send out HTTP headers for JSON.
     *
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-5.1
     * @see http://tools.ietf.org/html/draft-ietf-oauth-v2-20#section-5.2
     *
     * @ingroup oauth2_section_5
     */
    protected function sendHeaders() {
        parent::sendHeaders();
        header($this->header);
    }
}
