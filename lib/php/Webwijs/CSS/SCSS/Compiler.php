<?php
namespace Webwijs\CSS\SCSS;

use Webwijs\CSS\CompilerInterface;
use \Leafo\ScssPhp\Compiler as scssc;

/**
 * SCSS Compiler
 * 
 * The SCSS compiler, compiles SCSS css to CSS
 * @author Leo Flapper
 * @version 0.1.0
 */
class Compiler implements CompilerInterface {

	/**
	 * The SCSS compiler
	 * @var object SCSS compiler
	 */
	protected $scss;

	/**
	 * Sets he SCSS compiler
	 */
	public function __construct(){
		$this->setCompiler();
	}

	/**
	 * Returns the SCSS name
	 * @return string the SCSS name
	 */
	public function getName(){
		return 'SCSS';
	}

	/**
	 * Returns the relative path of the SCSS directory
	 * @return string the SCSS relative path
	 */
	public function getRelativePath(){
		return 'SCSS';
	}

	/**
	 * Returns the SCSS file extension
	 * @return string the SCSS file extension
	 */
	public function getFileExtension(){
		return '.scss';
	}

	/**
	 * Sets the SCSS compiler
	 * @see scss.inc.php
	 * @link http://leafo.net/scssphp
	 */
	public function setCompiler(){
		$this->scss = new scssc();
	}

	/**
	 * Adds import path to compiler for @import
	 * @param string $path import path
	 */
	public function addImportPath($path){
		$this->scss->addImportPath($path);
	}

	/**
	 * Compiles SCSS data to CSS
	 * @param  string $data the SCSS data
	 * @return string the CSS data
	 */
  public function compile($data){
  	return $this->scss->compile($data);
  }
    /**
   	 * Set compiler formatting options
   	 * @param string formatting option
   	 */
  public function setFormatting($style){
  	return $this->scss->setFormatter('\Leafo\\ScssPhp\\Formatter\\' . ucfirst($style));
  }
}
