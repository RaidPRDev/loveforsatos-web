<?php
/**
 * Created by IntelliJ IDEA.
 * User: fania
 * Date: 9/17/2018
 * Time: 9:23 PM
 */
?>
    <li class="navMenuItem" data-menu-id="0" onclick="mainNavSelect(this)">
        <div class="navItemIcon"><span class="fas fa-bars"></span> </div>
        <div class="dropdown-content">
            <a class='menu-sub-title' href="#">Menu</a>
            <a href="#" data-menu-id="home" onclick="navMenuSelect(this)">
                <span class="navMenuIcon fas fa-home"></span>
                <span class="itemLabel">&nbsp;&nbsp;Home</span>
            </a>
            <?php if ($isUserLogged) { ?>

                <a href="#" data-menu-id="createlist" onclick="navMenuSelect(this)">
                    <span class="navMenuIcon fas fa-list-alt"></span>
                    <span class="itemLabel">&nbsp;&nbsp;Lists</span>
                </a>
                <a href="#" data-menu-id="users" onclick="navMenuSelect(this)">
                    <span class="navMenuIcon fas fa-users"></span>
                    <span class="itemLabel">&nbsp;&nbsp;Users</span>
                </a>

                <a class='menu-line' href="#"></a>

                <a class='menu-sub-title' href="#">Options</a>
                <a href="#" data-menu-id="additem" onclick="navMenuSelect(this)">
                    <span class="navMenuIcon fas fa-plus-circle"></span>
                    <span class="itemLabel">&nbsp;&nbsp;Add Item</span>
                </a>
                <a href="#" data-menu-id="addlist" onclick="navMenuSelect(this)">
                    <span class="navMenuIcon fas fa-plus-circle"></span>
                    <span class="itemLabel">&nbsp;&nbsp;Add List</span>
                </a>

                <!--<a href="#" data-menu-id="adduser" onclick="navMenuSelect(this)">
                    <span class="navMenuIcon fas fa-user-plus"></span>
                    <span class="itemLabel">&nbsp;&nbsp;Add User</span>
                </a>-->

            <?php } ?>
        </div>
    </li>