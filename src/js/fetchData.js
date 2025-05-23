initEventListeners();

function initEventListeners() {
  // User Profile
  function getUserProfile() {
    fetch("scripts/user.php", {
      method: "GET",
      headers: {
        Accept: "application/json",
      },
      credentials: "same-origin", // Penting untuk mengirim cookies/session
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          // Menampilkan data user ke elemen HTML
          document.querySelector(".user-name").textContent = data.name;
          document.querySelector(".user-email").textContent = data.email;
          document.getElementById("navbar-profile-pic").src = data.profile_picture;
          document.getElementById("sidebar-profile-pic").src = data.profile_picture;
        } else {
          console.error("Failed to get user profile:", data.message);
          // Redirect ke login jika session tidak valid
          window.location.href = "public/login.html";
        }
      })
      .catch((error) => {
        console.error("Error fetching user profile:", error);
      });
  }

  // Panggil fungsi untuk mendapatkan profil user
  getUserProfile();

  // Tambahkan event listener untuk tombol logout
  const logoutButton = document.querySelector(".fa-sign-out-alt").parentElement;
  logoutButton.addEventListener("click", function () {
    fetch("scripts/logout.php", {
      method: "POST",
      credentials: "same-origin",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.href = "public/login.html";
        }
      })
      .catch((error) => {
        console.error("Error during logout:", error);
      });
  });

  function updateTaskCounts() {
    fetch("scripts/task_stats.php", {
      method: "GET",
      headers: {
        Accept: "application/json",
      },
      credentials: "same-origin", // Penting untuk mengirim cookies/session
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          // Update stats on the dashboard
          document.getElementById("totalTasks").textContent = data.data.total;
          document.getElementById("sidebarTotalTasks").textContent =
            data.data.total;
          document.getElementById("completedTasks").textContent =
            data.data.completed;
          document.getElementById("pendingTasks").textContent =
            data.data.pending;
          document.getElementById("overdueTasks").textContent =
            data.data.overdue;
        } else {
          console.error("Failed to fetch task stats:", data.message);
        }
      })
      .catch((error) => {
        console.error("Error fetching task stats:", error);
      });
  }
  updateTaskCounts();

  // Tasks
  let activeCategory = "";
  let activeLabel = null;

  const labelLinks = document.querySelectorAll(".label-filter");
  const taskFilterSelect = document.getElementById("taskFilter");
  const taskSortSelect = document.getElementById("taskSort");

  // Fungsi validasi apakah label aktif
  function isLabelActive() {
    return !!activeCategory;
  }

  // Event listener untuk label (kategori)
  labelLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const category = this.dataset.category;

      // Hapus tombol clear dari label lain
      if (activeLabel && activeLabel !== this) {
        const existingBtn = activeLabel.querySelector(".btn-clear-label");
        if (existingBtn) existingBtn.remove();
      }

      // Tambahkan tombol clear jika belum ada
      if (!this.querySelector(".btn-clear-label")) {
        const clearBtn = document.createElement("button");
        clearBtn.innerHTML = "&times;";
        clearBtn.type = "button";
        clearBtn.className =
          "btn-clear-label ml-2 text-gray-400 hover:text-red-600";
        clearBtn.setAttribute("aria-label", "Clear label filter");

        clearBtn.addEventListener("click", (e) => {
          e.preventDefault();
          e.stopPropagation();
          clearBtn.remove();
          activeLabel = null;
          activeCategory = "";
          this.classList.remove("active");
          labelLinks.forEach((l) => l.classList.remove("active")); // Hilangkan visual aktif
          fetchTaskList({ category: "" });
        });

        this.appendChild(clearBtn);
      }

      // Update state dan UI
      activeLabel = this;
      activeCategory = category;

      // Tandai label sebagai aktif secara visual
      labelLinks.forEach((l) => l.classList.remove("active"));
      this.classList.add("active");

      fetchTaskList({ category: activeCategory });
    });
  });

  // Event listener untuk filter status (All Tasks, Completed, dll)
  taskFilterSelect.addEventListener("change", () => {
    const filter = taskFilterSelect.value;
    fetchTaskList({ filter, category: activeCategory });
  });

  // Event listener untuk sortir (Due Date, Priority, dll)
  taskSortSelect.addEventListener("change", () => {
    const sort = taskSortSelect.value;
    fetchTaskList({ sort, category: activeCategory });
  });

  // Fungsi utama untuk fetch task list
  function fetchTaskList({ filter = "", sort = "", category = "" } = {}) {
    if (!filter) filter = document.getElementById("taskFilter").value;
    if (!sort) sort = document.getElementById("taskSort").value;

    const url = new URL("scripts/fetch_tasks.php", window.location.href);
    url.searchParams.set("filter", filter);
    url.searchParams.set("sort", sort);
    if (category) {
      url.searchParams.set("category", category);
    }

    fetch(url, {
      method: "GET",
      headers: { Accept: "application/json" },
      credentials: "same-origin",
    })
      .then((response) => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then((data) => {
        if (!data.success) {
          console.error("Failed to fetch task list:", data.message);
          return;
        }

        const todayList = document.querySelector(".today-task-list");
        const upcomingList = document.querySelector(".upcoming-task-list");

        if (!todayList || !upcomingList) {
          console.error("DOM elements not found");
          return;
        }

        todayList.innerHTML = "";
        upcomingList.innerHTML = "";

        const now = new Date();

        data.data.forEach((task) => {
          // ✅ Filter berdasarkan kategori (case-insensitive)
          if (
            category &&
            task.category.localeCompare(category, undefined, {
              sensitivity: "base",
            }) !== 0
          ) {
            return;
          }

          const taskDate = new Date(task.due_date);
          const isOverdue = taskDate < now && task.status !== "completed";

          const taskElement = createTaskElement(
            task.title,
            task.description,
            task.due_date,
            task.priority,
            task.category,
            task.status,
            task.id
          );

          if (!taskElement) {
            console.warn("Invalid task element", task);
            return;
          }

          // ✅ FILTER BERDASARKAN STATUS
          if (filter === "Overdue") {
            if (isOverdue) {
              todayList.appendChild(taskElement);
            }
          } else if (filter === "All Tasks") {
            if (
              taskDate.toDateString() === now.toDateString() ||
              isOverdue ||
              task.status === "completed"
            ) {
              todayList.appendChild(taskElement);
            } else if (task.status === "pending") {
              upcomingList.appendChild(taskElement);
            }
          } else if (filter === "Completed") {
            if (task.status === "completed") {
              todayList.appendChild(taskElement);
            }
          } else if (filter === "Pending") {
            if (task.status === "pending" && !isOverdue) {
              todayList.appendChild(taskElement);
            }
          }
        });
      })
      .catch((error) => {
        console.error("Error fetching task list:", error);
      });

    updateTaskCounts();
  }

  // Panggil fungsi awal untuk mendapatkan daftar tugas
  fetchTaskList();

  // Helper function to create task elements dynamically
  function createTaskElement(
    title,
    description,
    dueDate,
    priority,
    category,
    status,
    taskId
  ) {
    const li = document.createElement("li");
    li.className = "px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700";
    li.setAttribute("data-task-id", taskId);

    const formattedDate = formatDueDate(dueDate);
    const isCompleted = status === "completed";

    const isOverdue =
      dueDate && new Date(dueDate) < new Date() && status !== "completed";

    if (isOverdue) {
      li.classList.add("overdue-border");
    }

    li.innerHTML = `
        <div class="flex items-center">
            <input type="checkbox" id="task-${taskId}" class="hidden task-checkbox" ${
      isCompleted ? "checked" : ""
    }>
            <label for="task-${taskId}" class="task-checkbox-label w-5 h-5 border-2 border-gray-300 rounded-md cursor-pointer relative mr-4 dark:border-white"></label>
            <div class="flex-1 min-w-0">
                <div class="flex items-center">
                    <p class="text-sm font-medium text-gray-900 ${
                      isCompleted ? "line-through" : ""
                    }">${title}</p>
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium priority-${priority}">${capitalizeFirstLetter(
      priority
    )}</span>
                </div>
                <p class="text-sm text-gray-500">${description}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium category-${category}">${capitalizeFirstLetter(
      category
    )}</span>
                    <span class="inline-flex items-center text-xs ${
                      isOverdue ? "text-red-500 font-medium" : "text-gray-500"
                    }">
                        <i class="fas fa-calendar-alt mr-1"></i> ${
                          isOverdue
                            ? "OVERDUE: " + formattedDate
                            : "Due: " + formattedDate
                        }
                    </span>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0 flex space-x-2">
                <button class="p-1 rounded-full text-gray-400 hover:text-green-600 focus:outline-none edit-task-btn">
                    <i class="fas fa-edit"></i>
                </button>
                <button id="deleteTaskBtn" class="p-1 rounded-full text-gray-400 hover:text-red-600 focus:outline-none delete-task-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    // Add checkbox event listener
    const checkbox = li.querySelector(".task-checkbox");
    checkbox.addEventListener("change", handleTaskCompletion);

    // Add edit button handler
    const editBtn = li.querySelector(".edit-task-btn");
    editBtn.addEventListener("click", () => openEditModal(taskId));

    // Add delete button handler
    const deleteBtn = li.querySelector(".delete-task-btn");
    deleteBtn.addEventListener("click", () => confirmDeleteTask(taskId));

    return li;
  }

  // Helper function to format due date
  function formatDueDate(dateString) {
    if (!dateString) return "No due date";
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    const tomorrow = new Date(today);

    yesterday.setDate(today.getDate() - 1);
    tomorrow.setDate(today.getDate() + 1);

    if (date.toDateString() === today.toDateString()) {
      return `Today, ${formatTime(date)}`;
    } else if (date.toDateString() === tomorrow.toDateString()) {
      return `Tomorrow, ${formatTime(date)}`;
    } else if (date.toDateString() === yesterday.toDateString()) {
      return `Yesterday, ${formatTime(date)}`;
    } else if (date < today) {
      // If the date is in the past but not yesterday
      return `${date.toLocaleDateString()}, ${formatTime(date)}`;
    } else {
      // For future dates beyond tomorrow
      return `${date.toLocaleDateString()}, ${formatTime(date)}`;
    }
  }

  // Helper function to format time
  function formatTime(date) {
    return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  }

  // Helper function to capitalize first letter
  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  // Function to handle task completion
  function handleTaskCompletion(e) {
    const checkbox = e.target;
    const taskItem = checkbox.closest("li");
    const taskId = taskItem.getAttribute("data-task-id");
    const isChecked = checkbox.checked;

    // Update task status on the server
    fetch(`scripts/update_task_status.php`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        taskId: taskId,
        status: isChecked ? "completed" : "pending",
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const taskText = taskItem.querySelector("p.text-sm.font-medium");
          if (isChecked) {
            taskText.classList.add("line-through");
            showToast("success", "Completed", "Task completed successfully");
          } else {
            taskText.classList.remove("line-through");
            showToast("info", "Pending", "Task marked as pending");
          }
          updateTaskCounts();
          fetchTaskList();
        } else {
          console.error("Failed to update task status:", data.message);
          showToast("error", "Error", "Failed to update task status");
        }
      })
      .catch((error) => {
        console.error("Error updating task status:", error);
        showToast("error", "Network Error", "Unable to connect to server");
      });
  }

  function setupSearch() {
    const searchInput = document.querySelector(
      'input[placeholder="Search tasks..."]'
    );
    if (!searchInput) return;

    let timeoutId;
    searchInput.addEventListener("input", function () {
      clearTimeout(timeoutId);
      const query = this.value.trim();

      // Delay search to avoid too many requests
      timeoutId = setTimeout(() => {
        if (query.length >= 1 || query === "") {
          fetchSearchResults(query);
        }
      }, 500); // 500ms delay
    });
  }

  function fetchSearchResults(query) {
    fetch("scripts/search_tasks.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ query: query }),
      credentials: "same-origin",
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          // Clear existing task list
          const taskList = document.querySelector("main ul.divide-y");
          if (taskList) {
            taskList.innerHTML = "";
          }
          // Populate task list with fetched data
          data.data.forEach((task) => {
            const taskElement = createTaskElement(
              task.title,
              task.description,
              task.due_date,
              task.priority,
              task.category,
              task.status,
              task.id
            );
            taskList.appendChild(taskElement);
          });
          // Update task counts
          updateTaskCounts();
        } else {
          console.error("Failed to fetch search results:", data.message);
        }
      })
      .catch((error) => {
        console.error("Error fetching search results:", error);
      });
  }

  // Panggil fungsi untuk mengatur search
  setupSearch();

  function isValidDateTime(value) {
    const regex = /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/;
    return regex.test(value);
  }

  taskForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const title = document.getElementById("taskTitle").value.trim();
    const description = document.getElementById("taskDescription").value.trim();
    const dueDate = document.getElementById("taskDueDate").value;
    const priority = document.getElementById("taskPriority").value;
    const category = document.getElementById("taskCategory").value;

    if (!title || !isValidDateTime(dueDate) || !priority || !category) {
      showToast("error", "Error", "All fields are required");
      return;
    }

    const isEditMode = !!taskForm.dataset.editTaskId;
    const url = isEditMode
      ? "scripts/edit_task.php"
      : "scripts/add_task.php";
    const payload = isEditMode
      ? {
          id: taskForm.dataset.editTaskId,
          title,
          description,
          due_date: dueDate,
          priority,
          category,
        }
      : { title, description, due_date: dueDate, priority, category };

    fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
      credentials: "same-origin",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const action = isEditMode ? "updated" : "added";
          showToast("success", "Success", `Task successfully ${action}`);
          closeModal("addTaskModal");
          fetchTaskList(); // Refresh daftar tugas
        } else {
          showToast(
            "error",
            "Failed",
            data.message || "Failed to save changes"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showToast("error", "Network Error", "Unable to connect to the server");
      });
  });

  function openEditModal(taskId) {
    // Ambil task berdasarkan ID
    fetch(`scripts/fetch_tasks.php`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          const task = data.data.find((t) => t.id === parseInt(taskId));
          if (!task) {
            showToast("error", "Error", "Task not found");
            return;
          }

          const formatDateForInput = (datetime) => {
            const date = new Date(datetime);
            if (isNaN(date)) return "";
            const pad = (n) => String(n).padStart(2, "0");
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(
              date.getDate()
            )}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
          };

          // Isi form dengan data tugas
          document.getElementById("taskTitle").value = task.title;
          document.getElementById("taskDescription").value = task.description;
          document.getElementById("taskDueDate").value = formatDateForInput(
            task.due_date
          );
          document.getElementById("taskPriority").value = task.priority;
          document.getElementById("taskCategory").value = task.category;

          // Ganti tombol submit menjadi "Update Task"
          const submitButton = document.querySelector(
            "#addTaskModal button[type='submit']"
          );
          submitButton.textContent = "Update Task";

          // Simpan ID tugas di form
          const taskForm = document.getElementById("taskForm");
          taskForm.dataset.editTaskId = taskId;

          // Buka modal
          openModal("addTaskModal");
        }
      });
  }

  let taskIdToDelete = null;

  function confirmDeleteTask(taskId) {
    taskIdToDelete = taskId;
    document.getElementById("deleteConfirmModal").classList.remove("hidden");
  }

  function closeDeleteModal() {
    document.getElementById("deleteConfirmModal").classList.add("hidden");
    taskIdToDelete = null;
  }

  document.getElementById("confirmDeleteBtn").addEventListener("click", () => {
    if (!taskIdToDelete) return;

    fetch("scripts/delete_task.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ taskId: taskIdToDelete }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Hapus elemen tugas dari DOM atau refresh halaman
          const taskElement = document.querySelector(
            `[data-task-id="${taskIdToDelete}"]`
          );
          if (taskElement) taskElement.remove();
          closeDeleteModal();
          updateTaskCounts();
        } else {
          alert("Gagal menghapus tugas: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menghapus tugas.");
      });
  });
}
