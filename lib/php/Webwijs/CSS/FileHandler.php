<?php
namespace Webwijs\CSS;

/**
 * File Handler
 * 
 * File handler uses for handling the compiler and CSS files
 * @author Leo Flapper
 * @version 0.1.0
 */
class FileHandler{

	/**
	 * The input directory
	 * @var string $inputDirectory the input directory
	 */
	protected $inputDirectory;

	/**
	 * The output directory
	 * @var string $outputDirectory the output directory
	 */
	protected $outputDirectory;

	/**
	 * The file extension to be used
	 * @var string $fileExtension the file extension
	 */
	protected $fileExtension;

	/**
	 * Returns the file extension
	 * @return string the file extension
	 */
	public function getFileExtension(){
		return $this->fileExtension;
	}

	/**
	 * Sets the file extension
	 * @param string $fileExtension the file extension
	 */
	public function setFileExtension($fileExtension){
		if (!is_string($fileExtension)) {
        throw new \InvalidArgumentException(sprintf(
            '%s: expects a string argument; received "%s"',
            __METHOD__,
            (is_object($fileExtension) ? get_class($fileExtension) : gettype($fileExtension))
        ));
    }

		$this->fileExtension = $fileExtension;
	}

	/**
	 * Returns content of a file by file name
	 * @param  string $filename the filename
	 * @return string|false the content of the file or false on failure
	 */
	public function getFileContents($filename){
		if (!is_string($filename)) {
        throw new \InvalidArgumentException(sprintf(
            '%s: expects a string argument; received "%s"',
            __METHOD__,
            (is_object($filename) ? get_class($filename) : gettype($filename))
        ));
    }

		return file_get_contents($filename);	
	}

	/**
	 * Write a string to a file
	 * @param  string $filename the file name
	 * @param  string $content the content to be written
	 * @return boolean true if successfull, false on failure
	 */
	public function putFileContents($filename, $content){
		if (!is_string($filename)) {
        throw new \InvalidArgumentException(sprintf(
            '%s: expects a string argument; received "%s"',
            __METHOD__,
            (is_object($filename) ? get_class($filename) : gettype($filename))
        ));
    }

		return file_put_contents($filename, $content);	
	}

	/**
	 * Returns the input directory
	 * @return string the input directory
	 */
	public function getInputDirectory(){
		return $this->inputDirectory;
	}

	/**
	 * Sets the input directory
	 * @param string $inputDirectory the input directory
	 */
	public function setInputDirectory($inputDirectory){
		if (!is_string($inputDirectory)) {
        throw new \InvalidArgumentException(sprintf(
            '%s: expects a string argument; received "%s"',
            __METHOD__,
            (is_object($inputDirectory) ? get_class($inputDirectory) : gettype($inputDirectory))
        ));
    }

		$this->inputDirectory = $inputDirectory;
	}

	/**
	 * Returns the output directory
	 * @return string the output directory
	 */
	public function getOutputDirectory(){
		return $this->outputDirectory;
	}

	/**
	 * Sets the output directory
	 * @param string $outputDirectory the output directory
	 */
	public function setOutputDirectory($outputDirectory){
		$this->outputDirectory = $outputDirectory;
	}

	/**
   * Gets all the compiler files from a directory
   *
   * Only reads the filenames from the directory given, compiler files in a child map of the directory given are not included. 
   *
   * @return array $names - array of file names
   */
  public function getFileNames(){
  		$directory = $this->getInputDirectory();
  		$fileExtension = $this->getFileExtension();
      if(file_exists($directory)){
          if($files = scandir($directory)){
              $names = array();
              foreach($files as $file){
                  if(substr($file, -strlen($fileExtension)) === $fileExtension){
                      $names[] = $file;
                  }
              }
              return $names;
          } else {
              throw new \LogicException(sprintf('There are no %s files at path: %s', $fileExtension, $path));
          }
      } else {
          throw new \LogicException(sprintf('%s Directory does not exist at path: %s', $directory, $path));
      }
  }

}