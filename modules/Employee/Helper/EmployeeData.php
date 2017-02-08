<?php

namespace Module\Employee\Helper;

use Webwijs\Post;
use Webwijs\Util\Arrays;

use Module\Employee\Admin\Metabox\Data as DataMetabox;

/**
 * Returns employee data
 *
 * @author Leo Flapper
 * @version 1.0.0
 */
class EmployeeData
{

	/**
	 * Sets the Employee Data Metabox class
	 */
	public function __construct()
	{
		$this->dataMetabox = new DataMetabox();
	}

	/**
	 * Returns a single employee data value by post id and data id
	 * @param  integer 	$postId the post id
	 * @param  string 	$id     the post meta id
	 * @return string   $output the output result
	 */
    public function employeeData($postId, $id)
    {    
    	$output = '';
    	if($this->dataMetabox->idAllowed($id)){
    		$output = get_post_meta($postId, '_'.$this->dataMetabox->getMetaboxId($id), true);
    	}
        return $output;
    }
    
}