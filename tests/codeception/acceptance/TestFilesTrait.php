<?php

trait TestFilesTrait {

  /**
   * @var string
   */
  protected $logoPath;

  /**
   * @var string
   */
  protected $filePath;

  /**
   * List of files that have been prepared for the tests.
   *
   * @var array
   */
  protected $files = [];

  /**
   * Always cleanup the config after testing.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   */
  public function _after(AcceptanceTester $I) {
    $this->removeFiles();
  }

  protected function prepareImage() {
    $data_directory = $this->getDataDirectory();
    $this->logoPath = $this->getUniqueFilePrefix() . '-logo.jpg';
    $this->files[] = $this->logoPath;
    copy(__DIR__ . '/assets/logo.jpg', "$data_directory/{$this->logoPath}");
  }

  protected function removeFiles() {
    $data_directory = $this->getDataDirectory();
    foreach ($this->files as $file) {
      if (file_exists("$data_directory/$file")) {
        unlink("$data_directory/$file");
      }
    }
  }

  protected function preparePdf() {
    $data_directory = $this->getDataDirectory();
    $this->filePath = $this->getUniqueFilePrefix() . '-test.pdf';
    copy(__DIR__ . '/assets/logo.jpg', "$data_directory/{$this->filePath}");
  }

  protected function getDataDirectory() {
    $data_directory = rtrim(codecept_data_dir(), '/\\');
    if (!file_exists($data_directory)) {
      mkdir($data_directory, 0777, TRUE);
    }
    return $data_directory;
  }

  protected function getUniqueFilePrefix() {
    return substr(md5(self::class . microtime()), 0, 5);
  }

}
