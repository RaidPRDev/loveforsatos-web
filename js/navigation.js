/* NAVIGATION COMPONENT */

const HOME = 0;
const SETTINGS = 1;
const VIEW_SETTINGS = 0;
const LOCK_ITEM = 2;
const ADD_ITEM = 3;
const UPDATE_ITEM = 4;
const REMOVE_ITEM = 5;
const ADD_USER = 6;

const SETTINGS_SORT_LABEL = "filter_sort_index";
const SETTINGS_SHOW_LABEL = "filter_show_index";
const SETTINGS_SORT_INDEX = 0;
const SETTINGS_SHOW_INDEX = 1;

var settings = {};
settings[SETTINGS_SORT_INDEX] = SETTINGS_SORT_LABEL;
settings[SETTINGS_SHOW_INDEX] = SETTINGS_SHOW_LABEL;

var sortListSelected = 0;
var showListSelected = 0;

function mainNavSelect(navItem)
{
    trace("mainNavSelect.navItem: ", navItem);

    // get index
    var dataItemID = parseInt($(navItem).attr('data-menu-id'));

    trace("mainNavSelect.dataItemID:", dataItemID);

    switch (dataItemID) {

        case HOME:
        case SETTINGS:
            dropDownSelect(navItem);
            break;
        case ADD_USER:
        case LOCK_ITEM:
            trace("navItem.logged",$(navItem).attr('data-logged'));
            var userLogged = (parseInt($(navItem).attr('data-logged')) == 1) ? true : false;
            unlockSite(userLogged);
            break;
    }
}

function navMenuSelect(menuItem)
{
    trace("menuItem: ", menuItem);

    hideDropdown();

    // get index
    const dataItemID = $(menuItem).attr('data-menu-id');
    const dataGroupID = $(menuItem).attr('data-menu-group');
    const dataItemIndex = $(menuItem).attr('data-menu-index');

    trace("navMenuSelect().dataItemID:", dataItemID);

    switch (dataItemID) {

        case 'home':
            window.location.replace("satos.php?t=" + MD5(Date.now()));
            break;
        case 'home_view':
            window.location.replace("view.php?t=" + MD5(Date.now()));
            break;
        case 'additem':
            window.location.replace("add_item.php?t=" + MD5(Date.now()));
            break;
        case 'createlist':
            window.location.replace("createlist_home.php?t=" + MD5(Date.now()));
            break;
        case 'users':
            window.location.replace("users.php?t=" + MD5(Date.now()));
            break;
        case 'adduser':
            window.location.replace("register_user.php?t=" + MD5(Date.now()));
            break;
        case 'addlist':
            window.location.replace("add_playlist.php?t=" + MD5(Date.now()));
            break;
        case LOCK_ITEM:
            trace("navItem.logged",$(navItem).attr('data-logged'));
            var userLogged = (parseInt($(navItem).attr('data-logged')) == 1) ? true : false;
            unlockSite(userLogged);
            break;
    }
}

function onFilterSelect(menuItem)
{
    trace("onFilterSelect.menuItem: ", menuItem);

    hideDropdown();

    // get index
    const dataItemID = $(menuItem).attr('data-menu-id');
    const dataGroupID = $(menuItem).attr('data-menu-group');
    const dataItemIndex = $(menuItem).attr('data-menu-index');

    trace("onFilterSelect().dataItemID:", dataItemID);

    switch (dataItemID) {

        case 'ascending':
        case 'descending':
        case 'name':
        case 'age':
        case 'gender':
        case 'fixed':
        case 'adopted':
        case 'updated':
        case 'created':
            trace('Option Selected: ', dataGroupID, dataItemIndex);
            dropSetSelectedMenuIndex(dataGroupID, dataItemIndex, menuItem);
            reloadLocation();
            break;
    }
}

function addItemNavSelect(navItem)
{
    trace("addItemNavSelect.navItem: ", navItem);

    // get index
    var dataItemID = parseInt($(navItem).attr('data-menu-id'));

    trace("addItemNavSelect().dataItemID:", dataItemID);

    switch (dataItemID) {

        case ADD_ITEM:
            onUpdateItemSelect(navItem);
            break;
    }
}

function updateNavSelect(navItem)
{
    trace("updateNavSelect.navItem: ", navItem);

    // get index
    var dataItemID = parseInt($(navItem).attr('data-menu-id'));

    trace("updateNavSelect().dataItemID:", dataItemID);

    switch (dataItemID) {

        case UPDATE_ITEM:
            onUpdateItemSelect(navItem);
            break;
        case REMOVE_ITEM:
            onRemoveItemSelect(navItem);
            break;
    }
}

function onUpdateItemSelect(navItem)
{
    trace("onUpdateItemSelect.navItem: ", navItem);

    $form = $('.itemForm');
    $form.trigger( 'submit' );
}

function onRemoveItemSelect(navItem)
{
    removeItem(selectedItemId, selectedItemName);
}

function unlockSite(userLogged)
{
    trace("unlockSite.userLogged", userLogged)
    if (userLogged) signOut(); else signIn()
}

