<?php

use Faker\Factory;

/**
 * Test the Course functionality.
 */
class CoursesCest {

    /**
     * Test for view pages
     */
    public function testViewPagesExist(AcceptanceTester $I) {
        $I->logInWithRole('administrator');
        $quarters_term = $this->createCourseQuartersTerm($I, 'Autumn');
        $subject_term = $this->createCourseSubjectsTerm($I, 'TEST');
        $tags_term = $this->createCourseTagsTerm($I, 'TEST::test');
        $tags = [
            'su_course_subject' => $subject_term->id(),
            'su_course_quarters' => $quarters_term->id(),
            'su_course_tags' => $tags_term->id(),
        ];
        $node = $this->createCourseNode($I, $tags);
        $path = $node->toUrl()->toString();
        $I->amOnPage($path);
        $I->canSee("A Course Title");
        $I->canSee("Lorem ipsum body");
        $I->canSee('Autumn');
        $I->canSee('TEST');
        $I->canSee('TEST::test');
        $I->canSee('Shared Tags');
        $I->amOnPage('/courses');
        $I->canSeeResponseCodeIs(200);
        $I->canSee("A Course Title");
        $I->canSee("Doe, J., Doe, M.");
        $I->canSee("2021-2022");
        $I->canSee("TEST101");
        $I->canSee("Courses Menu");
        $I->amOnPage('/courses/autumn');
        $I->canSeeResponseCodeIs(200);
        $I->seeLink("A Course Title");
        $I->canSee("Lorem ipsum body");
        $I->seeLink("TEST");
        $I->click("a[href='/courses/test']");
        $I->amOnPage('/courses/test');
        $I->canSeeResponseCodeIs(200);
        $I->canSee("A Course Title");
        $I->canSee("Lorem ipsum body");
    }

    /**
     * Test rabbit hole settings
     */
    public function testRabbitHoleRedirects() {
        $I->logInWithRole('administrator');
        $quarters_term = $this->createCourseQuartersTerm($I, 'Autumn');
        $subject_term = $this->createCourseSubjectsTerm($I, 'TEST');
        $tags_term = $this->createCourseTagsTerm($I, 'TEST::test');
        $tags = [
            'su_course_subject' => $subject_term->id(),
            'su_course_quarters' => $quarters_term->id(),
            'su_course_tags' => $tags_term->id(),
        ];
        $node = $this->createCourseNode($I, $tags);
        $I->amOnPage($path);
        $I->canSee("This page will redirect");
        $I->amOnPage("/node" . $node->id() . "/edit");
        $I->canSee("Some fields can not be edited ");
    }


  /**
   * Create a Course Node.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param string $content
   *   A key/value array for field names and content.
   *
   * @return object
   *   Node Object
   */
  protected function createCourseNode(AcceptanceTester $I, $content = []) {
    $values = [
      'type' => 'stanford_course',
      'title' => 'A Course Title',
      'body' => [
        "value" => "<p>Lorem ipsum body</p>",
        "summary" => "",
      ],
      'su_course_link' => [
        "uri" => "https://google.com/",
        "title" => "T",
      ],
      'su_course_academic_year' => '2021-2022',
      'su_course_code' => '101',
      'su_course_id' => 123456,
      'su_course_instructors' => 'Doe, J., Doe, M.',
      'su_course_section_units' => '2',

    ];
    $values = array_merge($values, $content);
    $e = $I->createEntity($values);
    $I->runDrush('cache:rebuild');
    return $e;
  }

  /**
   * Creates a new course quarters term.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param string $name
   *   The name of the term.
   *
   * @return object
   *   A taxonomy term.
   */
  protected function createCourseQuartersTerm(AcceptanceTester $I, $name = NULL) {
    $e = $I->createEntity([
      'name' => $name ?: 'Foo',
      'vid' => 'su_course_quarters',
    ], 'taxonomy_term');
    $I->runDrush('cache:rebuild');
    return $e;
  }

  /**
   * Creates a new course subjects term.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param string $name
   *   The name of the term.
   *
   * @return object
   *   A taxonomy term.
   */
  protected function createCourseSubjectsTerm(AcceptanceTester $I, $name = NULL) {
    $e = $I->createEntity([
      'name' => $name ?: 'Foo',
      'vid' => 'su_course_subjects',
    ], 'taxonomy_term');
    $I->runDrush('cache:rebuild');
    return $e;
  }

  /**
   * Creates a new course tags term.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param string $name
   *   The name of the term.
   *
   * @return object
   *   A taxonomy term.
   */
  protected function createCourseTagsTerm(AcceptanceTester $I, $name = NULL) {
    $e = $I->createEntity([
      'name' => $name ?: 'Foo',
      'vid' => 'su_course_tags',
    ], 'taxonomy_term');
    $I->runDrush('cache:rebuild');
    return $e;
  }

}
