<?php

namespace Drupal\Tests\stanford_courses_importer\Kernel\Plugin\migrate\process;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;

/**
 * Class ExploreCoursesMigrationPluginsTest.
 *
 * @group stanford_courses_importer
 */
class ExploreCoursesMigrationPluginsTest extends EntityKernelTestBase {

  /**
   * Migrate plugin object.
   */
  protected $plugin;

  /**
   * Migrate process manager service.
   *
   * @var \Drupal\migrate\Plugin\MigratePluginManager
   */
  protected $processManager;

  /**
   * Section XML data to use for tests.
   *
   * @var string
   */
  protected $section_xml;

  /**
   * Tags XML data to use for tests.
   *
   * @var string
   */
  protected $tags_xml;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'system',
    'stanford_courses_importer',
    'migrate',
  ];

  /**
   * {@inheritDoc}.
   */
  protected function setUp(): void {
    parent::setUp();
    $this->processManager = $this->container->get('plugin.manager.migrate.process');
    $this->section_xml = <<<DATA
<sections>
    <section>
        <classId>25525</classId>
        <term>2021-2022 Autumn</term>
        <termId>1222</termId>
        <subject>BIO</subject>
        <code>141</code>
        <units />
        <sectionNumber>02</sectionNumber>
        <component>DIS</component>
        <numEnrolled>11</numEnrolled>
        <maxEnrolled>999</maxEnrolled>
        <numWaitlist>0</numWaitlist>
        <maxWaitlist>0</maxWaitlist>
        <enrollStatus>Open</enrollStatus>
        <addConsent>N</addConsent>
        <dropConsent>N</dropConsent>
        <instructionMode>P</instructionMode>
        <courseId>201047</courseId>
        <schedules>
            <schedule>
                <startDate />
                <endDate />
                <startTime />
                <endTime />
                <location />
                <days></days>
                <instructors></instructors>
            </schedule>
        </schedules>
        <currentClassSize>11</currentClassSize>
        <maxClassSize>999</maxClassSize>
        <currentWaitlistSize>0</currentWaitlistSize>
        <maxWaitlistSize>0</maxWaitlistSize>
        <notes />
        <attributes>
            <attribute>
                <name>NQTR</name>
                <value>AUT</value>
                <description>Autumn</description>
                <catalogPrint>true</catalogPrint>
                <schedulePrint>false</schedulePrint>
            </attribute>
        </attributes>
    </section>
    <section>
        <classId>24996</classId>
        <term>2021-2022 Autumn</term>
        <termId>1222</termId>
        <subject>BIO</subject>
        <code>141</code>
        <units>5</units>
        <sectionNumber>01</sectionNumber>
        <component>LEC</component>
        <numEnrolled>32</numEnrolled>
        <maxEnrolled>100</maxEnrolled>
        <numWaitlist>0</numWaitlist>
        <maxWaitlist>0</maxWaitlist>
        <enrollStatus>Open</enrollStatus>
        <addConsent>N</addConsent>
        <dropConsent>N</dropConsent>
        <instructionMode>P</instructionMode>
        <courseId>201047</courseId>
        <schedules>
            <schedule>
                <startDate>Sep 20, 2021</startDate>
                <endDate>Dec 3, 2021</endDate>
                <startTime>9:45:00 AM</startTime>
                <endTime>11:15:00 AM</endTime>
                <location>Lathrop 282</location>
                <days> Tuesday Thursday</days>
                <instructors>
                    <instructor>
                        <name>Lu, S.</name>
                        <firstName>Sophia</firstName>
                        <middleName />
                        <lastName>Lu</lastName>
                        <sunet>sophialu</sunet>
                        <role>TA</role>
                    </instructor>
                    <instructor>
                        <name>Sklar, M.</name>
                        <firstName>Michael</firstName>
                        <middleName>Benjamin</middleName>
                        <lastName>Sklar</lastName>
                        <sunet>sklarm</sunet>
                        <role>PI</role>
                    </instructor>
                    <instructor>
                        <name>Rajanala, S.</name>
                        <firstName>Samyak</firstName>
                        <middleName />
                        <lastName>Rajanala</lastName>
                        <sunet>samyak</sunet>
                        <role>TA</role>
                    </instructor>
                    <instructor>
                        <name>Morrison, T.</name>
                        <firstName>Tim</firstName>
                        <middleName />
                        <lastName>Morrison</lastName>
                        <sunet>tm98</sunet>
                        <role>TA</role>
                    </instructor>
                </instructors>
            </schedule>
        </schedules>
        <currentClassSize>32</currentClassSize>
        <maxClassSize>100</maxClassSize>
        <currentWaitlistSize>0</currentWaitlistSize>
        <maxWaitlistSize>0</maxWaitlistSize>
        <notes />
        <attributes>
            <attribute>
                <name>NQTR</name>
                <value>AUT</value>
                <description>Autumn</description>
                <catalogPrint>true</catalogPrint>
                <schedulePrint>false</schedulePrint>
            </attribute>
        </attributes>
    </section>
</sections>
DATA;
    $this->tags_xml = <<<TAGS
<tags>
    <tag>
        <organization>EDUC</organization>
        <name>alluniversity</name>
    </tag>
    <tag>
        <organization>EDUC</organization>
        <name>alluniversityabove200</name>
    </tag>
    <tag>
        <organization>EDUC</organization>
        <name>alluniversityabove100</name>
    </tag>
</tags>
TAGS;
  }

  /**
   * Test the Instructors plugin.
   *
   * @covers \Drupal\stanford_courses_importer\Plugin\migrate\process\ExploreCoursesInstructors
   */
  public function testExploreCoursesInstructors() {
    /** @var \Drupal\stanford_courses_importer\Plugin\migrate\process\ExploreCoursesInstructors $plugin */
    $plugin = $this->processManager->createInstance('explore_courses_instructors');

    $row = new Row();
    $migrate = new MigrateExecutableTest();
    // good data.
    $translated = $plugin->transform($this->section_xml, $migrate, $row, '');
    $this->assertEquals([
      0 => 'Lu, S.',
      1 => 'Sklar, M.',
      2 => 'Rajanala, S.',
      3 => 'Morrison, T.',
    ], $translated);
    // non string value
    $translated = $plugin->transform([], $migrate, $row, '');
    $this->assertEquals('', $translated);
    // unparseable xml
    $translated = $plugin->transform('<?xml version="1.0" encoding="ASCII"?>', $migrate, $row, '');
    $this->assertEquals('', $translated);
  }

  /**
   * Test the Quarters plugin.
   *
   * @covers \Drupal\stanford_courses_importer\Plugin\migrate\process\ExploreCoursesQuarters
   *
   */
  public function testExploreCoursesQuarters() {
    /** @var \Drupal\stanford_courses_importer\Plugin\migrate\process\ExploreCoursesQuarters$plugin */
    $plugin = $this->processManager->createInstance('explore_courses_quarters');

    $row = new Row();
    $migrate = new MigrateExecutableTest();
    // Good value
    $translated = $plugin->transform($this->section_xml, $migrate, $row, '');
    $this->assertEquals([0 => 'Autumn'], $translated);
    // non string value
    $translated = $plugin->transform([], $migrate, $row, '');
    $this->assertEquals('', $translated);
    // unparseable xml
    $translated = $plugin->transform('<?xml version="1.0" encoding="ASCII"?>', $migrate, $row, '');
    $this->assertEquals('', $translated);
  }

  /**
   * Test the Tags plugin.
   *
   * @covers \Drupal\stanford_courses_importer\Plugin\migrate\process\ExploreCoursesTags
   *
   */
  public function testExploreCoursesTags() {
    /** @var \Drupal\stanford_courses_importer\Plugin\migrate\process\ExploreCoursesTags $plugin */
    $plugin = $this->processManager->createInstance('explore_courses_tags');

    $row = new Row();
    $migrate = new MigrateExecutableTest();
    // a good value.
    $translated = $plugin->transform($this->tags_xml, $migrate, $row, '');
    $this->assertEquals('EDUC::alluniversity;EDUC::alluniversityabove200;EDUC::alluniversityabove100', $translated);
    // a non string value
    $translated = $plugin->transform([], $migrate, $row, '');
    $this->assertEquals('', $translated);
    // unparseable xml
    $translated = $plugin->transform('<?xml version="1.0" encoding="ASCII"?>', $migrate, $row, '');
    $this->assertEquals('', $translated);
  }

  /**
   * Test the Units plugin.
   *
   * @covers \Drupal\stanford_courses_importer\Plugin\migrate\process\ExploreCoursesUnits
   *
   */
  public function testExploreCoursesUnits() {
    /** @var \Drupal\stanford_courses_importer\Plugin\migrate\process\ExploreCoursesUnits $plugin */
    $plugin = $this->processManager->createInstance('explore_courses_units');

    $row = new Row();
    $migrate = new MigrateExecutableTest();
    // Returns a single value if they match.
    $translated = $plugin->transform('1-1', $migrate, $row, '');
    $this->assertEquals('1', $translated);
    // Returns the original string if they don't match.
    $translated = $plugin->transform('3-5', $migrate, $row, '');
    $this->assertEquals('3-5', $translated);
    // Returns nothing if the data is a weird string.
    $translated = $plugin->transform('bad data', $migrate, $row, '');
    $this->assertEquals('', $translated);
    // Throws an exception if you feed it a string without exactly two values.
    $this->expectException(\Exception::class);
    $translated = $plugin->transform('-1-8-9', $migrate, $row, '');
  }

}

/**
 * Class MigrateExecutableTest for testing purposes.
 */
class MigrateExecutableTest implements MigrateExecutableInterface {

  /**
   * {@inheritDoc}.
   */
  public function import() {

  }

  /**
   * {@inheritDoc}.
   */
  public function rollback() {

  }

  /**
   * {@inheritDoc}.
   */
  public function processRow(Row $row, array $process = NULL, $value = NULL) {

  }

  /**
   * {@inheritDoc}.
   */
  public function saveMessage($message, $level = MigrationInterface::MESSAGE_ERROR) {

  }

}
