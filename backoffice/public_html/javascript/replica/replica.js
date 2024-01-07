$(document).ready(function() {

    $.validator.addMethod("phone_number", function(value, element) {
        return this.optional(element) || value == value.match(/^[0-9+()-\s]+$/);
    }, trans('phone_number'));
    
    $.validator.messages.required = function(param, input) {
        var label = getLabelName(input);
        if($(input).is('select')) {
            return trans('required_select', trans(label))
        }
        return trans('required', trans(label));
    };
    
    $.validator.messages.maxlength = function(param, input) {
        let label = getLabelName(input);
        return trans('maxlength', trans(label), param);
    };

    $.validator.messages.email = function() {
        return trans('valid_email');
    }
    
    $('#contact_form').validate({
        rules: {
            name: {
                required: true,
                maxlength: 250,
            },
            email: {
                required: true,
                email: true,
                maxlength: 250,
            },
            phone: {
                required: true,
                phone_number: true,
                maxlength: 50,
            },
            address: {
                required: true,
                maxlength: 1000
            },
            message: {
                required: true,
                maxlength: 10000
            }
        },
    })
});

/**
 * [trans validation language]
 * @param  {[string]} key     [language key]
 * @param  {[string]} replace [replace string]
 * @return {[string]}         [language]
 */
function trans(key = "", replace = "", replace2 = "") {
    var replace = replace || "";
    var replace2 = replace2 || "";
    if(window.translations) {
        translation = window.translations[key] || key;
        if(translation) {
            translation = translation.replace("*", "").replace("%s", replace).toLowerCase();
            translation = translation.replace("*", "").replace("%s", replace2).toLowerCase();
            // return translation;
            return capitalizeFirstLetter(translation);
        }
    }
    return capitalizeFirstLetter(key.toLowerCase());
}

/**
 * [getLabel name of input]
 * @param  {[element]} input
 * @return {[string]}
 */
function getLabelName(input) {
    label = $(input).attr('name');
    return label
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}