$(document).ready(function(){
    var noteActuelle=$(".note-actuelle").attr('id');
    var contentID = $("#note-oeuvre").data("content-id");
    $("#note-oeuvre div").hover(
        function() {
            var selectedNote=$(this).attr('id');
            for(var i=1; i<=4;i++)
            {
                $("#"+i).removeClass("note-active");
                $("#"+i).addClass("note-inactive");
            }
            for(var i=1;i<=selectedNote;i++)
            {
                $("#"+i).removeClass("note-inactive");
                $("#"+i).addClass("note-active");
            }

        },function(){

            for(var i=1; i<=4;i++)
            {
                $("#"+i).removeClass("note-active");
                $("#"+i).addClass("note-inactive");
            }
            for(var i=1;i<=noteActuelle;i++)
            {
                $("#"+i).removeClass("note-inactive");
                $("#"+i).addClass("note-active");
            }
    });

    $("#note-oeuvre div").click(
        function(){
            var selectedNote=$(this).attr('id');
            noteActuelle=selectedNote;
            $.ajax({
                type: "POST",
                url: "/app_dev.php/contenus/note/save",
                data: {"note": noteActuelle, "content-id": contentID},
            })
            .done(function(data){
                for(var i=1; i<=4;i++)
                {
                    $("#"+i).removeClass("note-actuelle");
                    $("#"+i).removeClass("note-active");
                }
                for(var i=1;i<=selectedNote;i++)
                {
                    $("#"+i).addClass("note-active");
                }
                $("#"+selectedNote).addClass("note-actuelle");
                var params = '';
                if ($('#lobby_id').val() > 0) {
                    if ($('#from_invite') == true) {
                        params = '?from_invite=true';
                    }
                    window.location = "/app_dev.php/salon/" + $('#lobby_id').val() + "/register"+params
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown){
                alert('Error : ' + errorThrown);
            });



        }
    )
});
