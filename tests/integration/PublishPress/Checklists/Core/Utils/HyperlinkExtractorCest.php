<?php

namespace PublishPress\Checklists\Core\Utils;


use Codeception\Example;
use WpunitTester;

class HyperlinkExtractorCest
{

    public function extractLinksFromHyperlinksInTextWithNoHyperlinksReturnEmptyArray(WpunitTester $I)
    {
        $extractor = new HyperlinkExtractor();

        $actual = $extractor->extractLinksFromHyperlinksInText(
            'This is <b>text</b> with no hyperlinks. <img src="/img/test.jpg />'
        );

        $I->assertIsArray($actual);
        $I->assertEmpty($actual);
    }

    public function extractLinksFromHyperlinksInTextWithHyperlinksReturnArrayWithAllTheLinks(WpunitTester $I)
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
                    Emails:
                        <a href="mailto:test@example.com">Simple email</a>
                        <a href="mailto:test@example.com?subject=Mail from Our Site">Email and Subject email</a>
                        <a href="mailto:someone@yoursite.com?cc=someoneelse@theirsite.com, another@thatsite.com, me@mysite.com&bcc=lastperson@theirsite.com&subject=Big%20News">Email and CC/BCC</a>
                        <a href="mailto:someone@yoursite.com?cc=someoneelse@theirsite.com, another@thatsite.com, me@mysite.com&bcc=lastperson@theirsite.com&subject=Big%20News&body=Body-goes-here">Email and Body</a>
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
            'mailto:test@example.com',
            'mailto:test@example.com?subject=Mail from Our Site',
            'mailto:someone@yoursite.com?cc=someoneelse@theirsite.com, another@thatsite.com, me@mysite.com&bcc=lastperson@theirsite.com&subject=Big%20News',
            'mailto:someone@yoursite.com?cc=someoneelse@theirsite.com, another@thatsite.com, me@mysite.com&bcc=lastperson@theirsite.com&subject=Big%20News&body=Body-goes-here',
        ];

        $I->assertIsArray($actual);
        $I->assertEquals($expected, $actual);
    }
}
