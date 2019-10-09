<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2014-17 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
// No direct access to this file
defined('_JEXEC') or die;
class J2StoreAppController extends F0FController {

	// the same as the plugin's one!
	var $_element = '';
	protected $cacheableTasks = array();
	
	function __construct($config = array()) {
		parent::__construct ( $config );
		$this->registerTask ( 'apply', 'save' );
	}
	
	/**
	 * Overrides the getView method, adding the plugin's layout path
	 */
 	public function getView( $name = '', $type = '', $prefix = '', $config = array() ){
    	$view = parent::getView( $name, $type, $prefix, $config );
    	$view->addTemplatePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/tmpl/');
    	return $view;
    }
    
    function save() {
    	$app = JFactory::getApplication ();
    	$data = $app->input->getArray ( $_POST );
        $data['params']  = $app->input->post->get('params', array(), 'array');
    	$save_params = new JRegistry ();
    	$save_params->loadArray ( $data['params'] );
		$json = $save_params->toString ();

    	$db = JFactory::getDbo ();
    
    	$query = $db->getQuery ( true )->update ( $db->qn ( '#__extensions' ) )->set ( $db->qn ( 'params' ) . ' = ' . $db->q ( $json ) )->where ( $db->qn ( 'element' ) . ' = ' . $db->q ( $this->_element ) )->where ( $db->qn ( 'folder' ) . ' = ' . $db->q ( 'j2store' ) )->where ( $db->qn ( 'type' ) . ' = ' . $db->q ( 'plugin' ) );
    
       	$db->setQuery ( $query );
    	$db->execute ();
    	if ($data ['appTask'] == 'apply' && isset ( $data ['app_id'] )) {
    		$url = 'index.php?option=com_j2store&view=apps&task=view&id=' . $data ['app_id'];
    	} else {
    		$url = 'index.php?option=com_j2store&view=apps';
    	}
	    $cache = JFactory::getCache();
	    $cache->clean();
    	$this->setRedirect ( $url );
    }

    /**
     * Overrides the delete method, to include the custom models and tables.
     */
    public function delete()
    {	
    	$this->includeCustomTables();
    	parent::delete();
    }

    protected function includeCustomTables(){
   		// Include the custom table
    	F0FModel::addTablePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/tables');
		JFactory::getApplication()->triggerEvent('includeCustomTables', array() );
    }
    
    protected function includeCustomModels(){
    	// Include the custom table
    	F0FModel::addIncludePath(JPATH_SITE.'/plugins/j2store/'.$this->_element.'/'.$this->_element.'/models');
    	JFactory::getApplication()->triggerEvent('includeCustomModels', array() );
    }

    protected function includeCustomModel( $name ){
    	
		JFactory::getApplication()->triggerEvent('includeCustomModel', array($name, $this->_element) );
    }

    protected function includeJ2StoreModel( $name ){    	
		JFactory::getApplication()->triggerEvent('includeJ2StoreModel', array($name) );
    }

    protected function baseLink(){
    	$id = JFactory::getApplication()->input->getInt('id', '');
    	return "index.php?option=com_j2store&view=apps&task=view&id={$id}";
    }
}
