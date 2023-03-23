jQuery(document).ready(function ($){
    $(".awdr-om-select-rules").selectWoo();

    $('input[name="show_omnibus_message_option"]').change(function(){
        if($(this).val() == "1"){
            $('#wdr_om_select_message_position').show();
            $('#wdr_om_select_rule').show();
            $('#wdr_om_override_omnibus_message').show();
            $('#wdr_om_omnibus_message').show();
        }else{
            $('#wdr_om_select_message_position').hide();
            $('#wdr_om_select_rule').hide();
            $('#wdr_om_override_omnibus_message').hide();
            $('#wdr_om_omnibus_message').hide();
        }
    });
});