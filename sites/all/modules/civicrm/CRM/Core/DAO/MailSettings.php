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
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Utils/Type.php';
class CRM_Core_DAO_MailSettings extends CRM_Core_DAO
{
    /**
     * static instance to hold the table name
     *
     * @var string
     * @static
     */
    static $_tableName = 'civicrm_mail_settings';
    /**
     * static instance to hold the field values
     *
     * @var array
     * @static
     */
    static $_fields = null;
    /**
     * static instance to hold the FK relationships
     *
     * @var string
     * @static
     */
    static $_links = null;
    /**
     * static instance to hold the values that can
     * be imported / apu
     *
     * @var array
     * @static
     */
    static $_import = null;
    /**
     * static instance to hold the values that can
     * be exported / apu
     *
     * @var array
     * @static
     */
    static $_export = null;
    /**
     * static value to see if we should log any modifications to
     * this table in the civicrm_log table
     *
     * @var boolean
     * @static
     */
    static $_log = false;
    /**
     * primary key
     *
     * @var int unsigned
     */
    public $id;
    /**
     * Which Domain is this match entry for
     *
     * @var int unsigned
     */
    public $domain_id;
    /**
     * name of this group of settings
     *
     * @var string
     */
    public $name;
    /**
     * whether this is the default set of settings for this domain
     *
     * @var boolean
     */
    public $is_default;
    /**
     * email address domain (the part after @)
     *
     * @var string
     */
    public $domain;
    /**
     * optional local part (like civimail+ for addresses like civimail+s.1.2@example.com)
     *
     * @var string
     */
    public $localpart;
    /**
     * contents of the Return-Path header
     *
     * @var string
     */
    public $return_path;
    /**
     * name of the protocol to use for polling (like IMAP, POP3 or Maildir)
     *
     * @var string
     */
    public $protocol;
    /**
     * server to use when polling
     *
     * @var string
     */
    public $server;
    /**
     * port to use when polling
     *
     * @var int unsigned
     */
    public $port;
    /**
     * username to use when polling
     *
     * @var string
     */
    public $username;
    /**
     * password to use when polling
     *
     * @var string
     */
    public $password;
    /**
     * whether to use SSL or not
     *
     * @var boolean
     */
    public $is_ssl;
    /**
     * folder to poll from when using IMAP, path to poll from when using Maildir, etc.
     *
     * @var string
     */
    public $source;
    /**
     * class constructor
     *
     * @access public
     * @return civicrm_mail_settings
     */
    function __construct()
    {
        parent::__construct();
    }
    /**
     * returns all the column names of this table
     *
     * @access public
     * @return array
     */
    function &fields()
    {
        if (!(self::$_fields)) {
            self::$_fields = array(
                'id' => array(
                    'name' => 'id',
                    'type' => CRM_Utils_Type::T_INT,
                    'required' => true,
                ) ,
                'domain_id' => array(
                    'name' => 'domain_id',
                    'type' => CRM_Utils_Type::T_INT,
                    'required' => true,
                ) ,
                'name' => array(
                    'name' => 'name',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Name') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
                'is_default' => array(
                    'name' => 'is_default',
                    'type' => CRM_Utils_Type::T_BOOLEAN,
                ) ,
                'domain' => array(
                    'name' => 'domain',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Domain') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
                'localpart' => array(
                    'name' => 'localpart',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Localpart') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
                'return_path' => array(
                    'name' => 'return_path',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Return Path') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
                'protocol' => array(
                    'name' => 'protocol',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Protocol') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
                'server' => array(
                    'name' => 'server',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Server') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
                'port' => array(
                    'name' => 'port',
                    'type' => CRM_Utils_Type::T_INT,
                    'title' => ts('Port') ,
                ) ,
                'username' => array(
                    'name' => 'username',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Username') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
                'password' => array(
                    'name' => 'password',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Password') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
                'is_ssl' => array(
                    'name' => 'is_ssl',
                    'type' => CRM_Utils_Type::T_BOOLEAN,
                ) ,
                'source' => array(
                    'name' => 'source',
                    'type' => CRM_Utils_Type::T_STRING,
                    'title' => ts('Source') ,
                    'maxlength' => 255,
                    'size' => CRM_Utils_Type::HUGE,
                ) ,
            );
        }
        return self::$_fields;
    }
    /**
     * returns the names of this table
     *
     * @access public
     * @return string
     */
    function getTableName()
    {
        return self::$_tableName;
    }
    /**
     * returns if this table needs to be logged
     *
     * @access public
     * @return boolean
     */
    function getLog()
    {
        return self::$_log;
    }
    /**
     * returns the list of fields that can be imported
     *
     * @access public
     * return array
     */
    function &import($prefix = false)
    {
        if (!(self::$_import)) {
            self::$_import = array();
            $fields = & self::fields();
            foreach($fields as $name => $field) {
                if (CRM_Utils_Array::value('import', $field)) {
                    if ($prefix) {
                        self::$_import['mail_settings'] = & $fields[$name];
                    } else {
                        self::$_import[$name] = & $fields[$name];
                    }
                }
            }
        }
        return self::$_import;
    }
    /**
     * returns the list of fields that can be exported
     *
     * @access public
     * return array
     */
    function &export($prefix = false)
    {
        if (!(self::$_export)) {
            self::$_export = array();
            $fields = & self::fields();
            foreach($fields as $name => $field) {
                if (CRM_Utils_Array::value('export', $field)) {
                    if ($prefix) {
                        self::$_export['mail_settings'] = & $fields[$name];
                    } else {
                        self::$_export[$name] = & $fields[$name];
                    }
                }
            }
        }
        return self::$_export;
    }
}
