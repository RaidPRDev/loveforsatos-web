/* DROPDOWN COMPONENT */

var currentDropdown = null;
var previousDropdown = null;
var prevNav = null;
var currNav = null;
var modalOverlay = null;
var touchClickOverlay = null;
var currentDropIndex = -1;

function dropInitialize()
{
    createWindowTouchHandler();
}

function hideDropdown()
{
    if (currentDropdown && currentDropIndex > -1)
    {
        dropRevertMoveParent();
        onDropRemoveAllOverlays();
        currentDropdown.css("display", "none");
        currentDropIndex = -1;
    }
}

function dropDownSelect(navItem) {

    onDropRemoveAllOverlays();

    var dataItemID = parseInt($(navItem).attr('data-menu-id'));

    if (dataItemID == currentDropIndex)
    {
        dropRevertMoveParent();
        onDropRemoveAllOverlays();
        currentDropdown.css("display", "none");
        currentDropIndex = -1;
        return;
    }

    currentDropIndex = dataItemID;

    if (prevNav == null) currNav = navItem;

    prevNav = currNav;
    currNav = navItem;

    if (previousDropdown == null) currentDropdown = $(navItem).find('.dropdown-content');

    previousDropdown = currentDropdown;
    currentDropdown = $(navItem).find('.dropdown-content');

    if (currNav != prevNav)
    {
        previousDropdown.css("display", "none");
        dropRevertPreviousMoveParent();
        onDropRemoveAllOverlays();
    }

    dropMoveParent(navItem);

    if (currentDropdown.css("display") == "none")
    {
        currentDropdown.css("display", "block");

        var itemPanel = $('.itemPanel');

        createDropdownOverlays(itemPanel);
    }
    else
    {
        currentDropdown.css("display", "none");
        dropRevertMoveParent();
    }
}

function dropMoveParent(navItem)
{
    currentDropdown = $(navItem).find('.dropdown-content');
    var detachedDrop = currentDropdown.detach();
    $(navItem).parent().append(detachedDrop);
}

function dropRevertMoveParent()
{
    if (currentDropdown == null) return;

    var detachedDrop = currentDropdown.detach();
    $(currNav).append(detachedDrop);
}

function dropRevertPreviousMoveParent()
{
    if (previousDropdown == null) return;

    var detachedDrop = previousDropdown.detach();
    $(prevNav).append(detachedDrop);
}

/*
    Dropdown overlay

    Contains 2 layers, to prevent touch focus on iOS devices
    For now this will do, but needs to be better
    Maybe resetting focus

 */
function createDropdownOverlays(parentItem)
{
    modalOverlay = jQuery('<div/>', {
        class: 'dropdown-modal-base dropdown-modal modal-test'
    });

    touchClickOverlay = jQuery('<div/>', {
        class: 'dropdown-modal-base dropdown-modal-overlay modal-test'
    });

    var innerItemContainer = $('.innerItemContainer');
    var screenWidth = $(window).width();

    modalOverlay.css('width', screenWidth);
    modalOverlay.css('height', innerItemContainer.height());

    touchClickOverlay.css('width', screenWidth);
    touchClickOverlay.css('height', innerItemContainer.height());

    // add elements
    parentItem.append(touchClickOverlay);
    parentItem.append(modalOverlay);

    setTimeout(function() {
        modalOverlay.on('click touchstart', onDropModalClick);
    }, 500);
}

function onDropRemoveAllOverlays(forceDelete)
{
    if (modalOverlay != null)
    {
        modalOverlay.off('click touchstart', onDropModalClick);
        modalOverlay.remove();
        modalOverlay = null;
    }

    if (touchClickOverlay != null)
    {
        touchClickOverlay.off('click touchstart', onDropTouchOverlayClick);
        touchClickOverlay.remove();
        touchClickOverlay = null;
    }

    if (forceDelete)
    {
        trace('onDropRemoveAllOverlays[force delete all overlays]');

        if ( $('.dropdown-modal') != null )
        {
            $('.dropdown-modal').off('click touchstart', onDropModalClick);
            $('.dropdown-modal').remove();
        }

        if ( $('.dropdown-modal-overlay') != null )
        {
            $('.dropdown-modal-overlay').off('click touchstart', onDropModalClick);
            $('.dropdown-modal-overlay').remove();
        }
    }
}

