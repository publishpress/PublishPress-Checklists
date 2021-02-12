<?php

namespace PublishPress\Checklists\Core\Utils;


use Codeception\Example;
use UnitTester;

class HyperlinkExtractorCest
{

    public function extractLinksFromHyperlinksInTextWithNoHyperlinksReturnEmptyArray(UnitTester $I)
    {
        $extractor = new HyperlinkExtractor();

        $actual = $extractor->extractLinksFromHyperlinksInText(
            'This is <b>text</b> with no hyperlinks. <img src="/img/test.jpg />'
        );

        $I->assertIsArray($actual);
        $I->assertEmpty($actual);
    }

    public function extractLinksFromHyperlinksInTextWithHyperlinksReturnArrayWithAllTheLinks(UnitTester $I)
    {
        $extractor = new HyperlinkExtractor();

        $actual = $extractor->extractLinksFromHyperlinksInText(
            '
                    This is <b>text</b> with many hyperlinks. <img src="/img/test.jpg />
                    Lorem ipsum <a href="https://www.test.example.com">dolor</a> sit amet, consectetur adipiscing elit.
                    Ut luctus eleifend tristique.
                    Suspendisse aliquet fringilla <a class="test" href="https://test2.example.com/?arg1=14&arg2=6">nisi
                    vel</a> iaculis. Aliquam porta lacus dolor, ut pharetra lectus rhoncus ac. Nulla eu odio nec dolor
                    <a rel="alternate" href="http://example.com/">accumsan</a> sollicitudin. Pellentesque pulvinar ac
                    risus id elementum. <a href="tel:203-525-1214">Vestibulum</a> dignissim dolor ut ultricies faucibus.
                    Fusce quis posuere nunc, <a href="tel:+1-205-555-1212">nec</a> imperdiet ligula. Integer consectetur
                    vehicula nisl, <a href="http://validlink.com">sit amet</a> dictum risus rutrum ut. Integer eget dui
                    dui. Nunc sit <a href="http://invalidlinkcom">amet</a> nisl auctor, laoreet mauris in, lacinia
                    <a href="htt//invalidlink.com/">augue</a>. Interdum <a href="http:/invalidlink.com">et malesuada</a>
                    fames ac ante ipsum primis in <a href="skype://test">faucibus. Phasellus ac sapien et sapien
                    tincidunt fringilla non quis purus.
                 '
        );

        $expected = [
            'https://www.test.example.com',
            'https://test2.example.com/?arg1=14&arg2=6',
            'http://example.com/',
            'tel:203-525-1214',
            'tel:+1-205-555-1212',
            'http://validlink.com',
            'http://invalidlinkcom',
            'htt//invalidlink.com/',
            'http:/invalidlink.com',
            'skype://test',
        ];

        $I->assertIsArray($actual);
        $I->assertEquals($expected, $actual);
    }
}
