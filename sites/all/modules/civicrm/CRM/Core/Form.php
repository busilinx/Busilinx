<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * This is our base form. It is part of the Form/Controller/StateMachine
 * trifecta. Each form is associated with a specific state in the state
 * machine. Each form can also operate in various modes
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'HTML/QuickForm/Page.php';

require_once 'CRM/Utils/Rule.php';
require_once 'CRM/Utils/Request.php';
require_once 'CRM/Utils/Weight.php';
require_once 'CRM/Core/Permission.php';
require_once 'CRM/Core/Smarty.php';
require_once 'CRM/Core/Form/Renderer.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Utils/String.php';

class CRM_Core_Form extends HTML_QuickForm_Page {

    /**
     * The state object that this form belongs to
     * @var object
     */
    protected $_state;

    /**
     * The name of this form
     * @var string
     */
    protected $_name;

    /**
     * The title of this form
     * @var string
     */
    protected $_title = null;

    /**
     * The options passed into this form
     * @var mixed
     */
    protected $_options = null;

    /**
     * The mode of operation for this form
     * @var int
     */
    protected $_action;

    /**
     * the renderer used for this form
     *
     * @var object
     */
    protected $_renderer;

    /**
     * cache the smarty template for efficiency reasons
     *
     * @var CRM_Core_Smarty
     */
    static protected $_template;

    /**
     * constants for attributes for various form elements
     * attempt to standardize on the number of variations that we 
     * use of the below form elements
     *
     * @var const string
     */
    const
        ATTR_SPACING = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

    /**
     * All checkboxes are defined with a common prefix. This allows us to
     * have the same javascript to check / clear all the checkboxes etc
     * If u have multiple groups of checkboxes, you will need to give them different
     * ids to avoid potential name collision
     *
     * @var const string / int
     */
    const
        CB_PREFIX     = 'mark_x_',
        CB_PREFIY     = 'mark_y_',
        CB_PREFIZ     = 'mark_z_',
        CB_PREFIX_LEN = 7;

    /**
     * Constructor for the basic form page
     *
     * We should not use QuickForm directly. This class provides a lot
     * of default convenient functions, rules and buttons
     *
     * @param object    $state     State associated with this form
     * @param enum      $action    The mode the form is operating in (None/Create/View/Update/Delete)
     * @param string    $method    The type of http method used (GET/POST)
     * @param string    $name      The name of the form if different from class name
     * 
     * @return object
     * @access public
     */

    function __construct( $state = null,
                          $action = CRM_Core_Action::NONE,
                          $method = 'post',
                          $name = null ) {

        if ( $name ) {
            $this->_name  = $name;
        } else {
            $this->_name  = CRM_Utils_String::getClassName(CRM_Utils_System::getClassName($this));
        }

        $this->HTML_QuickForm_Page( $this->_name, $method );
    
        $this->_state   = $state;
        $this->_action  = (int) $action;

        $this->registerRules( );

        // let the constructor initialize this, should happen only once
        if ( ! isset( self::$_template ) ) {
            self::$_template = CRM_Core_Smarty::singleton( );
        }

    }

    static function generateID( ) {
    }

    /**
     * register all the standard rules that most forms potentially use
     *
     * @return void
     * @access private
     *
     */
    function registerRules( ) {
        static $rules = array( 'title', 'longTitle', 'variable', 'qfVariable', 
                               'phone', 'integer', 'query',
                               'url', 'wikiURL',
                               'domain','numberOfDigit',
                               'date', 'currentDate',
                               'asciiFile', 'htmlFile', 'utf8File',
                               'objectExists', 'optionExists', 'postalCode', 'money', 'positiveInteger',
                               'xssString', 'fileExists', 'autocomplete', 'validContact' );

        foreach ( $rules as $rule ) {
            $this->registerRule( $rule, 'callback', $rule, 'CRM_Utils_Rule' );
        }
    }