function onDropModalClick(e)
{
    currentDropIndex = -1;

    if (modalOverlay != null)
    {
        modalOverlay.off('click touchstart', onDropModalClick);
        modalOverlay.remove();
        modalOverlay = null;

        setTimeout(function() {
            touchClickOverlay.on('click touchstart', onDropTouchOverlayClick);
        }, 500);
    }
    else
    {
        onDropRemoveAllOverlays(true);
    }
}

function onDropTouchOverlayClick(e)
{
    if (touchClickOverlay != null)
    {
        touchClickOverlay.off('click touchstart', onDropTouchOverlayClick);
        touchClickOverlay.remove();
        touchClickOverlay = null;
    }
    else
    {
        onDropRemoveAllOverlays(true);
    }
}

function createWindowTouchHandler()
{
    $(window).on("click touchstart", onClickOutsideEvent);
}

function removeWindowTouchHandler()
{
    $(window).off("click touchstart", onClickOutsideEvent);
}

function onClickOutsideEvent(e)
{
    var target = $(e.target);
    var parent = $(e.target).parent();

    if (parent.hasClass('dropdown-content'))
    {
        //  removeWindowTouchHandler();
        return;
    }

    if (!e.target.matches('.navMenuItem'))
    {
        var dropdownList = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdownList.length; i++)
        {
            if ($(dropdownList[i]).css("display") == "block")
            {
                $(dropdownList[i]).css("display", "none");
            }
        }

        dropRevertMoveParent();
    }
}

function dropGetAllMenuItems()
{
    var groupIndex = 0;
    var itemElement = $(currentDropdown);
    var menuItems = itemElement.find( "a[data-menu-group='" + groupIndex + "']" );

    var selectedItem = menuItems.find('selected');
    if (selectedItem != null)
    {
        selectedItem.removeClass('selected');
    }

    selectedItem = menuItems.find("a[data-menu-id='" + sortListSelected + "']");
    if (selectedItem != null)
    {
        selectedItem.addClass('selected');
    }
}

function setDropSelectionMenuIndex(navItemIndex, groupIndex, menuIndex)
{
    //trace("setDropSelectionMenuIndex.navItemIndex:", navItemIndex);
    //trace("setDropSelectionMenuIndex.groupIndex:", groupIndex);
    //trace("setDropSelectionMenuIndex.menuIndex:", menuIndex);
    var itemNavElementRight = $('.innerNavigation');
    var navMenuItems = itemNavElementRight.find('.navMenuItem');
    var navMenuItem = $(navMenuItems.get(navItemIndex));
    var menuItems = navMenuItem.find( "a[data-menu-group='" + groupIndex + "']" );
    var optionItems = $(menuItems).find('.navMenuOptionIcon');
    var selectedItem = optionItems.find('span.selected');
    var dataItemID = $(menuItems.get(menuIndex)).attr('data-menu-id');

    // remove any selected items
	if (selectedItem != null) selectedItem.removeClass('selected');
  
	// set selected
    var optionItem = optionItems.get(menuIndex);
    $(optionItem).addClass('selected');

    setCookie(settings[groupIndex], menuIndex + "," + dataItemID);
}

function dropSetSelectedMenuIndex(groupIndex, menuIndex, menuItem)
{
    var dataItemID = $(menuItem).attr('data-menu-id');
    var dataItemIndex = $(menuItem).attr('data-menu-index');
    var itemElement = $(currentDropdown);
    var menuItems = itemElement.find( "a[data-menu-group='" + groupIndex + "']" );
    // var dataItemID = menuItems.attr('data-menu-id');

    // find current selected and unset
    var selectedItem = menuItems.find('span.selected');
    if (selectedItem != null)
    {
        selectedItem.removeClass('selected');
    }

    // find new selected item and set
    selectedItem = $(menuItem).find(".navMenuOptionIcon");
    if (selectedItem != null)
    {
        selectedItem.addClass('selected');
    }

    setCookie(settings[groupIndex], menuIndex + "," + dataItemID);
    sortListSelected = dataItemIndex;
}