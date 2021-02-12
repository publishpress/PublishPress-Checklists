<?php

namespace PublishPress\Checklists\Core\Utils;


use Codeception\Example;
use UnitTester;

class HyperlinkValidatorCest
{
    /**
     * @example ["htt://invalidlink.com"]
     * @example ["httpw://invalidlink.com"]
     * @example ["http:/invalidlink.com"]
     * @example ["http:///invalidlink.com"]
     * @example ["https:///invalidlink.com"]
     * @example ["https:///invalidlink.com"]
     * @example [":https:///invalidlink.com"]
     * @example [":https:///invalidlink.com"]
     * @example ["https//invalidlink.com"]
     * @example ["tel:+134-42059f"]
     * @example ["tel:+2523552*"]
     * @example ["http://invalidlinkcom"]
     * @example ["htt//invalidlink.com/"]
     * @example ["http:/invalidlink.com"]
     * @example ["skype://test"]
     */
    public function isValidLinkWithInvalidLinkReturnsFalse(UnitTester $I, Example $example)
    {
        $validator = new HyperlinkValidator();

        $actual = $validator->isValidLink($example[0]);

        $I->assertFalse($actual);
    }

    /**
     * @example ["https://validlink.com"]
     * @example ["http://validlink.com"]
     * @example ["https://www.validlink.com"]
     * @example ["https://www.validlink.com/?arg1=3"]
     * @example ["https://validlink.com/?arg1=3&arg2=new"]
     * * @example ["https://validlink.com/?arg1=3&amp;arg2=new"]
     * @example ["tel:+5544999993820"]
     * @example ["tel:+55-44-99999-3820"]
     * @example ["tel:205-555-1212"]
     * @example ["tel:+1-205-555-1212"]
     * @example ["tel:12055551212"]
     */
    public function isValidLinkWithValidLinkReturnsTrue(UnitTester $I, Example $example)
    {
        $validator = new HyperlinkValidator();

        $actual = $validator->isValidLink($example[0]);

        $I->assertTrue($actual);
    }
}
