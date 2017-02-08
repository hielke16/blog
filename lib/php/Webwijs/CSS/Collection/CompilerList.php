<?php
namespace Webwijs\CSS\Collection;

use Webwijs\CSS\CompilerInterface;
use ArrayIterator;

/**
 * Compiler list
 * 
 * The compiler list is a collection of CSS compilers
 * @author Leo Flapper
 * @version 0.1.0
 */
class CompilerList implements \Countable, \IteratorAggregate{
		
	/**
	 * List of CSS compilers
	 * @var array list of compilers
	 */
	private $compilers = array();

	/**
	 * Returns the total of compilers
	 * @return int the the total of compilers
	 */
	public function count(){
		return count($this->compilers);
	}

	/**
	 * Returns the compilers as iterator class for modication
	 * @return ArrayIterator list of compilers
	 */
	public function getIterator(){
		return new ArrayIterator($this->compilers);
	}

	/**
	 * Adds a compiler to the compiler list collectiom
	 * @param CompilerInterface $compiler the compiler
	 */
	public function addCompiler(CompilerInterface $compiler){
		$name = $compiler->getName();
		$this->compilers[$name] = $compiler;
	}

	/**
	 * Checks if a compiler exists by name
	 * @param  string  $name the compiler name
	 * @return boolean true if exists, false if not
	 */
	public function hasCompiler($name){
		if (!is_string($name)) {
        throw new \InvalidArgumentException(sprintf(
            '%s: expects a string argument; received "%s"',
            __METHOD__,
            (is_object($name) ? get_class($name) : gettype($name))
        ));
    }

		return isset($this->compilers[$name]);
	}

	/**
	 * Returns the compiler if exists
	 * @param  string $name the compiler name
	 * @return CompilerInterface|false compiler if found, false if not
	 */
	public function getCompiler($name){
		if (!is_string($name)) {
        throw new \InvalidArgumentException(sprintf(
            '%s: expects a string argument; received "%s"',
            __METHOD__,
            (is_object($name) ? get_class($name) : gettype($name))
        ));
    }
    
		if($this->hasCompiler($name)){
			return $this->compilers[$name];
		}
		return false;
	}

}