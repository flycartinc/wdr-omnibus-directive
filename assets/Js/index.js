jQuery(document).ready(function ($){
    $(".awdr-od-select-rules").selectWoo();

    $('input[name="wdr-od-is-show-message-option"]').change(function(){
        if($(this).val() == "1"){
            $('#wdr_od_select_message_position').show();
            $('#wdr_od_override_omnibus_message_show').show();
            $('#wdr_od_omnibus_message').show();
        }else{
            $('#wdr_od_select_message_position').hide();
            $('#wdr_od_override_omnibus_message_show').hide();
            $('#wdr_od_omnibus_message').hide();
        }
    });

    $('#wdr-od-is-override-omnibus-message').change(function() {
        if(this.checked) {
            $('#wdr_od_select_message_position').hide();
        }else{
            $('#wdr_od_select_message_position').show();
        }
    });
});