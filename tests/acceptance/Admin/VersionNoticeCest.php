<?php namespace Admin;

class VersionNoticeCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->loginAsTheAdmin($I);
    }

    public function tryToSeeTheVersionNoticeBanner(\AcceptanceTester $I)
    {
        $I->wantTo('see the top banner: Upgrade to Pro');
        $I->amOnAdminPage('/admin.php?page=ppch-checklists');
        $I->see('You\'re using PublishPress Checklists Free', '#wpcontent .pp-version-notice-bold-purple');
        $I->see('Upgrade to Pro', '#wpcontent .pp-version-notice-bold-purple-button');
    }

    public function tryToSeeTheVersionNoticeMenu(\AcceptanceTester $I)
    {
        $I->wantTo('see the submenu: Upgrade to Pro');
        $I->amOnAdminPage('/');
        $I->seeElementInDOM(
            '.toplevel_page_ppch-checklists li a.pp-version-notice-upgrade-menu-item.publishpress-checklists'
        );
    }
}