    /**
     * Simple easy to use wrapper around addElement. Deal with
     * simple validation rules
     *
     * @param string type of html element to be added
     * @param string name of the html element
     * @param string display label for the html element
     * @param string attributes used for this element.
     *               These are not default values
     * @param bool   is this a required field
     *
     * @return object    html element, could be an error object
     * @access public
     *
     */
    function &add($type, $name, $label = '',
                  $attributes = '', $required = false, $javascript = null ) {
        $element =& $this->addElement($type, $name, $label, $attributes, $javascript );
        if (HTML_QuickForm::isError($element)) {
            CRM_Core_Error::fatal(HTML_QuickForm::errorMessage($element));
        }
        
        if ( $required ) {
            $error = $this->addRule($name, ts('%1 is a required field.', array(1 => $label)) , 'required');
            if (HTML_QuickForm::isError($error)) {
                CRM_Core_Error::fatal(HTML_QuickForm::errorMessage($element));
            }
        }
    
        return $element;
    }
  
    /**
     * This function is called before buildForm. Any pre-processing that
     * needs to be done for buildForm should be done here
     *
     * This is a virtual function and should be redefined if needed
     *
     * @access public
     * @return void
     *
     */
    function preProcess() {
    }

    /**
     * This function is called after the form is validated. Any
     * processing of form state etc should be done in this function.
     * Typically all processing associated with a form should be done
     * here and relevant state should be stored in the session
     *
     * This is a virtual function and should be redefined if needed
     * 
     * @access public
     * @return void
     *
     */
    function postProcess() {
    }

    /**
     * This function is just a wrapper, so that we can call all the hook functions
     */
    function mainProcess() {
        $this->postProcess( );

        $this->postProcessHook( );
    }

    /**
     * The postProcess hook is typically called by the framework
     * However in a few cases, the form exits or redirects early in which 
     * case it needs to call this function so other modules can do the needful
     * Calling this function directly should be avoided if possible. In general a
     * better way is to do setUserContext so the framework does the redirect
     *
     */
    function postProcessHook( ) {
        CRM_Utils_Hook::postProcess( get_class( $this ),
                                     $this );
    }

    /**
     * This virtual function is used to build the form. It replaces the
     * buildForm associated with QuickForm_Page. This allows us to put 
     * preProcess in front of the actual form building routine
     *
     * @access public
     * @return void
     *
     */
    function buildQuickForm() {
    }

    /**
     * This virtual function is used to set the default values of
     * various form elements
     *
     * access        public
     * @return array reference to the array of default values
     *
     */
    function setDefaultValues( ) {
    }

    /**
     * This is a virtual function that adds group and global rules to
     * the form. Keeping it distinct from the form to keep code small
     * and localized in the form building code
     *
     * @access public
     * @return void
     *
     */
    function addRules() {
    }

    function validate( ) {
        $error = parent::validate( );

        require_once 'CRM/Utils/Hook.php';
        $hookErrors = CRM_Utils_Hook::validate( get_class( $this ),
                                                $this->_submitValues, $this->_submitFiles, $this );
        if ( $hookErrors !== true && is_array($hookErrors) && !empty($hookErrors) ) {
            $this->_errors += $hookErrors;
        }

        return (0 == count($this->_errors));
    }

    /**
     * Core function that builds the form. We redefine this function
     * here and expect all CRM forms to build their form in the function
     * buildQuickForm.
     *
     */
    function buildForm() {
        $this->_formBuilt = true;

        $this->preProcess();

        $this->assign('translatePermission', CRM_Core_Permission::check('translate CiviCRM'));

        if ( $this->controller->_key &&
             $this->controller->_print != CRM_Core_Smarty::PRINT_NOFORM ) {
            $this->addElement( 'hidden', 'qfKey', $this->controller->_key );
            $this->assign( 'qfKey', $this->controller->_key );
        }

        require_once 'CRM/Utils/Hook.php';
        
        $this->buildQuickForm();

        $defaults =& $this->setDefaultValues( );
        unset( $defaults['qfKey'] );
        
        if ( ! empty( $defaults ) ) {
            $this->setDefaults( $defaults );
        }

        // call the form hook
        // also call the hook function so any modules can set thier own custom defaults
        // the user can do both the form and set default values with this hook
        CRM_Utils_Hook::buildForm( get_class( $this ),
                                   $this );

        $this->addRules();

    }

