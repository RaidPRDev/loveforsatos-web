/* APP CORE */

const VERSION = "1.0.0";
const SAVING_DATA = "Saving Data";
const UPDATE_DATA = "Updating Data";
var isAutomaticUpload = true;
var useUploadListForTransfer = false;
var isUserLogged = false;
var isTraceEnabled = true;
var selectedItemId = -1;
var selectedItemName = "";
var viewToken = '';

var iOS = /iPad|iPhone|iPod/.test(navigator.platform);
window.isiOS = iOS;
window.isMobile = false;

// Initialize
$( document ).ready(function() 
{
	appInitialized();

    updateUserLock();

	dropInitialize();
});

function appInitialized()
{
	trace("version ", VERSION);
}

function trace(...args)
{
    if (isTraceEnabled)
    {
        if (args[1] != null) console.log("[app] " + args[0], args[1]);
        else console.log("[app] " + args[0]);
    }
}

function mainNavSelect(navItem)
{
    trace("mainNavSelect.navItem:", navItem);
}

// For TextArea elements, adjusts height according to text amount
function adjust_textarea(h) 
{
	h.style.height = "20px";
	h.style.height = (h.scrollHeight)+"px";
}

function removeItemFromDatabase(itemID, itemName)
{
	trace("removeItemFromDatabase.itemID:", itemID);
	trace("removeItemFromDatabase.itemName:", itemName);

    swal({
        title: "Remove " + itemName + "?",
        text: "You will not be able to recover this data!",
        icon: "warning",
        closeOnClickOutside: false,
        closeOnEsc: false,
        buttons: ["CANCEL","REMOVE"],
        dangerMode: true,
        className: "", // iconalertwarning
    })
    .then(function(willDelete)
    {
        trace('willDelete:', willDelete);
        if (!willDelete) return;

        showLoader("Removing");

        const $form		= $('.itemForm');
        const ajaxData    = new FormData( $form[0] );
        ajaxData.append( "itemID", itemID );

        // ajax request
        $.ajax(
        {
            url: 			'api/remove_db_item.php',
            type:			$form.attr( 'method' ),
            data: 			ajaxData,
            dataType:		'json',
            cache:			false,
            contentType:	false,
            processData:	false,
            complete: function()
            {
                trace("removeItemFromDatabase.complete");
                hideLoader();
                //$form.removeClass( 'is-uploading' );
            },
            success: function( data )
            {
                trace("removeItemFromDatabase.success.data: ", data);
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
				trace("removeItemFromDatabase.error", error);
                
                showAlert("Error", error, "error");
            }
        });
    });
}

function goHome()
{
    window.location.replace("satos.php?t=" + MD5(Date.now()));
}

function goEditItem(itemID)
{
    window.location.replace("update_item.php?id=" + itemID + "&t=" + MD5(Date.now()));
}

function goToUsersScreen()
{
    window.location.replace("users.php?t=" + MD5(Date.now()));
}

function reloadLocation()
{
    window.location.reload(true);
}

function showLoader(text)
{
    if (!text) text = "Saving Data";

    swal(text, {
        icon: "images/spinner.svg",
        buttons: false,
        closeOnEsc: false,
        closeOnClickOutside: false
    });
}

function hideLoader()
{
    swal.close();
}

function showAlert(title, message, icon, callback, callbackParam)
{
    swal({
        title: title,
        text: message,
        icon: icon,
        closeOnClickOutside: false,
        closeOnEsc: false,
        dangerMode: false,
        buttons: "OK"
    })
    .then(function()
    {
        if (callback != null)
        {
            if (callbackParam != null) callback(callbackParam);
            else callback();
        }
    });
}
function clearFileInput(ctrl)
{
    try {
        ctrl.value = null;
    } catch(ex) { }
    if (ctrl.value) {
        ctrl.parentNode.replaceChild(ctrl.cloneNode(true), ctrl);
    }
}

