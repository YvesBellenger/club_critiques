var timer;
function search(elt) {
    clearTimeout(timer);
    if ($("#keywords").val()){
        timer = setTimeout(function request() {
            $.ajax({
                type: "POST",
                url: '/admin/api/content/search',
                data: {'keywords': $("#keywords").val()},
                async: false
            })
                .done(function (response) {
                    $('#contents').html(response);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    //alert('Error : ' + errorThrown);
                    $('#contents').html(' ');
                });
        }, 1000);
    }
    else
        $('#contents').html(' ');
}

function addContent(elt) {
    if (confirm('Voulez vous ajouter ce livre ?')) {
        var index = $(elt).data('index');
        $.ajax({
            type: "POST",
            url: '/admin/api/content/add',
            data: {'title': $("#title-"+index).text(),
                    'authors': $("#authors-"+index).text(),
                    'publishedDate': $("#publishedDate-"+index).text(),
                    'image': $("#image-"+index).attr('src'),
                    'description': $("#description-"+index).text(),
                    'category': $("#category-"+index).text()},
            async: false
        })
            .done(function (response) {
                alert(response)
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                alert('Error : ' + errorThrown);
            });
    }
}