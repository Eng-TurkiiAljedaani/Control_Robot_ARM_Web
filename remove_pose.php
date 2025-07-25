<?php
$conn = new mysqli("localhost", "root", "", "robot_arm");

$id = $_POST['id'];
$conn->query("DELETE FROM arm_joints WHERE id = $id");

header("Location: index.php");
exit();
