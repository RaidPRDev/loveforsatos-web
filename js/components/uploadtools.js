/* UPLOAD TOOLS */

// Detect Drag and Drop support
var isAdvancedUpload = function() {
    var div = document.createElement('div');
    return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
}();

// Drag and Drop, File Upload Handling
function initializeUpload()
{
    $( '.itemForm' ).each( function()
    {
        var $form		 = $( this ),
            $uploadBox	 = $form.find( '.box' ),
            $icon		 = $form.find( '.box__icon' ),
            $input		 = $form.find( 'input[type="file"]' ),
            $label		 = $form.find( '.inputLabel' ),
            $errorMsg	 = $form.find( '.box__error span' ),
            droppedFiles = false,
            showFiles	 = function( files )
            {
                trace("showFiles() ", files);

                // if we have a slider, add files to SliderTools.uploadedFileList
                addFileToList(files);

                if (files.length > 20)
                {
                    droppedFiles = null;
                    showAlert("Max photos exceeded", "The limit is 20 photos at any item", "error");
                    return;
                }

                //$label.text = (files.length > 1) ? "Photos Added" : "Photo Added";
            };

        // letting the server side to know we are going to make an Ajax request
        $form.append( '<input type="hidden" name="ajax" value="1" />' );

        // automatically submit the form on file select
        $input.on( 'change', function( e )
        {
            trace("input.change() ", e.target.files);

            showFiles( e.target.files );

            if (isAutomaticUpload) $form.trigger( 'submit' );
        });


        // drag&drop files if the feature is available
        if( isAdvancedUpload )
        {
            trace("advanced uploaded enabled");

            $uploadBox
                .addClass( 'has-advanced-upload' ) // letting the CSS part to know drag&drop is supported by the browser
                .on( 'drag dragstart dragend dragover dragenter dragleave drop', function( e )
                {
                    // preventing the unwanted behaviours
                    e.preventDefault();
                    e.stopPropagation();
                })
                .on( 'dragover dragenter', function() //
                {
                    if ($icon != null) $icon.addClass('is-dragover');

                    $uploadBox.addClass( 'is-dragover' );
                })
                .on( 'dragleave dragend drop', function()
                {
                    if ($icon != null) $icon.removeClass('is-dragover')

                    $uploadBox.removeClass( 'is-dragover' );
                })
                .on( 'drop', function( e )
                {
                    droppedFiles = e.originalEvent.dataTransfer.files; // the files that were dropped
                    showFiles( droppedFiles );

                    // automatically submit the form on file drop
                    if (isAutomaticUpload) $form.trigger( 'submit' );
                });
        }

        // if the form was submitted
        $form.on( 'submit', function( e )
        {
            // remove input on submit
            $('.box__input').find('.inputLabel').css('display', 'none');

            var form = $form[ 0 ];
            for (i = 0; i < form.elements.length; i++)
            {
                if (form.elements[i].type == 'file')
                {
                    if (form.elements[i].value == '')
                    {
                        form.elements[i].parentNode.removeChild(form.elements[i]);
                    }
                }
            }

            showLoader();

            // preventing the duplicate submissions if the current one is in progress
            if( $form.hasClass( 'is-uploading' ) ) return false;

            $form.addClass( 'is-uploading' ).removeClass( 'is-error' );

            if( isAdvancedUpload ) // ajax file upload for modern browsers
            {
                e.preventDefault();

                // gathering the form data
                var ajaxData = null;
                if( droppedFiles && !useUploadListForTransfer )
                {
                    ajaxData = new FormData( $form[0] );

                    $.each( droppedFiles, function( i, file )
                    {
                        ajaxData.append( $input.attr( 'name' ), file );
                    });
                }
                else
                {
                    ajaxData = new FormData( $form[0] );

                    // check if we are using photo upload slider
                    if (useUploadListForTransfer)
                    {
                        ajaxData = addSliderPhotosToAjaxData($form, $input);
                        ajaxData = setRemovePhotoListToAjax(ajaxData);
                    }
                }

                // check fields
                var i;
                for (i = 0; i < $form[0].length; i++)
                {
                    if ($form[0][i].type == "hidden"
                        || $form[0][i].type == "file"
                        || $form[0][i].type == "submit") continue;

                    ajaxData.append( $form[0][i].name, $form[0][i].value );
                }

                // ajax request
                $.ajax(
                {
                    url: 			$form.attr( 'action' ),
                    type:			$form.attr( 'method' ),
                    data: 			ajaxData,
                    dataType:		'json',
                    xhr: function() {
                        var myXhr = $.ajaxSettings.xhr();
                        if(myXhr.upload) {
                            myXhr.upload.addEventListener('progress', onUploadProgress, false);
                        }
                        return myXhr;
                    },
                    cache:			false,
                    contentType:	false,
                    processData:	false,
                    complete: function()
                    {
                        $form.removeClass( 'is-uploading' );
                    },
                    success: function( data )
                    {
                        $form.addClass( data.success == true ? 'is-success' : 'is-error' );
                        trace("data:", data);
                        if( !data.success )
                        {
                            $errorMsg.text( data.error );

                            updateInputFieldStatus(data.error, data.errorCode);

                            showAlert("Error", data.error, "error");

                            // show input on submit
                            $('.box__input').find('.inputLabel').css('display', 'block');
                        }
                        else
                        {
                            var itemID = data.itemID;
                            showAlert("Saving Complete",
                                "data.itemID: " + itemID,
                                "success",
                                goEditItem,
                                itemID);
                        }
                    },
                    error: function(request, status, error)
                    {
                        trace("initializeUpload.error:", error);

                        showAlert("Connection Failed", error + " Check your internet connection and try again.", "error");
                    }
                });
            }
            else // fallback Ajax solution upload for older browsers
            {
                var iframeName	= 'uploadiframe' + new Date().getTime(),
                    $iframe		= $( '<iframe name="' + iframeName + '" style="display: none;"></iframe>' );

                $( 'body' ).append( $iframe );
                $form.attr( 'target', iframeName );

                $iframe.one( 'load', function()
                {
                    var data = $.parseJSON( $iframe.contents().find( 'body' ).text() );
                    $form.removeClass( 'is-uploading' ).addClass( data.success == true ? 'is-success' : 'is-error' ).removeAttr( 'target' );
                    if( !data.success ) $errorMsg.text( data.error );
                    $iframe.remove();
                });
            }
        });

        // Firefox focus bug fix for file input
        $input
            .on( 'focus', function(){ $input.addClass( 'has-focus' ); })
            .on( 'blur', function(){ $input.removeClass( 'has-focus' ); });
    });
}

function onUploadProgress(e)
{
    if (e.lengthComputable)
    {
        var max = e.total;
        var current = e.loaded;

        var percentage = (current * 100)/max;
        trace("onUploadProgress.percent: ", percentage);

        $('.swal-text').text(SAVING_DATA + " " + Math.floor( percentage ) + "%");

        if(percentage >= 100)
        {
            // process complete
            trace("onUploadProgress.complete");

            $('.swal-text').text("Processing Image Data");
        }
    }
}

// called when we have empty fields
function updateInputFieldStatus(error, errorCode)
{
    // errorCode: Is the name of the text field
    getInputField(errorCode);
}

function getInputField(keyName)
{
    trace("KEY:", keyName)

    var $form		= $('.itemForm');
    var $input      = $form.find('ul li');

    // scan and find the listItem's label and span elements and set style alert
    $input.each(function(listIndex, listItem) {

        // we will look at the 'for' attribute to match with key
        var forAttr = $(listItem).find('label').attr('for');
        if (forAttr == keyName)
        {
            $(listItem).find('label').addClass('warning-field-dark-grey-label');
            $(listItem).find('span').addClass('warning-field-red-label');
        }
    });
}
