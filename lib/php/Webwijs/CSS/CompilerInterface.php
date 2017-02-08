<?php
namespace Webwijs\CSS;

/**
 * Compiler Interface
 * 
 * The Compiler is an interface that defines methods to gain access
 * to compiler data.
 * @author Leo Flapper
 * @version 0.2.0
 */
interface CompilerInterface {

	/**
	 * Sets the compiler of the CSS compiler
	 */
	public function setCompiler();

	/**
	 * Compiles the data to plain CSS
	 * @param  string $data pre-compiled data
	 * @return string $data compiled data
	 */
  public function compile($data);

  /**
	 * Adds path to the compiler for @imports
	 * @param  string $path the path
	 */
  public function addImportPath($path);

  /**
   * Returns the compiler name
   * @return string the compiler name
   */
  public function getName();

  /**
   * Returns the relative path to the compiler files
   * @return string the relative path
   */
  public function getRelativePath();

  /**
   * Returns the compiler file extension
   * @return string the file extension
   */
  public function getFileExtension();

  /**
   * Set compiler formatting options
   */
  public function setFormatting($style);
}
