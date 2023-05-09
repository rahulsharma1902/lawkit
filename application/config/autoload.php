<?php
defined('BASEPATH') OR exit('No direct script access allowed'); 
$autoload['packages'] = array(APPPATH.'third_party', '/usr/local/shared');
// $autoload['libraries'] = array('database', 'session', 'email', 'form_validation', 'upload', 'encryption', 'uuid', 'CSVReader', 'excel', 'messages', 'pagination', 'simplecalendar', 'doupload', 'acl', 'apicall', 'webtoken');
$autoload['libraries'] = array('database', 'session', 'email', 'form_validation', 'upload', 'encryption', 'uuid', 'CSVReader', 'messages', 'pagination', 'simplecalendar', 'doupload', 'acl', 'apicall', 'webtoken');

$autoload['drivers'] = array();
$autoload['helper'] = array('url', 'string', 'pdf_helper', 'file', 'directory', 'text', 'cookie');
$autoload['config'] = array();
$autoload['language'] = array();
$autoload['model'] = array('Account_model', 'Searches_model', 'Client_model', 'Admin_model', 'Bugs_model', 'Comments_model', 'Invoices_model', 'Sessions_model', 'Contact_model');