    /**
     * Add default Next / Back buttons 
     *
     * @param array   array of associative arrays in the order in which the buttons should be
     *                displayed. The associate array has 3 fields: 'type', 'name' and 'isDefault'
     *                The base form class will define a bunch of static arrays for commonly used
     *                formats
     *
     * @return void
     *
     * @access public
     *
     */
    function addButtons( $params ) 
    {
        $prevnext = array( );
        $spacing = array( );
        foreach ( $params as $button ) {
            $js = CRM_Utils_Array::value( 'js', $button );
            $isDefault = CRM_Utils_Array::value( 'isDefault', $button, false );
            if ( $isDefault ) {
                $attrs = array( 'class' => 'form-submit default' );
            } else {
                $attrs = array( 'class' => 'form-submit' );
            }

            if ( $js ) {
                $attrs = array_merge( $js, $attrs );
            }

            if ( $button['type'] === 'reset' ) {
                $prevnext[] =& $this->createElement( $button['type'], 'reset', $button['name'], $attrs );
            } else {
                if ( CRM_Utils_Array::value( 'subName', $button ) ) {
                    $buttonName = $this->getButtonName( $button['type'], $button['subName'] );
                } else {
                    $buttonName = $this->getButtonName( $button['type'] );
                }
                
                if ( in_array( $button['type'], array( 'next', 'upload' ) ) && $button['name'] === 'Save' ) {
                    $attrs = array_merge( $attrs , ( array ( 'accesskey' => 'S' ) ) );
                }                
                $prevnext[] =& $this->createElement( 'submit', $buttonName, $button['name'], $attrs );
            }
            if ( CRM_Utils_Array::value( 'isDefault', $button ) ) {
                $this->setDefaultAction( $button['type'] );
            }
            
            // if button type is upload, set the enctype
            if ( $button['type'] == 'upload' ) {
                $this->updateAttributes(array('enctype' => 'multipart/form-data'));
                $this->setMaxFileSize();
            }

            // hack - addGroup uses an array to express variable spacing, read from the last element
            $spacing[] = CRM_Utils_Array::value('spacing', $button, self::ATTR_SPACING);
        }
        $this->addGroup( $prevnext, 'buttons', '', $spacing, false );
    }

    /**
     * getter function for Name
     *
     * @return string
     * @access public
     */     
    function getName() {
        return $this->_name;
    }
   
    /**
     * getter function for State
     *
     * @return object
     * @access public
     */     
    function &getState() {
        return $this->_state;
    }

    /**
     * getter function for StateType
     *
     * @return int
     * @access public
     */     
    function getStateType( ) {
        return $this->_state->getType( );
    }

    /**
     * getter function for title. Should be over-ridden by derived class
     *
     * @return string
     * @access public
     */     
    function getTitle( ) {
        return $this->_title ? $this->_title : ts( 'ERROR: Title is not Set' );
    }
	
    /**
     * setter function for title.
     *
     * @param string $title the title of the form
     *
     * @return void
     * @access public
     */     
    function setTitle( $title ) {
        $this->_title = $title;
    }

    /**
     * Setter function for options
     * @param mixed 
     *
     * @return void
     * @access public
     */
    function setOptions( $options ) {
        $this->_options = $options;
    }

    /**
     * getter function for link.
     *
     * @return string
     * @access public
     */     
    function getLink( ) {
        $config = CRM_Core_Config::singleton( );
        return CRM_Utils_System::url( $_GET[$config->userFrameworkURLVar],
                                      '_qf_' . $this->_name . '_display=true' );
    }
	
    /**
     * boolean function to determine if this is a one form page
     *
     * @return boolean
     * @access public
     */     
    function isSimpleForm() {
        return $this->_state->getType( ) & ( CRM_Core_State::START | CRM_Core_State::FINISH );
    }

    /**
     * getter function for Form Action
     *
     * @return string
     * @access public
     */     
    function getFormAction() {
        return $this->_attributes['action'];
    }
	
