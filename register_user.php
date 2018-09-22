<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/forms/class_forms.php');

/* =====> Global Page Properties */
$currScreen = $ADD_USER_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Add User";

/* =====> PHP/mySQL Application Start */
$forms = new FORMS();

/* =====> Page Header Initialization */
include ('layout/page_start_block.php');
?>

<!-- Add User Screen JS
<link type="text/javascript" src="js/screens/add_user_screen.js?t=<=MD5(uniqid())?>" />
-->

<!-- Application Start -->
<script type="text/javascript">

    var isRoleAdmin = true;

    // @Override called from app.js
    function appInitialized()
    {
        trace("add user page initialized");

        initializeUserAdd();

        $( "#role" ).change(onRoleSelectChange);
    }

    function initializeUserAdd()
    {
        var $form		= $('.itemForm');
        $form.append( '<input type="hidden" name="ajax" value="1" />' );
        $form.on( 'submit', onSubmitAddUser);
    }

    function menuSelect(menuItem)
    {
        trace("menuSelect.menuItem: ", menuItem);

        hideDropdown();

        // get index
        const dataItemID = $(menuItem).attr('data-menu-id');
        const dataGroupID = $(menuItem).attr('data-menu-group');
        const dataItemIndex = $(menuItem).attr('data-menu-index');

        trace("menuSelect().dataItemID:", dataItemID);

        switch (dataItemID) {

            case 'users':
                window.location.replace("users.php?t=" + MD5(Date.now()));
                break;
        }
    }

    function onSubmitAddUser(e)
    {
        e.preventDefault();

        trace("onSubmitAddUser");

        // validate fields
        if ( !validateInputField($("input[name='name']"), "name") ) return;
        if ( !validateInputField($("input[name='email']"), "email") ) return;

        // validate password
        if (isRoleAdmin)
        {
            if (!validateInputField($("input[name='password']"), "password") ) return;
            if (!validateInputField($("input[name='password_confirm']"), "password_confirm") ) return;
            if (!validatePassword()) return;
        }

        var $form		= $('.itemForm');
        $form.append( '<input type="hidden" name="ajax" value="1" />' );

        var ajaxData    = new FormData( $form[0] );

        // check fields
        var i;
        for (i = 0; i < $form[0].length; i++)
        {
            if ($form[0][i].type == "hidden"
                || $form[0][i].type == "submit") continue;

            trace("addUser: ", $form[0][i].name + ': ' + $form[0][i].value);

            ajaxData.append( $form[0][i].name, $form[0][i].value );
        }

        // ajax request
        $.ajax(
        {
            url: 			'api/add_db_user.php',
            type:			$form.attr( 'method' ),
            data: 			ajaxData,
            dataType:		'json',
            cache:			false,
            contentType:	false,
            processData:	false,
            complete: function()
            {
                trace("addUser.complete");

            },
            success: function( data )
            {
                trace("addUser.success.data: ", data);
                $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                if( !data.success )
                {
                    showAlert("Error", data.error, "error");
                    updateInputFieldStatus(data.error, data.errorCode);
                }
                else
                {
                    showAlert("User Added",
                        "User has been registered.",
                        "success",
                        goToUsersScreen);
                }
            },
            error: function(request, status, error)
            {
                trace("addUser.error", error);

                showAlert("Error", error, "error");
            }
        });
    }

    function validateInputField(input, name)
    {
        name = name.charAt(0).toUpperCase() + name.substr(1).toLowerCase();

        if (input.val() < 5)
        {
            showAlert(name, name + " is missing or invalid", "error");

            getInputField(name);

            return false;
        }

        return true;
    }

    function validatePassword()
    {
        const password = $("input[name='password']");
        const passConfirm = $("input[name='password_confirm']");

        trace("password:", password.val());
        trace("passConfirm:", passConfirm.val());

        if (password.val().length == 0 || passConfirm.val().length == 0)
        {
            showAlert("Password", "Missing password", "error");

            return false;
        }

        if (password.val() != passConfirm.val())
        {
            showAlert("Password", "Password does not match.", "error");

            getInputField("password");
            getInputField("password_confirm");

            return false;
        }

        return true;
    }

    function onRoleSelectChange(e)
    {
        var roleID = $(this).val();

        trace("onRoleSelectChange().e:", roleID);

        switch (roleID)
        {
            case "admin":
                $("label[for='username']").parent().css("display", "block");
                $("label[for='password']").parent().css("display", "block");
                $("label[for='password_confirm']").parent().css("display", "block");
                isRoleAdmin = true;
                break;
            case "team":
                $("label[for='username']").parent().css("display", "none");
                $("label[for='password']").parent().css("display", "none");
                $("label[for='password_confirm']").parent().css("display", "none");
                isRoleAdmin = false;
                break;
        }
    }

</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel">
    <div class="innerItemPanel">
        <form class="itemForm" method="post" action="api/add_db_user.php" autocomplete="on"
              enctype="multipart/form-data">
            <ul>
                <li>
                    <label for="role">Role</label>
                    <select id="role" name="role" class="itemSelect arrowIcon">
                        <optgroup label="Choose Role">
                            <?php
                            echo $forms->generateRoleOptions($itemInfo['role']);
                            ?>
                        </optgroup>
                    </select>
                    <span>Note: Team users can only view with some features</span>
                </li>
                <li>
                    <label for="name">Name</label>
                    <input type="text" name="name" maxlength="100"/>
                    <span>Enter name here</span>
                </li>
                <li>
                    <label for="email">Email</label>
                    <input type="text" name="email" maxlength="100"/>
                    <span>Enter email here</span>
                </li>
                <li>
                    <label for="username">Username</label>
                    <input type="text" name="username" maxlength="100"/>
                    <span>Enter username</span>
                </li>
                <li>
                    <label for="password">Password</label>
                    <input type="text" name="password" maxlength="100"/>
                    <span>Enter password</span>
                </li>
                <li>
                    <label for="password_confirm">Confirm Password</label>
                    <input type="text" name="password_confirm" maxlength="100"/>
                    <span>Enter password again</span>
                </li>
                <li class="li-nopadding">
                    <div class='itemEditButton'>
                        <button type="submit" class='itemButton'>
                            <span class="navMenuIcon fas fa-plus-circle"></span>
                            <span class="navMenuItemText">ADD USER</span>
                        </button>
                    </div>
                </li>
            </ul>
        </form>
        <!-- End of Form -->
    </div>
</div>
<!-- Page Content End -->

<!-- Page Container End -->
<?php include ('layout/page_end_block.php'); ?>