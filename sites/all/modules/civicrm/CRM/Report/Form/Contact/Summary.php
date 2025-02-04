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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'CRM/Report/Form.php';

class CRM_Report_Form_Contact_Summary extends CRM_Report_Form {

    protected $_summary      = null;

    protected $_emailField   = false;
    
    protected $_phoneField   = false;
    
    protected $_customGroupExtends = array( 'Contact', 'Individual', 'Household', 'Organization' );
    
    function __construct( ) {
        $this->_autoIncludeIndexedFieldsAsOrderBys = 1;
        $this->_columns = 
            array( 'civicrm_contact' =>
                   array( 'dao'       => 'CRM_Contact_DAO_Contact',
                          'fields'    =>
                          array( 'sort_name' => 
                                 array( 'title'     => ts( 'Contact Name' ),
                                        'required'  => true,
                                        'no_repeat' => true ),

                                 'id'           => 
                                 array( 'no_display'=> true,
                                        'required'  => true, ), ),
                          'filters'   =>             
                          array( 'sort_name'    => 
                                 array( 'title'      => ts( 'Contact Name' )  ),
                                 'source'       => 
                                 array( 'title'      => ts( 'Contact Source' ),
                                        'type'       => CRM_Utils_Type::T_STRING ),
                                 'id'           => 
                                 array( 'title'      => ts( 'Contact ID' ),
                                        'no_display' => true ), ),
                          'grouping'  => 'contact-fields',
                          'order_bys'  =>
                          array( 'sort_name' =>
                                 array( 'title' => ts( 'Last Name, First Name'), 'default' => '1', 'default_weight' => '0', 'default_is_section' => 1, 'default_order' => 'DESC'
                                      )
                          ),
                          ),
                   'civicrm_email'   =>
                   array( 'dao'       => 'CRM_Core_DAO_Email',
                          'fields'    =>
                          array( 'email' => 
                                 array( 'title'      => ts( 'Email' ),
                                        'no_repeat'  => true 
                                        ),
                                 ),
                          'grouping'  => 'contact-fields', 
                          'order_bys'  =>
                          array( 'email' =>
                                 array( 'title' => ts( 'Email' ),
                                      )
                          ),
                          ),
                   
                   'civicrm_address' =>
                   array( 'dao'       => 'CRM_Core_DAO_Address',
                          'grouping'  => 'contact-fields',
                          'fields'    =>
                          array( 'street_address'    => 
                                 array( 'default' => true ),
                                 'city'              => 
                                 array( 'default' => true ),
                                 'postal_code'       => null,
                                 'state_province_id' => 
                                 array( 'title'   => ts( 'State/Province' ), ),
                                 ),
                          'filters'   =>             
                          array( 'country_id' => 
                                 array( 'title'   => ts( 'Country' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options' => CRM_Core_PseudoConstant::country( ),
                                        ), 
                                 'state_province_id' =>  
                                 array( 'title'   => ts( 'State / Province' ), 
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'options' => CRM_Core_PseudoConstant::stateProvince( ), ), 
                                 ), 
                          ),
                  'civicrm_country' =>
                  array( 'dao'      => 'CRM_Core_DAO_Country',
                         'fields'   =>
                          array( 'name' =>
                                 array( 'title' => 'Country', 'default' => true),
                          ),
                         'order_bys'   =>
                          array( 'name' =>
                                 array( 'title' => 'Country'),
                          ),
                          'grouping' => 'contact-fields',
                          ),
                   'civicrm_phone' => 
                   array( 'dao'       => 'CRM_Core_DAO_Phone',
                          'fields'    =>
                          array( 'phone'  => null),
                          'grouping'  => 'contact-fields',
                          ),

                   'civicrm_group' => 
                   array( 'dao'    => 'CRM_Contact_DAO_Group',
                          'alias'  => 'cgroup',
                          'filters' =>             
                          array( 'gid' => 
                                 array( 'name'    => 'group_id',
                                        'title'   => ts( 'Group' ),
                                        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
                                        'group'   => true,
                                        'options' => CRM_Core_PseudoConstant::group( ) 
                                        ), 
                                 ), 
                          ),
                   );

        $this->_tagFilter = true;
        parent::__construct( );
    }
    
    function preProcess( ) {
        parent::preProcess( );
    }
    
    function select( ) {
        $select = array( );
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('fields', $table) ) {
                foreach ( $table['fields'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( 'required', $field ) ||
                         CRM_Utils_Array::value( $fieldName, $this->_params['fields'] ) ) {
                        if ( $tableName == 'civicrm_email' ) {
                            $this->_emailField = true;
                        } else if ( $tableName == 'civicrm_phone' ) {
                            $this->_phoneField = true;
                        } else if ( $tableName == 'civicrm_country' ) {
                            $this->_countryField = true;
                        }

                        $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['type']  = CRM_Utils_Array::value( 'type', $field );
                        $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                    }
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    static function formRule( $fields, $files, $self ) {  
        $errors = $grouping = array( );
        return $errors;
    }

    function from( ) {
        $this->_from = "
        FROM civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom}
            LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']} 
                   ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_address']}.contact_id AND 
                      {$this->_aliases['civicrm_address']}.is_primary = 1 ) ";
        
        if ( $this->isTableSelected('civicrm_email') ) {
            $this->_from .= "
            LEFT JOIN  civicrm_email {$this->_aliases['civicrm_email']} 
                   ON ({$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_email']}.contact_id AND
                      {$this->_aliases['civicrm_email']}.is_primary = 1) ";
        }

        if ( $this->_phoneField ) {
            $this->_from .= "
            LEFT JOIN civicrm_phone {$this->_aliases['civicrm_phone']} 
                   ON {$this->_aliases['civicrm_contact']}.id = {$this->_aliases['civicrm_phone']}.contact_id AND 
                      {$this->_aliases['civicrm_phone']}.is_primary = 1 ";
        }   

        if ($this->isTableSelected('civicrm_country')) {
            $this->_from .= "
            LEFT JOIN civicrm_country {$this->_aliases['civicrm_country']}
                   ON {$this->_aliases['civicrm_address']}.country_id = {$this->_aliases['civicrm_country']}.id AND
                      {$this->_aliases['civicrm_address']}.is_primary = 1 ";
    }
    }
    
    
    function postProcess( ) {

        $this->beginPostProcess( );

        // get the acl clauses built before we assemble the query
        $this->buildACLClause( $this->_aliases['civicrm_contact'] );

        $sql  = $this->buildQuery( true );
             
        $rows = $graphRows = array();
        $this->buildRows ( $sql, $rows );
        
        $this->formatDisplay( $rows );
        $this->doTemplateAssignment( $rows );
        $this->endPostProcess( $rows );	
    }

    function alterDisplay( &$rows ) {
        // custom code to alter rows
        $entryFound = false;
        foreach ( $rows as $rowNum => $row ) {
            // make count columns point to detail report
            // convert sort name to links
            if ( array_key_exists('civicrm_contact_sort_name', $row) && 
                 array_key_exists('civicrm_contact_id', $row) ) {
                $url = CRM_Report_Utils_Report::getNextUrl( 'contact/detail', 
                                              'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'],
                                              $this->_absoluteUrl, $this->_id );
                $rows[$rowNum]['civicrm_contact_sort_name_link' ] = $url;
                $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts("View Constituent Detail Report for this contact.");
                $entryFound = true;
            }

            if ( array_key_exists('civicrm_address_state_province_id', $row) ) {
                if ( $value = $row['civicrm_address_state_province_id'] ) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince( $value, false );
                }
                $entryFound = true;
            }


            // skip looking further in rows, if first row itself doesn't 
            // have the column we need
            if ( !$entryFound ) {
                break;
            }
        }
    }
}