    /**
     * setter function for Form Action
     *
     * @param string
     * @return void
     * @access public
     */     
    function setFormAction($action) {
        $this->_attributes['action'] = $action;
    }

    /**
     * render form and return contents
     * 
     * @return string
     * @access public
     */  
    function toSmarty() {
        $renderer =& $this->getRenderer();
        $this->accept($renderer);
        $content = $renderer->toArray();
        $content['formName'] = $this->getName();
        return $content;
    }

    /** 
     * getter function for renderer. If renderer is not set
     * create one and initialize it  
     *
     * @return object
     * @access public
     */
    function &getRenderer() {
        if (! isset($this->_renderer)) {
            $this->_renderer =& CRM_Core_Form_Renderer::singleton( );
        }
        return $this->_renderer;
    }
  
    /**
     * Use the form name to create the tpl file name
     *
     * @return string
     * @access public
     */
    function getTemplateFileName() {
        require_once( 'CRM/Core/Extensions.php' );
        $ext = new CRM_Core_Extensions();
        if( $ext->isExtensionClass( CRM_Utils_System::getClassName( $this ) ) ) {
            $filename = $ext->getTemplateName( CRM_Utils_System::getClassName( $this ) );
            $tplname =  $ext->getTemplatePath( CRM_Utils_System::getClassName( $this ) ) . DIRECTORY_SEPARATOR . $filename;
        } else {
            $tplname = str_replace( '_',
                                    DIRECTORY_SEPARATOR,
                                    CRM_Utils_System::getClassName( $this ) ) .'.tpl';
        }
        return $tplname;
    }

    /**
     * Error reporting mechanism
     *
     * @param string  $message Error Message
     * @param int     $code    Error Code
     * @param CRM_Core_DAO $dao     A data access object on which we perform a rollback if non - empty
     * @return void
     * @access public
     */
    function error( $message, $code = null, $dao = null ) {
        if ( $dao ) {
            $dao->query( 'ROLLBACK' );
        }

        $error =& CRM_Core_Error::singleton();
        
        $error->push( $code, $message );
    }
  
    /**
     * Store the variable with the value in the form scope
     *
     * @param  string name  : name  of the variable
     * @param  mixed  value : value of the variable
     *
     * @access public
     * @return void
     *
     */
    function set( $name, $value ) {
        $this->controller->set( $name, $value );
    }

    /**
     * Get the variable from the form scope
     *
     * @param  string name  : name  of the variable
     *
     * @access public
     * @return mixed
     *
     */
    function get( $name ) {
        return $this->controller->get( $name );
    }

    /**
     * getter for action
     *
     * @return int
     * @access public
     */
    function getAction( ) {
        return $this->_action;
    }

    /**
     * setter for action
     *
     * @param  int  $action the mode we want to set the form
     *
     * @return void
     * @access public
     */
    function setAction( $action ) {
        $this->_action = $action;
    }

    /**
     * assign value to name in template
     *
     * @param array|string $name  name  of variable
     * @param mixed $value value of varaible
     *
     * @return void
     * @access public
     */
    function assign( $var, $value = null) {
        self::$_template->assign($var, $value);
    }

    /**
     * assign value to name in template by reference
     *
     * @param array|string $name  name  of variable
     * @param mixed $value value of varaible
     *
     * @return void
     * @access public
     */
    function assign_by_ref( $var, &$value ) {
        self::$_template->assign_by_ref($var, $value);
    }

    function &addRadio( $name, $title, &$values, $attributes = null, $separator = null, $required = false ) {
        $options = array( );
        foreach ( $values as $key => $var ) {
            $options[] =& HTML_QuickForm::createElement('radio', null, null, $var, $key, $attributes);
        }
        $group =& $this->addGroup($options, $name, $title, $separator);
        if ($required) {
            $this->addRule($name, ts('%1 is a required field.', array(1 => $title)), 'required');
        }           
        return $group;
    }

