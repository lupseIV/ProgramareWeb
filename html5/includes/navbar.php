<svg style="display: none;">
    <symbol id="dashboard-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M3 13h8v-10h-8v10zm0 8h8v-6h-8v6zm10 0h8v-10h-8v10zm0-18v6h8v-6h-8z"></path>
    </symbol>
    <symbol id="contact-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path>
    </symbol>
    <symbol id="home-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
    </symbol>
</svg>

<header>
    <h1>Management Resurse Inteligent</h1>
    <nav>
        <?php if (isLogged()) : ?>
        <div class="nav-item">
            <svg fill="#3b82f6" height="24" width="24">
                <use href="#dashboard-icon"></use>
            </svg>
            <a href="?page=dashboard">DashBoard</a>
        </div>
        <?php endif; ?>
        <div class="nav-item">
            <svg fill="#3b82f6" height="24" width="24">
                <use href="#contact-icon"></use>
            </svg>
            <a href="?page=contact">Contact</a>
        </div>
        <div class="nav-item">
            <svg fill="#3b82f6" height="24" width="24">
                <use href="#home-icon"></use>
            </svg>
            <a href="?page=home">Home</a>
        </div>

        <div class="nav-item login-btn">
            <?php if (!isLogged()) : ?>
                <a href="?page=login">Autentificare</a>
            <?php else:?>
                <a href="?page=logout">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
</header>