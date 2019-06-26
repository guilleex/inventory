'use strict';

(function(window, $) {

    window.LogApp = function ($wrapper) {

        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'change',
            '.submitreplacement',
            this.submitForm.bind(this)
        );

        this.$wrapper.on(
            'submit',
            '.js-form',
            this.handleNewFormSubmit.bind(this)
        );
    };


    $.extend(window.LogApp.prototype, {

        submitForm: function (e) {
            var FF = !(window.mozInnerScreenX == null);
            if(FF) {
                // is firefox
                $(e.currentTarget).closest('form').trigger('submit');
            }
        },

        setFocus: function () {

            this.$wrapper.find('.submitreplacement').each(function () {

                var $string = $(this).attr("class");

                if ($(this).val() == '' && $string.toLocaleLowerCase().indexOf("jp") == -1) {
                    $(this).focus();

                    return false;
                }
            });

        },

        handleNewFormSubmit: function(e) {
            e.preventDefault();
            var $form = $(e.currentTarget);
            var self = this;
            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(jqXHR) {
                    $form.closest('.js-form-wrapper')
                        .html(jqXHR);
                    self.setFocus();
                },
                error: function(jqXHR) {
                    $form.closest('.js-form-wrapper')
                        .html(jqXHR.responseText);
                }
            });
        }

    });


})(window, jQuery);

// $.when( this.$wrapper.done ).then(
//     this.setFocus.bind(this)
// );