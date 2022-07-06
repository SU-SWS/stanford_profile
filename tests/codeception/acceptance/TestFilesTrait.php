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
    $this->logoPath = $this->getUniqueFilePrefix() . '-logo.jpg';
    $full_path = $this->getFullPath($this->logoPath);
    $this->files[] = $full_path;
    copy(__DIR__ . '/assets/logo.jpg', $full_path);
  }

  protected function removeFiles() {
    foreach ($this->files as $file) {
      if (file_exists($file)) {
        unlink($file);
      }
    }
  }

  private function preparePdf() {
    $this->filePath = $this->getUniqueFilePrefix() . '-test.pdf';
    $full_path = $this->getFullPath($this->filePath);
    $this->files[] = $full_path;
    copy(__DIR__ . '/assets/logo.jpg', $full_path);
  }

  private function getFullPath($file) {
    $full_path = codecept_data_dir($file);
    if (!file_exists(dirname($full_path))) {
      mkdir(dirname($full_path), 0777, TRUE);
    }
    return $full_path;
  }

  private function getUniqueFilePrefix() {
    return substr(md5(self::class . microtime()), 0, 5);
  }

}
