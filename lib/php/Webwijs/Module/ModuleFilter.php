<?php

namespace Webwijs\Module;

use SplFileInfo;
use Webwijs\File\FilterInterface;

/**
 * Default Module Filter
 *
 * Checks whether the path of the file contains an XML extension
 *
 * @author Leo Flapper <leo@webwijs.nu>
 * @author Chris Harris <chris@webwijs.nu>
 * @version 1.1.0
 * @since 1.1.0
 */ 
class ModuleFilter implements FilterInterface 
{

 	/**
 	 * Checks if the whether the path of the file contains an XML extension
 	 * @param  SplFileInfo $file the Spl File Info object
 	 * @return boolean true if contains XML extension, false if not
 	 */
	public function accept(SplFileInfo $file)
	{
		$filename = sprintf('%s.xml', ucfirst(basename($file->getPath())));
        return ($filename === $file->getBasename());  
	}

}