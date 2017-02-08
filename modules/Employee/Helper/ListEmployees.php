<?php

namespace Module\Employee\Helper;

use Webwijs\Post;
use Webwijs\Util\Arrays;

/**
 * Helper for displaying a list of employees
 *
 * @author Leo Flapper
 * @version 1.0.0
 */
class ListEmployees
{

	/**
	 * Function to list employees.
	 * Arguments can be used to filter the employees or display the employees in a certain template.
	 * @param  array $args array containing arguments
	 * @return string $output the html output of the retrieved employees in a certain template, or null if no employees are found
	 */
    public function listEmployees($args = null)
    {    
        $defaults = array(
            'queryArgs' => array(
                'post_type' => 'employee',
                'nopaging' => true,
                'orderby' => 'menu_order',
                'order' => 'asc'
            ),
            'template' => 'partials/employee/list.phtml',
        );
        $args = Arrays::addAll($defaults, (array) $args);

        $output = '';
        query_posts($args['queryArgs']);
        if (have_posts()) {
            $output = $this->view->partial($args['template'], $args['vars']);
        }
        wp_reset_query();
        
        return $output;
    }
    
}