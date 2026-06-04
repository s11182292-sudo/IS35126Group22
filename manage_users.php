<?php

require 'session.php';
require 'security.php';
require 'auth_check.php';
require 'role_check.php';
require 'db.php';

requireRole('admin');

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| FETCH USERS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT id, fullname, email, role
    FROM users
    ORDER BY id DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Manage Users</span>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</nav>

<div class="container mt-4">

<h2>👤 Users</h2>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert alert-success">
        User deleted successfully.
    </div>
<?php endif; ?>

<table class="table table-bordered table-striped">

<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach ($users as $u): ?>

<tr>
    <td><?= $u['id'] ?></td>
    <td><?= htmlspecialchars($u['fullname']) ?></td>
    <td><?= htmlspecialchars($u['email']) ?></td>
    <td><?= htmlspecialchars($u['role']) ?></td>

    <td>

        <!-- SECURE DELETE FORM (POST + CSRF) -->
        <form method="POST"
              action="delete_user.php"
              class="d-inline">

            <input type="hidden"
                   name="id"
                   value="<?= $u['id'] ?>">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= $_SESSION['csrf_token'] ?>">

            <button type="submit"
                    class="btn btn-danger btn-sm"
                    onclick="return confirm('Delete this user?')">

                Delete
            </button>

        </form>

    </td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

</div>

</body>
</html>
