require(["jquery"], function ($) {
    $(document).ready(function(){

        $(".myModal_hika_link").click(function() {
            var link = $(this).attr('link');
            var target = $(this).attr('data-target');
            $(target + " .modal-body").html('<iframe src="' + link + '?frame=1"></iframe>');
        });

        $('#sel1').change(function() {
            $("#sel2 option:not([value='']), #sel3 option:not([value=''])").hide();
            $("#sel2 option[value='']").attr('selected', 'selected');
            $("#sel3 option[value='']").attr('selected', 'selected');
            var sel_lp_1 = $(this).val();
            if( sel_lp_1 != '' ){
                $("#sel2 option[data-parentid="+sel_lp_1+"]").show();
            }

        });

        $('#sel2').change(function() {
            $("#sel3 option:not([value=''])").hide();
            $("#sel3 option[value='']").attr('selected', 'selected');
            var sel_lp_2 = $(this).val();
            if( sel_lp_2 != '' ){
            $("#sel3 option[data-parentid="+sel_lp_2+"]").show();
            }
        });
        $('#sel3').change(function() {
            var sel_lp_3 = $(this).val();
            if( sel_lp_3 != '' ){
                window.location.href = sel_lp_3;
            }

        });

    });
});
