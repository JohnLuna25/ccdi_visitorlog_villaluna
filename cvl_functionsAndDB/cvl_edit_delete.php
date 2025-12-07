<?php
include 'cvl_db_connect.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if($action === 'get' && isset($_POST['id'])){
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("SELECT * FROM cvl_visitor_info WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    echo json_encode($result->fetch_assoc());
    exit;
}

if($action === 'update' && isset($_POST['id'])){
    $id = intval($_POST['id']);
    $full_name = $_POST['full_name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $school = $_POST['school'];
    $purpose_of_visit = $_POST['purpose_of_visit'];

    $stmt = $conn->prepare("UPDATE cvl_visitor_info SET full_name=?, contact=?, address=?, school=?, purpose_of_visit=? WHERE id=?");
    $stmt->bind_param("sssssi", $full_name, $contact, $address, $school, $purpose_of_visit, $id);
    $stmt->execute();
    header('Location: ../cvl_web_pages/cvl_visit_logs.php');
    exit;
}

if($action === 'delete' && isset($_GET['id'])){
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM cvl_visitor_info WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header('Location: ../cvl_web_pages/cvl_visit_logs.php');
    exit;
}
