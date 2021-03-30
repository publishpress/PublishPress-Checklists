<?php namespace Admin;

use Codeception\Util\Locator;

class MenuCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->loginAsTheAdmin($I);
    }

    public function tryToSeeTheChecklistsMenu(\AcceptanceTester $I)
    {
        $I->wantTo('see the main menu item: Checklists');
        $I->amOnAdminPage('/');
        $I->seeElement('.wp-has-submenu.menu-top.toplevel_page_ppch-checklists.menu-top-last');
        $I->seeElementInDOM(Locator::contains('.wp-menu-name', 'Checklists'));
    }

    public function tryToSeeTheSubmenuForChecklists(\AcceptanceTester $I)
    {
        $I->wantTo('see the submenu: Checklists');
        $I->amOnAdminPage('/');
        $I->seeElementInDOM(
            Locator::contains('.toplevel_page_ppch-checklists li.wp-first-item a.wp-first-item', 'Checklists')
        );
    }

    public function tryToSeeTheSubmenuForSettings(\AcceptanceTester $I)
    {
        $I->wantTo('see the submenu: Settings');
        $I->amOnAdminPage('/');
        $I->seeElementInDOM(Locator::contains('.toplevel_page_ppch-checklists li a', 'Settings'));
    }
}
