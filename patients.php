
<?php
require "db_connector.php";

$message = "";
$editData = null;

// ADD PATIENT
if (isset($_POST['add'])) {
    $fname = trim($_POST['patient_fname']);
    $lname = trim($_POST['patient_lname']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO patients (patient_fname, patient_lname, age, gender, contact)
         VALUES (?, ?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "ssiss", $fname, $lname, $age, $gender, $contact);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: patients.php");
        exit();
    } else {
        $message = "Failed to add patient.";
    }
}

// UPDATE PATIENT
if (isset($_POST['update'])) {
    $id = (int) $_POST['patient_id'];
    $fname = trim($_POST['patient_fname']);
    $lname = trim($_POST['patient_lname']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);

    $stmt = mysqli_prepare($conn,
        "UPDATE patients
         SET patient_fname=?, patient_lname=?, age=?, gender=?, contact=?
         WHERE patient_id=?");

    mysqli_stmt_bind_param($stmt, "ssissi", $fname, $lname, $age, $gender, $contact, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: patients.php");
        exit();
    } else {
        $message = "Failed to update patient.";
    }
}

// DELETE PATIENT
if (isset($_POST['delete'])) {
    $id = (int) $_POST['patient_id'];

    $stmt = mysqli_prepare($conn,
        "DELETE FROM patients WHERE patient_id=?");

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: patients.php");
        exit();
    } else {
        $message = "Cannot delete this patient. They may have consultation records.";
    }
}

// GET PATIENT FOR EDITING
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $id = (int) $_GET['edit'];

    $stmt = mysqli_prepare($conn,
        "SELECT * FROM patients WHERE patient_id=?");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $editData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// SEARCH
$search = trim($_GET['search'] ?? '');

// SORTING
$allowedSort = [
    "id" => "patient_id",
    "name" => "patient_fname",
    "age" => "age"
];

$sort = $_GET['sort'] ?? "id";
$sortColumn = $allowedSort[$sort] ?? "patient_id";

$direction = strtoupper($_GET['direction'] ?? "DESC");
if (!in_array($direction, ["ASC", "DESC"])) {
    $direction = "DESC";
}

// GET PATIENTS
$sql = "SELECT * FROM patients
        WHERE CAST(patient_id AS CHAR) LIKE ?
        OR patient_fname LIKE ?
        OR patient_lname LIKE ?
        ORDER BY $sortColumn $direction";

$stmt = mysqli_prepare($conn, $sql);

$keyword = "%$search%";
mysqli_stmt_bind_param($stmt, "sss", $keyword, $keyword, $keyword);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patients</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Patient Management</h1>
    <a href="index.php">Back to Dashboard</a>

    <h2><?php echo $editData ? "Edit Patient" : "Add Patient"; ?></h2>

    <p class="message"><?php echo htmlspecialchars($message); ?></p>

    <form method="POST">

        <?php if ($editData) { ?>
            <input type="hidden" name="patient_id"
                   value="<?php echo $editData['patient_id']; ?>">
        <?php } ?>

        <label>First Name:</label>
        <input type="text" name="patient_fname"
               value="<?php echo htmlspecialchars($editData['patient_fname'] ?? ''); ?>"
               required>

        <label>Last Name:</label>
        <input type="text" name="patient_lname"
               value="<?php echo htmlspecialchars($editData['patient_lname'] ?? ''); ?>"
               required>

        <label>Age:</label>
        <input type="number" name="age" min="0"
               value="<?php echo htmlspecialchars($editData['age'] ?? ''); ?>"
               required>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?php if (($editData['gender'] ?? '') == "Male") echo "selected"; ?>>Male</option>
            <option value="Female" <?php if (($editData['gender'] ?? '') == "Female") echo "selected"; ?>>Female</option>
            <option value="Other" <?php if (($editData['gender'] ?? '') == "Other") echo "selected"; ?>>Other</option>
        </select>

        <label>Contact:</label>
        <input type="text" name="contact"
               value="<?php echo htmlspecialchars($editData['contact'] ?? ''); ?>"
               required>

        <?php if ($editData) { ?>
            <button type="submit" name="update">Update Patient</button>
            <a href="patients.php">Cancel</a>
        <?php } else { ?>
            <button type="submit" name="add">Add Patient</button>
        <?php } ?>

    </form>

    <h2>Patient Records</h2>

    <form method="GET" class="search-sort-form">

        <input type="text" name="search"
               placeholder="Search patient ID or name"
               value="<?php echo htmlspecialchars($search); ?>">

        <select name="sort">
            <option value="id" <?php if ($sort == "id") echo "selected"; ?>>Sort by ID</option>
            <option value="name" <?php if ($sort == "name") echo "selected"; ?>>Sort by Name</option>
            <option value="age" <?php if ($sort == "age") echo "selected"; ?>>Sort by Age</option>
        </select>

        <select name="direction">
            <option value="ASC" <?php if ($direction == "ASC") echo "selected"; ?>>Ascending</option>
            <option value="DESC" <?php if ($direction == "DESC") echo "selected"; ?>>Descending</option>
        </select>

        <button type="submit">Search / Sort</button>
        <a href="patients.php">Reset</a>

    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Patient Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Contact</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['patient_id']; ?></td>
            <td><?php echo htmlspecialchars($row['patient_fname'] . ' ' . $row['patient_lname']); ?></td>
            <td><?php echo $row['age']; ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td><?php echo htmlspecialchars($row['contact']); ?></td>
            <td>
                <a href="patients.php?edit=<?php echo $row['patient_id']; ?>">Edit</a>

                <form method="POST" class="inline-form"
                      onsubmit="return confirm('Delete this patient?');">
                    <input type="hidden" name="patient_id"
                           value="<?php echo $row['patient_id']; ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
            </td>
        </tr>
        <?php } ?>

    </table>

</div>
</body>
</html>