    function addYesNo( $id, $title, $dontKnow = null ,$required = null, $attribute = null) {
        $choice   = array( );
        $choice[] =& $this->createElement( 'radio', null, '11', ts( 'Yes' ), '1', $attribute );
        $choice[] =& $this->createElement( 'radio', null, '11', ts( 'No' ) , '0', $attribute );
        if ( $dontKnow ) {
            $choice[] =& $this->createElement( 'radio', null, '22', ts( "Don't Know" ), '2', $attribute );
        }
        $group =& $this->addGroup( $choice, $id, $title );

        if ( $required ) {
            $this->addRule($id,  ts('%1 is a required field.', array(1 => $title)),'required');
        }
    }

    function addCheckBox( $id, $title, $values, $other = null,
                          $attributes = null, $required = null,
                          $javascriptMethod = null,
                          $separator = '<br />', $flipValues = false ) {
        $options = array( );

        if ($javascriptMethod) {
            foreach ( $values as $key => $var ) {
                if ( ! $flipValues ) {
                    $options[] =& HTML_QuickForm::createElement( 'checkbox', $var, null, $key, $javascriptMethod );
                } else {
                    $options[] =& HTML_QuickForm::createElement( 'checkbox', $key, null, $var, $javascriptMethod );
                }
            }
        } else {
            foreach ( $values as $key => $var ) {
                if ( ! $flipValues ) {
                    $options[] =& HTML_QuickForm::createElement( 'checkbox', $var, null, $key );
                } else {
                    $options[] =& HTML_QuickForm::createElement( 'checkbox', $key, null, $var );
                }
            }
        }

        $this->addGroup($options, $id, $title, $separator );

        if ( $other ) {
            $this->addElement( 'text', $id . '_other', ts( 'Other' ), $attributes[$id . '_other'] );
        }

        if ( $required ) {
            $this->addRule($id,
                           ts('%1 is a required field.', array(1 => $title ) ),
                           'required');
        }
    }
                          
    function resetValues( ) {
        $data =& $this->controller->container( );
        $data['values'][$this->_name] = array( );
    }


    /**
     * simple shell that derived classes can call to add buttons to
     * the form with a customized title for the main Submit
     *
     * @param string $title title of the main button
     * @param string $type  button type for the form after processing
     * @param string $submitOnce If true, add javascript to next button submit which prevents it from being clicked more than once
     * @return void
     * @access public
     */
    function addDefaultButtons( $title, $nextType = 'next', $backType = 'back', $submitOnce = false ) {
        $buttons = array();
        if ( $backType != null ) {
            $buttons[] = array ( 'type'      => $backType,
                                 'name'      => ts('Previous'));
        }
        if ( $nextType != null ) {
            $nextButton = array ( 'type'      => $nextType,
                                  'name'      => $title,
                                  'isDefault' => true   );
            if ( $submitOnce ) {
                $nextButton['js'] = 
                    array( 'onclick' => "return submitOnce(this,'{$this->_name}','" . ts('Processing') ."');" );
            }
            $buttons[] = $nextButton;
        }
        $this->addButtons( $buttons );
    }

    function addDateRange( $name, $label = 'From:', $dateFormat = 'searchDate', $required = false ) {
        $this->addDate( $name . '_from', $label   , $required, array( 'formatType' => $dateFormat ) ); 
        $this->addDate( $name . '_to'  , ts('To:'), $required, array( 'formatType' => $dateFormat ) ); 
    }
    
    function addSelect( $name, $label, $prefix = null, $required = null, $extra = null, $select = '- select -' ) {
        require_once "CRM/Core/OptionGroup.php";
        if ($prefix) {
            $this->addElement('select', $name . '_id' . $prefix , $label,
                              array('' => $select ) + CRM_Core_OptionGroup::values($name), $extra );
            if ( $required) {
                $this->addRule($name . '_id' . $prefix, ts('Please select %1', array(1 => $label)), 'required');
            }
        } else {
            require_once 'CRM/Core/OptionGroup.php';
            $this->addElement('select', $name. '_id' , $label,
                              array('' => $select ) + CRM_Core_OptionGroup::values($name), $extra );
            if ( $required) {
                $this->addRule($name . '_id', ts('Please select %1', array(1 => $label)), 'required');
            }

        }
        
    }
        
