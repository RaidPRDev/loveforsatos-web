/*
    Slider Tools
 */

const SLIDER = ".user-photo-slider";
const SLIDER_ITEMS = ".user-photo-slider .item";
const NO_SLIDER_ITEMS = ".no-slider-items";
var currentPhotoList = [];      // holds current photos
var removePhotoList = [];       // holds removed photos
var uploadedFileList = {};      // holds uploaded files

var currentItemList = [];       // holds current items
var currentSelectItemList = []; // holds selected items
var currentAddedItemList = [];  // holds added items
var removeItemList = [];        // holds removed items

function initializePhotoSlider()
{
    trace("initializePhotoSlider");
    $(SLIDER).slick({
        centerMode: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        centerPadding: '10%',
        touchThreshold: 280,
        swipeToSlide: true,
        infinite: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    centerPadding: '10%',
                    centerMode: true,
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    centerPadding: '6%',
                    centerMode: true,
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    currentPhotoList = $(SLIDER_ITEMS);
    // trace("currentPhotoList:", currentPhotoList);

    if (currentPhotoList.length == 0)
    {
        // trace("currentPhotoList.length:", currentPhotoList.length);

        var noSliderItems = jQuery('<div/>', {
            class: NO_SLIDER_ITEMS.substr(1)
        });

        noSliderItems.html("No Photos Found");

        $(SLIDER).append(noSliderItems);
    }
}

function initializeItemSlider()
{
    trace("initializeItemSlider");
    $(SLIDER).slick({
        centerMode: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        centerPadding: '10%',
        touchThreshold: 280,
        swipeToSlide: true,
        infinite: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    centerPadding: '10%',
                    centerMode: true,
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    centerPadding: '6%',
                    centerMode: true,
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    currentItemList = $(SLIDER_ITEMS);
    trace("currentItemList:", currentItemList);

    if (currentItemList.length == 0)
    {
        var noSliderItems = jQuery('<div/>', {
            class: NO_SLIDER_ITEMS.substr(1)
        });

        noSliderItems.html("No Items Found");

        $(SLIDER).append(noSliderItems);
    }
}


function initializeSliderSelector(element)
{
    trace("initializeSliderSelector");
    element.slick({
        centerMode: false,
        touchThreshold: 280,
        swipeToSlide: false,
        infinite: false,
        slidesToShow: 2,
        slidesToScroll: 2,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    return element;
}

/*
   Adds a file to the uploadedFileList dictionary
   @param: files = Array of Input.File elements
*/
function addFileToList(files) // param Array
{
    var slider = $(SLIDER);

    trace("addFileToList().SLIDER:", slider);

    // check if SLIDER exists
    if (slider.length == 0)
    {
        trace(SLIDER, " does not exist");
        return;
    }
    else
    {
        // remove no photos found if available
        var noSliderItem = $(NO_SLIDER_ITEMS);
        if (noSliderItem.length > 0)
        {
            noSliderItem.remove();
        }
    }

    trace("addFileToList().files:", files);

    var duplicateCount = 0;

    // uploadedFileList.push()
    for (var i = 0; i < files.length; i++)
    {
        trace("files:", files[i]);

        if (!checkIfFileExists(files[i]))
        {
            trace("File does not exist, save it:", files[i].name);
            uploadedFileList[files[i].name] = files[i]; // as File

            // add to slider div
            createSliderItemElement(files[i]);

        }
        else
        {
            // file is a duplicate
            duplicateCount++;
        }
    }

    // check if we have any duplicates
    if (duplicateCount > 0)
    {
        var photosTxt = (duplicateCount > 1) ? " photos " : " photo ";

        swal({
            title: "Found duplicates",
            text: "We removed " + duplicateCount + photosTxt + "that already exists.",
            icon: "warning",
            closeOnClickOutside: false,
            dangerMode: false,
            buttons: "Ok thanks!"
        });
    }

    trace("uploadedFileList.length:", getTotalFilesInDict(uploadedFileList));
}

/*
   Checks if file exits in our uploadedFileList dictionary
   @param: file = Input.File element
*/
function checkIfFileExists(file) // as File
{
    // check if already exists
    var exist = (uploadedFileList[file.name]) ? true : false;
    trace("checkIfFileExists.exist:", exist);
    return exist;
}

/*
   Adds a new Slider Item to Slider
   @param: dict = Dictionary
*/
function getTotalFilesInDict(dict)
{
    trace("getTotalFilesInDict:", dict);
    var count = 0;
    for(var key in dict)
    {
        trace("checkIfFileExists.exist:", dict[key].name);
        count++;
    }
    return count;
}

/*
   Adds a new Slider Item to Slider
   @param: file = Input.File element
*/
function createSliderItemElement(file)
{
    trace("createSliderItemElement");

    var sliderItem = jQuery('<div/>', {
        class: 'item'
    });

    var innerItem = jQuery('<div/>', {
        class: 'inner-item'
    });

    var itemRemovePhoto = jQuery('<div/>', {
        class: 'item-remove-photo',
        onclick : "onRemovePhotoItem(this)"
    });

    var itemRemovePhotoSpan = jQuery('<span/>', {
        class: 'fas fa-times-circle',
    });

    // add elements
    sliderItem.append(innerItem);
    innerItem.append(itemRemovePhoto)
    itemRemovePhoto.append(itemRemovePhotoSpan);

    // load image and resize
    const maxWidth = 280;
    const maxHeight = 280;

    loadImage(
        file,
        function (img) {

            trace("LOAD IMAGE:", img);

            // add style to image
            $(img).addClass('slick-slide-img');

            // add image to slider item
            sliderItem.append(img);
        },
        {
            minWidth: maxWidth,
            minHeight: maxHeight,
            maxWidth: maxWidth,
            maxHeight: maxHeight,
            orientation: true,
            crop: true
        } // Options
    );

    // add to current list
    currentPhotoList.push(sliderItem);

    // mark as new
    sliderItem.attr('data-item-id', 'new');
    sliderItem.attr('data-item-name', file.name);
    sliderItem.attr('data-item-index', currentPhotoList.length);

    trace("currentPhotoList.length:", currentPhotoList.length);
    trace("uploadedFileList.length:", getTotalFilesInDict(uploadedFileList));

    // add item to slider
    $(SLIDER).slick('slickAdd', sliderItem);
    $(SLIDER).slick('slickGoTo', currentPhotoList.length);
}

/*
   Adds a new Slider Item to Item Slider
   @param: item = Playlist Data Item
*/
function onAddPlaylistEntryItem(item)
{
    trace("onAddPlaylistEntryItem");

    const dog_id = item['dog_id'];
    const dog_name = item['dog_name'];
    const dog_age = item['dog_age'];
    const dog_gender = item['dog_gender'];
    const dog_adopted = item['dog_adopted'];
    const dog_fixed = item['dog_fixed'];
    const thumb_image_url = item['thumb_image_url'];

    /*const sliderItem = jQuery('<div/>', {
        class: 'item'
    });
    sliderItem.attr('data-item-id', dog_id);

    const innerItem = jQuery('<div/>', {
        class: 'inner-item'
    }).appendTo(sliderItem);

    const itemRemove = jQuery('<div/>', {
        class: 'item-remove-photo',
        onclick: 'onRemovePlaylistEntryItem(this)'
    }).appendTo(innerItem);
    itemRemove.attr('data-item-id', dog_id);

    const itemSpan = jQuery('<span/>', {
        class: 'fas fa-times-circle'
    }).appendTo(itemRemove);

    const itemNameLabel = jQuery('<div/>', {
        class: 'item-name-label'
    }).appendTo(innerItem);

    const itemLabelSpan = jQuery('<span/>', {
        class: '',
        text: dog_name
    }).appendTo(itemNameLabel);

    trace('dog_id:', dog_id);
    trace('dog_name:', dog_name);
    trace('thumb_image_url:', thumb_image_url);


*/





    const sliderItem = jQuery('<div/>', {
        class: 'item'
    });
    sliderItem.attr('data-item-id', dog_id);

    const innerItem = jQuery('<div/>', {
        class: 'inner-item'
    }).appendTo(sliderItem);

    const itemAdd = jQuery('<div/>', {
        class: 'item-remove-photo',
        onclick: 'onRemovePlaylistEntryItem(this)'
    }).appendTo(innerItem);
    itemAdd.attr('data-item-id', dog_id);

    const itemSpan = jQuery('<span/>', {
        class: 'fas fa-plus-circle'
    }).appendTo(itemAdd);

    const itemNameLabel = jQuery('<div/>', {
        class: 'item-name-label'
    }).appendTo(innerItem);

    const itemIcons = jQuery('<div/>', {
        class: 'item-icons'
    }).appendTo(itemNameLabel);

    const itemLabel = jQuery('<span/>', {
        class: 'item-label',
        text: dog_name
    }).appendTo(itemIcons);

    var dog_gender_icon = (dog_gender == 'male') ? "fa-mars" : "fa-venus";
    const itemIconsGender = jQuery('<span/>', {
        class: 'item-icon-fixed fas ' + dog_gender_icon
    }).appendTo(itemIcons);

    if (dog_fixed != 'intact')
    {
        const itemIconsFixed = jQuery('<span/>', {
            class: 'item-icon-fixed fas fa-notes-medical'
        }).appendTo(itemIcons);
    }

    const itemAge = jQuery('<span/>', {
        class: 'item-age',
        text: generateAgeShortLabel(dog_age)
    }).appendTo(itemIcons);

    /*const itemImage = jQuery('<img/>', {
        class: 'itemImageSelectorSrc'
    }).appendTo(sliderItem);
    itemImage.attr('src', 'images/photos/' + thumb_image_url);
    */
























    // load image and resize
    const maxWidth = 280;
    const maxHeight = 280;

    loadImage(
        'images/photos/' + thumb_image_url,
        function (img) {

            trace("LOAD IMAGE:", img);

            // add style to image
            $(img).addClass('slick-slide-img');
            $(img).addClass('itemImageViewSrc');

            // add image to slider item
            sliderItem.append(img);
        },
        {
            minWidth: maxWidth,
            minHeight: maxHeight,
            maxWidth: maxWidth,
            maxHeight: maxHeight,
            orientation: true,
            crop: true
        } // Options
    );

    // add to current list
    currentItemList.push(sliderItem);

    // mark as new
    sliderItem.attr('data-item-id', dog_id);
    sliderItem.attr('data-item-name', dog_name);
    sliderItem.attr('data-item-index', currentItemList.length);

    trace("currentItemList.length:", currentItemList.length);

    // add item to slider
    $(SLIDER).slick('slickAdd', sliderItem);
    $(SLIDER).slick('slickGoTo', currentItemList.length);
}

function onRemovePlaylistEntryItem(item)
{
    trace("onRemovePlaylistEntryItem().item:", item);

    swal({
        title: "Remove Item?",
        icon: "warning",
        closeOnClickOutside: false,
        closeOnEsc: false,
        buttons: true,
        dangerMode: true,
    })
        .then(function(willDelete)
        {
            if (!willDelete) return;

            trace("Removing photo from slider");

            // get actual slide item div
            var slideItem = $(item).parent().parent();

            // get index
            const dataItemID = $(slideItem).attr('data-item-id');             // database ID
            const dataItemIndex = $(slideItem).attr('data-slick-index');      // slide Index
            const dataItemName = $(slideItem).attr('data-item-name');         // slide Name

            trace("onRemovePlaylistEntryItem().slideItem:", slideItem);
            trace("onRemovePlaylistEntryItem().dataItemID:", dataItemID);
            trace("onRemovePlaylistEntryItem().dataItemIndex:", dataItemIndex);

            trace("onRemovePlaylistEntryItem().***currentItemList:", currentItemList);

            // save item to our remove list if its not a new item
            // delete currentItemList[dataItemIndex];
            // delete uploadedFileList[dataItemName];

            currentItemList.splice(dataItemIndex, 1);

            trace("onRemovePlaylistEntryItem().currentItemList:", currentItemList);

            // remove item from slider
            $(SLIDER).slick('slickRemove', dataItemIndex);

            // refresh slider
            $(SLIDER).slick('refresh', true);

        });
}

/*
   Removes current photo from slider list
   @param: item = SliderItem DOM element
*/
function onRemovePhotoItem(item)
{
    trace("onRemovePhotoItem().item:", item);

    swal({
        title: "Remove photo?",
        icon: "warning",
        closeOnClickOutside: false,
        buttons: true,
        dangerMode: true,
    })
    .then(function(willDelete)
    {
        if (!willDelete) return;

        trace("Removing photo from slider");

        // get actual slide item div
        var slideItem = $(item).parent().parent();

        // get index
        const dataItemID = $(slideItem).attr('data-item-id');             // database ID
        const dataItemIndex = $(slideItem).attr('data-slick-index');      // slide Index
        const dataItemName = $(slideItem).attr('data-item-name');         // slide Name

        trace("onRemovePhotoItem().slideItem:", slideItem);
        trace("onRemovePhotoItem().dataItemID:", dataItemID);
        trace("onRemovePhotoItem().dataItemIndex:", dataItemIndex);

        // save item to our remove list if its not a new item
        if (dataItemID != "new")
        {
            removePhotoList.push(dataItemID);
        }
        else
        {
            delete uploadedFileList[dataItemName];
        }

        // remove item from slider
        $(SLIDER).slick('slickRemove', dataItemIndex);

        // refresh slider
        $(SLIDER).slick('refresh', true);
     });
}

function setRemovePhotoListToAjax(ajaxData)
{
    if (removePhotoList.length > 0)
    {
        trace('[app] add removed photos from list');

        for (var i = 0; i < removePhotoList.length; i++)
        {
            trace('removePhotoList.ID:', removePhotoList[i]);


        }

        var serial_arr = removePhotoList.join("<#>");

        ajaxData.append( 'removePhotoList', serial_arr );
    }

    return ajaxData;
}

function addSliderPhotosToAjaxData($form, $input)
{
    trace("[slider-tools] addSliderPhotosToAjaxData.form:", $form);
    trace("[slider-tools] addSliderPhotosToAjaxData.input:", $input);

    var ajaxData = new FormData($form[0]);

    $.each( uploadedFileList, function( key, file )
    {
        trace("[slider-tools] key:", key);
        trace("[slider-tools] file:", file);
        trace("[slider-tools] END***");
        ajaxData.append( $input.attr( 'name' ), file );
        //ajaxData.append('files[]', file );
        trace("[slider-tools] input:****files[]");
    });

    return ajaxData;
}