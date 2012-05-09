<?php
namespace OAuth2Tests;

/**
 * \OAuth2\Server\Server test cases that invovle capturing output.
 */
class OAuth2OutputTest extends \PHPUnit_Extensions_OutputTestCase {

  /**
   * @var \OAuth2\Server\Server
   */
  private $fixture;

  /**
   * Tests \OAuth2\Server\Server->grantAccessToken() with successful Auth code grant
   *
   */
  public function testGrantAccessTokenWithGrantAuthCodeSuccess() {
    $inputData = array('grant_type' => \OAuth2\Server\Server::GRANT_TYPE_AUTH_CODE, 'redirect_uri' => 'http://www.example.com/my/subdir', 'client_id' => 'my_little_app', 'client_secret' => 'b', 'code'=> 'foo');
    $storedToken = array('redirect_uri' => 'http://www.example.com', 'client_id' => 'my_little_app', 'expires' => time() + 60);

    $mockStorage = $this->createBaseMock('\OAuth2\Grant\GrantCodeInterface');
    $mockStorage->expects($this->any())
      ->method('getAuthCode')
      ->will($this->returnValue($storedToken));

    // Successful token grant will return a JSON encoded token:
    $this->expectOutputRegex('/{"access_token":".*","expires_in":\d+,"token_type":"bearer"/');
    $this->fixture = new \OAuth2\Server\Server($mockStorage);
    $this->fixture->grantAccessToken($inputData, array());
  }

  /**
   * Tests \OAuth2\Server\Server->grantAccessToken() with successful Auth code grant, but without redreict_uri in the input
   */
  public function testGrantAccessTokenWithGrantAuthCodeSuccessWithoutRedirect() {
    $inputData = array('grant_type' => \OAuth2\Server\Server::GRANT_TYPE_AUTH_CODE, 'client_id' => 'my_little_app', 'client_secret' => 'b', 'code'=> 'foo');
    $storedToken = array('redirect_uri' => 'http://www.example.com', 'client_id' => 'my_little_app', 'expires' => time() + 60);

    $mockStorage = $this->createBaseMock('\OAuth2\Grant\GrantCodeInterface');
    $mockStorage->expects($this->any())
      ->method('getAuthCode')
      ->will($this->returnValue($storedToken));

    // Successful token grant will return a JSON encoded token:
    $this->expectOutputRegex('/{"access_token":".*","expires_in":\d+,"token_type":"bearer"/');
    $this->fixture = new \OAuth2\Server\Server($mockStorage);
    $this->fixture->setVariable(\OAuth2\Server\Server::CONFIG_ENFORCE_INPUT_REDIRECT, false);
    $this->fixture->grantAccessToken($inputData, array());
  }

// Utility methods

  /**
   *
   * @param string $interfaceName
   */
  protected function createBaseMock($interfaceName) {
    $mockStorage = $this->getMock($interfaceName);
    $mockStorage->expects($this->any())
      ->method('checkClientCredentials')
      ->will($this->returnValue(TRUE)); // Always return true for any combination of user/pass
    $mockStorage->expects($this->any())
      ->method('checkRestrictedGrantType')
      ->will($this->returnValue(TRUE)); // Always return true for any combination of user/pass

     return $mockStorage;
  }

}
