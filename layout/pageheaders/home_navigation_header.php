<header class="headerNavigation" data-logged="<?=$isUserLogged?>">
<div class="innerNavigation">
    <div class="itemNavElement left">
        <div class="itemNav">
            <ul>
                <?php include_once $ROOT_PATH . '/layout/page_header_menu.php'; ?>
            </ul>
        </div>
    </div>
    <div class="itemNavElement center">
        <h1 class="itemNavTitle">
            <span><img class="item-logo" src="images/loveforsatos-icon.svg"/></span>
            <!--<i class="fas fa-heart page-header-icon home"></i>&nbsp;<=$pageDescription?>-->
        </h1>
    </div>
    <div class="itemNavElement right">
        <div class="itemNav">
            <ul>
                <li class="navMenuItem" data-menu-id="1" onclick="mainNavSelect(this)">
                    <div class="navItemIcon"><span class="fas fa-cog"></span> </div>
                    <div class="dropdown-content right">
                        <a class='menu-sub-title' href="#">Order</a>
                        <a href="#" data-menu-id="ascending" data-menu-index="0" data-menu-group="0"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-sort-amount-up"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Ascending</span>
                        </a>
                        <a href="#" data-menu-id="descending" data-menu-index="1" data-menu-group="0"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-sort-amount-down"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Descending</span>
                        </a>
                        <a class='menu-line' href="#"></a>
                        <a class='menu-sub-title' href="#">Show</a>
                        <!--<a href="#" data-menu-id="updated" data-menu-index="0" data-menu-group="1"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-check-circle"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Modified</span>
                        </a>
                        <a href="#" data-menu-id="created" data-menu-index="1" data-menu-group="1"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-check-circle"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Created</span>
                        </a>-->
                        <a href="#" data-menu-id="name" data-menu-index="0" data-menu-group="1"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-check-circle"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Name</span>
                        </a>
                        <a href="#" data-menu-id="age" data-menu-index="1" data-menu-group="1"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-check-circle"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Age</span>
                        </a>
                        <a href="#" data-menu-id="gender" data-menu-index="2" data-menu-group="1"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-check-circle"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Gender</span>
                        </a>
                        <a href="#" data-menu-id="fixed" data-menu-index="3" data-menu-group="1"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-check-circle"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Fixed</span>
                        </a>
                        <a href="#" data-menu-id="adopted" data-menu-index="4" data-menu-group="1"
                           onclick="onFilterSelect(this)">
                            <span class="navMenuOptionIcon fas fa-check-circle"></span>
                            <span class="itemLabel">&nbsp;&nbsp;Promised</span>
                        </a>

                    </div>
                </li>
                <li class="navMenuItem" data-menu-id="2" data-logged="<?=$isUserLogged?>" onclick="mainNavSelect(this)">
                    <div class="navItemIcon"><span class="fas fa-lock"></span></div>
                </li>
            </ul>
        </div>
    </div>
</div>
</header>