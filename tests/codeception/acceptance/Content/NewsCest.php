<?php

use Faker\Factory;

/**
 * Test the news functionality.
 *
 * @group content
 */
class NewsCest {

  /**
   * Faker.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * News list intro block is at the top of the page.
   */
  public function testListIntro(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/news');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Test that the default content has installed and is unpublished.
   */
  public function testDefaultContentExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/content");
    $I->see("Sample: Smith Conference");
    $I->see("Sample: For Runners, Is 15 Feet the New 6 Feet for Social Distancing?");
    $I->see("Sample: Stanford researchers find that misfiring from jittery neurons");

    $I->amOnPage("/news/sample-smith-conference");
    $I->see("This page is currently unpublished and not visible to the public.");

    $I->amOnPage("/news/sample-runners-15-feet-new-6-feet-social-distancing");
    $I->see("This page is currently unpublished and not visible to the public.");

    $I->amOnPage("/news/sample-stanford-researchers-find-misfiring-jittery-neurons");
    $I->see("This page is currently unpublished and not visible to the public.");

    $I->see("News", ".su-multi-menu");
  }

  /**
   * Test that the vocabulary and terms exist.
   */
  public function testVocabularyTermsExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/structure/taxonomy/manage/stanford_news_topics/overview");
    $I->canSeeNumberOfElements("input.term-id", [2, 99]);
  }

  /**
   * Test that the view pages exist.
   */
  public function testViewPagesExist(AcceptanceTester $I) {
    $I->amOnPage("/news");
    $I->seeLink('Announcement');
    $I->click("a[href='/news/announcement']");
    $I->canSeeResponseCodeIs(200);
    $I->see("News Topics");
  }

  /**
   * Validate external content redirect.
   */
  public function testExternalSourceArticle(AcceptanceTester $I) {

    $node = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
      'su_news_source' => "http://google.com/",
    ]);

    // Redirect as anon.
    $I->amOnPage($node->toUrl()->toString());
    $I->seeCurrentUrlEquals('/');

    // See content as admin.
    $I->logInWithRole('administrator');
    $I->amOnPage($node->toUrl()->toString());
    $I->canSeeInCurrentUrl($node->toUrl()->toString());
  }

  /**
   * Test that only two of three new news nodes show up in the more news view
   * on the node page.
   */
  public function testMoreNewsView(AcceptanceTester $I) {
    $I->logInWithRole('administrator');

    $first_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $second_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $third_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
    ]);

    $I->amOnPage($second_news->toUrl()->toString());
    $I->canSeeNumberOfElements(".stanford-news--cards .su-card", [2, 3]);
  }

  /**
   * Test that the XML sitemap and metatag configuration is set.
   */
  public function testXMLMetaDataRevisions(AcceptanceTester $I) {
    $I->logInWithRole('administrator');

    // Revision Delete is enabled.
    $I->amOnPage('/admin/structure/types/manage/stanford_news');
    $I->seeCheckboxIsChecked("#edit-node-revision-delete-track");
    $I->seeCheckboxIsChecked("#edit-options-revision");
    $I->seeInField("#edit-minimum-revisions-to-keep", 5);

    // XML Sitemap.
    $I->amOnPage("/admin/config/search/xmlsitemap/settings");
    $I->see("News");
    $I->amOnPage("/admin/config/search/xmlsitemap/settings/node/stanford_news");
    $I->selectOption("#edit-xmlsitemap-status", 1);

    // Metatags.
    $I->amOnPage("/admin/config/search/metatag/node__stanford_news");
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Published checkbox should be hidden on term edit pages.
   */
  public function testTermPublishing(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $term = $I->createEntity([
      'vid' => 'stanford_news_topics',
      'name' => $this->faker->word,
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');
  }

  /**
   * Validate metadata information.
   *
   * @group metadata
   */
  public function testMetaData(AcceptanceTester $I) {
    $values = [
      'featured_image_alt' => $this->faker->words(3, TRUE),
      'banner_image_alt' => $this->faker->words(3, TRUE),
    ];

    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $banner_image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word . '.jpg');
    $featured_image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word . '.jpg');

    $file = $I->createEntity(['uri' => $banner_image_path], 'file');
    $banner_media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => $values['banner_image_alt'],
      ],
    ], 'media');

    $file = $I->createEntity(['uri' => $featured_image_path], 'file');
    $featured_media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => $values['featured_image_alt'],
      ],
    ], 'media');

    $time = \Drupal::time()->getCurrentTime();
    $date_string = \Drupal::service('date.formatter')
      ->format($time, 'custom', 'Y-m-d');
    $metadata_date = \Drupal::service('date.formatter')
      ->format($time, 'custom', 'D, m/d/Y - 12:00');

    /** @var \Drupal\node\NodeInterface $node */
    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'su_news_publishing_date' => $date_string,
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[property="og:title"]', 'content'), 'Metadata "og:title" should match.');
    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[name="twitter:title"]', 'content'), 'Metadata "twitter:title" should match.');
    $I->assertEquals('article', $I->grabAttributeFrom('meta[property="og:type"]', 'content'), 'Metadata "og:type" should match.');
    $I->assertEquals($metadata_date, $I->grabAttributeFrom('meta[property="article:published_time"]', 'content'), 'Metadata "article:published_time" should match.');

    $I->cantSeeElement('meta', ['name' => 'description']);
    $I->cantSeeElement('meta', ['property' => 'og:image']);
    $I->cantSeeElement('meta', ['property' => 'og:image:url']);
    $I->cantSeeElement('meta', ['name' => 'twitter:image']);
    $I->cantSeeElement('meta', ['name' => 'twitter:image:alt']);
    $I->cantSeeElement('meta', ['name' => 'twitter:description']);

    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'su_news_banner' => $banner_media->id(),
      'su_news_publishing_date' => $date_string,
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[name="twitter:title"]', 'content'), 'Metadata "twitter:title" should match.');
    $I->assertStringContainsString(basename($banner_image_path), $I->grabAttributeFrom('meta[property="og:image"]', 'content'), 'Metadata "og:image" should match.');
    $I->assertStringContainsString(basename($banner_image_path), $I->grabAttributeFrom('meta[property="og:image:url"]', 'content'), 'Metadata "og:image:url" should match.');
    $I->assertStringContainsString(basename($banner_image_path), $I->grabAttributeFrom('meta[name="twitter:image"]', 'content'), 'Metadata "twitter:image" should match.');
    $I->assertEquals($values['banner_image_alt'], $I->grabAttributeFrom('meta[property="og:image:alt"]', 'content'), 'Metadata "og:image:alt" should match.');
    $I->assertEquals($values['banner_image_alt'], $I->grabAttributeFrom('meta[name="twitter:image:alt"]', 'content'), 'Metadata "twitter:image:alt" should match.');

    $node = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'su_news_banner' => $banner_media->id(),
      'su_news_featured_media' => $featured_media,
      'su_news_publishing_date' => $date_string,
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->assertEquals($node->label(), $I->grabAttributeFrom('meta[name="twitter:title"]', 'content'), 'Metadata "twitter:title" should match.');
    $I->assertStringContainsString(basename($featured_image_path), $I->grabAttributeFrom('meta[property="og:image"]', 'content'), 'Metadata "og:image" should match.');
    $I->assertStringContainsString(basename($featured_image_path), $I->grabAttributeFrom('meta[property="og:image:url"]', 'content'), 'Metadata "og:image:url" should match.');
    $I->assertStringContainsString(basename($featured_image_path), $I->grabAttributeFrom('meta[name="twitter:image"]', 'content'), 'Metadata "twitter:image" should match.');
    $I->assertEquals($values['featured_image_alt'], $I->grabAttributeFrom('meta[property="og:image:alt"]', 'content'), 'Metadata "og:image:alt" should match.');
    $I->assertEquals($values['featured_image_alt'], $I->grabAttributeFrom('meta[name="twitter:image:alt"]', 'content'), 'Metadata "twitter:image:alt" should match.');
  }


}
