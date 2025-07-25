<?php
$conn = new mysqli("localhost", "root", "", "robot_arm");
$conn->query("UPDATE arm_joints SET status = 0");

echo "Status updated to 0";
