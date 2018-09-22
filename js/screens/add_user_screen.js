
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

function onSubmitAddUser(e)
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
            break;
        case "team":
            $("label[for='username']").parent().css("display", "none");
            $("label[for='password']").parent().css("display", "none");
            $("label[for='password_confirm']").parent().css("display", "none");
            break;
    }
}