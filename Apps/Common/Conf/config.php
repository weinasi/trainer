<?php

/**
 * 系统配置文件
 */
return array(
	'DB_TYPE'			 => 'mysqli',
	'DB_HOST'			 => 'xx',
	'DB_NAME'			 => 'xx',
	'DB_USER'			 => 'xx',
	'DB_PWD'			 => 'xx',
	'DB_PORT'			 => '3306',
	'DB_PREFIX'			 => 'sp_',
	/* Default Module */
	'DEFAULT_MODULE'	 => 'Weixin',
	/* Data Auth Key */
	"DATA_AUTH_KEY"		 => 'UYWIz6jJFYmL43trJe',
	/* cookies Prefix */
	"COOKIE_PREFIX"		 => '4xNX7o_',
	/* Potal Tpl Path */
	//'CMF_TPL_PATH'		 => CMF_ROOT . '/static/Portal/',
	/* CMF Config Path */
	'CMF_CONF_PATH'		 => CMF_DATA . '/config/',
	/* CMF Databack Path */
	'CMF_DATA_PATH'		 => CMF_DATA . '/backup/',
	/* TMPL Parse String  */
	'TMPL_PARSE_STRING'	 => array(
		'__TMPL__' => __ROOT__ . '/static/weixin',
		'__STATIC__' => __ROOT__ . '/static',
	),
	'URL_MODEL'=>0,
);