function signIn()
{
    var username = '';
    var password = '';
    var pin = '';

    swal({
        title: "Enter username",
        content: "input",
        icon: "info",
        closeOnClickOutside: false,
        closeOnEsc: false,
        dangerMode: false,
        buttons: ["CANCEL", "SUBMIT"]
    })
    .then(function(value)
    {
        if (value == null) {
            swal.close();
            return;
        }

        if (value === "") {
            swal.close();
            showAlert("Invalid Username", "Something is wrong with the username, try again.", "error", signIn);
            return;
        }

        trace("signIn.value ", value);
        username = value;
        swal({
            title: "Enter your password",
            content: {
                element: "input",
                attributes: {
                    type: "password",
                }
            },
            icon: "info",
            closeOnClickOutside: false,
            closeOnEsc: false,
            dangerMode: false,
            buttons: ["CANCEL", "SUBMIT"]
        })
        .then(function(value)
        {
            if (value == null) {
                swal.close();
                return;
            }

            if (value === "") {
                swal.close();
                showAlert("Invalid Password", "Something is wrong with the password, try again.", "error", signIn);
                return;
            }

            password = value;
            if (value)
            {
                var ajaxData    = new FormData();
                ajaxData.append( "username", username );
                ajaxData.append( "password", password );

                // ajax request
                $.ajax(
                {
                    url: 			'api/sign_user.php',
                    type:			'POST',
                    data: 			ajaxData,
                    dataType:		'json',
                    cache:			false,
                    contentType:	false,
                    processData:	false,
                    complete: function()
                    {
                        trace("SIGNIN.complete");
                        // $form.removeClass( 'is-uploading' );
                    },
                    success: function( data )
                    {
                        trace("SIGNIN.success.data: ", data);
                        // $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                        if( !data.success )
                        {
                            showAlert("Error", data.error, "error");
                        }
                        else
                        {
                            signInComplete();
                        }
                    },
                    error: function(request, status, error)
                    {
                        trace("SIGNIN.request", request);
                        trace("SIGNIN.responseText", request.responseText);
                        trace("SIGNIN.status", status);
                        trace("SIGNIN.error", error);

                        showAlert("Error", error, "error");
                    }
                });
            }
        });
    });
}

function signInComplete()
{
    isUserLogged = true;
    updateUserLock();

    showAlert("Authorized", "Your have signed in.", "success", reloadLocation);
}

function updateUserLock()
{
    trace("updateUserLock");

    var itemElement = $('.itemNavElement.right');
    var navItem = itemElement.find( "li[data-menu-id='" + LOCK_ITEM + "']" );
    var navItemIcon = navItem.find('span.fas');

    if (isUserLogged)
    {
        navItemIcon.removeClass('fa-lock');
        navItemIcon.addClass('fa-lock-open');
        $(navItem).addClass('navMenuItemActive');
    }
    else
    {
        navItemIcon.removeClass('fa-lock-open');
        navItemIcon.addClass('fa-lock');
        $(navItem).removeClass('navMenuItemActive');
    }
}

function signOut()
{
    var username = '';
    var password = '';
    var pin = '';

    swal({
        title: "Sign out",
        text: "Are you sure? ",
        icon: "warning",
        closeOnClickOutside: false,
        closeOnEsc: false,
        dangerMode: true,
        buttons: ["CANCEL", "YES"]
    })
    .then(function(isSigninOut)
    {
        if (!isSigninOut) return;

        showLoader("Singing Out");

        var ajaxData    = new FormData();
        ajaxData.append( "signing_out", "1" );

        // ajax request
        $.ajax(
        {
            url: 			'api/signout_user.php',
            type:			'POST',
            data: 			ajaxData,
            dataType:		'json',
            cache:			false,
            contentType:	false,
            processData:	false,
            complete: function()
            {
                trace("SIGNOUT.complete");
                hideLoader();
            },
            success: function( data )
            {
                trace("SIGNOUT.success.data: ", data);
                if( !data.success )
                {
                    showAlert("Error", data.error, "error");
                }
                else
                {
                    reloadLocation();
                }
            },
            error: function(request, status, error)
            {
                trace("SIGNOUT.error", error);

                showAlert("Error", error, "error");
            }
        });
    });
}

function listOptions()
{

}

var isUserLoggedIn = function() {

    var $form		= $('.itemForm');
    var ajaxData    = new FormData( $form[0] );
    ajaxData.append( "itemID", itemID );

    // ajax request
    $.ajax(
        {
            url: 			'api/check_login.php',
            type:			$form.attr( 'method' ),
            data: 			ajaxData,
            dataType:		'json',
            cache:			false,
            contentType:	false,
            processData:	false,
            complete: function()
            {
                trace("AJAX.complete");
                $form.removeClass( 'is-uploading' );
            },
            success: function( data )
            {
                trace("AJAX.success.data: ", data);
                $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                if( !data.success )
                {
                    showAlert("Error", data.error, "error");
                }
                else
                {
                    showAlert("Profile Removed",
                        "Your data has been removed.",
                        "success",
                        goHome);
                }
            },
            error: function(request, status, error)
            {
                trace("AJAX.request", request);
                trace("AJAX.responseText", request.responseText);
                trace("AJAX.status", status);
                trace("AJAX.error", error);

                showAlert("Error", error, "error");
            }
        });

}

