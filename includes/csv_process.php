<?php
function has_empty(array $array)
{
    return !in_array("", $array); //returns false
}

function array_keys_exist(array $keys_to_check, array $array_to_check, $strict = true)
{
    foreach ($keys_to_check as $value) {
        if (!in_array(trim($value), $array_to_check)) {
            return false;
        }
    }
    return true;
}

function check_array_type(array $array)
{
    $person_csv_required = ['person_id', 'first_name', 'last_name', 'email_address', 'group_id', 'state'];
    $group_csv_required = ['group_name'];

    if (array_keys_exist($person_csv_required, $array)) {
        // All required keys exist for person csv!
        return "person";
    } elseif (array_keys_exist($group_csv_required, $array)) {
        // All required keys exist for group csv!
        return "group";
    } else {
        // Wrong CSV
        return false;
    }
}

function csv_to_array($filename = '', $delimiter = ',')
{
    if (!file_exists($filename) || !is_readable($filename)) {
        return false;
    }

    $header = null;
    $data = array();
    $response = array();

    if (($handle = fopen($filename, 'r')) !== false) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {

            if (!$header) {
                if (has_empty($row)) {
                    foreach ($row as $key => $value) {
                        $header[] = trim($value);
                    }
                }
            } else {
                $data[] = array_combine($header, $row);
            }

        }
        fclose($handle);
    }
    if ($csv_type = check_array_type($header)) {
        $response['csv_type'] = $csv_type;
        $response['csv_data'] = $data;
        return $response;
    }
    return false;
}
