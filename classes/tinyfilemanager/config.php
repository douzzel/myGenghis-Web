<?php
/*
#################################################################################################################
This is an OPTIONAL configuration file. rename this file into config.php to use this configuration
The role of this file is to make updating of "tinyfilemanager.php" easier.
So you can:
-Feel free to remove completely this file and configure "tinyfilemanager.php" as a single file application.
or
-Put inside this file all the static configuration you want and forgot to configure "tinyfilemanager.php".
#################################################################################################################
*/
// Auth with login/password
// set true/false to enable/disable it
// Is independent from IP white- and blacklisting
$use_auth = true;
// myGenghis DB connector classs
require_once(__DIR__."/../MYSQL.class.php");
// Login user name and password
// Users: array('Username' => 'Password', 'Username2' => 'Password2', ...)
// Generate secure password hash - https://tinyfilemanager.github.io/docs/pwd.html
/*
$auth_users = array(
'gbsmAdmin' => '$2y$10$tXiZsU9YbLJgC0jg22tScu5C/Z9AjxKsaMFQa./EhfxWrtqPEplHa', //TinyFileManagerAdmin001
'testUser1' => '$2y$10$XydxF3iG1ZLzD7tiGB4lv.TiLsi/ldHgvQnRcjyQtlf783dtej93K', //TinyFileManagerUser002
'testUser2' => '$2y$10$XydxF3iG1ZLzD7tiGB4lv.TiLsi/ldHgvQnRcjyQtlf783dtej93K', //TinyFileManagerUser002
'testOPNCD' => '$2y$10$XpkB6NHzL04W4WCPvv6lf.MTTNqBuY59iCwoc109WHlizRSaIrxQK' //opencode003
);
/*/
$auth_users = array();
$getMGAccounts = MYSQL::query('SELECT Pseudo, Password FROM accounts');
while ($userAccounts = mysqli_fetch_object($getMGAccounts)) {
$auth_users[$userAccounts->Pseudo] = $userAccounts->Password;
}
//*/
// Readonly users
// e.g. array('users', 'guest', ...)
/*
$readonly_users = array(
'testUser2'
);
/ * /
$readonly_users = array();
$getROAccounts = MYSQL::query('SELECT Pseudo FROM accounts WHERE FM_ReadOnly = 1');
while ($roAccounts = mysqli_fetch_object($getROAccounts)) {
$readonly_users = array_push($readonly_users, $roAccounts->Pseudo);
}
//*/
// Enable highlight.js (https://highlightjs.org/) on view's page
$use_highlightjs = true;
// highlight.js style
// for light theme use 'vs'
$highlightjs_style = 'ir-black';
// Enable ace.js (https://ace.c9.io/) on view's page
$edit_files = true;
// Default timezone for date() and time()
// Doc - http://php.net/manual/en/timezones.php
$default_timezone = 'Etc/UTC'; // UTC
// Root path for file manager
// use absolute path of directory i.e: '/var/www/folder' or $_SERVER['DOCUMENT_ROOT'].'/folder'
$root_path = $_SERVER['DOCUMENT_ROOT'].'/uploads/Drive';
// Root url for links in file manager.Relative to $http_host. Variants: '', 'path/to/subfolder'
// Will not working if $root_path will be outside of server document root
$root_url = '';
// Server hostname. Can set manually if wrong
$http_host = $_SERVER['HTTP_HOST'];
// user specific directories
// array('Username' => 'Directory path', 'Username2' => 'Directory path', ...)
$directories_users = array(
'gbsmadmin' => $_SERVER['DOCUMENT_ROOT'],
);
$getOpenCodeAccounts = MYSQL::query('SELECT Pseudo FROM accounts WHERE OpenCode = 1');
while ($openCodeAccounts = mysqli_fetch_object($geOpenCodeAccounts)) {
$directories_users[$openCodeAccounts->Pseudo] = $_SERVER['DOCUMENT_ROOT'].'/themes';
}
// input encoding for iconv
$iconv_input_encoding = 'UTF-8';
// date() format for file modification date
// Doc - https://www.php.net/manual/en/datetime.format.php
$datetime_format = 'd.m.y H:i:s';
// Allowed file extensions for create and rename files
// e.g. 'txt,html,css,js'
$allowed_file_extensions = '';
// Allowed file extensions for upload files
// e.g. 'gif,png,jpg,html,txt'
$allowed_upload_extensions = '';
// Favicon path. This can be either a full url to an .PNG image, or a path based on the document root.
// full path, e.g http://example.com/favicon.png
// local path, e.g images/icons/favicon.png
$favicon_path = '';
// Files and folders to excluded from listing
// e.g. array('myfile.html', 'personal-folder', '*.php', ...)
$exclude_items = array('');
// Online office Docs Viewer
// Availabe rules are 'google', 'microsoft' or false
// google => View documents using Google Docs Viewer
// microsoft => View documents using Microsoft Web Apps Viewer
// false => disable online doc viewer
$online_viewer = 'false';
// Sticky Nav bar
// true => enable sticky header
// false => disable sticky header
$sticky_navbar = true;
// max upload file size
$max_upload_size_bytes = 5000;
// Possible rules are 'OFF', 'AND' or 'OR'
// OFF => Don't check connection IP, defaults to OFF
// AND => Connection must be on the whitelist, and not on the blacklist
// OR => Connection must be on the whitelist, or not on the blacklist
$ip_ruleset = 'OFF';
// Should users be notified of their block?
$ip_silent = true;
// IP-addresses, both ipv4 and ipv6
$ip_whitelist = array(
'127.0.0.1', // local ipv4
'::1' // local ipv6
);
// IP-addresses, both ipv4 and ipv6
$ip_blacklist = array(
'0.0.0.0', // non-routable meta ipv4
'::' // non-routable meta ipv6
);
?>