    function addWysiwyg( $name, $label, $attributes, $forceTextarea = false ) 
    {
        // 1. Get configuration option for editor (tinymce, ckeditor, pure textarea)
        // 2. Based on the option, initialise proper editor
        require_once 'CRM/Core/BAO/Preferences.php';
        $editor = strtolower( CRM_Utils_Array::value( CRM_Core_BAO_Preferences::value( 'editor_id' ),
                                                      CRM_Core_PseudoConstant::wysiwygEditor( )) );
        if ( !$editor || $forceTextarea ) {
            $editor = 'textarea';
        }
        if ( $editor == 'joomla default editor' ) {
            $editor = 'joomlaeditor';
        }
        
        if ( $editor == 'drupal default editor' ) {
            $editor = 'drupalwysiwyg';
        }
        
        $this->addElement( $editor, $name, $label, $attributes );
        $this->assign('editor', $editor);
    }    

    function addCountry( $id, $title ,$required = null, $extra = null ) {
        $this->addElement('select', $id, $title,
                          array('' => ts('- select -')) + CRM_Core_PseudoConstant::country( ), $extra );
        if( $required ) {
            $this->addRule($id, ts('Please select %1', array(1 => $title)), 'required');
        }

    }

    function addSelectOther( $name, $label, $options, $attributes ,$required = null, $javascriptMethod = null) {
        
        $this->addElement('select', $name . '_id' , $label, $options, $javascriptMethod);
        
        if( $required ) {
            $this->addRule($name . '_id', ts('Please select %1', array(1 => $label)), 'required');
        }
    }

    function buildAddressBlock( $locationId, $title, $phone,
                                $alternatePhone  = null, $addressRequired = null,
                                $phoneRequired = null, $altPhoneRequired = null,
                                $locationName = null ) {
        if ( ! $locationName ) {
            $locationName = "location";
        }
        
        $config = CRM_Core_Config::singleton( );
        $attributes = CRM_Core_DAO::getAttribute('CRM_Core_DAO_Address');

        $location[$locationId]['address']['street_address']         =
            $this->addElement('text', "{$locationName}[$locationId][address][street_address]", $title,
                              $attributes['street_address']);
        if( $addressRequired ){
            $this->addRule("{$locationName}[$locationId][address][street_address]" , ts("Please enter the Street Address for %1." , array( 1 => $title)),'required');
        }

        $location[$locationId]['address']['supplemental_address_1'] =
            $this->addElement('text', "{$locationName}[$locationId][address][supplemental_address_1]", ts('Additional Address 1'),
                              $attributes['supplemental_address_1']);
        $location[$locationId]['address']['supplemental_address_2'] =
            $this->addElement('text', "{$locationName}[$locationId][address][supplemental_address_2]", ts('Additional Address 2'),
                              $attributes['supplemental_address_2']);

        $location[$locationId]['address']['city']                   =
            $this->addElement('text', "{$locationName}[$locationId][address][city]", ts('City'),
                              $attributes['city']);
        if( $addressRequired ){
            $this->addRule("{$locationName}[$locationId][address][city]" , ts("Please enter the City for %1." , array( 1 => $title)),'required');
        }
        
        $location[$locationId]['address']['postal_code']            =
            $this->addElement('text', "{$locationName}[$locationId][address][postal_code]", ts('Zip / Postal Code'),
                              $attributes['postal_code']);
        if( $addressRequired ){
            $this->addRule("{$locationName}[$locationId][address][postal_code]" , ts("Please enter the Zip/Postal Code for %1." , array( 1 => $title)),'required');
        }
        
        $location[$locationId]['address']['postal_code_suffix']            =
            $this->addElement('text', "{$locationName}[$locationId][address][postal_code_suffix]", ts('Add-on Code'),
                              array( 'size' => 4, 'maxlength' => 12 ));
        $this->addRule( "{$locationName}[$locationId][address][postal_code_suffix]", ts('Zip-Plus not valid.'), 'positiveInteger' );

        if ( $config->includeCounty ) {
            $location[$locationId]['address']['county_id']             =
                $this->addElement('select', "{$locationName}[$locationId][address][county_id]", ts('County'),
                               array('' => ts('- select -')) + CRM_Core_PseudoConstant::county());                    
        }        
        
        $location[$locationId]['address']['state_province_id']      =
             $this->addElement('select', "{$locationName}[$locationId][address][state_province_id]", ts('State / Province'),
                               array('' => ts('- select -')) + CRM_Core_PseudoConstant::stateProvince());

        $location[$locationId]['address']['country_id']             =
             $this->addElement('select', "{$locationName}[$locationId][address][country_id]", ts('Country'),
                               array('' => ts('- select -')) + CRM_Core_PseudoConstant::country());
        if( $addressRequired ){
            $this->addRule("{$locationName}[$locationId][address][country_id]" , ts("Please select the Country for %1." , array( 1 => $title)),'required');
        }
        

         if ( $phone ) {
             $location[$locationId]['phone'][1]['phone']      = $this->addElement('text',
                                                                                  "{$locationName}[$locationId][phone][1][phone]", 
                                                                                  $phone,
                                                                                  CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                                                                             'phone'));
             if($phoneRequired) {
                 $this->addRule("{$locationName}[$locationId][phone][1][phone]", ts('Please enter a value for %1', array(1 => $phone)), 'required');
             }
             $this->addRule("{$locationName}[$locationId][phone][1][phone]", ts('Please enter a valid number for %1', array(1 => $phone)), 'phone');
         }

