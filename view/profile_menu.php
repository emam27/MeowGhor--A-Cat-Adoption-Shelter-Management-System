<?php
/**
 * Shared role-independent profile elements for adopter and shelter staff navbars.
 */
?>
<!-- Direct link on the navigation bar -->
<!-- Update this project-relative URL if the future profile page uses different routing. -->
<a href="common/view/profile.php" class="nav-profile-btn">My Profile</a>

<!-- Streamlined dropdown for logout and other settings -->
<details class="profile-menu">
    <summary class="profile-toggle" aria-label="Open profile menu" title="More Options">
    </summary>

    <div class="profile-dropdown">
        <a href="../../common/controller/AuthController.php?action=logout">Logout</a>
    </div>
</details>

