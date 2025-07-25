<?php
$conn = new mysqli("localhost", "root", "", "robot_arm");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // جلب القيم من الفورم
    $joints = [];
    for ($i=1; $i<=6; $i++) {
        $joints[$i] = isset($_POST["joint_$i"]) ? intval($_POST["joint_$i"]) : 90;
    }

    // إدخال البيانات في الجدول
    $sql = "INSERT INTO arm_joints (joint_1, joint_2, joint_3, joint_4, joint_5, joint_6)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiii", $joints[1], $joints[2], $joints[3], $joints[4], $joints[5], $joints[6]);

    if ($stmt->execute()) {
        // نجاح الإدخال، العودة للصفحة الرئيسية
        header("Location: index.php");
        exit;
    } else {
        echo "خطأ في الحفظ: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
