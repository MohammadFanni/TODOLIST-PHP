initEventListeners();

function initEventListeners() {
  // User Profile
  function getUserProfile() {
    fetch("../scripts/user_info.php", {
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
          document.getElementById("navbar-profile-pic").src =
            data.profile_picture;
          document.getElementById("sidebar-profile-pic").src =
            data.profile_picture;
        } else {
          console.error("Failed to get user profile:", data.message);
          // Redirect ke login jika session tidak valid
          window.location.href = "login.html";
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
    fetch("../scripts/logout.php", {
      method: "POST",
      credentials: "same-origin",
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.href = "login.html";
        }
      })
      .catch((error) => {
        console.error("Error during logout:", error);
      });
  });

  function updateTaskCounts() {
    fetch("../scripts/task_stats.php", {
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

  let taskLineChart;

  function updateLineChart(labels, completed, pending, overdue) {
    const ctx = document.getElementById("taskLineChart").getContext("2d");

    if (taskLineChart) {
      taskLineChart.destroy(); // Hapus chart lama jika ada
    }

    taskLineChart = new Chart(ctx, {
      type: "line",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Selesai",
            data: completed,
            borderColor: "rgba(16, 185, 129, 1)",
            backgroundColor: "rgba(16, 185, 129, 0.2)",
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: "rgba(16, 185, 129, 1)",
          },
          {
            label: "Pending",
            data: pending,
            borderColor: "rgba(234, 179, 8, 1)",
            backgroundColor: "rgba(234, 179, 8, 0.2)",
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: "rgba(234, 179, 8, 1)",
          },
          {
            label: "Overdue",
            data: overdue,
            borderColor: "rgba(239, 68, 68, 1)",
            backgroundColor: "rgba(239, 68, 68, 0.2)",
            tension: 0.4,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: "rgba(239, 68, 68, 1)",
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
            },
          },
        },
        plugins: {
          legend: {
            position: "top",
          },
          tooltip: {
            mode: "index",
            intersect: false,
          },
        },
      },
    });
  }

  function fetchTaskStatsForChart() {
    fetch("../scripts/task_chart.php")
      .then((response) => {
        if (!response.ok) throw new Error("HTTP status " + response.status);
        return response.json();
      })
      .then((data) => {
        console.log(data);
        if (data.success) {
          updateLineChart(
            data.data.labels,
            data.data.completed,
            data.data.pending,
            data.data.overdue
          );
        } else {
          console.error("Failed to load stats:", data.message);
        }
      })
      .catch((error) => {
        console.error("Error fetching chart data:", error);
      });
  }

  document.addEventListener("DOMContentLoaded", () => {
    fetchTaskStatsForChart();
  });
}
