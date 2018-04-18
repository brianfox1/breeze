<?php
ini_set('display_errors', "1");
require_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";

function runQuery($sql_query)
{
    $result = mysqli_query(get_dbc(), $sql_query);
    return $result;
}

function tableCount($table_name)
{
    $result = runQuery("select count(*) count from $table_name");
    $r = mysqli_fetch_array($result);
    return $count = (int) $r['count'];
}

function findCountById($table_name, $idField, $id)
{
    $result = runQuery("select count(*) count from $table_name where $idField = $id");
    $r = mysqli_fetch_array($result);
    return $count = (int) $r['count'];
}

function insert($table_name, $fields, $values)
{
    $result = runQuery("INSERT INTO  $table_name ($fields) VALUES ($values)");
}

function update($table_name, $set, $idField, $id)
{
    $result = runQuery("UPDATE $table_name SET $set WHERE  $idField = $id");
}
