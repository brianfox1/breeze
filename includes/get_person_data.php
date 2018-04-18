<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/model.php";

$sQuery = "SELECT persons.person_id, persons.first_name, persons.last_name, persons.email_address, groups.group_name, persons.state FROM persons INNER JOIN groups ON persons.group_id=groups.group_id where persons.state = 'active'";

//echo $sQuery;
$rResultTotal = mysqli_query(get_dbc(), $sQuery);

$aColumns = array(
    'person_id'
    , 'first_name'
    , 'last_name'
    , 'email_address'
    , 'group_name'
    , 'state',
);
/* * Output     */

while ($aRow = mysqli_fetch_array($rResultTotal)) {

    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        if ($aRow[$aColumns[$i]] === null) {$aRow[$aColumns[$i]] = "";}
        $row[] = $aRow[$aColumns[$i]];

    }
    $output['aaData'][] = $row;
}
if (isset($output['aaData'])) {
    $output = array(
        "data" => $output['aaData'],
    );
} else {
    $output = array(
        //"draw" => intval(1),
        "data" => [],
    );
}

echo json_encode($output);
