<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 16/7/14
 * Time: 11:24
 */
include_once "mysqlAnalyse.php";
$src = file_get_contents("./oms4.sql");
$target = file_get_contents("./oms4-local.sql");

$analyse = array();
$ss = new mysqlAnalyse();
$ss->initTables($src);
$tt = new mysqlAnalyse();
$tt->initTables($target);

var_dump(analyseDiff($ss->getTables(),$tt->getTables()));

function analyseDiff($source, $target){
	$newTables = array_diff_key($source, $target);
	$deletedTables = array_diff_key($target, $source);
	$result = array();
	$result["newTables"] = $newTables;
	$result["deletedTables"] = $deletedTables;
	$result["modifiedTables"] = array();

	$sourceTables = array_intersect_key($source,$target);
	foreach ($sourceTables as $key=>$sourceTable) {
		$newFields = array_diff_key($sourceTable["tableFields"], $target[$key]["tableFields"]);
		$deletedFields = array_diff_key($target[$key]["tableFields"], $sourceTable["tableFields"]);
		$result["modifiedTables"][$key] = array("newFields"=>$newFields,"deletedFields"=>$deletedFields);
	}
	return $result;
}