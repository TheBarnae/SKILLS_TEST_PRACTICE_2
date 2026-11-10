
<?php
require "db_connector.php";

$message = "";
$editData = null;

// ADD CONSULTATION
if (isset($_POST['add'])) {

    $doctor_id = (int) $_POST['doctor_id'];
    $patient_id = (int) $_POST['patient_id'];
    $date = $_POST['consultation_date'];
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);

    $sql = "INSERT INTO consultations
            (doctor_id, patient_id, consultation_date, diagnosis, treatment)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "iisss",
        $doctor_id,
        $patient_id,
        $date,
        $diagnosis,
        $treatment
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: consultations.php");
        exit();
    } else {
        $message = "Failed to add consultation.";
    }
}

// UPDATE CONSULTATION
if (isset($_POST['update'])) {

    $id = (int) $_POST['consultation_id'];
    $doctor_id = (int) $_POST['doctor_id'];
    $patient_id = (int) $_POST['patient_id'];
    $date = $_POST['consultation_date'];
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);

    $sql = "UPDATE consultations
            SET doctor_id=?,
                patient_id=?,
                consultation_date=?,
                diagnosis=?,
                treatment=?
            WHERE consultation_id=?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "iisssi",
        $doctor_id,
        $patient_id,
        $date,
        $diagnosis,
        $treatment,
        $id
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: consultations.php");
        exit();
    } else {
        $message = "Failed to update consultation.";
    }
}

// DELETE CONSULTATION
if (isset($_POST['delete'])) {

    $id = (int) $_POST['consultation_id'];

    $sql = "DELETE FROM consultations WHERE consultation_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: consultations.php");
        exit();
    } else {
        $message = "Failed to delete consultation.";
    }
}

// GET CONSULTATION FOR EDITING
if (isset($_GET['edit']) && ctype_digit($_GET['edit'])) {

    $id = (int) $_GET['edit'];

    $sql = "SELECT * FROM consultations WHERE consultation_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $resultEdit = mysqli_stmt_get_result($stmt);
    $editData = mysqli_fetch_assoc($resultEdit);
}

// GET ALL DOCTORS FOR DROPDOWN
$doctors = mysqli_query($conn, "SELECT * FROM doctors");

// GET ALL PATIENTS FOR DROPDOWN
$patients = mysqli_query($conn, "SELECT * FROM patients");

// DISPLAY CONSULTATIONS WITH DOCTOR AND PATIENT NAMES
$sql = "SELECT
            consultations.*,
            doctors.doctor_name,
            patients.patient_name
        FROM consultations
        INNER JOIN doctors
            ON consultations.doctor_id = doctors.doctor_id
        INNER JOIN patients
            ON consultations.patient_id = patients.patient_id
        ORDER BY consultations.consultation_id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consultations</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Consultation Management</h1>

    <a href="index.php">Back to Dashboard</a>

    <h2><?php echo $editData ? "Edit Consultation" : "Add Consultation"; ?></h2>

    <p class="message"><?php echo htmlspecialchars($message); ?></p>

    <form method="POST">

        <?php if ($editData) { ?>
            <input type="hidden" name="consultation_id"
                   value="<?php echo $editData['consultation_id']; ?>">
        <?php } ?>

        <label>Doctor:</label>

        <select name="doctor_id" required>
            <option value="">Select Doctor</option>

            <?php while ($doc = mysqli_fetch_assoc($doctors)) { ?>

                <option value="<?php echo $doc['doctor_id']; ?>"
                    <?php
                    if (($editData['doctor_id'] ?? '') == $doc['doctor_id']) {
                        echo "selected";
                    }
                    ?>>

                    <?php echo htmlspecialchars($doc['doctor_name']); ?>

                </option>

            <?php } ?>

        </select>

        <label>Patient:</label>

        <select name="patient_id" required>
            <option value="">Select Patient</option>

            <?php while ($pat = mysqli_fetch_assoc($patients)) { ?>

                <option value="<?php echo $pat['patient_id']; ?>"
                    <?php
                    if (($editData['patient_id'] ?? '') == $pat['patient_id']) {
                        echo "selected";
                    }
                    ?>>

                    <?php echo htmlspecialchars($pat['patient_name']); ?>

                </option>

            <?php } ?>

        </select>

        <label>Consultation Date:</label>

        <input type="date" name="consultation_date"
               value="<?php echo htmlspecialchars($editData['consultation_date'] ?? date('Y-m-d')); ?>"
               required>

        <label>Diagnosis:</label>

        <textarea name="diagnosis" placeholder="Enter diagnosis" required><?php echo htmlspecialchars($editData['diagnosis'] ?? ''); ?></textarea>

        <label>Treatment:</label>

        <textarea name="treatment" placeholder="Enter treatment" required><?php echo htmlspecialchars($editData['treatment'] ?? ''); ?></textarea>

        <?php if ($editData) { ?>

            <button type="submit" name="update">Update Consultation</button>
            <a href="consultations.php">Cancel</a>

        <?php } else { ?>

            <button type="submit" name="add">Add Consultation</button>

        <?php } ?>

    </form>

    <h2>Consultation Records</h2>

    <table>

        <tr>
            <th>ID</th>
            <th>Doctor</th>
            <th>Patient</th>
            <th>Date</th>
            <th>Diagnosis</th>
            <th>Treatment</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

        <tr>

            <td><?php echo $row['consultation_id']; ?></td>

            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>

            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>

            <td><?php echo $row['consultation_date']; ?></td>

            <td><?php echo htmlspecialchars($row['diagnosis']); ?></td>

            <td><?php echo htmlspecialchars($row['treatment']); ?></td>

            <td>

                <a href="consultations.php?edit=<?php echo $row['consultation_id']; ?>">
                    Edit
                </a>

                <form method="POST" class="inline-form"
                      onsubmit="return confirm('Delete this consultation?');">

                    <input type="hidden" name="consultation_id"
                           value="<?php echo $row['consultation_id']; ?>">

                    <button type="submit" name="delete">Delete</button>

                </form>

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
