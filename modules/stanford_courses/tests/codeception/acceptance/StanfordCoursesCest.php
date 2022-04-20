<?php

/**
 * Class StanfordCoursesCest.
 *
 * @group stanford_courses
 */
class StanfordEventsCest {

  /**
   * Node Fields as Admin.
   */
  public function testEventNodeFieldsAsAdmin(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/stanford_course/fields');
    $I->canSee('su_course_academic_year');
    $I->canSee('body');
    $I->canSee('su_course_code');
    $I->canSee('su_course_id');
    $I->canSee('su_course_link');
    $I->canSee('su_course_quarters');
    $I->canSee('su_course_subject');
    $I->canSee('su_course_tags');
    $I->canSee('su_course_instructors');
    $I->canSee('su_course_section_units');

  }

  /**
   * Test for create node and view display.
   */
  public function testCreateNewNode(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $subject = $this->createCourseSubjectsTerm($I, 'TEST');
    $tags = [
        'su_course_subject' => $subject->id(),
    ];
    $node = $this->createCourseNode($I, $tags);
    $path = $node->toUrl()->toString();
    $I->amOnPage($path);
    $I->canSee("A Course Title");
    $I->canSee("Lorem ipsum body");
  }

  /**
   * Test for pathauto Node
   */
  public function testForPathAutoNode(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $subject = $this->createCourseSubjectsTerm($I, 'TEST');
    $tags = [
        'su_course_subject' => $subject->id(),
    ];
    $node = $this->createCourseNode($I, $tags);
    $path = $node->toUrl()->toString();
    $I->assertEquals("/courses/test101", $path);
  }

  /**
   * Test for pathauto Terms
   */
  public function testForPathAutoTerm(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $term = $this->createCourseSubjectsTerm($I, 'TEST');
    $path = $term->toUrl()->toString();
    $I->assertEquals("/courses/test", $path);

    $term = $this->createCourseQuartersTerm($I, 'Autumn');
    $path = $term->toUrl()->toString();
    $I->assertEquals("/courses/autumn", $path);
  }

  /**
   * Test for Views.
   */
  public function testForViewsExist(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/structure/views/view/stanford_courses");
    $I->canSeeResponseCodeIs(200);
    $I->canSee("- Default List View -");
    $I->canSee("Taxonomy Term Page List");
    $I->canSee("Card Grid");
  }


  /**
   * Test for Taxonomy Groups.
   */
  public function testForTaxonomyVocabularies(AcceptanceTester $I) {
    $I->logInWithRole('administrator');

    // Quarters.
    $I->amOnPage("/admin/structure/taxonomy/manage/su_course_quarters/overview");
    $I->canSeeResponseCodeIs(200);

    // Subjects.
    $I->amOnPage("/admin/structure/taxonomy/manage/su_course_subjects/overview");
    $I->canSeeResponseCodeIs(200);

    // Tags.
    $I->amOnPage("/admin/structure/taxonomy/manage/su_course_tags/overview");
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Test for patterns.
   */
  public function testForPatternsExist(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/patterns");
    $I->canSee("Course List Item");
    $I->canSee("Course Vertical Teaser");
  }

  /**
   * Test for importer
   */
  public function testImporterForm(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/config/importers/courses-importer");
    $I->canSee("ExploreCourses URL");
    $I->canSee("Use this field to import courses from ExploreCourses.");
  }

  /**
   * Test cron settings.
   */
  public function testForCronSettings(AcceptanceTester $I) {
    $I->logInWithRole("administrator");
    $I->amOnPage("/admin/config/system/cron/jobs");
    $I->canSee("Importer: Courses");
    $I->amOnPage("/admin/config/system/cron/jobs/manage/stanford_migrate_stanford_courses");
    $I->canSeeResponseCodeIs(200);
    $I->canSee("stanford_migrate_ultimate_cron_task");
    $I->seeCheckboxIsChecked("#edit-status");
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
