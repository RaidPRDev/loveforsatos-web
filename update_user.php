<?php
/* =====> Page Initialization, Server Connections */
include ('layout/page_access_control.php');
include ('api/forms/class_forms.php');

/* =====> Global Page Properties */
$currScreen = $UPDATE_USER_SCREEN;
$pageTitle = "Love For Satos";
$pageDescription = "Update User";

/* =====> PHP/mySQL Application Start */
$forms = new FORMS();
$itemInfo = null;
$itemName = '';

if ( isset($_GET['id']) )
{
    // check if id exists
    $itemId = $_GET['id'];
    $resultData = $users->getUser($itemId);

    if ($resultData["success"])
    {
        $itemInfo = $resultData["userInfo"];
        $itemName = $itemInfo['name'];

        $removeLink = "onclick='removeUser(\"".$itemInfo["id"]."\", \"".$itemInfo["name"]."\")'";
    }
    else
    {
        $errorMessage = "Unable to retrieve user info.";
    }
}

/* =====> Page Header Initialization */
include ('layout/page_start_block.php');
?>

<!-- Add User Screen JS
<link type="text/javascript" src="js/screens/add_user_screen.js?t=<=MD5(uniqid())?>" />
-->

<!-- Application Start -->
<script type="text/javascript">

    var isSuperUser = false;
    var isRoleAdmin = true;
    var isPasswordChanged = false;

    // @Override called from app.js
    function appInitialized()
    {
        trace("update user page initialized");

        selectedItemId = parseInt('<?=$itemId ?>');
        selectedItemName = '<?=$itemName ?>';

        initializeUserUpdate();
     }

    function initializeUserUpdate()
    {
        const $form		= $('.itemForm');
        $form.append( '<input type="hidden" name="ajax" value="1" />' );
        $form.on( 'submit', onSubmitUpdateUser);

        // update the form by role id
        const $role = $("select[name='role']");
        $role.change(onRoleSelectChange);
        changeFormsByRoleID($role.val());
        isRoleAdmin = ($role.val() == 'admin') ? true : false;

        // name = master is a reserved keyword
        const $name = $("input[name='name']");
        isSuperUser = ($name.val() == "master");

        // monitor password confirm change *only for admin
        if (isRoleAdmin)
        {
            const $passConfirm = $("input[name='password_confirm']");
            // $passConfirm.change(onPasswordConfirmChange);
            $passConfirm.on('input', onPasswordConfirmChange);
        }
        else
        {
            // disable role select for team
            $role.prop('disabled', true);
        }

        // disable name and role for superuser
        if (isSuperUser)
        {
            $name.prop('disabled', true);
            $role.prop('disabled', true);

            // get span bottom text
            const $spanRole = $role.parent().find('span');
            trace("$spanRole", $spanRole);
            $spanRole.html("Master account role can't be removed nor modified");

            const $spanName = $name.parent().find('span');
            trace("$spanName", $spanName);
            $spanName.html("Master account name can't be removed nor modified");

            const $removeHeaderButton = $('li[data-menu-id="remove"]');
            $removeHeaderButton.css("display", "none");

            const $removeButton = $('.itemRemoveButton');
            $removeButton.css("display", "none");
        }
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

            case 'remove':
                // removeUser(menuItem, dataItemID);
                removeUser(selectedItemId, selectedItemName);
                break;

            case 'update':
                onUpdateUserSelect(menuItem);
                break;

            case 'users':
                goToUsersScreen();
                break;
        }
    }

    function onUpdateUserSelect(navItem)
    {
        trace("onUpdateUserSelect.navItem: ", navItem);

        $form = $('.itemForm');
        $form.trigger( 'submit' );
    }

    function onSubmitUpdateUser(e)
    {
        e.preventDefault();

        trace("onSubmitUpdateUser.isRoleAdmin: ", isRoleAdmin);

        // validate fields
        if ( !validateInputField($("input[name='name']"), "name") ) return;
        if ( !validateInputField($("input[name='email']"), "email") ) return;

        // validate password
        if (isRoleAdmin)
        {
            if (!validateInputField($("input[name='password']"), "password") ) return;
            if (isPasswordChanged)
            {
                if (!validateInputField($("input[name='password_confirm']"), "password_confirm") ) return;
                if (!validatePassword()) return;
            }
        }

        var $form		= $('.itemForm');
        $form.append( '<input type="hidden" name="ajax" value="1" />' );

        var ajaxData    = new FormData( $form[0] );

        // check fields
        var i;
        for (i = 0; i < $form[0].length; i++)
        {
            if ($form[0][i].type == "hidden"
                || $form[0][i].type == "submit"
                || $form[0][i].name == "" ) continue;

            if (!isRoleAdmin)
            {
                if ($form[0][i].name == "username"
                || $form[0][i].name == "password"
                || $form[0][i].name == "password_confirm")
                {
                    continue;
                }
            }

            trace("updateUser: ", $form[0][i].name + ': ' + $form[0][i].value);

            ajaxData.append( $form[0][i].name, $form[0][i].value );
        }

        trace("ajaxData:", ajaxData);

        // ajax request
        $.ajax(
        {
            url: 			'api/update_db_user.php',
            type:			$form.attr( 'method' ),
            data: 			ajaxData,
            dataType:		'json',
            cache:			false,
            contentType:	false,
            processData:	false,
            complete: function()
            {
                trace("updateUser.complete");

            },
            success: function( data )
            {
                trace("updateUser.success.data: ", data);
                $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                if( !data.success )
                {
                    showAlert("Error", data.error, "error");
                }
                else
                {
                    showAlert("User Update",
                        "User info has been saved.",
                        "success",
                        reloadLocation);
                }
            },
            error: function(request, status, error)
            {
                trace("updateUser.error", error);

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
        const roleID = $(this).val();

        changeFormsByRoleID(roleID);
    }

    function changeFormsByRoleID(roleID)
    {
        var userDiv = $("label[for='username']").parent();
        var passDiv = $("label[for='password']").parent();
        var passConfirmDiv = $("label[for='password_confirm']").parent();

        var userField = userDiv.find("input[name='username']");
        var passField = passDiv.find("input[name='password']");
        var passConfirmField = passConfirmDiv.find("input[name='password_confirm']");

        switch (roleID)
        {
            case "admin":
                userDiv.css("display", "block");
                passDiv.css("display", "block");
                passConfirmDiv.css("display", "block");

                // monitor password confirm change
                // passConfirmDiv.change(onPasswordConfirmChange);
                passConfirmDiv.on('input', onPasswordConfirmChange);

                isRoleAdmin = true;
                break;
            case "team":
                userField.val("");
                passField.val("");
                passConfirmField.val("");
                userDiv.css("display", "none");
                passDiv.css("display", "none");
                passConfirmDiv.css("display", "none");

                // monitor password confirm change
                // passConfirmDiv.unbind("change", onPasswordConfirmChange);
                passConfirmDiv.off('input', onPasswordConfirmChange);

                isRoleAdmin = false;
                break;
        }
    }

    function onPasswordConfirmChange(e)
    {
        trace("onPasswordConfirmChange");

        isPasswordChanged = true;
    }

    /*
        Removes current itemID data and photos from Server
     */
    function removeUser(id, name)
    {
        removeUserFromDatabase(id, name);
    }

    function removeUserFromDatabase(itemID, itemName)
    {
        trace("removeFromDatabase.itemID:", itemID + ' itemName: ' + itemName);

        if (isSuperUser)
        {
            showAlert("Error", "Master account can't be removed.", "error");

            return;
        }

        swal({
            title: "Remove " + itemName + "?",
            text: "You will not be able to recover this data!",
            icon: "warning",
            closeOnClickOutside: false,
            closeOnEsc: false,
            buttons: ["CANCEL","REMOVE"],
            dangerMode: true,
            className: ""
        })
        .then(function(willDelete)
        {
            if (!willDelete) return;

            const ajaxData    = new FormData();
            ajaxData.append( "itemID", itemID );

            // ajax request
            $.ajax(
            {
                url: 			'api/remove_db_user.php',
                type:			'post',
                data: 			ajaxData,
                dataType:		'json',
                cache:			false,
                contentType:	false,
                processData:	false,
                complete: function()
                {
                    trace("removeUserFromDatabase.complete");
                },
                success: function( data )
                {
                    trace("removeUserFromDatabase.success.data: ", data);
                    if( !data.success )
                    {
                        showAlert("Error", data.error, "error");
                        updateInputFieldStatus(data.error, data.errorCode);
                    }
                    else
                    {
                        showAlert("User Removed",
                            "Your data has been deleted.",
                            "success",
                            goToUsersScreen);
                    }
                },
                error: function(request, status, error)
                {
                    trace("removeItemFromDatabase.error", error);

                    showAlert("Error", error, "error");
                }
            });
        });
    }

</script>

<!-- Page Container Start -->
<?php include ('layout/page_mid_block.php'); ?>

<!-- Page Content Start -->
<div class="itemPanel">
    <div class="innerItemPanel">
        <form class="itemForm" method="post" action="api/update_db_user.php" autocomplete="on"
              enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?=$itemInfo['id'];?>"/>
            <ul>
                <li>
                    <label for="role">Role</label>
                    <select name="role" class="itemSelect arrowIcon">
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
                    <input type="text" name="name" value="<?=$itemInfo['name']?>" maxlength="100"/>
                    <span>Enter name here</span>
                </li>
                <li>
                    <label for="email">Email</label>
                    <input type="text" name="email" value="<?=$itemInfo['email']?>" maxlength="100"/>
                    <span>Enter email here</span>
                </li>
                <li>
                    <label for="username">Username</label>
                    <input type="text" name="username" value="<?=$itemInfo['username']?>" maxlength="100"/>
                    <span>Enter username</span>
                </li>
                <li>
                    <label for="password">Password</label>
                    <input type="text" name="password" value="<?=$itemInfo['password']?>" maxlength="100"/>
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
                            <span class="navMenuIcon fas fa-check-circle"></span>
                            <span class="navMenuItemText">UPDATE USER</span>
                        </button>
                    </div>
                    <div class='itemRemoveButton'>
                        <button type="button" <?=$removeLink?>  class='itemButton error'>
                            <span class="navMenuIcon fas fa-trash-alt"></span>
                            <span class="navMenuItemText">REMOVE</span>
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