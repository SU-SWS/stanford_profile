<?php

namespace Drupal\Tests\stanford_publication\Kernel\Entity;

use Drupal\stanford_publication\Entity\Citation;
use Drupal\stanford_publication\Entity\CitationInterface;
use Drupal\Tests\stanford_publication\Kernel\PublicationTestBase;

/**
 * Class CitationTest
 *
 * @group stanford_publication
 * @coversDefaultClass \Drupal\stanford_publication\Entity\Citation
 */
class CitationTest extends PublicationTestBase {

  /**
   * Trying to get a biblio with an unknown format returns an empty string.
   */
  public function testEmptyCitation() {
    /** @var \Drupal\stanford_publication\Entity\CitationInterface $citation */
    $citation = Citation::create(['type' => 'su_book']);
    $citation->setLabel('Foo Bar');
    $citation->save();
    $this->assertEmpty($citation->getBibliography('foo-bar'));
  }

  /**
   * A book source in APA format should be formatted exactly as expected.
   */
  public function testBookApa() {
    /** @var \Drupal\stanford_publication\Entity\CitationInterface $citation */
    $citation = Citation::create([
      'type' => 'su_book',
      'su_author' => [['given' => 'John', 'family' => 'Doe']],
      'su_year' => 2021,
      'su_edition' => 5,
      'su_page' => '10-20',
      'su_publisher' => 'Awesome Publishing',
      'su_publisher_place' => 'California',
      'su_subtitle' => 'subtitle of book',
    ]);
    $citation->setLabel('Foo Bar');
    $citation->save();

    // Nothing to link to.
    $biblio = $citation->getBibliography();
    $expected = 'Doe, J. (2021). <i>Foo Bar</i> (5th ed.). Subtitle of Book. Awesome Publishing.';
    $this->assertStringContainsString($expected, $biblio);

    // Links to the parent node.
    $citation->setParentEntity($this->parentNode, 'field_foo');
    $biblio = $citation->getBibliography();
    $expected = 'Doe, J. (2021). <a href="/node/' . $this->parentNode->id() . '" hreflang="en"><i>Foo Bar</i></a> (5th ed.). Subtitle of Book. Awesome Publishing.';
    $this->assertStringContainsString($expected, $biblio);

    // Links to the url field.
    $citation->set('su_url', 'http://domain.org')->save();
    $biblio = $citation->getBibliography();
    $expected = 'Doe, J. (2021). <a href="http://domain.org"><i>Foo Bar</i></a> (5th ed.). Subtitle of Book. Awesome Publishing.';
    $this->assertStringContainsString($expected, $biblio);

    // The DOI field does not get a link. The result should be the same as the
    // result above.
    $citation->set('su_doi', 'doi/doi')->save();
    $biblio = $citation->getBibliography();
    $this->assertStringContainsString($expected, $biblio);
  }

  /**
   * A book source in Chicago format should be formatted exactly as expected.
   */
  public function testBookChicago() {
    /** @var \Drupal\stanford_publication\Entity\CitationInterface $citation */
    $citation = Citation::create([
      'type' => 'su_book',
      'su_year' => 2021,
      'su_edition' => 5,
      'su_page' => '10-20',
      'su_publisher' => 'Awesome Publishing',
      'su_publisher_place' => 'California',
      'su_subtitle' => 'subtitle of book',
    ]);
    $citation->setLabel('Foo Bar');
    $citation->save();

    // Nothing to link to.
    $biblio = $citation->getBibliography(CitationInterface::CHICAGO);
    $expected = '<i>Foo Bar</i>. 5th ed. Subtitle of Book. California: Awesome Publishing, 2021.';
    $this->assertStringContainsString($expected, $biblio);

    // Links to the parent node.
    $citation->setParentEntity($this->parentNode, 'field_foo');
    $biblio = $citation->getBibliography(CitationInterface::CHICAGO);
    $expected = '<a href="/node/' . $this->parentNode->id() . '" hreflang="en"><i>Foo Bar</i></a>. 5th ed. Subtitle of Book. California: Awesome Publishing, 2021.';
    $this->assertStringContainsString($expected, $biblio);

    // Links to the url field.
    $citation->set('su_url', 'http://domain.org')->save();
    $biblio = $citation->getBibliography(CitationInterface::CHICAGO);
    $expected = '<a href="http://domain.org"><i>Foo Bar</i></a>. 5th ed. Subtitle of Book. California: Awesome Publishing, 2021. http://domain.org.';
    $this->assertStringContainsString($expected, $biblio);

    // The DOI field does not get a link. The link should be the same as the
    // result above.
    $citation->set('su_doi', 'doi/doi')->save();
    $biblio = $citation->getBibliography(CitationInterface::CHICAGO);
    $expected = '<a href="http://domain.org"><i>Foo Bar</i></a>. 5th ed. Subtitle of Book. California: Awesome Publishing, 2021. https://doi.org/doi/doi.';
    $this->assertStringContainsString($expected, $biblio);
  }

  /**
   * A Journal source in APA format should be formatted exactly as expected.
   */
  public function testJournalApa() {
    /** @var \Drupal\stanford_publication\Entity\CitationInterface $citation */
    $citation = Citation::create([
      'type' => 'su_article_journal',
      'su_author' => [['given' => 'John', 'family' => 'Doe']],
      'su_year' => 2021,
      'su_month' => 5,
      'su_day' => 12,
      'su_edition' => 5,
      'su_page' => '10-20',
      'su_publisher' => 'Awesome Publishing',
      'su_issue' => 5,
      'su_volume' => 3,
    ]);
    $citation->setLabel('Foo Bar');
    $citation->save();

    // Nothing to link to.
    $biblio = $citation->getBibliography();
    $expected = 'Doe, J. (2021). Foo Bar. <i>Awesome Publishing</i>, <i>3</i>(5), 10-20.';
    $this->assertStringContainsString($expected, $biblio);

    // Links to the parent node.
    $citation->setParentEntity($this->parentNode, 'field_foo');
    $biblio = $citation->getBibliography();
    $expected = 'Doe, J. (2021). <a href="/node/1" hreflang="en">Foo Bar</a>. <i>Awesome Publishing</i>, <i>3</i>(5), 10-20.';
    $this->assertStringContainsString($expected, $biblio);

    // Links to the url field.
    $citation->set('su_url', 'http://domain.org')->save();
    $biblio = $citation->getBibliography();
    $expected = 'Doe, J. (2021). <a href="http://domain.org">Foo Bar</a>. <i>Awesome Publishing</i>, <i>3</i>(5), 10-20. http://domain.org';
    $this->assertStringContainsString($expected, $biblio);

    // The DOI field does not get a link. The link should be the same as the
    // result above.
    $citation->set('su_doi', 'doi/doi')->save();
    $biblio = $citation->getBibliography();
    $expected = 'Doe, J. (2021). <a href="http://domain.org">Foo Bar</a>. <i>Awesome Publishing</i>, <i>3</i>(5), 10-20. https://doi.org/doi/doi';
    $this->assertStringContainsString($expected, $biblio);
  }

}
