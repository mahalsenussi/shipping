<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة الشحن - Shipping Management System</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/shipping/v2/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/shipping/v2">نظام إدارة الشحن</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/shipping/v2?page=dashboard"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/shipping/v2?page=shipments&action=index"><i class="fas fa-ship"></i> الشحنات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/shipping/v2?page=companies&action=index"><i class="fas fa-building"></i> الشركات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/shipping/v2?page=documents&action=index"><i class="fas fa-file-alt"></i> المستندات</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/shipping/v2?page=reports&action=index"><i class="fas fa-chart-bar"></i> التقارير</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user"></i> الملف الشخصي</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-sign-out-alt"></i> تسجيل خروج</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Page content will be inserted here -->
