initEventListeners();

function initEventListeners() {
  // User Profile - hanya dipanggil sekali
  function getUserProfile() {
    fetch("../scripts/user_info.php", {
      method: "GET",
      headers: {
        Accept: "application/json",
      },
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
          updateUserInterface(data);
          getUserProfile();
        } else {
          console.error("Failed to get user profile:", data.message);
          window.location.href = "login.html";
        }
      })
      .catch((error) => {
        console.error("Error fetching user profile:", error);
      });
  }

  // Function untuk update UI dengan data user
  function updateUserInterface(data) {
    // Sidebar & Navbar
    const userNameElements = document.querySelectorAll(".user-name, .main-user-name");
    const userEmailElements = document.querySelectorAll(".user-email, .main-user-email");

    userNameElements.forEach(el => el.textContent = data.name);
    userEmailElements.forEach(el => el.textContent = data.email);

    // Profile pictures
    const profilePicElements = document.querySelectorAll(
      "#navbar-profile-pic, #sidebar-profile-pic, #main-profile-pic"
    );
    profilePicElements.forEach(el => el.src = data.profile_picture);

    // Member since
    const memberSinceEl = document.getElementById("member-since");
    if (memberSinceEl) {
      memberSinceEl.textContent = "Member since " + data.member_since;
    }

    // Form input
    document.getElementById("full-name").value = data.name || "";
    document.getElementById("email").value = data.email || "";
    document.getElementById("phone").value = data.phone || "";
    document.getElementById("location").value = data.location || "";
  }

  // Panggil fungsi untuk mendapatkan profil user - HANYA SEKALI
  getUserProfile();

  // Logout button
  const logoutButton = document.querySelector(".fa-sign-out-alt")?.parentElement;
  if (logoutButton) {
    logoutButton.addEventListener("click", function () {
      fetch("../scripts/logout.php", {
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
  }

  // Change Password Form
  const changePasswordForm = document.getElementById("change-password-form");
  if (changePasswordForm) {
    changePasswordForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const currentPassword = document.getElementById("current-password").value;
      const newPassword = document.getElementById("new-password").value;
      const confirmPassword = document.getElementById("confirm-password").value;

      if (newPassword !== confirmPassword) {
        showToast("Error", "Passwords do not match", "error");
        return;
      }

      fetch("../scripts/change_password.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          current_password: currentPassword,
          new_password: newPassword,
        }),
        credentials: "same-origin",
      })
        .then((response) => response.json())
        .then((res) => {
          if (res.success) {
            showToast("Success", "Password changed successfully", "success");
            // Reset form setelah berhasil
            changePasswordForm.reset();
          } else {
            showToast("Error", res.message, "error");
          }
        })
        .catch((err) => {
          console.error(err);
          showToast("Error", "Something went wrong.", "error");
        });
    });
  }

  // Personal Info Form
  const personalInfoForm = document.getElementById("personal-info-form");
  if (personalInfoForm) {
    personalInfoForm.addEventListener("submit", function (e) {
      e.preventDefault(); // Mencegah reload halaman

      const data = {
        name: document.getElementById("full-name").value,
        email: document.getElementById("email").value,
        phone: document.getElementById("phone").value,
        location: document.getElementById("location").value,
      };

      fetch("../scripts/update_profile.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
        credentials: "same-origin",
      })
        .then((response) => response.json())
        .then((res) => {
          if (res.success) {
            showToast("Success", "Profile updated successfully!", "success");
            // Update UI dengan data baru tanpa reload
            updateUserInterface({
              name: data.name,
              email: data.email,
              phone: data.phone,
              location: data.location,
              profile_picture: document.getElementById("main-profile-pic").src,
              member_since: document.getElementById("member-since").textContent.replace("Member since ", "")
            });
          } else {
            showToast("Error", res.message, "error");
          }
        })
        .catch((err) => {
          console.error(err);
          showToast("Error", "Something went wrong.", "error");
        });
    });
  }

  const avatarInput = document.querySelector(".change-avatar input[type='file']");
  if (avatarInput) {
    avatarInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (!file) return;

      // Validasi jenis file
      const validTypes = ["image/jpeg", "image/png", "image/gif"];
      if (!validTypes.includes(file.type)) {
        showToast("Error", "Please upload a valid image file (jpg, png, gif)", "error");
        return;
      }

      const formData = new FormData();
      formData.append("profile_picture", file);

      fetch("../scripts/upload_profile_picture.php", {
        method: "POST",
        body: formData,
        credentials: "same-origin"
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showToast("Success", "Profile picture updated successfully!", "success");
            getUserProfile();

            // Update semua elemen gambar profil di UI
            const profilePicElements = document.querySelectorAll(
              "#navbar-profile-pic, #sidebar-profile-pic, #main-profile-pic"
            );
            profilePicElements.forEach((el) => {
              el.src = data.profile_picture + "?t=" + new Date().getTime(); // cache busting
            });
          } else {
            showToast("Error", data.message || "Failed to upload image.", "error");
          }
        })
        .catch((err) => {
          console.error("Error uploading profile picture:", err);
          showToast("Error", "Something went wrong during upload.", "error");
        });

      // Reset input agar bisa upload ulang file yang sama
      e.target.value = "";
    });

  }
}