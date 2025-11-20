// Mobile Menu (safe guard)
const mobileMenuButton = document.getElementById("mobile-menu-button");
const mobileMenu = document.getElementById("mobile-menu");
if (mobileMenuButton && mobileMenu) {
  mobileMenuButton.addEventListener("click", () => {
    mobileMenu.classList.toggle("hidden");
  });
}

// === BAGIAN UTAMA ===
const loginModal = document.getElementById("login-modal");
const authSection = document.getElementById("auth-section");
const loginForm = document.getElementById("login-form");
const errorMessage = document.getElementById("error-message");

function showLoginModal() {
  if (loginModal) loginModal.classList.remove("hidden");
}
function hideLoginModal() {
  if (loginModal) loginModal.classList.add("hidden");
  if (errorMessage) errorMessage.classList.add("hidden");
}

// expose register modal controls
window.showRegisterModal = function () {
  const r = document.getElementById("register-modal");
  if (r) r.classList.remove("hidden");
};
window.hideRegisterModal = function () {
  const r = document.getElementById("register-modal");
  const re = document.getElementById("register-error");
  if (r) r.classList.add("hidden");
  if (re) {
    re.classList.add("hidden");
    re.className = "text-red-500 text-sm mb-4 hidden"; // Reset warna
  }
};

function updateUIForLoggedIn(user, role) {
  let adminLink = '';
  if (role === 'admin') {
    adminLink = '<a href="admin.php" class="text-sm text-red-600 hover:text-red-800 font-medium ml-4">Admin Panel</a>';
  }

  if (authSection) {
    authSection.innerHTML =
      '<div class="flex items-center space-x-4">' +
      adminLink +
      '<span class="text-gray-700">Welcome, ' +
      (user || '') +
      "</span>" +
      '<button onclick="logout()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</button>' +
      "</div>";
  }

  // --- Perbarui Navigasi Mobile ---
  const mobileRegisterBtn = document.querySelector(
    '#mobile-menu button[onclick="showRegisterModal()"]'
  );
  const mobileLoginBtn = document.querySelector(
    '#mobile-menu button[onclick="showLoginModal()"]'
  );
  if (mobileLoginBtn) mobileLoginBtn.style.display = "none";
  if (mobileRegisterBtn) mobileRegisterBtn.style.display = "none";
}

function updateUIForLoggedOut() {
  if (authSection) {
    authSection.innerHTML =
      '<button id="login-btn" onclick="showLoginModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Login</button>' +
      '<button id="register-btn" onclick="showRegisterModal()" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100 border">Register</button>';
  }

  // --- Perbarui Navigasi Mobile ---
  const mobileRegisterBtn = document.querySelector(
    '#mobile-menu button[onclick="showRegisterModal()"]'
  );
  const mobileLoginBtn = document.querySelector(
    '#mobile-menu button[onclick="showLoginModal()"]'
  );
  if (mobileLoginBtn) mobileLoginBtn.style.display = "block";
  if (mobileRegisterBtn) mobileRegisterBtn.style.display = "block";
}

async function logout() {
  const token = localStorage.getItem("auth_token");

  if (token) {
    try {
      await fetch("api/logout.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ token: token }),
      });
    } catch (err) {
      console.error("Gagal menghubungi server untuk logout:", err);
    }
  }

  // Konsisten: hapus semua key yang digunakan
  localStorage.removeItem("auth_user_name");
  localStorage.removeItem("auth_role");
  localStorage.removeItem("auth_token");
  localStorage.removeItem("loggedInUser");
  localStorage.removeItem("userRole");

  updateUIForLoggedOut();
}

