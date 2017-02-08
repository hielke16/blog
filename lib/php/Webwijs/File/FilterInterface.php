<?php

namespace Webwijs\File;

use SplFileInfo;

/**
 * Filter Interface
 *
 * Filter function to accept a given SplFileInfo
 *
 * @author Chris Harris
 * @version 1.1.0
 * @since 1.0.0
 */ 
interface FilterInterface
{
    /**
     * Returns true if the specified file is acceptable.
     *
     * @param SplFileInfo $file the file to be tested.
     * @return bool true if the specified file is accepted, false otherwise.
     */
    public function accept(SplFileInfo $file);
}
