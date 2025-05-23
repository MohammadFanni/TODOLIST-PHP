<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" class="h-full overflow-auto select-none">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList - Dashboard</title>
    <link rel="stylesheet" href="../src/css/input.css">
    <link rel="stylesheet" href="../dist/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js "></script>
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
                  <a href="../index.php" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900">       
                        <i class="fas fa-tasks mr-3 flex-shrink-0 text-indigo-600  dark:text-white" ></i>
                        My Tasks
                        <span class="ml-auto inline-block py-0.5 px-3 text-xs rounded-full bg-indigo-100 dark:bg-gray-900 text-indigo-800  dark:text-white" id="sidebarTotalTasks"></span>
                    </a>
                    <a href="" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md bg-indigo-50 dark:bg-gray-900 dark:text-white text-indigo-800">
                        <i class="fas fa-chart-pie mr-3 flex-shrink-0 text-gray-400 group-hover:text-gray-500 "></i>
                        Analytics
                    </a>
                </div>
                
                <div class="mt-8">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Labels</h3>
                    <div class="mt-1 space-y-1">
                        <a href="#" data-category="work" class="label-filter group flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 relative">
                            <span class="flex items-center">
                                <span class="w-2 h-2 mr-3 rounded-full bg-indigo-500"></span>
                                Work
                            </span>
                        </a>
                        <a href="#" data-category="personal" class="label-filter group flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 relative">
                            <span class="flex items-center">
                                <span class="w-2 h-2 mr-3 rounded-full bg-purple-500"></span>
                                Personal
                            </span>
                        </a>
                        <a href="#" data-category="health" class="label-filter group flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 relative">
                            <span class="flex items-center">
                                <span class="w-2 h-2 mr-3 rounded-full bg-green-500"></span>
                                Health
                            </span>
                        </a>
                        <a href="#" data-category="family" class="label-filter group flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 hover:text-gray-900 relative">
                            <span class="flex items-center">
                                <span class="w-2 h-2 mr-3 rounded-full bg-yellow-500"></span>
                                Family
                            </span>
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
                <img class="h-8 w-8 rounded-full" id="sidebar-profile-pic" src="" alt="User profile">
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
                    <button class="sidebar-toggle lg:hidden text-gray-500 hover:text-gray-600 mr-4">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <!-- Breadcrumb -->
                    <nav class="flex bread" aria-label="Breadcrumb">
                        <ol class="flex items-center">
                            <li>
                                <div class="flex">
                                    <a href="../index.php" class="text-sm ml-2 font-medium text-gray-500 hover:text-gray-700">Home</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-gray-400 text-xs mx-4"></i>
                                    <a href="" class="text-sm font-medium text-gray-500 hover:text-gray-700">Analytics</a>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
                
                <!-- Right side -->
                <div class="flex items-center space-x-4">
                            
                    <!-- Notifications -->
                    <div class="relative bread">
                        <button class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">View notifications</span>
                            <i class="fas fa-bell"></i>
                        </button>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </div>
                    
                    <!-- User Profile -->
                    <div class="relative ml-3 bread">
                        <div>
                            <button type="button" class="max-w-xs flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full" id="navbar-profile-pic" src="" alt="">
                            </button>
                        </div>
                        
                        <!-- Dropdown menu -->
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Your Profile</a>
                            <a href="../scripts/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mt-6">
                    <!-- Total Tasks -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                    <i class="fas fa-tasks text-white"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Tasks</dt>
                                        <dd>
                                            <div class="text-lg font-medium text-gray-900" id="totalTasks">0</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Completed -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                        <dd>
                                            <div class="text-lg font-medium text-gray-900" id="completedTasks">0</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                    <i class="fas fa-exclamation-circle text-white"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                        <dd>
                                            <div class="text-lg font-medium text-gray-900" id="pendingTasks">0</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overdue -->
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Overdue</dt>
                                        <dd>
                                            <div class="text-lg font-medium text-gray-900" id="overdueTasks">0</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <!-- Analytics Chart -->
              <div class="mt-8 bg-white shadow rounded-lg p-6">
                  <h2 class="text-xl font-semibold px-4 py-4">Trend Perubahan Tugas</h2>
                  <canvas id="taskLineChart" height="100"></canvas>
              </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 hidden w-64 z-50">
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
    <script defer src="../src/js/fetchChart.js"></script>
</body>
</html>