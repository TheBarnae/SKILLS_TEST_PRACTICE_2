
<?php
include "db_connector.php";

$doctors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM doctors"));
$patients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM patients"));
$consultations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM consultations"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consultation System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Consultation Management System</h1>
    <p>Welcome to the system dashboard.</p>

    <div class="cards">

        <div class="card">
            <h3>Doctors</h3>
            <h2><?php echo $doctors['total']; ?></h2>
            <a href="doctors.php">Manage Doctors</a>
        </div>

        <div class="card">
            <h3>Patients</h3>
            <h2><?php echo $patients['total']; ?></h2>
            <a href="patients.php">Manage Patients</a>
        </div>

        <div class="card">
            <h3>Consultations</h3>
            <h2><?php echo $consultations['total']; ?></h2>
            <a href="consultations.php">Manage Consultations</a>
        </div>

    </div>

</div>

</body>
</html>
