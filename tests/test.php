<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<houruipeng@duoguan.com>
 * @date: 2020/3/13 16:32
 */
require_once '../vendor/autoload.php';

use hrp\pop\Pop;

$data=[
	'Action'=>'input',
	'AccessKeyId'=>'aid',
	'Url'=>'http://127.0.0.1',
	'Header'=>array('Content-Type:application/x-www-form-urlencoded','charset=utf-8'),
	'Body'=>['name'=>'jack','from'=>'beijing']
];
$accessSecret='填写你的秘钥';
$pop=new Pop($accessSecret);
$data=$pop->composeUrl($data);
print_r($data);
