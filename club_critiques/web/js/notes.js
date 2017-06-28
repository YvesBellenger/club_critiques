$(document).ready(function(){
    var url = window.location.href;
    var noteActuelle=$(".note-actuelle").attr('id');
    var contentID = $("#note-oeuvre").data("content-id")
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
                url : "/club_critiques/club_critiques/web/app_dev.php/contenus/note/save",
                data: { "note" : noteActuelle, "content-id" : contentID  },
                success: function(data) {
                    //console.log(data);
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
                }
            }).fail(function(){
              console.log("fail");
            });



        }
    )
});
