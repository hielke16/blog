<?php
namespace Webwijs\CSS;
use Webwijs\Application;
use Webwijs\CSS\Languages;
use Webwijs\CSS\Collection\CompilerList;
use Webwijs\CSS\Less\Compiler as LessCompiler;
use Webwijs\CSS\SCSS\Compiler as SCSSCompiler;

/**
 * The MainCompiler compiles different types of CSS formats to plain CSS.
 *
 * @author Leo Flapper
 * @version 0.1.0
 */
class MainCompiler
{
	/**
	 * List of CSS compilers.
	 *
	 * @var array with compilers
	 */
	protected $compilerList;

	/**
	 * The compiler which is used.
	 *
	 * @var CompilerInterface $compiler the used compiler
	 */
	protected $compiler;

	/**
	 * Contains the file handler for reading and writing data.
	 *
	 * @var object $filehandler the file handler class
     */
    protected $fileHandler;

	/**
	 * Sets the compiler list, sets the compiler to be used and loads the file handler for compiling to CSS.
	 *
	 * @param string $compilerName the compiler name
	 */
    public function __construct($compilerName = 'SCSS', $formatting = "Nested") {
		$this->setCompilerList();
		$this->setCompiler($compilerName);
        $this->compiler->setFormatting($formatting);
		$this->setFileHandler();
	}

	/**
     * Initiates and sets the FileHandler class
     */
	private function setFileHandler() {
        $this->fileHandler = new FileHandler();
	}

	/**
     * Compiles the compilerfiles to CSS
     *
     * Sets the input and output directory. It reads the input directory for the compiler files and compiles the data to CSS.
     *
     * @param string $inputDirectory optional - the directory with compiler input files
     * @param string $outputDirectory optional - the css output directory
     */
    public function compile($force = false, $inputDirectory = null, $outputDirectory = null)
    {
        if(!$inputDirectory) {
            $this->fileHandler->setInputDirectory(get_template_directory().'/assets/'.$this->compiler->getRelativePath());
	        $this->compiler->addImportPath(get_template_directory().'/assets/'.$this->compiler->getRelativePath().'/imports');
        } else {
	        $this->fileHandler->setInputDirectory($inputDirectory);
            $this->compiler->addImportPath($inputDirectory.'/imports');
        }

        if(!$outputDirectory) {
	        $this->fileHandler->setOutputDirectory(get_stylesheet_directory().'/assets/css/');
        } else {
	        $this->fileHandler->setOutputDirectory($outputDirectory);
        }

        $this->fileHandler->setFileExtension($this->compiler->getFileExtension());

        $names = $this->fileHandler->getFileNames();
        foreach($names as $name) {
          $this->compileFile($name, $force);
        }
    }

   /**
    * Add import path for @imports
    *
    * @param string $path the path
    */
    public function addImportPath($path)
    {
        $this->compiler->addImportPath($path);
    }

    /**
     * Compiles a file by name.
     *
     * Loads the single file from the input directory, compiles it to CSS and saves the CSS data in the output directory.
     *
     * @param  string $name the filename
     * @return boolean true if successful, false otherwise
     */
    private function compileFile($name, $force)
    {
        $inputFile = $this->fileHandler->getInputDirectory().'/'.$name;
        if (!file_exists($inputFile)) {
            throw new \LogicException(sprintf('%s input file does not exist at path: %s', $this->getLanguageName(), $inputFile));
        }

        $outputFile = $this->fileHandler->getOutputDirectory().str_replace($this->fileHandler->getFileExtension(), '', $name).'.css';
        if($this->needsCompile($inputFile, $outputFile) || $force == true) {
            if($css = $this->compiler->compile($this->fileHandler->getFileContents($inputFile))){
	            if($this->fileHandler->putFileContents($outputFile, $css)){
	                return true;
	            }
            }
        }
    }

    /**
     * Checks if the input file is newer than the output file and returns true if input file is newer, or if there is no output file.
     *
     * @param string $inputFile  the input file path
     * @param string $outputFile the output file path
     * @return boolean true if the input file newer than the output file, false otherwise.
     */
    private function needsCompile($inputFile, $outputFile)
    {

        if (!is_file($outputFile)) {
            return true;
        }

        return (filemtime($inputFile) > filemtime($outputFile));
    }

    /**
     * Sets the compiler list with Compilers.
     */
	public function setCompilerList()
	{
		$this->compilerList = new CompilerList();
		$this->compilerList->addCompiler(new LessCompiler());
		$this->compilerList->addCompiler(new SCSSCompiler());
	}

	/**
	 * Sets the compiler to be used
	 * @param string $name name of the compiler
	 */
    public function setCompiler($name)
    {
		$this->compiler = $this->compilerList->getCompiler($name);
	}
}
