<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" class="h-full  select-none">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList - Profile</title>
    <link rel="stylesheet" href="../src/css/input.css">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
</head>
<body class="w-full">
<!-- Sidebar Overlay (for mobile) -->
<div class="sidebar-overlay fixed inset-0 z-40 hidden"></div>

<!-- Sidebar -->
<div class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg flex flex-col">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
        <div class="flex items-center">
            <span class="ml-3 text-2xl font-bold text-primary-dark">TodoList</span>
        </div>
        <button class="sidebar-close-btn lg:hidden text-gray-500 hover:text-gray-600 dark:hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Sidebar Content -->
        <div class="flex-1 overflow-y-auto">
            <nav class="px-4 py-6">
                <div class="space-y-1">
                    <a href="../index.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900  dark:hover:text-gray-900">
                        <i class="fas fa-tasks mr-3 flex-shrink-0 text-gray-400 group-hover:text-gray-500 "></i>
                        My Tasks
                        <span class="ml-auto inline-block py-0.5 px-3 text-xs rounded-full bg-indigo-100 dark:bg-gray-900  dark:text-white">5</span>
                    </a>
                    <a href="analytics.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:hover:text-gray-900">
                        <i class="fas fa-chart-pie mr-3 flex-shrink-0 text-gray-400 group-hover:text-gray-500 "></i>
                        Analytics
                    </a>         
                <div class="mt-8">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Labels</h3>
                    <div class="mt-1 space-y-1">
                        <a href="#" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:hover:text-gray-900">
                            <span class="w-2 h-2 mr-3 rounded-full bg-indigo-500"></span>
                            Work
                        </a>
                        <a href="#" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:hover:text-gray-900">
                            <span class="w-2 h-2 mr-3 rounded-full bg-purple-500"></span>
                            Personal
                        </a>
                        <a href="#" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:hover:text-gray-900">
                            <span class="w-2 h-2 mr-3 rounded-full bg-green-500"></span>
                            Health
                        </a>
                        <a href="#" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:hover:text-gray-900">
                            <span class="w-2 h-2 mr-3 rounded-full bg-yellow-500"></span>
                            Family
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    
    <!-- Sidebar Footer -->
    <div class="px-4 py-4 border-t border-gray-200">
        <!-- Dark Mode Toggle -->
        <div class="flex items-center justify-between px-3 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
            <div class="flex items-center">
                <i class="fas fa-moon mr-3 text-gray-400"></i>
                <span class="text-sm font-medium text-gray-700">Dark Mode</span>
            </div>
            <div class="relative inline-block w-10 mr-2 align-middle select-none">
                <input type="checkbox" id="darkModeSwitch" class="sr-only toggle-checkbox">
                <label for="darkModeSwitch" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-200 cursor-pointer"></label>
            </div>
        </div>
        <!-- User Profile -->
        <div class="mt-4 flex items-center px-3 py-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
            <img class="h-8 w-8 rounded-full" src="" alt="User profile" id="navbar-profile-pic">
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-700 user-name"></p>
                <p class="text-xs text-gray-500 user-email"></p>
            </div>
            <button class="ml-auto text-gray-400 hover:text-gray-500">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="lg:pl-64 flex flex-col flex-1">
    <!-- Navbar -->
    <header class="sticky top-0 z-40 bg-white border-b border-gray-200">
        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
            <!-- Left side -->
            <div class="flex items-center">
                <button class="sidebar-toggle lg:hidden text-gray-500 hover:text-gray-600 mr-5">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Breadcrumb -->
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center">
                        <li>
                            <div class="flex">
                                <a href="../index.php" class="text-sm font-medium text-gray-500 hover:text-gray-700">Home</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 text-xs mx-4"></i>
                                <a href="" class="text-sm font-medium text-gray-500 hover:text-gray-700">My Profile</a>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
            
            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <div class="relative max-w-xs hidden">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search tasks...">
                </div>
                
                <!-- Add Task Button -->
                <button class="hidden items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="fas fa-plus mr-2"></i> Add Task
                </button>
                
                <!-- Notifications -->
                <div class="relative">
                    <button class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">View notifications</span>
                        <i class="fas fa-bell"></i>
                    </button>
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                </div>
                
                <!-- User Profile -->
                <div class="relative ml-3">
                    <div>
                        <button type="button" class="max-w-xs flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                            <span class="sr-only">Open user menu</span>
                            <img class="h-8 w-8 rounded-full" src="" id="sidebar-profile-pic" alt="">
                        </button>
                    </div>
                    
                    <!-- Dropdown menu -->
                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                        <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Your Profile</a>
                        <a href="../scripts/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Sign out</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

        <!-- Main Content Area -->
        <main class="flex-1 pb-8 mt-5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Profile Header -->
                <div class="profile-header rounded-lg shadow overflow-hidden text-white">
                    <div class="px-6 py-8 text-center">
                        <div class="avatar-upload mx-auto">
                            <img class="h-24 w-24 rounded-full border-4 border-white shadow-lg mx-auto"  src="" alt="User profile" id="main-profile-pic">
                            <label class="change-avatar">
                                <i class="fas fa-camera text-gray-950"></i>
                                <input type="file" class="hidden">
                            </label>
                        </div>
                        <h1 class="mt-4 text-2xl font-bold main-user-name"></h1>
                        <p class="mt-1 opacity-80 main-user-email"></p>
                        <p class="mt-2 text-sm opacity-70" id="member-since"></p>
                    </div>
                </div>

                <!-- Profile Content -->
                <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Personal Information -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Personal Information</h3>
                            <p class="mt-1 text-sm text-gray-500">Update your personal details</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <form class="space-y-6" id="personal-info-form">
                                <div>
                                    <label for="full-name" class="block text-sm font-medium text-gray-700">Full name</label>
                                    <input type="text" name="full-name" id="full-name" autocomplete="name" value="John Doe" class="mt-1 p-2 border focus:outline-none focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-transparent">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                                    <input type="email" name="email" id="email" autocomplete="email" value="john.doe@example.com" class="mt-1 p-2 border focus:outline-none focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-transparent">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone number</label>
                                    <input type="tel" name="phone" id="phone" autocomplete="tel" value="+1 (555) 123-4567" class="mt-1 p-2 border focus:outline-none focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-transparent">
                                </div>

                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                    <input type="text" name="location" id="location" value="San Francisco, CA" class="mt-1 p-2 border focus:outline-none focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-transparent">
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Cancel
                                    </button>
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Change Password</h3>
                            <p class="mt-1 text-sm text-gray-500">Ensure your account is using a strong password</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <form class="space-y-6" id="change-password-form">
                                <div>
                                    <label for="current-password" class="block text-sm font-medium text-gray-700">Current password</label>
                                    <input type="password" name="current-password" id="current-password" autocomplete="current-password" class="mt-1 p-2 border focus:outline-none focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-transparent">
                                </div>

                                <div>
                                    <label for="new-password" class="block text-sm font-medium text-gray-700">New password</label>
                                    <input type="password" name="new-password" id="new-password" autocomplete="new-password" class="mt-1 p-2 border focus:outline-none focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-transparent">
                                </div>

                                <div>
                                    <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm password</label>
                                    <input type="password" name="confirm-password" id="confirm-password" autocomplete="new-password" class="mt-1 p-2 border focus:outline-none focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md bg-transparent">
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 bg-transparent">
                                        Cancel
                                    </button>
                                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Preferences -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Account Preferences</h3>
                            <p class="mt-1 text-sm text-gray-500">Manage your account settings</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Dark Mode</h4>
                                        <p class="text-sm text-gray-500">Switch between light and dark theme</p>
                                    </div>
                                    <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                        <input type="checkbox" id="darkModeSwitchProfile" class="sr-only toggle-checkbox">
                                        <label for="darkModeSwitchProfile" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-200 cursor-pointer"></label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Email Notifications</h4>
                                        <p class="text-sm text-gray-500">Receive email notifications</p>
                                    </div>
                                    <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                        <input type="checkbox" id="notificationsSwitch" checked class="sr-only toggle-checkbox">
                                        <label for="notificationsSwitch" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-200 cursor-pointer"></label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">Public Profile</h4>
                                        <p class="text-sm text-gray-500">Make your profile visible to others</p>
                                    </div>
                                    <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                        <input type="checkbox" id="publicProfileSwitch" class="sr-only toggle-checkbox">
                                        <label for="publicProfileSwitch" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-200 cursor-pointer"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 hidden w-64">
        <div class="max-w-xs bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i id="toast-icon" class="fas fa-check-circle text-green-500"></i>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p id="toast-message" class="text-sm font-medium text-gray-900">Successfully saved!</p>
                        <p id="toast-description" class="mt-1 text-sm text-gray-500">Your changes have been saved.</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button onclick="hideToast()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../src/js/app.js"></script>
    <script src="../src/js/fetchProfile.js"></script>
</body>
</html>