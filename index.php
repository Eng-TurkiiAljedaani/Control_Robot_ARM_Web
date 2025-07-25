<?php
// Database connection

$conn = new mysqli("localhost", "root", "", "robot_arm");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Delete a movement when clicking Remove

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $sql_delete = "DELETE FROM arm_joints WHERE ID = $delete_id";
    $conn->query($sql_delete);
    header("Location: index.php");
    exit;
}

// Get movement data

$sql = "SELECT * FROM arm_joints ORDER BY ID ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8" />
<title>Robot ARM</title>

<!-- Google Font -->

<link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">

<style>
  body {
    font-family: 'Cairo', sans-serif;
    margin: 20px;
    direction: rtl;
    background-color: #f5f7fa;
  }

  .container {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
  }

  .panel {
    border: 1px solid #ddd;
    background-color: #fff;
    padding: 30px;
    border-radius: 15px;
    max-width: 500px;
    width: 100%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  h2 {
    text-align: center;
    margin-bottom: 20px;
  }

  .slider-container {
    margin: 20px 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .slider-container label {
    width: 90px;
  }

  .slider-container input[type="range"] {
    flex: 1;
    margin: 0 10px;
  }

  button {
    padding: 10px 25px;
    margin: 5px;
    cursor: pointer;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 8px;
    transition: background-color 0.3s ease;
  }

  button:hover {
    background-color: #0056b3;
  }

  img {
    width: 200px;
    display: block;
    margin: 0 auto 20px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }

  table, th, td {
    border: 1px solid #ddd;
  }

  th {
    background-color: #007BFF;
    color: white;
    padding: 10px;
  }

  td {
    padding: 10px;
    background-color: #f9f9f9;
  }

  form {
    display: inline;
  }
</style>
</head>
<body>

<div class="container">
  <div class="panel">
    <h2>Robot ARM</h2>
    <img src="ARM.png" alt="Robot Arm" />
    <form method="POST" action="save_pose.php" id="poseForm">
      <?php for ($i = 1; $i <= 6; $i++): ?>
      <div class="slider-container">
        <label for="motor<?= $i ?>">Joint - <?= $i ?>:</label>
        <input type="range" id="motor<?= $i ?>" name="joint_<?= $i ?>" min="0" max="180" value="90" />
        <span id="value<?= $i ?>">90</span>
      </div>
      <?php endfor; ?>
      <div style="text-align:center;">
        <button type="reset" id="resetBtn">Reset</button>
        <button type="submit">Save Pose</button>
        <button type="button" id="runBtn">Run</button>
      </div>
    </form>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <?php for ($i=1; $i <=6; $i++): ?>
        <th>Joint - <?= $i ?></th>
      <?php endfor; ?>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result && $result->num_rows > 0): 
      $count = 1;
      while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $count++ ?></td>
        <?php for ($i=1; $i<=6; $i++): ?>
          <td><?= htmlspecialchars($row["joint_$i"]) ?></td>
        <?php endfor; ?>
        <td>
          <form method="POST">
            <input type="hidden" name="load_id" value="<?= $row['id'] ?>">
            <button type="submit" name="load">Load</button>
          </form>
          <form method="POST" onsubmit="return confirm('Are you sure you want to delete ? );">
            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
            <button type="submit">Remove</button>
          </form>
        </td>
      </tr>
    <?php endwhile; else: ?>
      <tr><td colspan="8">No data saved.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<script>
  // Update slider values when changing

  for (let i = 1; i <= 6; i++) {
    const slider = document.getElementById(`motor${i}`);
    const output = document.getElementById(`value${i}`);
    slider.addEventListener('input', () => {
      output.textContent = slider.value;
    });
  }

  // Button Run
  document.getElementById('runBtn').addEventListener('click', () => {
    alert('Pose activated!');
  });

  // Reset button resets values to 90

  document.getElementById('resetBtn').addEventListener('click', (e) => {
    e.preventDefault();
    for (let i = 1; i <= 6; i++) {
      const slider = document.getElementById(`motor${i}`);
      const output = document.getElementById(`value${i}`);
      slider.value = 90;
      output.textContent = 90;
    }
  });
</script>

<?php
// Download movement data

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['load_id'])) {
    $load_id = intval($_POST['load_id']);
    $sql_load = "SELECT * FROM arm_joints WHERE ID = $load_id LIMIT 1";
    $res = $conn->query($sql_load);
    if ($res && $res->num_rows === 1) {
        $data = $res->fetch_assoc();
        echo "<script>\n";
        for ($i=1; $i<=6; $i++) {
            echo "document.getElementById('motor{$i}').value = " . intval($data["joint_$i"]) . ";\n";
            echo "document.getElementById('value{$i}').textContent = " . intval($data["joint_$i"]) . ";\n";
        }
        echo "</script>\n";
    }
}
$conn->close();
?>

</body>
</html>
