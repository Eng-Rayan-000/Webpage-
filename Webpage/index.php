<?php
require 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['age'])) {
    $stmt = $pdo->prepare('INSERT INTO people (name, age) VALUES (:name, :age)');
    $stmt->execute([
        ':name' => $_POST['name'],
        ':age'  => (int)$_POST['age']
    ]);
    // redirect to avoid resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch all records
$people = $pdo->query('SELECT * FROM people ORDER BY id')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>People Registry</title>
  <style>
    form { margin-bottom: 1em; }
    table { border-collapse: collapse; width: 100%; max-width: 500px; }
    th, td { border: 1px solid #ccc; padding: .5em; text-align: center; }
    button.toggle { padding: .3em .6em; }
  </style>
</head>
<body>

  <!-- One-line form -->
  <form method="post">
    <label>Name: <input type="text" name="name" required></label>
    <label>Age: <input type="number" name="age" min="0" required></label>
    <button type="submit">Submit</button>
  </form>

  <!-- Records table -->
  <table>
    <thead>
      <tr><th>ID</th><th>Name</th><th>Age</th><th>Status</th><th>Action</th></tr>
    </thead>
    <tbody id="people-body">
      <?php foreach ($people as $p): ?>
      <tr data-id="<?= $p['id'] ?>">
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= $p['age'] ?></td>
        <td class="status"><?= $p['status'] ?></td>
        <td>
          <button class="toggle">Toggle</button>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <script>
  // Attach click handlers to all toggle buttons
  document.querySelectorAll('button.toggle').forEach(btn => {
    btn.addEventListener('click', e => {
      const row = e.target.closest('tr');
      const id  = row.dataset.id;

      // Send AJAX request to flip status
      fetch('toggle_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          // update status cell instantly
          row.querySelector('.status').textContent = data.new_status;
        } else {
          alert('Error toggling status');
        }
      })
      .catch(console.error);
    });
  });
  </script>
</body>
</html>