         if ( $alternatePhone ) {
             $location[$locationId]['phone'][2]['phone']      = $this->addElement('text',
                                                                                  "{$locationName}[$locationId][phone][2][phone]", 
                                                                                  $alternatePhone,
                                                                                  CRM_Core_DAO::getAttribute('CRM_Core_DAO_Phone',
                                                                                                             
                                                                                                   'phone'));
             if ($alternatePhoneRequired) {
                 $this->addRule("{$locationName}[$locationId][phone][2][phone]", ts('Please enter a value for %1', array(1 => $alternatePhone)), 'required');
             }
             $this->addRule("{$locationName}[$locationId][phone][2][phone]", ts('Please enter a valid number for %1', array(1 => $alternatePhone)), 'phone');
         }
    }

    public function getRootTitle( ) {
        return null;
    }

    public function getCompleteTitle( ) {
        return $this->getRootTitle( ) . $this->getTitle( );
    }

    static function &getTemplate( ) {
        return self::$_template;
    }

    function addUploadElement( $elementName ) {
        $uploadNames = $this->get('uploadNames');
        if ( ! $uploadNames ) {
            $uploadNames = array( );
        }
        if ( is_array( $elementName ) ) {
            foreach ( $elementName as $name ) {
                if ( ! in_array( $name, $uploadNames ) ) {
                    $uploadNames[] = $name;
                }
            }
        } else {
            if ( ! in_array( $elementName, $uploadNames ) ) {
                $uploadNames[] = $elementName;
            }
        }
        $this->set( 'uploadNames', $uploadNames );

        $config = CRM_Core_Config::singleton( );
        if ( ! empty( $uploadNames ) ) {
            $this->controller->addUploadAction( $config->customFileUploadDir, $uploadNames );
        }
    }

    function buttonType( ) {
        $uploadNames = $this->get( 'uploadNames' );
        $buttonType = ( is_array( $uploadNames ) && ! empty( $uploadNames ) ) ? 'upload' : 'next';
        $this->assign( 'buttonType', $buttonType );
        return $buttonType;
    }

    function getVar( $name ) {
        return isset( $this->$name ) ? $this->$name : null;
    }

    function setVar( $name, $value ) {
        $this->$name = $value;
    }

    /**
     *  Function to add date
     *  @param string $name   name of the element
     *  @param string $label  label of the element
     *  @param array  $attributes key / value pair 
     *                
     *  $attributes = array ( 'addTime' => true, // if you need time 
     *                        'formatType' => 'relative' or 'birth' etc check advanced date settings    
     *                      );            
     *  @param boolean $required  true if required
     *
     */
    function addDate( $name, $label, $required = false, $attributes = null ) {
        if ( CRM_Utils_Array::value( 'formatType', $attributes ) ) {
            // get actual format
            $params = array( 'name' => $attributes['formatType'] );
            $values = array( );
            CRM_Core_DAO::commonRetrieve( 'CRM_Core_DAO_PreferencesDate', $params, $values );
            
            if ( $values['date_format'] ) {
                $attributes['format']  = $values['date_format'];
            }
            
            if ( CRM_Utils_Array::value( 'time_format', $values ) ) {
                $attributes['timeFormat']  = $values['time_format'];
            }
            $attributes['startOffset'] = $values['start'];
            $attributes['endOffset']   = $values['end'];
        }
        
        $config = CRM_Core_Config::singleton( );
        if ( !CRM_Utils_Array::value( 'format', $attributes ) ) {
            $attributes['format']  = $config->dateInputFormat;
        }
        
        if ( !isset( $attributes['startOffset'] ) ) {
            $attributes['startOffset'] = 10; 
        }
        
        if ( !isset( $attributes['endOffset'] ) ) { 
            $attributes['endOffset']   = 10; 
        }
        
        $this->add('text', $name, $label, $attributes );

        if ( CRM_Utils_Array::value( 'addTime', $attributes ) || 
             CRM_Utils_Array::value( 'timeFormat', $attributes ) ) {
            
            if ( !isset( $attributes['timeFormat'] ) ) {
                $timeFormat = $config->timeInputFormat;
            } else {
                $timeFormat = $attributes['timeFormat'];
            }
            
            // 1 - 12 hours and 2 - 24 hours, but for jquery widget it is 0 and 1 respectively
            if ( $timeFormat ) {
                $show24Hours = true;
                if ( $timeFormat == 1 ) {
                    $show24Hours = false;
                }
                
                //CRM-6664 -we are having time element name 
                //in either flat string or an array format. 
                $elementName = $name.'_time';
                if ( substr( $name, -1 ) == ']' ) {
                    $elementName = substr( $name, 0, strlen($name) - 1).'_time]';
                }
                
                $this->add('text', $elementName, ts('Time'), array( 'timeFormat' => $show24Hours ) );
            } 
        }
        
        if ( $required ) {
            $this->addRule( $name, ts('Please select %1', array(1 => $label)), 'required');
            if ( CRM_Utils_Array::value( 'addTime', $attributes ) && CRM_Utils_Array::value( 'addTimeRequired', $attributes ) ) {
                $this->addRule( $elementName, ts('Please enter a time.'), 'required'); 
            }
        }
    }
    
    /**
     *  Function that will add date and time
     */
    function addDateTime( $name, $label, $required = false, $attributes = null ) {
        $addTime = array( 'addTime' => true );
        if ( is_array( $attributes ) ) {
            $attributes = array_merge( $attributes, $addTime );
        } else {
            $attributes = $addTime;
        }
        
        $this->addDate( $name, $label, $required, $attributes );
    }

    /**
     * add a currency and money element to the form
     */
    function addMoney( $name,
                       $label,
                       $required = false,
                       $attributes = null,
                       $addCurrency = true,
                       $currencyName = 'currency',
                       $defaultCurrency = null ) {
        $element = $this->add( 'text', $name, $label, $attributes, $required );
        $this->addRule( $name, ts('Please enter a valid amount.'), 'money');
        
        if ( $addCurrency ) {
            $this->addCurrency( $currencyName, null, true, $defaultCurrency );
        }
        
        return $element;
    }
    
    /**
     * add currency element to the form
     */
    function addCurrency( $name  = 'currency', 
                          $label = null,
                          $required = true,
                          $defaultCurrency = null ) {
        require_once 'CRM/Core/OptionGroup.php';
        $currencies = CRM_Core_OptionGroup::values( 'currencies_enabled' );
        if ( !$required ) $currencies = array( ''=> ts( '- select -' ) ) + $currencies;
        $this->add( 'select', $name, $label, $currencies, $required );
        if ( !$defaultCurrency ) {
            $config = CRM_Core_Config::singleton( );
            $defaultCurrency = $config->defaultCurrency;
        }
        $this->setDefaults( array( $name => $defaultCurrency ) );
    }

}
