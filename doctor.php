
<?php
include "db.php";

// ADD DOCTOR
if (isset($_POST['add'])) {

    $name = $_POST['doctor_name'];
    $specialization = $_POST['specialization'];
    $contact = $_POST['contact'];

    $sql = "INSERT INTO doctors
            (doctor_name, specialization, contact)
            VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $name,
        $specialization,
        $contact
    );

    mysqli_stmt_execute($stmt);

    header("Location: doctors.php");
    exit();
}
?>

<?php
$sql = "SELECT * FROM doctors";
$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Doctors</title>
</head>
<body>

    <h2>Add Doctor</h2>

    <form method="POST">

        <input type="text" name="doctor_name"
               placeholder="Doctor Name" required>

        <br><br>

        <input type="text" name="specialization"
               placeholder="Specialization" required>

        <br><br>

        <input type="text" name="contact"
               placeholder="Contact Number" required>

        <br><br>

        <button type="submit" name="add">
            Add Doctor
        </button>

    </form>


<h2>Doctor List</h2>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Specialization</th>
        <th>Contact</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>

    <tr>
        <td><?php echo $row['doctor_id']; ?></td>
        <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
        <td><?php echo htmlspecialchars($row['specialization']); ?></td>
        <td><?php echo htmlspecialchars($row['contact']); ?></td>
    </tr>

    <?php } ?>

</table>


</body>
</html>
