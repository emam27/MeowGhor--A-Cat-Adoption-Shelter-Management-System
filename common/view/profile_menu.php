<?php
/** Shared role-independent profile menu. */
?>
<details class="profile-menu">
    <summary class="profile-toggle" aria-label="Open profile menu" title="Profile">
        <svg class="profile-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <circle cx="12" cy="8" r="3.5"></circle>
            <path d="M5.5 20c.7-3.3 3-5 6.5-5s5.8 1.7 6.5 5"></path>
        </svg>
    </summary>
    <div class="profile-dropdown">
        <a href="/MeowGhor/common/view/profile.php">My Profile</a>
        <a href="/MeowGhor/common/controller/AuthController.php?action=logout">Logout</a>
    </div>
</details>
