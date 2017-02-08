<?php

namespace Webwijs\File;

use SplFileInfo;

/**
 * Default Filter
 *
 * Always returns true. 
 * This is an example filter
 *
 * @author Leo Flapper
 * @version 1.1.0
 * @since 1.0.0
 */ 
class DefaultFilter implements FilterInterface 
{

 	/**
 	 * Returns true
 	 * @param  SplFileInfo $file the Spl File Info object
 	 * @return true
 	 */
	public function accept(SplFileInfo $file)
	{
		return true;
	}

}	

