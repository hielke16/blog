<?php
namespace Webwijs\Less;


use Webwijs\CSS\MainCompiler as CSSCompiler;

/**
 * Webwijs Less Compiler
 *
 * This class uses the CSSCompiler to compile Less files. This class is being used for backwards compatability with older theme versions 
 * who use the old Less Compiler
 * 
 */
class Less
{
    public function compile($addCss = true, $name = null, $path = null){
        $cssCompiler = new CSSCompiler('Less');
        $cssCompiler->compile();
    }
}
