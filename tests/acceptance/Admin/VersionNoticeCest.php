<?php namespace Admin;

class VersionNoticeCest
{
    public function tryToSeeTheVersionNoticeBanner(\AcceptanceTester $I)
    {
        $I->wantTo('see the top banner: Upgrade to Pro');
        $I->loginAsAdmin();
        $I->amOnAdminPage('/admin.php?page=ppch-checklists');
        $I->see('You\'re using PublishPress Checklists Free', '#wpcontent .pp-version-notice-bold-purple');
        $I->see('Upgrade to Pro', '#wpcontent .pp-version-notice-bold-purple .pp-version-notice-bold-purple-button');
    }

    public function tryToSeeTheVersionNoticeMenu(\AcceptanceTester $I)
    {
        $I->wantTo('see the submenu: Upgrade to Pro');
        $I->loginAsAdmin();
        $I->amOnAdminPage('/');
        $I->seeElement('.pp-version-notice-upgrade-menu-item.publishpress-checklists');
        $I->see(
            'Upgrade to Pro',
            '.toplevel_page_ppch-checklists li a.pp-version-notice-upgrade-menu-item.publishpress-checklists',
        );
    }
}
