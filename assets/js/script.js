require(["jquery"], function ($) {
    $(document).ready(function(){

        $(".myModal_hika_link").click(function() {
            var link = $(this).attr('link');
            var target = $(this).attr('data-target');
            $(target + " .modal-body").html('<iframe src="' + link + '?frame=1"></iframe>');
        });

        $('#sel1').on('change', function(e){

            $("#sel1style").html();

            $(".sel2options, .sel3options").addClass('hideimportant');
            $("#sel2 option[value='']").attr('selected', 'selected');
            $("#sel3 option[value='']").attr('selected', 'selected');
            var sel_lp_1 = $(this).val();
            if( sel_lp_1 != '' ){
                $(".sel2options.parentcat_"+sel_lp_1+"").removeClass('hideimportant');
                $("#sel1style").html(".sel2options.parentcat_"+sel_lp_1+"{display:block!important}");
            }

        });

        $('#sel2').on('change', function(e){

            $("#sel2style").html();

            $(".sel3options").addClass('hideimportant');
            $("#sel3 option[value='']").attr('selected', 'selected');
            var sel_lp_2 = $(this).val();
            if( sel_lp_2 != '' ){
                $(".sel3options.parentcar_"+sel_lp_2+"").removeClass('hideimportant');
                $("#sel2style").html(".sel3options.parentcar_"+sel_lp_2+"{display:block!important}");
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
