import { showSnackbar } from "../components/snackbarAuth.js";

function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(";").shift();
}

document.addEventListener("DOMContentLoaded", () => {
  const rememberMeCookie = getCookie("remember_me");
  if (rememberMeCookie) {
    document.getElementById("email").value = rememberMeCookie;
    document.getElementById("loginForm").submit();
  }

  // 🔐 Login
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value.trim();

      if (!email || !password) {
        showSnackbar("Please fill in all fields", "error");
        return;
      }

      try {
        const res = await fetch("../scripts/login.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ email, password }),
        });

        const data = await res.json();
        showSnackbar(data.message, data.success ? "success" : "error");

        if (data.success) {
          setTimeout(() => {
            window.location.href = "../index.php";
          }, 500);
        }
      } catch (error) {
        console.error(error);
        showSnackbar("An error occurred during login", "error");
      }
    });
  }

  // 📝 Register
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    const passwordInput = document.getElementById("password");
    const passwordStrength = document.querySelector(".password-strength");

    passwordInput.addEventListener("input", function () {
      const password = this.value;
      let strength = 0;
      if (password.length >= 8) strength++;
      if (/[a-z].*[A-Z]|[A-Z].*[a-z]/.test(password)) strength++;
      if (/\d/.test(password)) strength++;
      if (/[!@#$%^&*?_~]/.test(password)) strength++;

      const strengthBars = passwordStrength.querySelectorAll("div");
      strengthBars.forEach((bar, i) => {
        bar.classList.remove("bg-red-500", "bg-yellow-500", "bg-green-500");
        if (i < strength) {
          bar.classList.add(strength <= 1 ? "bg-red-500" : strength <= 3 ? "bg-yellow-500" : "bg-green-500");
        } else {
          bar.classList.add("bg-gray-200");
        }
      });
    });

    registerForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirm-password").value;
      const terms = document.getElementById("terms").checked;

      if (!name || !email || !password || !confirmPassword) {
        showSnackbar("Please fill in all fields", "error");
        return;
      }

      if (password !== confirmPassword) {
        showSnackbar("Passwords do not match", "error");
        return;
      }

      if (!terms) {
        showSnackbar("You must agree to the terms and conditions", "error");
        return;
      }

      try {
        const res = await fetch("../scripts/register.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ name, email, password }),
        });

        const data = await res.json();
        showSnackbar(data.message, data.success ? "success" : "error");

        if (data.success) {
          registerForm.reset();
          setTimeout(() => {
            window.location.href = "login.html";
          }, 1500);
        }
      } catch (error) {
        console.error(error);
        showSnackbar("An error occurred during registration", "error");
      }
    });
  }
});
