
<?php
require "db_connector.php";

$message = "";
$editData = null;

// ADD DOCTOR
if (isset($_POST['add'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $specialization = trim($_POST['specialization']);
    $contact = trim($_POST['contact']);

    $stmt = mysqli_prepare($conn,
        "INSERT INTO doctors (fname, lname, specialization, contact)
         VALUES (?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "ssss", $fname, $lname, $specialization, $contact);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: doctors.php");
        exit();
    } else {
        $message = "Failed to add doctor.";
    }
}

// UPDATE DOCTOR
if (isset($_POST['update'])) {
    $id = (int) $_POST['doctor_id'];
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $specialization = trim($_POST['specialization']);
    $contact = trim($_POST['contact']);

    $stmt = mysqli_prepare($conn,
        "UPDATE doctors
         SET fname=?, lname=?, specialization=?, contact=?
         WHERE doctor_id=?");

    mysqli_stmt_bind_param($stmt, "ssssi", $fname, $lname, $specialization, $contact, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: doctors.php");
        exit();
    } else {
        $message = "Failed to update doctor.";
    }
}

// DELETE DOCTOR
if (isset($_POST['delete'])) {
    $id = (int) $_POST['doctor_id'];

    $stmt = mysqli_prepare($conn,
        "DELETE FROM doctors WHERE doctor_id=?");

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: doctors.php");
        exit();
    } else {
        $message = "Cannot delete this doctor. They may have consultation records.";
    }
}

// GET DOCTOR FOR EDITING
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {
    $id = (int) $_GET['edit'];

    $stmt = mysqli_prepare($conn,
        "SELECT * FROM doctors WHERE doctor_id=?");

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $editData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// SEARCH
$search = trim($_GET['search'] ?? '');

// SORTING
$allowedSort = [
    "id" => "doctor_id",
    "name" => "fname",
    "specialization" => "specialization"
];

$sort = $_GET['sort'] ?? "id";
$sortColumn = $allowedSort[$sort] ?? "doctor_id";

$direction = strtoupper($_GET['direction'] ?? "DESC");
if (!in_array($direction, ["ASC", "DESC"])) {
    $direction = "DESC";
}

// GET DOCTORS
$sql = "SELECT * FROM doctors
        WHERE CAST(doctor_id AS CHAR) LIKE ?
        OR fname LIKE ?
        OR lname LIKE ?
        OR specialization LIKE ?
        ORDER BY $sortColumn $direction";

$stmt = mysqli_prepare($conn, $sql);

$keyword = "%$search%";
mysqli_stmt_bind_param($stmt, "ssss", $keyword, $keyword, $keyword, $keyword);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctors</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Doctor Management</h1>
    <a href="index.php">Back to Dashboard</a>

    <h2><?php echo $editData ? "Edit Doctor" : "Add Doctor"; ?></h2>

    <p class="message"><?php echo htmlspecialchars($message); ?></p>

    <form method="POST">

        <?php if ($editData) { ?>
            <input type="hidden" name="doctor_id"
                   value="<?php echo $editData['doctor_id']; ?>">
        <?php } ?>

        <label>First Name:</label>
        <input type="text" name="fname"
               value="<?php echo htmlspecialchars($editData['fname'] ?? ''); ?>"
               required>

        <label>Last Name:</label>
        <input type="text" name="lname"
               value="<?php echo htmlspecialchars($editData['lname'] ?? ''); ?>"
               required>

        <label>Specialization:</label>
        <input type="text" name="specialization"
               value="<?php echo htmlspecialchars($editData['specialization'] ?? ''); ?>"
               required>

        <label>Contact:</label>
        <input type="text" name="contact"
               value="<?php echo htmlspecialchars($editData['contact'] ?? ''); ?>"
               required>

        <?php if ($editData) { ?>
            <button type="submit" name="update">Update Doctor</button>
            <a href="doctors.php">Cancel</a>
        <?php } else { ?>
            <button type="submit" name="add">Add Doctor</button>
        <?php } ?>

    </form>

    <h2>Doctor Records</h2>

    <form method="GET" class="search-sort-form">

        <input type="text" name="search"
               placeholder="Search ID, name, or specialization"
               value="<?php echo htmlspecialchars($search); ?>">

        <select name="sort">
            <option value="id" <?php if ($sort == "id") echo "selected"; ?>>Sort by ID</option>
            <option value="name" <?php if ($sort == "name") echo "selected"; ?>>Sort by Name</option>
            <option value="specialization" <?php if ($sort == "specialization") echo "selected"; ?>>Sort by Specialization</option>
        </select>

        <select name="direction">
            <option value="ASC" <?php if ($direction == "ASC") echo "selected"; ?>>Ascending</option>
            <option value="DESC" <?php if ($direction == "DESC") echo "selected"; ?>>Descending</option>
        </select>

        <button type="submit">Search / Sort</button>
        <a href="doctors.php">Reset</a>

    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Doctor Name</th>
            <th>Specialization</th>
            <th>Contact</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['doctor_id']; ?></td>
            <td><?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></td>
            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
            <td><?php echo htmlspecialchars($row['contact']); ?></td>
            <td>
                <a href="doctors.php?edit=<?php echo $row['doctor_id']; ?>">Edit</a>

                <form method="POST" class="inline-form"
                      onsubmit="return confirm('Delete this doctor?');">
                    <input type="hidden" name="doctor_id"
                           value="<?php echo $row['doctor_id']; ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
            </td>
        </tr>
        <?php } ?>

    </table>

</div>
</body>
</html>
