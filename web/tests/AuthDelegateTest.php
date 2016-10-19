<?php
    require_once __DIR__.'/../delegates/auth_delegate.php';

    class AuthDelegateTest extends PHPUnit_Framework_TestCase {
        
        public function setUp() {
            parent::setUp();
            $this->user = array('id' => '1',
                          'name' => 'Mark',
                          'email' => 'mark@facebook.com',
                          'avatar_path' => '/images/mark.jpg',
                          'password_hash' => '891ba5a7e4c3793a00ad93848084ddb1');
        }
        
        public function tearDown() {
            parent::tearDown();
            unset($this->user);
        }
        
        public function testGetUserByEmail() {
            $databaseUser = get_user_by_email('mark@facebook.com');
            $this->assertEquals($this->user['id'], $databaseUser['id']);
        }
        
        public function testCorrectPasswordForUser() {
            $this->assertFalse(correct_password_for_user($this->user, 'wrongpassword'));
            $this->assertTrue(correct_password_for_user($this->user, 'friendface'));
        }
        
    }

?>