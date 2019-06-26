'use strict';

(function(window, $, Routing) {

    window.LogApp = function ($wrapper) {

        this.$wrapper = $wrapper;

        this.loadCashRecords();

        this.loadInValueForms();

        this.loadOutValueForms();

        this.loadJackpotForms();

        this.calculate();

        this.formatNumber();

        this.$wrapper.on(
            'keypress',
            '.submitreplacement',
            this.submitForm.bind(this)
        );

        this.$wrapper.on(
            'submit',
            '.js-form',
            this.handleNewFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.js-delete-cash-register',
            this.handleCashRegisterDelete.bind(this)
        );

    };


    $.extend(window.LogApp.prototype, {

        formatNumber: function () {
           var self =this;
           this.$wrapper.find('.js-format').each(function(){

               if($(this).hasClass( "submitreplacement" )) {
                   self._formatNumberClearOnEmpty($(this));
               } else {
                   self._formatNumber($(this));
               }
           });
        },

        submitForm: function (e) {

            if(e.which == 13 && $(e.currentTarget).attr("name") == "value") {
                var FF = !(window.mozInnerScreenX == null);
                if(FF) {
                    // is firefox
                    $(e.currentTarget).closest('form').trigger('submit');
                    return false;
                }

                if (!$(e.currentTarget).closest('form').hasClass('js-new')) {
                    $(e.currentTarget).closest('form').trigger('submit');
                }

            } else if(e.which == 13) {
                $(e.currentTarget).next().focus();
            }

        },

        calculate: function () {

            var machOutArray = [];
            this.$wrapper.find(".js-machValueOut").each(function() {

                if ($( this ).find(".submitreplacement").val()) {
                    var machOut = $( this ).find(".submitreplacement").unmask();
                } else {
                    machOut = $(this).find(".js-machOut").html();
                }

                if ($( this ).find(".js-machOutOld").data('outvalueold')) {
                    var machOutOld = $( this ).find(".js-machOutOld").data('outvalueold');
                } else {
                    machOutOld = $( this ).find(".js-machOutOld").html();
                }

                if (machOut =='' || machOutOld === null) {

                    var machOutDiff = '';

                } else {

                    machOutDiff = machOut - machOutOld;
                    machOutArray.push(machOutDiff);

                }

                $(this).find(".js-machOutDiff").html(machOutDiff);

            });

            var types = this._getMachineTypes();

            var inSum = [];
            $.each(types, function( index, value ) {
                inSum[value] = []
            });
            var sumTotal = {};

            this.$wrapper.find(".js-machValue").each(function(index) {

                if ($( this ).find(".submitreplacement").val()) {
                    var machIn = $( this ).find(".submitreplacement").unmask();
                } else {
                    machIn = $(this).find(".js-machIn").html();
                }

                machIn = Number(machIn);

                if ($( this ).find(".js-machInOld").data('invalueold')) {
                    var machInOld = Number($( this ).find(".js-machInOld").data('invalueold'));
                } else {
                    machInOld = $( this ).find(".js-machInOld").html();
                }

                if (machIn ==='' || machInOld === null) {

                    var credits = '';
                    var din = '';

                } else {

                    var machInDiff = machIn - machInOld;

                    credits = machInDiff - machOutArray[index];

                    var ratio = $(this).data('ratio');
                    din = Math.round(credits / ratio);

                }

                $(this).find(".js-machInDiff").html(machInDiff);
                $(this).find(".js-credits").html(credits);
                $(this).find(".js-din").html(din);

                var type = $(this).data('type');

                inSum[type].push(machInDiff);
                var total = 0;
                for (var i = 0; i < inSum[type].length; i++) {
                    total += inSum[type][i]/$(this).data('ratio') << 0;
                }
                sumTotal[type] = total;


            });
            var self = this.$wrapper;
            $.each(sumTotal, function( index, value ) {
                self.find('.js-statistics').each(function() {
                    if($(this).data('type') == index) {
                        $(this).find('.js-inValue').html(value);
                    }
                });
            });

            var sum = 0;
            this.$wrapper.find('.js-din').each(function() {

                sum = sum + Number($(this).html());

            });

            this.$wrapper.find(".js-jp-din").each(function() {

                if ($(this).val()) {
                    sum = sum + Number($(this).val());
                } else {
                    sum = sum + Number($(this).html());
                }

            });

            var profit = [];
            $.each(types, function( index, value ) {
                profit[value] = []
            });
            var sumProfit = {};
            this.$wrapper.find(".js-machValue").each(function() {
                var type = $(this).data('type');
                var din = $(this).find(".js-din").html();
                profit[type].push(din);

                var total = 0;
                for (var i = 0; i < profit[type].length; i++) {
                    total += profit[type][i] << 0;
                }
                sumProfit[type] = total;
            });


            this.$wrapper.find(".js-jp-din").each(function() {
                var type = $(this).closest('tr').data('type-name');
                sumProfit[type] += Number($(this).val());
            });

            $.each(sumProfit, function( index, value ) {
                self.find('.js-statistics').each(function() {
                    if($(this).data('type') == index) {
                        $(this).find('.js-profit').html(value);
                    }
                });
            });

            this.$wrapper.find(".js-statistics").each(function() {
                var inValue = $(this).find('.js-inValue').html();
                var profit = $(this).find('.js-profit').html();
                var percentage = Math.round(profit/inValue * 100);

                $(this).find('.js-percentage').html(percentage);
            });

            this.$wrapper.find("#js-total").html(sum);
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
            var self = this;
            var $form = $(e.currentTarget);

            var formData = {};

            $.each($form.serializeArray(), function(key, fieldData) {
                if (fieldData.name === 'value') {
                    formData[fieldData.name] = $form.find('.js-format').unmask();;
                } else {
                    formData[fieldData.name] = fieldData.value;
                }
            });

            if($form.attr('name') == 'cash_register_form') {

                $.ajax({
                    url: $form.data('url'),
                    method: 'POST',
                    data: JSON.stringify(formData),
                    success: function(data) {
                        self._addRow(data, $form);
                        self._clearForm($form);
                        self.setFocus();
                        self.formatNumber();
                    },
                    error: function(jqXHR) {
                        var errorData = JSON.parse(jqXHR.responseText);
                        self._mapErrorsToForm(errorData.errors, $form);
                    }
                });
            } else {

                $.ajax({
                    url: $form.data('url'),
                    method: 'POST',
                    data: JSON.stringify(formData),
                    success: function(data) {
                        self._clearForm($form);
                        self._replaceForm(data, $form);
                        self.setFocus();
                        self.calculate();
                        self.formatNumber();
                    },
                    error: function(jqXHR) {
                        var errorData = JSON.parse(jqXHR.responseText);
                        self._mapErrorsToForm(errorData.errors, $form);
                    }
                });
            }
        },

        _mapErrorsToForm: function(errorData, $form) {

            this._removeFormErrors();

            $form.find(':input').each(function() {
                var fieldName = $(this).attr('name');
                var $wrapper = $(this).closest('.js-cash-register-log');

                if (!errorData[fieldName]) {
                    // no error!
                    return;
                }
                var $error = $('<span class="js-field-error help-block"></span>');
                $error.html(errorData[fieldName]);
                $error.insertAfter( $(this));
                $wrapper.addClass('has-error');

            });
        },

        _removeFormErrors: function() {
            // reset things!
            this.$wrapper.find('.js-field-error').remove();
            this.$wrapper.find('.js-cash-register-log').removeClass('has-error');
        },

        _clearForm: function($form) {
            this._removeFormErrors();
            $form[0].reset();
        },

        _addRow: function(cr, $form) {
            this.$wrapper.each(function() {
                if($(this).attr('id') == 'cash_register') {
                    var tplText = $('#js-cash-register-row-template').html();
                    var tpl = _.template(tplText);
                    var html = tpl(cr);

                    if($form.hasClass('js-new-form')) {
                        $form.closest('tr').before($.parseHTML(html));
                        return;
                    }
                    $form.closest('tr').replaceWith($.parseHTML(html));
                }
            });

            this._updateTotalCash();
        },

        _replaceForm: function(data, $form) {
            this.$wrapper.each(function() {
                if($(this).attr('id') == 'table_main') {

                    if($form.attr('name') == 'in_value_form') {
                        var tplText = $('#js-inValue-template').html();
                    } else if ($form.attr('name') == 'out_value_form') {
                        tplText = $('#js-outValue-template').html();
                    } else {
                        tplText = $('#js-jp-template').html();
                    }

                    var tpl = _.template(tplText);
                    var html = tpl(data);

                    $form.closest('td').replaceWith($.parseHTML(html));
                }
            });
        },

        loadInValueForms: function () {
              this.$wrapper.find('.js-in').each(function() {

                  var data = {};
                  data.machine = $(this).attr('data-machine');

                  if ($(this).attr('data-inValue') !== undefined) {

                      data.id = $(this).attr('data-id');
                      data.value = $(this).attr('data-inValue');
                      data.date = $(this).attr('data-date');

                      var tplText = $('#js-inValue-template').html();

                  } else {
                      tplText = $('#js-inValue-new-template').html();
                  }

                  var tpl = _.template(tplText);
                  var html = tpl(data);
                  $(this).replaceWith($.parseHTML(html));

              })
        },

        loadOutValueForms: function () {
            this.$wrapper.find('.js-out').each(function() {

                var data = {};
                data.machine = $(this).attr('data-machine');

                if ($(this).attr('data-outValue') !== undefined) {

                    data.id = $(this).attr('data-id');
                    data.value = $(this).attr('data-outValue');
                    data.date = $(this).attr('data-date');

                    var tplText = $('#js-outValue-template').html();

                } else {
                    tplText = $('#js-outValue-new-template').html();
                }

                var tpl = _.template(tplText);
                var html = tpl(data);
                $(this).replaceWith($.parseHTML(html));

            })
        },

        loadJackpotForms: function () {
            this.$wrapper.find('.js-jp').each(function() {

                var data = {};
                data.machineType = $(this).closest('tr').attr('data-type');

                if ($(this).closest('tr').attr('data-jp') !== undefined) {

                    data.id = $(this).closest('tr').attr('data-id');
                    data.value = $(this).closest('tr').attr('data-jp');
                    data.date = $(this).closest('tr').attr('data-date');

                    var tplText = $('#js-jp-template').html();

                } else {
                    tplText = $('#js-jp-new-template').html();
                }

                var tpl = _.template(tplText);
                var html = tpl(data);
                $(this).replaceWith($.parseHTML(html));

            })
        },

        loadCashRecords: function() {
            var self = this;

            this.$wrapper.each(function() {
                if($(this).data('date')) {
                    var date = $(this).data('date');
                    var $form = $(this).find('.js-new-form');

                    $.ajax({
                        url: Routing.generate('cash_register_list', { 'date': date }),
                        success: function(data) {
                            $.each(data.items, function(key, cr) {
                                self._addRow(cr,$form);
                            });
                        }
                    });
                }
            });
        },

        handleCashRegisterDelete: function (e) {
            e.preventDefault();
            var self = this;

            var $link = $(e.currentTarget);

            $link.addClass('text-danger');
            $link.find('.fa')
                .removeClass('fa-trash')
                .addClass('fa-spinner')
                .addClass('fa-spin');

            var deleteUrl = $link.data('url');
            var $row = $link.closest('tr');

            $.ajax({
                url: deleteUrl,
                method: 'DELETE',
                success: function () {
                    $row.fadeOut('normal', function () {
                        $(this).remove();
                        self._updateTotalCash();
                    });
                }
            });
        },

        _updateTotalCash: function () {

            var self = this;
            var totalCash = 0;

            this.$wrapper.find('.js-cr-log').each(function() {
                totalCash += Number($(this).attr('value'));
            });

            this.$wrapper.find('#js-total-cash').html(totalCash);

            this.$wrapper.find('.js-cr-log').each(function(){
                self._formatNumber($(this));
            });

            self._formatNumber(this.$wrapper.find('#js-total-cash'));

        },

        _getMachineTypes: function () {
            var types = [];

            this.$wrapper.find('.js-machValue').each(function() {
                types.push($(this).data('type'));
            });

            return types;
        },

        _formatNumber: function (number) {
            number.priceFormat({
                allowNegative: true,
                clearPrefix: true,
                prefix: '',
                centsLimit: 0,
                thousandsSeparator: ','
            });
        },

        _formatNumberClearOnEmpty: function (number) {
            number.priceFormat({
                allowNegative: true,
                clearPrefix: true,
                prefix: '',
                centsLimit: 0,
                thousandsSeparator: ',',
                clearOnEmpty: true
            });
        }

    });


})(window, jQuery, Routing);

$(document).ready(function() {
     var $wrapper = $('.js-table');
     var logApp = new LogApp($wrapper);
});