if (loginForm) {
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (errorMessage) errorMessage.classList.add("hidden");

    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    if (!username || !password) {
      if (errorMessage) {
        errorMessage.textContent = "Please enter a username and password.";
        errorMessage.classList.remove("hidden");
      }
      return;
    }

    try {
      const res = await fetch("api/login.php", { // Panggil login.php
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password }),
      });

      const resJson = await res.json();

      if (res.ok && resJson.token) {
          // simpan dengan key yang konsisten
          localStorage.setItem('auth_token', resJson.token);
          localStorage.setItem('auth_user_name', resJson.user?.name || username);
          localStorage.setItem('auth_role', resJson.user?.role || 'user');

          // juga simpan legacy keys supaya bagian lain tetap kompatibel
          localStorage.setItem('loggedInUser', resJson.user?.name || username);
          localStorage.setItem('userRole', resJson.user?.role || 'user');

          // perbarui UI tanpa reload jika mungkin
          updateUIForLoggedIn(localStorage.getItem('auth_user_name'), localStorage.getItem('auth_role'));
          hideLoginModal();
      } else {
        if (errorMessage) {
          errorMessage.textContent = resJson.error || "Login gagal.";
          errorMessage.classList.remove("hidden");
        }
      }
    } catch (err) {
      if (errorMessage) {
        errorMessage.textContent = "Tidak dapat terhubung ke server.";
        errorMessage.classList.remove("hidden");
      }
    }
  });
}

const registerForm = document.getElementById("register-form");
if (registerForm) {
  registerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const username =
      (document.getElementById("reg-username") || {}).value || "";
    const email = (document.getElementById("reg-email") || {}).value || "";
    const password =
      (document.getElementById("reg-password") || {}).value || "";
    const errEl = document.getElementById("register-error");

    if (!username || !email || !password) {
      if (errEl) {
        errEl.textContent = "Semua field wajib diisi.";
        errEl.className = "text-red-500 text-sm mb-4"; // Pastikan warna merah
        errEl.classList.remove("hidden");
      }
      return;
    }

    try {
      const res = await fetch("api/register.php", { 
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password, email }),
      });

      const data = await res.json();
      
      if (res.status === 201) {
        if (errEl) {
          errEl.textContent = data.message || "Registrasi berhasil! Silakan login.";
          errEl.className = "text-green-500 text-sm mb-4"; // Ganti warna jadi hijau
          errEl.classList.remove("hidden");
        }
        registerForm.reset();
        // Tutup modal setelah 2 detik
        setTimeout(hideRegisterModal, 2000);
      } else {
        if (errEl) {
          errEl.textContent = data.error || "Gagal register.";
          errEl.className = "text-red-500 text-sm mb-4"; // Pastikan warna merah
          errEl.classList.remove("hidden");
        }
      }
    } catch (err) {
      if (errEl) {
        errEl.textContent = "Tidak dapat terhubung ke server. " + err.message;
        errEl.className = "text-red-500 text-sm mb-4";
        errEl.classList.remove("hidden");
      }
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  // --- Cek Status Login --- (pakai kunci yang konsisten)
  const user = localStorage.getItem("auth_user_name") || localStorage.getItem("loggedInUser");
  const role = localStorage.getItem("auth_role") || localStorage.getItem("userRole");
  if (user) {
    updateUIForLoggedIn(user, role);
  } else {
    updateUIForLoggedOut();
  }

  async function loadArticles() {
    const articleDropdown = document.querySelector(".relative.group .group-hover\\:block");
    if (!articleDropdown) return;
    try {
        const res = await fetch("api/get_articles.php");
        if (!res.ok) {
           articleDropdown.innerHTML = '<span class="block px-4 py-2 text-sm text-red-500">Gagal memuat.</span>';
           return;
        }
        const articles = await res.json();
        articleDropdown.innerHTML = ''; 
        if (articles.length > 0) {
            articles.forEach(article => {
                const link = document.createElement('a');
                link.href = 'view_article.php?id=' + article.id; 
                link.textContent = article.title;
                link.className = 'block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50';
                articleDropdown.appendChild(link);
            });
        } else {
            articleDropdown.innerHTML = '<span class="block px-4 py-2 text-sm text-gray-500">Belum ada artikel.</span>';
        }
    } catch (err) {
        console.error("Gagal memuat artikel:", err);
        articleDropdown.innerHTML = '<span class="block px-4 py-2 text-sm text-red-500">Gagal memuat.</span>';
    }
  }

  loadArticles();
});
