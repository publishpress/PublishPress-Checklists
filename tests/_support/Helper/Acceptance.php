<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
    protected $adminLogin;

    protected $adminPassword = 'secret';

    public function loginAsTheAdmin(\AcceptanceTester $I)
    {
        $userId = $I->factory()->user->create(
            [
                'role'       => 'administrator',
                'user_pass'  => $this->adminPassword,
            ]
        );

        $user = get_user_by('ID', $userId);

        $this->adminLogin = $user->user_login;
        $I->loginAs($this->adminLogin, $this->adminPassword);
    }
}
