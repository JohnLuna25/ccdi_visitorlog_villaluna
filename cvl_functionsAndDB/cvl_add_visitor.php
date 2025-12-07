<?php
// cvl_add_visitor.php
include 'cvl_db_connect.php';

/**
 * Adds a new visitor to the database.
 *
 * @param mysqli $conn
 * @param string $full_name
 * @param string $address
 * @param string $contact
 * @param string $school
 * @param string $purpose_of_visit
 * @return array ['success'=>string, 'errors'=>array]
 */
function add_visitor($conn, $full_name, $address, $contact, $school, $purpose_of_visit) {
    $errors = [];
    $success = '';

    if (!$full_name || !$purpose_of_visit) {
        $errors[] = 'Please fill in the required fields: Full Name and Purpose of Visit.';
    } else {
        $stmt = $conn->prepare("INSERT INTO cvl_visitor_info (full_name, address, contact, school, purpose_of_visit) VALUES (?, ?, ?, ?, ?)");
        if(!$stmt){
            $errors[] = "Prepare failed: ".$conn->error;
        } else {
            $stmt->bind_param("sssss", $full_name, $address, $contact, $school, $purpose_of_visit);
            if($stmt->execute()){
                $success = 'Visitor added successfully!';
            } else {
                $errors[] = 'Execute failed: '.$stmt->error;
            }
            $stmt->close();
        }
    }

    return ['success'=>$success, 'errors'=>$errors];
}
?>