// crypto
var MD5 = function(d){result = M(V(Y(X(d),8*d.length)));return result.toLowerCase()};function M(d){for(var _,m="0123456789ABCDEF",f="",r=0;r<d.length;r++)_=d.charCodeAt(r),f+=m.charAt(_>>>4&15)+m.charAt(15&_);return f}function X(d){for(var _=Array(d.length>>2),m=0;m<_.length;m++)_[m]=0;for(m=0;m<8*d.length;m+=8)_[m>>5]|=(255&d.charCodeAt(m/8))<<m%32;return _}function V(d){for(var _="",m=0;m<32*d.length;m+=8)_+=String.fromCharCode(d[m>>5]>>>m%32&255);return _}function Y(d,_){d[_>>5]|=128<<_%32,d[14+(_+64>>>9<<4)]=_;for(var m=1732584193,f=-271733879,r=-1732584194,i=271733878,n=0;n<d.length;n+=16){var h=m,t=f,g=r,e=i;f=md5_ii(f=md5_ii(f=md5_ii(f=md5_ii(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_ff(f=md5_ff(f=md5_ff(f=md5_ff(f,r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+0],7,-680876936),f,r,d[n+1],12,-389564586),m,f,d[n+2],17,606105819),i,m,d[n+3],22,-1044525330),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+4],7,-176418897),f,r,d[n+5],12,1200080426),m,f,d[n+6],17,-1473231341),i,m,d[n+7],22,-45705983),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+8],7,1770035416),f,r,d[n+9],12,-1958414417),m,f,d[n+10],17,-42063),i,m,d[n+11],22,-1990404162),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+12],7,1804603682),f,r,d[n+13],12,-40341101),m,f,d[n+14],17,-1502002290),i,m,d[n+15],22,1236535329),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+1],5,-165796510),f,r,d[n+6],9,-1069501632),m,f,d[n+11],14,643717713),i,m,d[n+0],20,-373897302),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+5],5,-701558691),f,r,d[n+10],9,38016083),m,f,d[n+15],14,-660478335),i,m,d[n+4],20,-405537848),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+9],5,568446438),f,r,d[n+14],9,-1019803690),m,f,d[n+3],14,-187363961),i,m,d[n+8],20,1163531501),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+13],5,-1444681467),f,r,d[n+2],9,-51403784),m,f,d[n+7],14,1735328473),i,m,d[n+12],20,-1926607734),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+5],4,-378558),f,r,d[n+8],11,-2022574463),m,f,d[n+11],16,1839030562),i,m,d[n+14],23,-35309556),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+1],4,-1530992060),f,r,d[n+4],11,1272893353),m,f,d[n+7],16,-155497632),i,m,d[n+10],23,-1094730640),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+13],4,681279174),f,r,d[n+0],11,-358537222),m,f,d[n+3],16,-722521979),i,m,d[n+6],23,76029189),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+9],4,-640364487),f,r,d[n+12],11,-421815835),m,f,d[n+15],16,530742520),i,m,d[n+2],23,-995338651),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+0],6,-198630844),f,r,d[n+7],10,1126891415),m,f,d[n+14],15,-1416354905),i,m,d[n+5],21,-57434055),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+12],6,1700485571),f,r,d[n+3],10,-1894986606),m,f,d[n+10],15,-1051523),i,m,d[n+1],21,-2054922799),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+8],6,1873313359),f,r,d[n+15],10,-30611744),m,f,d[n+6],15,-1560198380),i,m,d[n+13],21,1309151649),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+4],6,-145523070),f,r,d[n+11],10,-1120210379),m,f,d[n+2],15,718787259),i,m,d[n+9],21,-343485551),m=safe_add(m,h),f=safe_add(f,t),r=safe_add(r,g),i=safe_add(i,e)}return Array(m,f,r,i)}function md5_cmn(d,_,m,f,r,i){return safe_add(bit_rol(safe_add(safe_add(_,d),safe_add(f,i)),r),m)}function md5_ff(d,_,m,f,r,i,n){return md5_cmn(_&m|~_&f,d,_,r,i,n)}function md5_gg(d,_,m,f,r,i,n){return md5_cmn(_&f|m&~f,d,_,r,i,n)}function md5_hh(d,_,m,f,r,i,n){return md5_cmn(_^m^f,d,_,r,i,n)}function md5_ii(d,_,m,f,r,i,n){return md5_cmn(m^(_|~f),d,_,r,i,n)}function safe_add(d,_){var m=(65535&d)+(65535&_);return(d>>16)+(_>>16)+(m>>16)<<16|65535&m}function bit_rol(d,_){return d<<_|d>>>32-_}

document.addEventListener('gesturestart', function (e)
{
    e.preventDefault();
});

document.addEventListener('touchmove', function(event)
{
    event = event.originalEvent || event;
    if(event.scale > 1) {
        event.preventDefault();
    }
}, false);

// cookies

function setCookie(key, value)
{
    //trace("setCookie.key", key)
    //trace("setCookie.value", value)
    document.cookie = key + "=" + value + "; expires=24 Dec 2090 12:00:00 UTC; path=/";
}

function getCookie(cname)
{
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// ==========================================================================================

function addUser()
{
    trace("addUser");

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
            }
            else
            {
                showAlert("User Added",
                    "User has been registered.",
                    "success",
                    goHome);
            }
        },
        error: function(request, status, error)
        {
            trace("addUser.error", error);

            showAlert("Error", error, "error");
        }
    });
}