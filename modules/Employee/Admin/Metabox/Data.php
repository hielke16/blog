<?php

namespace Module\Employee\Admin\Metabox;

use Webwijs\Admin\Metabox\Multibox;

/**
 * Adds metaboxes which handles the data for the Employee admin page
 *
 * @author Leo Flapper
 * @version 1.0.0
 */
class Data {

	/**
	 * The prefix used for the meta_key in the post meta table of the database
	 * @var string
	 */
	public $prefix = 'employee';

	/**
	 * Multdimensional array containing the data to use for the Employee metaboxes
	 * @var array
	 */
	public $data = array(
		array('type' => 'Text', 'id' => 'function', 'title' => 'Functie'),
		array('type' => 'Text', 'id' => 'phone', 	'title' => 'Telefoonnummer'),
		array('type' => 'Text', 'id' => 'email', 	'title' => 'E-mailadres'),
		array('type' => 'Text', 'id' => 'linkedin', 'title' => 'LinkedIn'),
		array('type' => 'Text', 'id' => 'facebook', 'title' => 'Facebook'),
		array('type' => 'Text', 'id' => 'twitter', 	'title' => 'Twitter')
	);

	/**
	 * Registers the metaboxes for the Employee custom post type
	 * @param  string $context the context of the metabox
	 * @param  string $title the title of the metabox
	 * @return void
	 */
	public function register($context = 'normal', $title = 'Contactgegevens')
	{
		Multibox::register('employee', array('id' => 'employee', 'context' => $context, 'title' => $title, 'boxes' => $this->getBoxes()));	
	}
	
	/**
	 * Returns the metaboxes data to use with the Multibox.
	 * It also checks if the box is allowed for use
	 * @return array $boxes multidimensional array containing the metabox data
	 */
	public function getBoxes()
	{
		$boxes = array();
		foreach($this->getData() as $data){
			if($this->idAllowed($data['id'])){
				$boxes[] = array(
					'class' => sprintf('Webwijs\Admin\Metabox\%s', $data['type']), 
					'settings' => array('id' => $this->getMetaboxId($data['id']), 'title' => $data['title'])
				);	
			}
		};
		return $boxes;
	}

	/**
	 * Returns the metabox data array
	 * @return array $data multdimensional array containing the data to use for the Employee metaboxes
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Checks if the metabox id is allowed by applying the designated filter
	 * 
	 * Example: add_filter('employee_linkedin_allowed', '__return_false');
	 * 'linkedin' is the id.
	 * 
	 * @param  string $id the metabox id to be checked
	 * @return boolean true if allowed, false if not
	 */
	public function idAllowed($id)
	{
		if(apply_filters(sprintf('%s_%s_allowed', $this->getPrefix(), $id), true)){
			return true;
		}
		return false;
	}

	/**
	 * Returns the metabox id. It adds the prefix to the id given
	 * @param  string $id the id
	 * @return string $metaboxId the id with the metabox prefix
	 */
	public function getMetaboxId($id)
	{
		return sprintf('%s_%s', $this->getPrefix(), $id);
	}

	/**
	 * Returns the metabox prefix
	 * @return string $prefix the metabox prefix
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}
	
}