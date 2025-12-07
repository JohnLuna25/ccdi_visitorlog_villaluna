<?php
session_start();
require_once '../cvl_functionsAndDB/cvl_add_visitor.php'; 
$errors = [];
$success = '';

// Redirect to login if not logged in
if (empty($_SESSION['username'])) {
    header('Location: ../cvl_login_page/cvl_login.php');
    exit;
}

// Handle Add Visitor form submission with POST-Redirect-GET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_visitor'])) {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $school = trim($_POST['school']);
    $purpose_of_visit = trim($_POST['purpose_of_visit']);

    $result = add_visitor($conn, $full_name, $address, $contact, $school, $purpose_of_visit);

    // Store success/errors in session
    if (!empty($result['success'])) {
        $_SESSION['success'] = $result['success'];
    }
    if (!empty($result['errors'])) {
        $_SESSION['errors'] = $result['errors'];
    }

    // Redirect to clear POST data
    header('Location: cvl_visit_logs.php');
    exit;
}

// CSV Exporter
if (isset($_GET['export_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="visitors_'.date('Y-m-d').'.csv"');

    $output = fopen('php://output','w');
    fputcsv($output, ['Date','Full Name','Contact','Address','School/Office','Purpose']);

    $result = $conn->query("SELECT * FROM cvl_visitor_info WHERE DATE(created_at) = CURDATE()");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            date('d M Y', strtotime($row['created_at'])),
            $row['full_name'],
            $row['contact'],
            $row['address'],
            $row['school'],
            $row['purpose_of_visit']
        ]);
    }
    fclose($output);
    exit;
}

// Fetch today's visitors
$visitors_result = $conn->query("SELECT * FROM cvl_visitor_info WHERE DATE(created_at) = CURDATE()");

