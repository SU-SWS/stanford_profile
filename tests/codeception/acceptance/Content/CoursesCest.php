<?php

use Faker\Factory;

/**
 * Test the Course functionality.
 *
 * @group content
 */
class CoursesCest {

  /**
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Test for view pages and taxonomy functionality
   */
  public function testViewPagesExist(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $quarters_term = $this->createCourseQuartersTerm($I);
    $subject_term = $this->createCourseSubjectsTerm($I);
    $tags_term = $this->createCourseTagsTerm($I);
    $tags = [
      'su_course_subject' => $subject_term->id(),
      'su_course_quarters' => $quarters_term->id(),
      'su_course_tags' => $tags_term->id(),
    ];
    $node = $this->createCourseNode($I, $tags);
    $path = $node->toUrl()->toString();
    $I->amOnPage($path);
    $I->canSee($node->label());
    $I->canSee('Lorem ipsum body');
    $I->canSee($quarters_term->label());
    $I->canSee($subject_term->label());
    $I->canSee($tags_term->label());
    $I->canSee('Shared Tags');
    $I->amOnPage('/courses');
    $I->canSeeResponseCodeIs(200);
    $I->canSee($node->label());
    $I->canSee('Doe, J., Doe, M.');
    $I->canSee('2021-2022');
    $I->canSee('Courses Menu');
    $I->amOnPage($quarters_term->toUrl()->toString());
    $I->canSeeResponseCodeIs(200);
    $I->seeLink($node->label());
    $I->canSee('Lorem ipsum body');
    $I->seeLink($subject_term->label());
    $I->amOnPage($subject_term->toUrl()->toString());
    $I->canSeeResponseCodeIs(200);
    $I->canSee($node->label());
    $I->canSee('Lorem ipsum body');
  }

  /**
   * Test rabbit hole settings and field locking
   */
  public function testRabbitHoleRedirects(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $quarters_term = $this->createCourseQuartersTerm($I);
    $subject_term = $this->createCourseSubjectsTerm($I);
    $tags_term = $this->createCourseTagsTerm($I);
    $tags = [
      'su_course_subject' => $subject_term->id(),
      'su_course_quarters' => $quarters_term->id(),
      'su_course_tags' => $tags_term->id(),
    ];
    $node = $this->createCourseNode($I, $tags);
    $path = $node->toUrl()->toString();
    $I->amOnPage($path);
    $I->canSee('This page will redirect');
  }


  /**
   * Create a Course Node.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param string $content
   *   A key/value array for field names and content.
   *
   * @return \Drupal\node\NodeInterface
   *   Node Object
   */
  protected function createCourseNode(AcceptanceTester $I, $content = []) {
    $values = [
      'type' => 'stanford_course',
      'title' => $this->faker->words(3, TRUE),
      'body' => [
        'value' => '<p>Lorem ipsum body</p>',
        'summary' => '',
      ],
      'su_course_link' => [
        'uri' => 'https://google.com/',
        'title' => 'T',
      ],
      'su_course_academic_year' => '2021-2022',
      'su_course_code' => '101',
      'su_course_id' => 123456,
      'su_course_instructors' => 'Doe, J., Doe, M.',
      'su_course_section_units' => '2',

    ];
    $values = array_merge($values, $content);
    return $I->createEntity($values);
  }

  /**
   * Creates a new course quarters term.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param string $name
   *   The name of the term.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   A taxonomy term.
   */
  protected function createCourseQuartersTerm(AcceptanceTester $I) {
    return $I->createEntity([
      'name' => $this->faker->words(2, TRUE),
      'vid' => 'su_course_quarters',
    ], 'taxonomy_term');
  }

  /**
   * Creates a new course subjects term.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   *
   * @return \Drupal\taxonomy\TermInterface
   *   A taxonomy term.
   */
  protected function createCourseSubjectsTerm(AcceptanceTester $I) {
    return $I->createEntity([
      'name' => $this->faker->words(2, TRUE),
      'vid' => 'su_course_subjects',
    ], 'taxonomy_term');
  }

  /**
   * Creates a new course tags term.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   *
   * @return \Drupal\taxonomy\TermInterface
   *   A taxonomy term.
   */
  protected function createCourseTagsTerm(AcceptanceTester $I) {
    return $I->createEntity([
      'name' => $this->faker->word . '::' . $this->faker->word,
      'vid' => 'su_course_tags',
    ], 'taxonomy_term');
  }

}
