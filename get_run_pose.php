<?php
$conn = new mysqli("localhost", "root", "", "robot_arm");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);

    $query = "SELECT * FROM arm_joints WHERE id = $id LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $pose = $result->fetch_assoc();
        
        // رجع القيم للواجهة بشكل مباشر باستخدام JavaScript
        echo "<script>";
        for ($i = 1; $i <= 6; $i++) {
            echo "document.getElementById('joint_$i').value = {$pose['joint_' . $i]};";
            echo "document.getElementById('val_$i').innerText = {$pose['joint_' . $i]};";
        }
        echo "window.history.back();"; // يرجع لصفحة index.php
        echo "</script>";
    } else {
        echo "Pose not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
