(function(window, $) {

    window.FormApp = function ($wrapper) {

        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.js-worker-add',
            this.addWorker.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-worker-remove',
            this.removeWorker.bind(this)
        );
    };

    $.extend(window.FormApp.prototype, {

        addWorker: function(e) {

            e.preventDefault();

            // Get the data-prototype
            var prototype = this.$wrapper.find(".js-worker-wrapper").data('prototype');

            // get the new index
            var workerIndex = this.$wrapper.find(".js-worker-wrapper").data('index');

            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newForm = prototype.replace(/__name__/g, workerIndex);

            // increase the index with one for the next item
            this.$wrapper.find(".js-worker-wrapper").data('index', workerIndex + 1);

            // Display the form in the page before the "new" link
            this.$wrapper.find(".js-worker-add").before(newForm);

        },

        removeWorker: function(e){

            e.preventDefault();
            var $link = $(e.currentTarget);

            $link.addClass('text-danger');
            $link.find('.fa')
                .removeClass('fa-close')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            $(e.currentTarget).closest('.form-page').fadeOut('normal', function () {
                $(this).remove();
            });

        }

    });

})(window, jQuery);

$(document).ready(function() {
    var $wrapper = $('.js-form-wrapper');
    var formApp = new FormApp($wrapper);
});