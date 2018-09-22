/* Promise Item */

function onSelectPromise(id, pid)    // dog_id
{
    swal({
        title: "Promise",
        text: "Are you sure?",
        icon: "warning",
        closeOnClickOutside: false,
        closeOnEsc: false,
        buttons: ["NO","YES"],
        dangerMode: false,
        className: "",
    })
        .then(function(willCancel)
        {
            if (!willCancel) return;

            updatePromise(id, pid, true);
        });
}

function onDeSelectPromise(id, pid)    // dog_id
{
    swal({
        title: "UnPromise",
        text: "Are you sure?",
        icon: "warning",
        closeOnClickOutside: false,
        closeOnEsc: false,
        buttons: ["NO","YES"],
        dangerMode: false,
        className: "",
    })
        .then(function(willCancel)
        {
            if (!willCancel) return;

            updatePromise(id, pid, false);
        });
}

function onPromiseDisabledAlert(id, pid)
{
    swal({
        title: "Sorry",
        text: "Already been promised!",
        icon: "info",
        buttons: "OK",
        dangerMode: false,
        className: "",
    });
}

function updatePromise(id, pid, isPromise)      // dog_id, playlist_id
{
    trace("updatePromise.id:", id);
    trace("updatePromise.pid:", pid);

    showLoader("Submitting");

    const ajaxData    = new FormData();
    ajaxData.append( "id", id );
    ajaxData.append( "pid", pid );

    // ajax request
    $.ajax({
        url: 			'api/update_db_promise.php',
        type:			'post',
        data: 			ajaxData,
        dataType:		'json',
        cache:			false,
        contentType:	false,
        processData:	false,
        complete: function()
        {
            trace("updatePromise.complete");

        },
        success: function( data )
        {
            trace("updatePromise.success.data: ", data);
            if( !data.success )
            {
                showAlert("Error", data.error, "error");
                updateInputFieldStatus(data.error, data.errorCode);
            }
            else
            {
                trace("updatePromise.done");
                // update button
                if (isPromise)
                {
                    showAlert("Promised!", "Thank you!", "success", reloadLocation);
                }
                else
                {
                    showAlert("UnPromised", "You have deselected successfully", "success", reloadLocation);
                }


            }
        },
        error: function(request, status, error)
        {
            trace("updatePromise.error", error);

            showAlert("Error", error.toString(), "error");
        }
    });
}
