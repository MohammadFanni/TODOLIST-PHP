// DOM Elements
document.addEventListener("DOMContentLoaded", function () {
  // Sidebar Toggle
  const sidebarToggleBtn = document.querySelector(".sidebar-toggle");
  const sidebarCloseBtn = document.querySelector(".sidebar-close-btn");
  const sidebar = document.querySelector(".sidebar");
  const sidebarOverlay = document.querySelector(".sidebar-overlay");

  // User Menu
  const userMenuButton = document.getElementById("user-menu-button");
  const userDropdown = document.querySelector('[role="menu"]');

  // Dark Mode Toggle
  const darkModeSwitch = document.getElementById("darkModeSwitch");
  const darkModeSwitchProfile = document.getElementById(
    "darkModeSwitchProfile"
  );

  // Initialize Event Listeners
  initEventListeners();

  // Functions
  function initEventListeners() {
    // Sidebar Toggle
    if (sidebarToggleBtn) {
      sidebarToggleBtn.addEventListener("click", openSidebar);
    }
    if (sidebarCloseBtn) {
      sidebarCloseBtn.addEventListener("click", closeSidebar);
    }
    if (sidebarOverlay) {
      sidebarOverlay.addEventListener("click", closeSidebar);
    }

    // User Menu Toggle
    if (userMenuButton && userDropdown) {
      userMenuButton.addEventListener("click", function () {
        userDropdown.classList.toggle("hidden");
      });
      document.addEventListener("click", function (event) {
        if (
          !userMenuButton.contains(event.target) &&
          !userDropdown.contains(event.target)
        ) {
          userDropdown.classList.add("hidden");
        }
      });
    }

    // Dark Mode Toggle
    if (darkModeSwitch) {
      darkModeSwitch.addEventListener("change", toggleDarkMode);
    }
    if (darkModeSwitchProfile) {
      darkModeSwitchProfile.addEventListener("change", toggleDarkMode);
      darkModeSwitchProfile.checked =
        document.documentElement.classList.contains("dark");
    }

    // Initialize dark mode
    initDarkMode();

    // Add Task Modal
    const addTaskBtn = document.querySelector('[data-bs-toggle="modal"]');
    if (addTaskBtn) {
      addTaskBtn.addEventListener("click", function () {
        openModal("addTaskModal");
      });
    }
  }

  // Sidebar Functions
  function openSidebar() {
    sidebar.classList.add("translate-x-0");
    sidebar.classList.remove("-translate-x-full");
    sidebarOverlay.classList.remove("hidden");
  }

  function closeSidebar() {
    sidebar.classList.remove("translate-x-0");
    sidebar.classList.add("-translate-x-full");
    sidebarOverlay.classList.add("hidden");
  }
});

// Modal Functions
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("hidden");
    document.body.classList.add("overflow-hidden");
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");

    // Reset form if exists
    const form = modal.querySelector("form");
    if (form) {
      form.reset();
      form.dataset.editTaskId = "";
      const submitButton = modal.querySelector(".bg-indigo-600");
      if (submitButton && submitButton.textContent !== "Add Task") {
        submitButton.textContent = "Add Task";
      }
    }
  }
}

// Toast Notification Functions
function showToast(type, title, message) {
  const toast = document.getElementById("toast");
  if (!toast) return;

  const toastIcon = document.getElementById("toast-icon");
  const toastMessage = document.getElementById("toast-message");
  const toastDescription = document.getElementById("toast-description");

  // Set toast content
  toastMessage.textContent = title;
  toastDescription.textContent = message;

  // Set icon and color based on type
  toastIcon.className = ""; // Clear existing classes
  switch (type) {
    case "success":
      toastIcon.className = "fas fa-check-circle text-green-500";
      break;
    case "error":
      toastIcon.className = "fas fa-exclamation-circle text-red-500";
      break;
    case "warning":
      toastIcon.className = "fas fa-exclamation-triangle text-yellow-500";
      break;
    case "info":
      toastIcon.className = "fas fa-info-circle text-blue-500";
      break;
    default:
      toastIcon.className = "fas fa-bell text-gray-500";
  }

  // Show toast
  toast.classList.remove("hidden");

  // Auto hide after 5 seconds
  setTimeout(hideToast, 5000);
}

function hideToast() {
  const toast = document.getElementById("toast");
  if (toast) {
    toast.classList.add("hidden");
  }
}

// Dark Mode Functions
function toggleDarkMode() {
  const html = document.documentElement;
  const isDark = html.classList.contains("dark");
  if (isDark) {
    html.classList.remove("dark");
    localStorage.setItem("darkMode", "false");
  } else {
    html.classList.add("dark");
    localStorage.setItem("darkMode", "true");
  }

  // Sync both dark mode switches
  const profileSwitch = document.getElementById("darkModeSwitchProfile");
  const sidebarSwitch = document.getElementById("darkModeSwitch");
  if (profileSwitch) {
    profileSwitch.checked = !isDark;
  }
  if (sidebarSwitch) {
    sidebarSwitch.checked = !isDark;
  }
}

function initDarkMode() {
  const darkMode = localStorage.getItem("darkMode");
  const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;

  if (darkMode === "true" || (darkMode === null && prefersDark)) {
    document.documentElement.classList.add("dark");

    const darkModeSwitch = document.getElementById("darkModeSwitch");
    const darkModeSwitchProfile = document.getElementById(
      "darkModeSwitchProfile"
    );
    if (darkModeSwitch) {
      darkModeSwitch.checked = true;
    }
    if (darkModeSwitchProfile) {
      darkModeSwitchProfile.checked = true;
    }
  }
}



