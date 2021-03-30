<?php namespace Admin\Checklists;


use Codeception\Util\Locator;

class ChecklistsCest
{
    public function _before(\AcceptanceTester $I)
    {
        $I->loginAsTheAdmin($I);
    }

    public function tryToOpenTheChecklistsPage(\AcceptanceTester $I)
    {
        $I->wantTo('open the admin page for Checklists');
        $I->amOnAdminPage('/admin.php?page=ppch-checklists');
        $I->see('Checklists', 'h1.wp-heading-inline');
        $I->dontSee('Error');
        $I->dontSee('Warning');
        $I->dontSee('Exception');
        $I->dontSee('Notice');
    }

    public function tryToSaveAChecklist(\AcceptanceTester $I)
    {
        $I->wantTo('open the admin page for Checklists');
        $I->amOnAdminPage('/admin.php?page=ppch-checklists');
        $I->selectOption('#post-checklists-filled_excerpt_rule', 'warning');
        $I->click('Save Changes');
        $I->seeOptionIsSelected( '#post-checklists-filled_excerpt_rule', 'Recommended');
    }
}
