var imagesSelected_array = new Array();

$(document).ready(function(){
    document.getElementById('deletePicture').disabled = true;


    $("img").click(function(){
        if($(this).attr("style") == 'border: 2px solid blue;')
        {
            var index_imageSelected = imagesSelected_array.indexOf($(this).attr("id")); //Find the index of deselected image in the array of selected images
            imagesSelected_array.splice(index_imageSelected, 1 ); //Delete the deselected image from the array

            $(this).css("border", "0px solid black");
        }

        else
        {
            var imageSelected = $(this).attr("id"); //Recuperation nom image
            imagesSelected_array.push($(this).attr("id")); //Put the selected image in the array of selected image
            $(this).css("border", "2px solid blue");
        }

        if(imagesSelected_array.length > 0) //Avoid deleting no photo, so disbale delete button if there is no selected photo
            document.getElementById('deletePicture').disabled = false;
        else
            document.getElementById('deletePicture').disabled = true;
    });



    $("input").click(function()
    {
        if($(this).attr("id") == 'deletePicture')
            window.location.href = "../scripts/deleteImagesSelected.php?selectedImages=" + imagesSelected_array; //Send selected images via URL to the delete script
    });


});