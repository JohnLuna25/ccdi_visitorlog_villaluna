<?php
session_start();
include '../cvl_functionsAndDB/cvl_db_connect.php';

// Redirect to login if not logged in
if (empty($_SESSION['username'])) {
    header('Location: ../cvl_login_page/cvl_login.php');
    exit;
}

// Add new visitor
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_visitor'])) {
    $full_name = trim($_POST['full_name']);
    $address = trim($_POST['address']);
    $contact = trim($_POST['contact']);
    $school = trim($_POST['school']);
    $purpose_of_visit = trim($_POST['purpose_of_visit']);

    if (!$full_name || !$purpose_of_visit) {
        $errors[] = 'Please fill in the required fields: Full Name and Purpose of Visit.';
    } else {
        $stmt = $conn->prepare("INSERT INTO cvl_visitor_info (full_name,address,contact,school,purpose_of_visit) VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $full_name, $address, $contact, $school, $purpose_of_visit);
        if ($stmt->execute()) {
            $success = 'Visitor added successfully!';
        } else {
            $errors[] = 'Error adding visitor.';
        }
        $stmt->close();
    }
}

// Export CSV
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
    <a href="../cvl_login_page/cvl_logout.php" class="logout-button">Logout</a>
</div>

<div class="sidebar">
    <h2>Dashboard</h2>
    <ul>
        <li><a href="cvl_visit_logs.php">Visit Logs</a></li>
        <li><a href="cvl_statistics.php">Statistics</a></li>
        <li><a href="cvl_settings.php">Settings</a></li>
    </ul>
</div>

<div class="main-content">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

    <!-- Statistics -->
    <div class="stats">
        <p>Total Visitors Today: <?php echo $totalVisitors; ?></p>
        <p>Students who took Exam: <?php echo $totalExam; ?></p>
        <p>Others: <?php echo $totalOthers; ?></p>
    </div>

    <!-- Add Visitor Button -->
    <button id="addVisitorBtn" class="popup-btn">Add Visitor</button>

    <!-- Popup Form -->
    <div id="visitorModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Add New Visitor</h3>
        <?php if($errors): ?>
            <div style="color:red;">
                <?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
            <div style="color:green;"><?php echo $success; ?></div>
        <?php endif; ?>
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
            
            <!-- Actions column -->
            <td>
                <form method="post" action="edit_visitor.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="action-btn edit">Edit</button>
                </form>
                <form method="post" action="delete_visitor.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this visitor?');">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="action-btn delete">Delete</button>
                </form>
            </td>

        </tr>
        <?php endwhile; ?>

    </table>

    <!-- Export CSV -->
    <br>
    <a href="?export_csv=1">Export Today's Visitors to CSV</a>
</div>

<script>
// Modal JS
const modal = document.getElementById("visitorModal");
const btn = document.getElementById("addVisitorBtn");
const span = document.getElementsByClassName("close")[0];

// Open modal
btn.onclick = function() {
  modal.style.display = "block";
}

// Close modal when X clicked
span.onclick = function() {
  modal.style.display = "none";
}

// Close modal when clicking outside content
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
</script>

</body>
</html>
