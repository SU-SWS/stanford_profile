<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

/**
 * Test the news functionality.
 */
#[CodeceptionAttribute\Group('content')]
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
    $I->seeCheckboxIsChecked("#edit-amount-status");
    $I->seeInField("Minimum number of revisions to keep (per language)", 5);

    // XML Sitemap.
    $I->amOnPage("/admin/config/search/xmlsitemap/settings");
    $I->see("News");
    $I->amOnPage("/admin/config/search/xmlsitemap/settings/node/stanford_news");
    $I->selectOption("#edit-xmlsitemap-status", '1');

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
      'name' => $this->faker->word(),
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->canSeeCheckboxIsChecked('Published');
  }

  /**
   * Validate metadata information.
   */
  #[CodeceptionAttribute\Group('metadata')]
  public function testMetaData(AcceptanceTester $I) {
    $time = \Drupal::time()->getCurrentTime();
    $now = DateTime::createFromFormat('U', $time);
    $now->setTime(12, 0, 0);
    $now = $now->getTimestamp();

    /** @var \Drupal\Core\Datetime\DateFormatterInterface $date_time_formatter */
    $date_time_formatter = \Drupal::service('date.formatter');

    $date_string = $date_time_formatter->format($now, 'custom', 'Y-m-d');
    $metadata_date = $date_time_formatter->format($now, 'custom', 'c', 'America/Los_Angeles');

    $values = [
      'featured_image_alt' => $this->faker->words(3, TRUE),
      'banner_image_alt' => $this->faker->words(3, TRUE),
    ];

    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $banner_image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word() . '.jpg');
    $featured_image_path = $file_system->copy(__DIR__ . '/../assets/logo.jpg', 'public://' . $this->faker->word() . '.jpg');

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

  #[CodeceptionAttribute\Group('body')]
  public function testBodyField(AcceptanceTester $I) {
    $dek = substr($this->faker->sentences(20, TRUE), 0, 499);

    $body_text = '<p>' . implode('</p><p>', $this->faker->paragraphs()) . '</p>';
    $node = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
      'body' => ['value' => $body_text, 'format' => 'stanford_html'],
      'su_news_dek' => $dek,
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee(strip_tags($body_text));
    $I->canSee($dek);
  }

  #[CodeceptionAttribute\Group('related-news')]
  public function testRelatedNewsPerson(AcceptanceTester $I) {
    $person = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $this->faker->firstName(),
      'su_person_last_name' => $this->faker->lastName(),
    ]);
    $otherPerson = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $this->faker->firstName(),
      'su_person_last_name' => $this->faker->lastName(),
    ]);
    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
      'su_news_person' => $person->id(),
    ]);
    $otherNews = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $I->amOnPage($person->toUrl()->toString());
    $I->canSee($person->label(), 'h1');
    $I->canSee('Related News', 'h2');
    $I->canSee($news->label(), 'h3');
    $I->cantSee($otherNews->label());

    $I->amOnPage($otherPerson->toUrl()->toString());
    $I->canSee($otherPerson->label(), 'h1');
    $I->cantSee('Related News');
    $I->cantSee($news->label());
    $I->cantSee($otherNews->label());
  }

  /**
   * Test that news variants display correct fields and can be populated with
   * contributor roles.
   */
  #[CodeceptionAttribute\Group('news_variant')]
  public function testNewsVariantFieldsDisplay(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_news');
    $I->canSeeResponseCodeIs(200);

    // Generate test data using faker
    $testData = [
      'Dek' => $this->faker->sentence(),
      'Byline' => $this->faker->name(),
      'Banner Caption' => $this->faker->sentence(),
      'Quote' => $this->faker->sentence(),
    ];

    // Fill in required title field
    $I->fillField('Headline', $this->faker->words(3, TRUE));

    // Verify fields are initially empty using canSeeInField()
    $I->canSeeInField('Dek', '');
    $I->canSeeInField('Byline', '');
    $I->canSeeInField('Banner Caption', '');
    $I->canSeeInField('Quote / Big Text', '');

    // Fill in the fields
    $I->fillField('Dek', $testData['Dek']);
    $I->fillField('Byline', $testData['Byline']);
    $I->fillField('Banner Caption', $testData['Banner Caption']);
    $I->fillField('Quote / Big Text', $testData['Quote']);

    // Save the node
    $I->click('Save');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('has been created');

    // Verify the information is reflected on the page
    $I->canSee($testData['Dek']);
    $I->canSee($testData['Byline']);
    $I->canSee($testData['Banner Caption']);
  }


  /**
   * Test that Related Spotlights filters by matching taxonomy terms.
   *
   */
  #[CodeceptionAttribute\Group('news_variant')]
  public function testRelatedSpotlightsFiltersByTaxonomy(AcceptanceTester $I) {

    // Create taxonomy terms.
    $term_a = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_news_spotlight_filters',
    ], 'taxonomy_term');

    $term_b = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_news_spotlight_filters',
    ], 'taxonomy_term');

    // Create 3 spotlight nodes with term A and 2 with term B.
    $spotlight_a1 = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_a->id()],
      'status' => 1,
    ]);

    $spotlight_a2 = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_a->id()],
      'status' => 1,
    ]);

    $spotlight_a3 = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_a->id()],
      'status' => 1,
    ]);

    $spotlight_b1 = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_b->id()],
      'status' => 1,
    ]);

    $spotlight_b2 = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'su_news_spotlight_filters' => [$term_b->id()],
      'status' => 1,
    ]);

    // Create a spotlight with no terms.
    $spotlight_c = $I->createEntity([
      'title' => $this->faker->words(3, TRUE),
      'type' => 'stanford_news',
      'layout_selection' => 'news_spotlight',
      'status' => 1,
    ]);

    // Visit spotlight A1 - should show other nodes with term A.
    $I->amOnPage($spotlight_a1->toUrl()->toString());
    // Make sure there's an h2
    $I->see('More Spotlights', 'h2');
    // Make sure the links are h3s
    $I->seeLink($spotlight_a2->label());
    $I->see($spotlight_a2->label(), 'h3');
    $I->seeLink($spotlight_a3->label());
    $I->see($spotlight_a3->label(), 'h3');
    $I->dontSee($spotlight_b1->label());
    $I->dontSee($spotlight_b2->label());

    // If there are no terms attached, we should see three spotlights.
    $I->amOnPage($spotlight_c->toUrl()->toString());
    $I->see('More Spotlights', 'h2');
    $I->seeNumberOfElements('.related-spotlights article', 3);
  }

}
