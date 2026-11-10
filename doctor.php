
<?php
include "db_connector.php";

$message = "";
$editData = null;

// ADD DOCTOR
if (isset($_POST['add'])) {

    $name = trim($_POST['doctor_name']);
    $specialization = trim($_POST['specialization']);
    $contact = trim($_POST['contact']);

    $sql = "INSERT INTO doctors (doctor_name, specialization, contact)
            VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $specialization, $contact);

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
    $name = trim($_POST['doctor_name']);
    $specialization = trim($_POST['specialization']);
    $contact = trim($_POST['contact']);

    $sql = "UPDATE doctors
            SET doctor_name=?, specialization=?, contact=?
            WHERE doctor_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $specialization, $contact, $id);

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

    $sql = "DELETE FROM doctors WHERE doctor_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: doctors.php");
        exit();
    } else {
        $message = "Cannot delete doctor. Existing consultations depend on this doctor.";
    }
}

// GET DOCTOR FOR EDITING
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {

    $id = (int) $_GET['edit'];

    $sql = "SELECT * FROM doctors WHERE doctor_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $resultEdit = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($resultEdit);
}

// DISPLAY DOCTORS
$result = mysqli_query($conn, "SELECT * FROM doctors ORDER BY doctor_id DESC");
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

        <input type="text" name="doctor_name"
               placeholder="Doctor Name"
               value="<?php echo htmlspecialchars($editData['doctor_name'] ?? ''); ?>"
               required>

        <input type="text" name="specialization"
               placeholder="Specialization"
               value="<?php echo htmlspecialchars($editData['specialization'] ?? ''); ?>"
               required>

        <input type="text" name="contact"
               placeholder="Contact Number"
               value="<?php echo htmlspecialchars($editData['contact'] ?? ''); ?>"
               required>

        <?php if ($editData) { ?>
            <button type="submit" name="update">Update Doctor</button>
            <a href="doctors.php">Cancel</a>
        <?php } else { ?>
            <button type="submit" name="add">Add Doctor</button>
        <?php } ?>

    </form>

    <h2>Doctor List</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Specialization</th>
            <th>Contact</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <tr>
            <td><?php echo $row['doctor_id']; ?></td>
            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
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
