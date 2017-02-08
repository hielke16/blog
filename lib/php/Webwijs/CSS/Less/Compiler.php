<?php
namespace Webwijs\CSS\Less;

use Webwijs\CSS\CompilerInterface;

require_once __DIR__ . '/lessc.inc.php';

/**
 * Less Compiler
 * 
 * The Less compiler, compiles Less CSS to CSS
 * @author Leo Flapper
 * @version 0.1.0
 */
class Compiler implements CompilerInterface{

	/**
	 * The Less compiler
	 * @var object Less compiler
	 */
	protected $less;

	/**
	 * Sets he Less compiler
	 */
	public function __construct(){
		$this->setCompiler();
	}

	/**
	 * Returns the Less name
	 * @return string the Less name
	 */
	public function getName(){
		return 'Less';
	}

	/**
	 * Returns the relative path of the Less directory
	 * @return string the Less relative path
	 */
	public function getRelativePath(){
		return 'Less';
	}

	/**
	 * Returns the Less file extension
	 * @return string the Less file extension
	 */
	public function getFileExtension(){
		return '.less';
	}

	/**
	 * Sets the Less compiler
	 * @see lessc.inc.php
	 * @link http://leafo.net/lessphp
	 */
	public function setCompiler(){
		$this->less = new \lessc();
	}

	/**
	 * Adds import path to compiler for @import
	 * @param string $path import path
	 */
	public function addImportPath($path){
		$this->less->addImportDir($path);
	}

	/**
	 * Compiles Less data to CSS
	 * @param  string $data the Less data
	 * @return string the Less data
	 */
  public function compile($data){
  	return $this->less->compile($data);
  }
  public function setFormatting($style){
  	
  }
}
