<header class="bg-white shadow-md fixed w-full z-20 top-0">
    <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold text-blue-600">Inovasi Digital</a>
        <div class="hidden md:flex space-x-6 items-center">
            <a href="index.php" class="text-gray-600 hover:text-blue-600">Home</a>
            <a href="about.php" class="text-gray-600 hover:text-blue-600">About Us</a>
            <a href="Potensi.php" class="text-gray-600 hover:text-blue-600">Potensi</a>
            <a href="Gallery.php" class="text-gray-600 hover:text-blue-600">Gallery</a>
            <a href="index.php#contact" class="text-gray-600 hover:text-blue-600">Contact</a>
            <!-- Admin link (tampil hanya untuk admin) -->
            <a id="admin-link" href="admin.php" class="text-red-600 hover:text-red-700 hidden">Admin Panel</a>
        </div>
        <div id="auth-section" class="hidden md:flex items-center space-x-3">
            <!-- default (not logged in) -->
            <div id="anon-actions" class="flex items-center space-x-2">
                <button id="login-btn" onclick="showLoginModal()"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Login
                </button>
                <button id="register-btn" onclick="showRegisterModal()"
                    class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100 border">
                    Register
                </button>
            </div>

            <!-- when logged in -->
            <div id="user-actions" class="hidden items-center space-x-3">
                <span id="welcome-text" class="text-gray-700"></span>
                <a id="my-admin" href="admin.php" class="text-sm text-red-600 hover:underline hidden">Admin</a>
                <button id="logout-btn" class="bg-red-600 text-white px-4 py-2 rounded">Logout</button>
            </div>
        </div>
        <button id="mobile-menu-button" class="md:hidden text-gray-600 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </nav>
    <div id="mobile-menu" class="hidden md:hidden">
        <a href="index.php#home" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Home</a>
        <a href="about.php" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">About Us</a>
        <a href="index.php#services" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Services</a>
        <a href="index.php#gallery" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Gallery</a>
        <a href="index.php#clients" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Clients</a>
        <a href="index.php#contact" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-200">Contact</a>
    </div>
</header>

<script>
    // simple client-side toggle for header controls
    (function () {
        const token = localStorage.getItem('auth_token');
        const role = localStorage.getItem('auth_role');      // optional: set this at login
        const name = localStorage.getItem('auth_user_name'); // optional: set at login

        const anon = document.getElementById('anon-actions');
        const user = document.getElementById('user-actions');
        const welcome = document.getElementById('welcome-text');
        const adminLink = document.getElementById('admin-link');
        const myAdmin = document.getElementById('my-admin');

        function showLoggedIn() {
            anon.classList.add('hidden');
            user.classList.remove('hidden');
            welcome.textContent = name ? `Welcome, ${name}` : 'Welcome';
            if (role === 'admin') {
                adminLink.classList.remove('hidden');
                myAdmin.classList.remove('hidden');
            } else {
                adminLink.classList.add('hidden');
                myAdmin.classList.add('hidden');
            }
        }
        function showAnon() {
            anon.classList.remove('hidden');
            user.classList.add('hidden');
            adminLink.classList.add('hidden');
            myAdmin.classList.add('hidden');
        }

        if (token) {
            // quick path: if role stored in localStorage use it
            if (role) showLoggedIn();
            else {
                // fallback: try to verify token and get user info from server
                fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'me', token })
                }).then(r => r.json()).then(j => {
                    if (j && j.user) {
                        localStorage.setItem('auth_user_name', j.user.name || '');
                        localStorage.setItem('auth_role', j.user.role || '');
                        showLoggedIn();
                    } else showAnon();
                }).catch(() => showAnon());
            }
        } else showAnon();

        document.getElementById('logout-btn').addEventListener('click', async () => {
            const t = localStorage.getItem('auth_token');
            if (t) {
                try {
                    await fetch('api/logout.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ token: t })
                    });
                } catch (e) { }
            }
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_role');
            localStorage.removeItem('auth_user_name');
            location.reload();
        });
    })();
</script>