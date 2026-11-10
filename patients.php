
<?php
include "db_connector.php";

$message = "";
$editData = null;

// ADD PATIENT
if (isset($_POST['add'])) {

    $name = trim($_POST['patient_name']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);

    $sql = "INSERT INTO patients (patient_name, age, gender, contact)
            VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "siss", $name, $age, $gender, $contact);

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
    $name = trim($_POST['patient_name']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);

    $sql = "UPDATE patients
            SET patient_name=?, age=?, gender=?, contact=?
            WHERE patient_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sissi", $name, $age, $gender, $contact, $id);

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

    $sql = "DELETE FROM patients WHERE patient_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: patients.php");
        exit();
    } else {
        $message = "Cannot delete patient. Existing consultations depend on this patient.";
    }
}

// GET PATIENT FOR EDITING
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {

    $id = (int) $_GET['edit'];

    $sql = "SELECT * FROM patients WHERE patient_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $resultEdit = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($resultEdit);
}

// DISPLAY PATIENTS
$result = mysqli_query($conn, "SELECT * FROM patients ORDER BY patient_id DESC");
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

        <input type="text" name="patient_name"
               placeholder="Patient Name"
               value="<?php echo htmlspecialchars($editData['patient_name'] ?? ''); ?>"
               required>

        <input type="number" name="age"
               placeholder="Age" min="1"
               value="<?php echo htmlspecialchars($editData['age'] ?? ''); ?>"
               required>

        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male"
                <?php if (($editData['gender'] ?? '') == 'Male') echo 'selected'; ?>>
                Male
            </option>
            <option value="Female"
                <?php if (($editData['gender'] ?? '') == 'Female') echo 'selected'; ?>>
                Female
            </option>
        </select>

        <input type="text" name="contact"
               placeholder="Contact Number"
               value="<?php echo htmlspecialchars($editData['contact'] ?? ''); ?>"
               required>

        <?php if ($editData) { ?>
            <button type="submit" name="update">Update Patient</button>
            <a href="patients.php">Cancel</a>
        <?php } else { ?>
            <button type="submit" name="add">Add Patient</button>
        <?php } ?>

    </form>

    <h2>Patient List</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Contact</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <tr>
            <td><?php echo $row['patient_id']; ?></td>
            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
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
