<header class="bg-white shadow-md fixed w-full z-20 top-0">
    <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold text-blue-600">Inovasi Digital</a>
        <div class="hidden md:flex space-x-6 items-center">
            <a href="index.php" class="text-gray-600 hover:text-blue-600">Home</a>
            <a href="about.php" class="text-gray-600 hover:text-blue-600">About Us</a>
            <a href="Potensi.php" class="text-gray-600 hover:text-blue-600">Potensi</a>
            <a href="index.php#gallery" class="text-gray-600 hover:text-blue-600">Gallery</a>
            <a href="index.php#contact" class="text-gray-600 hover:text-blue-600">Contact</a>
        </div>
        <div id="auth-section" class="hidden md:flex">
            <div class="flex items-center space-x-2">
                <button id="login-btn" onclick="showLoginModal()"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Login
                </button>
                <button id="register-btn" onclick="showRegisterModal()"
                    class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100 border">
                    Register
                </button>
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