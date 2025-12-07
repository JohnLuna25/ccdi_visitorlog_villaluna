<?php
// cvl_add_visitor.php

/**
 * Adds a new visitor to the database.
 *
 * @param mysqli $conn Database connection
 * @param string $full_name Visitor full name
 * @param string $address Visitor address
 * @param string $contact Visitor contact number
 * @param string $school Visitor school/office
 * @param string $purpose_of_visit Purpose of visit
 * @return array ['success' => string, 'errors' => array]
 */
function add_visitor($conn, $full_name, $address, $contact, $school, $purpose_of_visit) {
    $errors = [];
    $success = '';

    // Validate required fields
    if (!$full_name || !$purpose_of_visit) {
        $errors[] = 'Please fill in the required fields: Full Name and Purpose of Visit.';
    } else {
        $stmt = $conn->prepare("INSERT INTO cvl_visitor_info (full_name, address, contact, school, purpose_of_visit) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            $errors[] = "Database prepare error: " . $conn->error;
        } else {
            $stmt->bind_param("sssss", $full_name, $address, $contact, $school, $purpose_of_visit);
            if ($stmt->execute()) {
                $success = 'Visitor added successfully!';
            } else {
                $errors[] = 'Error adding visitor: ' . $stmt->error;
            }
            $stmt->close();
        }
    }

    return ['success' => $success, 'errors' => $errors];
}
