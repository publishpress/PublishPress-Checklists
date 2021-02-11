<?php

class FrontendCest
{
    public function tryToLoadTheHomePage(\AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Just another WordPress site');
        $I->dontSee('error');
        $I->dontSee('notice');
        $I->dontSee('warning');
    }
}