// Statistics
$totalVisitors = $conn->query("SELECT COUNT(*) FROM cvl_visitor_info WHERE DATE(created_at) = CURDATE()")->fetch_row()[0];
$totalExam = $conn->query("SELECT COUNT(*) FROM cvl_visitor_info WHERE DATE(created_at) = CURDATE() AND purpose_of_visit='exam'")->fetch_row()[0];
$totalOthers = $totalVisitors - $totalExam;

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CCDI Visitor Log</title>
<link rel="stylesheet" href="cvl_visit_logs.css">
</head>
<body>

    <div class="header">
        <h1>CCDI Visitor Log</h1>
    </div>
    <a href="../cvl_login_page/cvl_login.php" class="logout-button">Logout</a>

    <div class="main-content">

        <!-- Mini Statistics Bar -->
        <div class="header-stats">
            <div class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</div>
            <div class="stats">
                <p>Total Visitors Today: <?php echo $totalVisitors; ?></p>
                <p>Students who took Exam: <?php echo $totalExam; ?></p>
                <p>Others: <?php echo $totalOthers; ?></p>
            </div>
        </div>

        <!-- Success message (flash) -->
        <?php if(!empty($_SESSION['success'])): ?>
            <div id="successMsg" style="color:green; text-align:center;">
                <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']); // show once
                ?>
            </div>
        <?php endif; ?>

        <!-- Add Visitor Button -->
        <button id="addVisitorBtn" class="popup-btn">Add Visitor</button>

        <!-- Modal for Add Visitor -->
        <div id="visitorModal" class="modal">
          <div class="modal-content">
            <span class="close">&times;</span>
            <h3 style="text-align: center">Add New Visitor</h3>
            <form method="post">
                <input type="hidden" name="add_visitor" value="1">
                <label>Full Name*: </label>
                <input type="text" name="full_name" required><br>
                <label>Contact #: </label>
                <input type="text" name="contact"><br>
                <label>Address: </label>
                <input type="text" name="address"><br>
                <label>School/Office: </label>
                <input type="text" name="school"><br>
                <label>Purpose*: </label>
                <select name="purpose_of_visit" required>
                    <option value="">--Select--</option>
                    <option value="inquiry">Inquiry</option>
                    <option value="exam">Exam</option>
                    <option value="visit">Visit</option>
                </select><br><br>
                <button type="submit">Add Visitor</button>
            </form>
          </div>
        </div>

        <!-- Edit Visitor Modal -->
        <div id="editVisitorModal" class="modal">
          <div class="modal-content">
            <span class="close" id="editClose">&times;</span>
            <h3 style="text-align: center">Edit Visitor</h3>
            <form method="post" id="editVisitorForm" action="../cvl_functionsAndDB/cvl_edit_delete.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <label>Full Name*: </label>
                <input type="text" name="full_name" id="edit_full_name" required><br>
                <label>Contact #: </label>
                <input type="text" name="contact" id="edit_contact"><br>
                <label>Address: </label>
                <input type="text" name="address" id="edit_address"><br>
                <label>School/Office: </label>
                <input type="text" name="school" id="edit_school"><br>
                <label>Purpose*: </label>
                <select name="purpose_of_visit" id="edit_purpose" required>
                    <option value="">--Select--</option>
                    <option value="inquiry">Inquiry</option>
                    <option value="exam">Exam</option>
                    <option value="visit">Visit</option>
                </select><br><br>
                <button type="submit">Save Changes</button>
            </form>
          </div>
        </div>

        <!-- Visitor Table -->
        <h3>Today's Visitors</h3>
        <table>
            <tr class="row-head">
                <th>Date</th>
                <th>Full Name</th>
                <th>Contact #</th>
                <th>Address</th>
                <th>School/Office</th>
                <th>Purpose</th>
                <th>Actions</th>
            </tr>
           <?php while($row = $visitors_result->fetch_assoc()): ?>
            <tr class="table-content">
                <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                <td><?php echo htmlspecialchars($row['address']); ?></td>
                <td><?php echo htmlspecialchars($row['school']); ?></td>
                <td><?php echo htmlspecialchars($row['purpose_of_visit']); ?></td>
                <td>
                    <button class="action-btn edit" onclick="openEditModal(<?php echo $row['id']; ?>)">Edit</button>
                    <button class="action-btn delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <br>
        <a href="?export_csv=1">Export Today's Visitors to CSV</a>
    </div>

<script>
    // Add Visitor Modal
    const modal = document.getElementById("visitorModal");
    const btn = document.getElementById("addVisitorBtn");
    const span = modal.getElementsByClassName("close")[0];
    btn.onclick = () => modal.style.display = "block";
    span.onclick = () => modal.style.display = "none";
    window.onclick = (event) => { if(event.target == modal) modal.style.display = "none"; }

    // Edit Visitor Modal
    const editModal = document.getElementById("editVisitorModal");
    const editClose = document.getElementById("editClose");

    function openEditModal(id){
        fetch('../cvl_functionsAndDB/cvl_edit_delete.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'action=get&id=' + id
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_full_name').value = data.full_name;
            document.getElementById('edit_contact').value = data.contact;
            document.getElementById('edit_address').value = data.address;
            document.getElementById('edit_school').value = data.school;
            document.getElementById('edit_purpose').value = data.purpose_of_visit;
            editModal.style.display = 'block';
        });
    }

    editClose.onclick = () => editModal.style.display = 'none';
    window.onclick = (event) => { if(event.target == editModal) editModal.style.display = 'none'; }

    // Delete confirmation
    function confirmDelete(id){
        if(confirm('Are you sure you want to delete this visitor?')){
            window.location.href = '../cvl_functionsAndDB/cvl_edit_delete.php?action=delete&id=' + id;
        }
    }

    // Hide success message after 5s
    const successDiv = document.getElementById("successMsg");
    if(successDiv){
        setTimeout(()=>{ successDiv.style.display = "none"; }, 5000);
    }
</script>

</body>
</html>
