
function storePost(route) {
    const csrf = $("input:hidden[name='_token']").attr('value');
    var form = new FormData();
    const val = $("#message").val();
    form.append('message', val);
    form.append('_token', csrf);
    if($('#picture')[0].files[0]){
        try {
            form.append('picture', $('#picture')[0].files[0]) //for now only one image can be sent
        }catch (e){
            console.log(e.message);
        }
    }

    $.ajax({
        type: 'POST',
        url: route,
        data: form,
        processData: false,
        contentType: false,

    }).done(function(data){
        $('#post_wrap').html(data);
    }).fail(function ( data , error){

    });

}

function editPost(route){
    $.get(route, function (data) {
        $('#post_modal').html(data);
    });
}

function updatePost(url){
    var form = new FormData();
    const csrf = $("input:hidden[name='_token']").attr('value');
    const message =  $("#edit_message").val();
    form.append('_token', csrf);
    form.append('message', message);

    if($('#editImage')[0].files[0]) {
        form.append('picture', $('#editImage')[0].files[0]);
    }

    form.append('_method', 'patch');
    $.ajax({
        type: 'POST',
        url: url,
        data: form,
        contentType: false,
        processData: false,

    }).done(function(){
        $('#edit_message').parent().after('<p>Successfuly updated</p>');
        window.setTimeout(function(){
            location.reload();
        }, 1000);

    }).fail(function(xhr){
            console.log(xhr.status);
        });

}

function deletePost(currentElement,url){
    const csrf = $("input:hidden[name='_token']").attr('value');
    $.ajax({
        type: "DELETE",
        url: url,
        data:{_token : csrf}
    }).done(function(){
        $(currentElement).parentsUntil(".gedf-card", ).parent().remove();
    }).fail(function(xhr){
        $(currentElement).parentsUntil(".gedf-card", ).parent().append('p').addClass('alert-danger').text(errorDisplay(xhr));
    })
}

function errorDisplay(xhr){
    if(xhr.status === 403){
        return 'You are not allowed to do that!';
    }
}
