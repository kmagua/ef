
function getDataForm(url, header='<h3>Add Staff</h3>'){
    
    $("#igfrsystem-modal").modal('show');
    //console.log('here')
    //basePathWeb is defined in main.php layout file
    $('div#modalContent').html('<img src="' + basePathWeb + '/images/ajax-loader-blue.gif"/><p>Please wait ...</p>');
    //setTimeout(() => {  console.log("World!"); }, 5000);
    $.ajax({
        type: "GET",
        url: url,
        cache: false,
        success: function(result){
            //alert("huku ndani")
            $('#modalHeader').html(header);
            $('div#modalContent').html(result);
            //$( "#companystaff-dob" ).datepicker();
        },
        error:function(jqXHR){
            $('#modalHeader').html("Error Occured!");
            $('div#modalContent').html("<p style='color:red'>" + jqXHR.responseText + '</p>');
        }
    });
}

function saveDataForm(clickedButton, contentDivID='', tabToOpen = '', redirectUrl = ''){
    
    //console.log(clickedButton)
    var url = $(clickedButton.form).attr('action');
    var data = new FormData(clickedButton.form);
    //basePathWeb is defined in main.php layout file
    $('div#modalContent').html('<img src="' + basePathWeb + '/images/ajax-loader-blue.gif"/> <p>Please wait ...</p>');
    //console.log(data)
    //console.log(clickedButton.form.action)
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        contentType: false,       
        cache: false,
        processData:false,
        success: function(result){
            if(contentDivID){
                $('div#' + contentDivID).html(result);
            }else{
                $('div#modalContent').html(result);
            }
            if(tabToOpen){
                $('.tabs-x  a[href="#' + tabToOpen + '"]').tab('show');
                $(".tabs-x").find("li.active a").click();
            }
            if(redirectUrl){
                $(location).attr('href', redirectUrl)
            }
        },
        error:function(jqXHR){
            $('#modalHeader').html("Error Occured!");
            $('div#modalContent').html("<p style='color:red'>" + jqXHR.responseText + '</p>');
        }
    });
}

function ajaxDeleteRecord(url, returnUrl,header){
    $.ajax({
        type: "POST",
        url: url,
        success: function(){
            $.ajax({
                type: "GET",
                url: returnUrl,
                success: function(result){
                    $('#modalHeader').html(header);
                    $('div#modalContent').html(result);
                },        
            });
        },        
    });
}

function navigateToTab(tabId){
    $('.tabs-x  a[href="#' + tabId + '"]').tab('show');
    $(".tabs-x").find("li.active a").click();
}

function hideShow(val, hide, clear){
    if( Number(val) == 1 ){
        $('div#'+hide).show('slow');
    }else{
        $('#'+clear).val('');
        $('div#'+hide).hide('slow');
    }  
}

function updateSession(val, path){
    $.ajax({
        type: "GET",
        url: path,
        data: {value:val},
        success: function(){
            location.reload();
        },        
    });
}

function getFolderAndFilesData(link, folder_id){
    $.ajax({
        type: "GET",
        url: link,
        //contentType: "application/json",
        cache: false,
        //data: {value:val},
        success: function(rst){
            //console.log(rst)
            $('div#folder_id_' +folder_id).html(rst);
            $('div#folder_results_div').html(rst);
            $('#current_folder_id').val(folder_id)
        },
    });
    return false;
}

function dropDownItemsSelect(type)
{
    $("#igfrsystem-modal").modal('show');
    //alert(folder_link);
    var val = $('#current_folder_id').val();
    if(type == 'folder'){
        $.ajax({
            type: "GET",
            url: folder_link,
            cache: false,
            data: { fid : val},
            //contentType: "application/json",
            //data: {value:val},
            success: function(rst){
                $('#modalHeader').html("New Folder");
                $('div#modalContent').html(rst);
            },
        });
    }else if(type == 'file'){
        $.ajax({
            type: "GET",
            url: file_link,
            data: { fid : val},
            //contentType: "application/json",
            //data: {value:val},
            success: function(rst){
                $('#modalHeader').html("New File");
                $('div#modalContent').html(rst);           
            },
        });
    }
}

function deleteFolder()
{
    var confir = confirm('Are you sure you want to delete this folder? All contents in it will be lost!');
    if(confir){
        var val = $('#current_folder_id').val();
        //  = $('#current_folder_id').val();
        $.ajax({
            type: "GET",
            url: delete_link,
            data: { fid : val},
            //contentType: "application/json",
            //data: {value:val},
            success: function(rst){
                $('#modalHeader').html("New File");
                $('div#modalContent').html(rst);           
            },
        });
    }
}

function renameFolder()
{
    //var confir = confirm('Are you sure you want to delete this folder? All contents in it will be lost!');
    //if(confir){
        var val = $('#current_folder_id').val();
        //  = $('#current_folder_id').val();
        getDataForm(rename_link + '?id='+val, 'Rename Folder');
        /*$.ajax({
            type: "GET",
            url: rename_link,
            data: { id : val},
            //contentType: "application/json",
            //data: {value:val},
            success: function(rst){
                $('#modalHeader').html("New File");
                $('div#modalContent').html(rst);           
            },
        });*/
    //}
}

function showTab(tab_id){
    //alert(tab_id)
    //$('.nav-tabs a[href="#' + tab_id +'"]').click();
    $('.nav-tabs a[href="#' + tab_id + '"]').tab('show');
}