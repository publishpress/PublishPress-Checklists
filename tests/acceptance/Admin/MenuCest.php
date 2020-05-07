<?php namespace Admin;

use Codeception\Util\Locator;

class MenuCest
{
    public function tryToSeeTheChecklistsMenu(\AcceptanceTester $I)
    {
        $I->wantTo('see the main menu item: Checklists');
        $I->loginAsAdmin();
        $I->amOnAdminPage('/');
        $I->seeElement('.wp-has-submenu.menu-top.toplevel_page_ppch-checklists.menu-top-last');
        $I->seeElement(Locator::contains('.wp-menu-name', 'Checklists'));
    }

    public function tryToSeeTheSubmenuForChecklists(\AcceptanceTester $I)
    {
        $I->wantTo('see the submenu: Checklists');
        $I->loginAsAdmin();
        $I->amOnAdminPage('/');
        $I->seeElement(
            Locator::contains('.toplevel_page_ppch-checklists li.wp-first-item a.wp-first-item', 'Checklists')
        );
    }

    public function tryToSeeTheSubmenuForSettings(\AcceptanceTester $I)
    {
        $I->wantTo('see the submenu: Settings');
        $I->loginAsAdmin();
        $I->amOnAdminPage('/');
        $I->seeElement(Locator::contains('.toplevel_page_ppch-checklists li a', 'Settings'));
    }
}
