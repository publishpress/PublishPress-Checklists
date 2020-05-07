<?php

class FrontendCest
{
    public function tryToLoadTheHomePage(\AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Hello world!');
        $I->dontSee('error');
        $I->dontSee('notice');
        $I->dontSee('warning');
    }
}
