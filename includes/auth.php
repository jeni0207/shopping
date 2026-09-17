<?php

/*
|--------------------------------------------------------------------------
| Start Session
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Customer Session
|--------------------------------------------------------------------------
*/

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}


function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}


function currentUserId()
{
    return $_SESSION['user_id'] ?? null;
}


function currentUserName()
{
    return $_SESSION['user_name'] ?? '';
}


/*
|--------------------------------------------------------------------------
| Blocked User Check
|--------------------------------------------------------------------------
*/

function checkIfBlocked($conn)
{
    if (isLoggedIn()) {
        $stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
        $stmt->execute([currentUserId()]);
        $user = $stmt->fetch();

        if (!$user || $user['status'] === 'blocked') {
            session_unset();
            session_destroy();
            header("Location: login.php?blocked=1");
            exit;
        }
    }
}


/*
|--------------------------------------------------------------------------
| Admin Session
|--------------------------------------------------------------------------
*/

function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}


function requireAdmin()
{
    if (!isAdminLoggedIn()) {
        header("Location: ../admin/login.php");
        exit;
    }
}


function currentAdminName()
{
    return $_SESSION['admin_name'] ?? '';
}