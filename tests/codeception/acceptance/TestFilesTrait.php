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

  /**
   * Delete previously prepared files.
   */
  protected function removeFiles(): void {
    foreach ($this->files as $file) {
      if (file_exists($file)) {
        unlink($file);
      }
    }
  }

  /**
   * Prepare the logo jpg file for uploading.
   */
  protected function prepareImage(): void {
    $this->logoPath = $this->getUniqueFilePrefix() . '-logo.jpg';
    $full_path = $this->getFullPath($this->logoPath);
    $this->files[] = $full_path;
    copy(__DIR__ . '/assets/logo.jpg', $full_path);
  }

  /**
   * Create a file that can be used for test uploads.
   *
   * @param string $file_name
   *   File and extension.
   * @param string $contents
   *   Contents of the file that work with file_put_contents().
   *
   * @return string
   *   Path of the file relative to the codeception data directory.
   */
  protected function createFile(string $file_name, string $contents = ''): string {
    $this->filePath = $this->getUniqueFilePrefix() . "-$file_name";
    $full_path = $this->getFullPath($this->filePath);
    $this->files[] = $full_path;
    $success = file_put_contents($full_path, $contents);
    if ($success === FALSE) {
      throw new \Exception('Failed to write contents to ' . $file_name);
    }
    return str_replace(codecept_data_dir(), '', $full_path);
  }

  /**
   * Prepare the test PDF file for uploading.
   */
  protected function preparePdf(): void {
    $this->filePath = $this->getUniqueFilePrefix() . '-test.pdf';
    $full_path = $this->getFullPath($this->filePath);
    $this->files[] = $full_path;
    copy(__DIR__ . '/assets/test.pdf', $full_path);
  }

  /**
   *
   *
   * @param string $file
   *
   * @return string
   */
  protected function getFullPath(string $file): string {
    $full_path = codecept_data_dir($file);
    if (!file_exists(dirname($full_path))) {
      mkdir(dirname($full_path), 0777, TRUE);
    }
    return $full_path;
  }

  /**
   * @return string
   */
  protected function getUniqueFilePrefix(): string {
    return substr(md5(self::class . microtime()), 0, 5);
  }

}
