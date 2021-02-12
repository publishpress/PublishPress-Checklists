<?php namespace core\Requirement;


use PublishPress\Checklists\Core\Requirement\Validate_links;
use WpunitTester;
use Codeception\Example;

class Validate_linksCest
{
    public function _before(WpunitTester $I)
    {
    }

    public function getCurrentStatusForContentWithoutLinksReturnsTrue(WpunitTester $I)
    {
        $uid = microtime();

        $I->wantTo('call getCurrentStatus for content without links to return true');
        $postId = $I->factory()->post->create(
            [
                'post_name'    => 'the_post_' . $uid,
                'post_title'   => 'The post ' . $uid,
                'post_content' => 'This is a post with no links.',
                'post_status'  => 'publish',
            ]
        );

        $post = get_post($postId);

        $requirementInstance = new Validate_links(null, null);
        $status = $requirementInstance->get_current_status($post, '');

        $I->assertTrue($status);
    }

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
     */
    public function getCurrentStatusForContentWithInvalidLinkReturnsFalse(WpunitTester $I, Example $example)
    {
        $uid = microtime();

        $I->wantTo('call getCurrentStatus for content with links to return false');
        $postId = $I->factory()->post->create(
            [
                'post_name'    => 'the_post_' . $uid,
                'post_title'   => 'The post ' . $uid,
                'post_content' => 'This is a post has an <a href="' . $example[0] . '">invalid link</a>.',
                'post_status'  => 'publish',
            ]
        );

        $post = get_post($postId);

        $requirementInstance = new Validate_links(null, null);
        $status = $requirementInstance->get_current_status($post, '');

        $I->assertFalse($status);
    }

    /**
     * @example ["https://validlink.com"]
     * @example ["http://validlink.com"]
     * @example ["https://www.validlink.com"]
     * @example ["https://www.validlink.com/?arg1=3"]
     * @example ["https://validlink.com/?arg1=3&arg2=new"]
     * @example ["https://validlink.com/?arg1=3&amp;arg2=new"]
     * @example ["tel:+5544999993820"]
     * @example ["tel:+55-44-99999-3820"]
     * @example ["tel:205-555-1212"]
     * @example ["tel:+1-205-555-1212"]
     * @example ["tel:12055551212"]
     */
    public function getCurrentStatusForContentWithValidLinkReturnsTrue(WpunitTester $I, Example $example)
    {
        $uid = microtime();

        $I->wantTo('call getCurrentStatus for content with links to return false');
        $postId = $I->factory()->post->create(
            [
                'post_name'    => 'the_post_' . $uid,
                'post_title'   => 'The post ' . $uid,
                'post_content' => 'This is a post has an <a href="' . $example[0] . '">valid link</a>.',
                'post_status'  => 'publish',
            ]
        );

        $post = get_post($postId);

        $requirementInstance = new Validate_links(null, null);
        $status = $requirementInstance->get_current_status($post, '');

        $I->assertTrue($status);
    }
}
