<?php
ini_set('display_errors', "1");
require_once $_SERVER['DOCUMENT_ROOT'] . "/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/model.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/csv_process.php";

if (isset($_FILES['csv'])) {

    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/";
    $target_file = $target_dir . basename($_FILES['csv']["name"]);
    $uploadOk = 1;
    $csvFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $target_file = $target_dir . time() . '.' . $csvFileType;

    // Check if file already exists
    if (file_exists($target_file)) {
        $msg = "Sorry, file already exists.";
        echo json_encode(array("type" => 'error', "msg" => $msg));

        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES['csv']["size"] > 500000) {
        $msg = "Sorry, your file is too large.";
        echo json_encode(array("type" => 'error', "msg" => $msg));

        $uploadOk = 0;
    }
    // Allow certain file formats
    if ($csvFileType != "csv") {
        $msg = "Sorry, only CSV files are allowed.";
        echo json_encode(array("type" => 'error', "msg" => $msg));

        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $msg = "Sorry, your file was not uploaded.";
        echo json_encode(array("type" => 'error', "msg" => $msg));

        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES['csv']["tmp_name"], $target_file)) {
            $csv_data = csv_to_array($target_file);
            if ($csv_data) {
                if ($csv_data['csv_type'] == 'group') {
                    # group
                    $table_name = 'groups';
                    $csv = $csv_data['csv_data'];

                    $before_import_table_count = tableCount($table_name);

                    foreach ($csv as $key => $csvData) {
                        $group_id = trim($csvData['group_id']);
                        $group_name = trim($csvData['group_name']);

                        if ($group_id && $group_name) {
                            if (findCountById($table_name, 'group_id', $group_id) > 0) {
                                # update
                                update($table_name, "group_name = '$group_name'", 'group_id', $group_id);
                            } else {
                                # Insert
                                insert($table_name, "group_id ,group_name", "'$group_id','$group_name'");
                            }
                        }
                    }

                    $after_import_table_count = tableCount($table_name);

                    $newly_added_data_count = $after_import_table_count - $before_import_table_count;
                    if ($newly_added_data_count == 0) {
                        $msg = "Group CSV imported successfully !!";
                    } else {
                        $msg = $newly_added_data_count . " new group(s) imported successfully !!";
                    }
                    echo json_encode(array("type" => 'success', "msg" => $msg));

                } else {
                    # person
                    $table_name = 'persons';
                    $csv = $csv_data['csv_data'];

                    $before_import_table_count = tableCount($table_name);

                    foreach ($csv as $key => $csvData) {
                        $person_id = trim($csvData['person_id']);
                        $first_name = trim($csvData['first_name']);
                        $last_name = trim($csvData['last_name']);
                        $email_address = trim($csvData['email_address']);
                        $group_id = trim($csvData['group_id']);
                        $state = trim($csvData['state']);

                        if ($person_id && $first_name && $group_id && $state) {

                            if (findCountById('groups', 'group_id', $group_id) > 0) {
                                if ($state == 'active' || $state == 'archived') {
                                    if (findCountById($table_name, 'person_id', $person_id) > 0) {
                                        # update
                                        update($table_name, "first_name = '$first_name', last_name = '$last_name', email_address = '$email_address', group_id = '$group_id', state = '$state'", 'person_id', $person_id);
                                    } else {
                                        # Insert
                                        insert($table_name, "person_id ,first_name, last_name, email_address, group_id, state", "'$person_id','$first_name','$last_name','$email_address','$group_id','$state'");
                                    }
                                }
                            }
                        }
                    }

                    $after_import_table_count = tableCount($table_name);

                    $newly_added_data_count = $after_import_table_count - $before_import_table_count;
                    if ($newly_added_data_count == 0) {
                        $msg = "Person CSV imported successfully !!";
                    } else {
                        $msg = $newly_added_data_count . " new people imported successfully !!";
                    }
                    echo json_encode(array("type" => 'success', "msg" => $msg));

                }
            } else {
                $msg = "Please upload a valid CSV file";
                echo json_encode(array("type" => 'error', "msg" => $msg));

            }
        } else {
            $msg = "Sorry, there was an error uploading your file.";
            echo json_encode(array("type" => 'error', "msg" => $msg));

        }
    }
}
