<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">Vehicle Booking</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($user)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('dashboard') ?>">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <?php if ($user['role'] === 'user') : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('vehicle-booking') ?>">
                                    <i class="fas fa-car"></i> Vehicle Booking
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($user['role'] === 'approver') : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('approval') ?>">
                                    <i class="fas fa-check-circle"></i> Approval
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($user['role'] === 'admin') : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/users') ?>">
                                    <i class="fas fa-users"></i> Users
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url('admin/vehicles') ?>">
                                    <i class="fas fa-truck"></i> Vehicles
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('report') ?>">
                                <i class="fas fa-file-alt"></i> Reports
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($user)) : ?>
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="fas fa-user"></i> <?= $user['name'] ?> (<?= ucfirst($user['role']) ?>)
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('auth/login') ?>">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html> 