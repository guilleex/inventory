(function(window, $) {

	window.IncomeApp = function ($wrapper) {

		this.$wrapper = $wrapper;

		this.addForm();

		this.loadIncomeInputs();

		this.$wrapper.on(
			'keypress',
			'.submitreplacement',
			this.submitForm.bind(this)
		);

		this.$wrapper.on(
			'submit',
			'.js-form',
			this.handleFormSubmit.bind(this)
		);

		this.$wrapper.on(
			'click',
			'.js-delete-income',
			this.handleIncomeInputDelete.bind(this)
		);

	};

	$.extend(window.IncomeApp.prototype, {

		submitForm: function (e) {

			if(e.which == 13) {
				if($(e.currentTarget).attr("name") == "value") {
					$(e.currentTarget).closest('form').trigger('submit');
				} else {
					$(e.currentTarget).next().focus();
				}
			}

		},

		addForm: function() {
			var self = this;
			this.$wrapper.each(function() {
				var data = {};
				data.worker = $(this).attr('data-worker');

				tplText = $('#js-inc-new-template').html();

				var tpl = _.template(tplText);
				var html = tpl(data);

				$(this).append($.parseHTML(html));
				$(this).find(':input').each(function () {
					if($(this).attr('name') == 'value') {
						self._formatNumber($(this));
					}
				});

			});
		},

		_addRow: function(incomeInput, $row) {

			var tplText = $('#js-inc-template').html();
			var tpl = _.template(tplText);
			var html = tpl(incomeInput);

			if($row.hasClass('js-new')) {
				$row.before($.parseHTML(html));
				return;
			}
			$row.replaceWith($.parseHTML(html));

		},

		_mapErrorsToForm: function(errorData, $form) {

			this._removeFormErrors();

			$form.find(':input').each(function() {
				var fieldName = $(this).attr('name');
				var $wrapper = $(this).closest('tr');

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
			this.$wrapper.find('.js-new-input-row').removeClass('has-error');
			this.$wrapper.find('.js-input-row').removeClass('has-error');
		},

		_clearForm: function($form) {

			this._removeFormErrors();
			$form[0].reset();
		},

		loadIncomeInputs: function() {

			var self = this;

			this.$wrapper.each(function() {

				if($(this).data('inputs')>0) {

					var $table = $(this);
					var worker = $(this).data('worker');
					var $row = $(this).find('.js-input-row');

					$.ajax({
						url: Routing.generate('incomeInput_list', { 'worker': worker }),
						success: function(data) {
							$.each(data.items, function(key, incomeInput) {
								self._addRow(incomeInput, $row);
							});
							self._calculate($table);

							$row.prevAll().find('.js-input-log').each(function () {
								self._formatNumber($(this));
							});

						}
					});

				}
			});
		},

		handleFormSubmit: function(e) {

			e.preventDefault();
			var self = this;
			var $form = $(e.currentTarget);
			var $row = $form.closest('tr');
			var $table = $(e.currentTarget).closest('table');

			var formData = {};

			$.each($form.serializeArray(), function(key, fieldData) {
				if (fieldData.name === 'value') {
					formData[fieldData.name] = $form.find('.js-number-format').unmask();;
				} else {
					formData[fieldData.name] = fieldData.value;
				}
			});

			$.ajax({
				url: $form.data('url'),
				method: 'POST',
				data: JSON.stringify(formData),
				success: function(data) {
					self._addRow(data, $row);
					self._clearForm($form);
					self._calculate($table);
					$row.prevAll().find('.js-number-format').each(function () {
						self._formatNumber($(this));
					});

				},
				error: function(jqXHR) {
					var errorData = JSON.parse(jqXHR.responseText);
					self._mapErrorsToForm(errorData.errors, $form);
				}
			});

		},

		_calculate: function (wrapper) {
			var total = 0;
			wrapper.find('.js-input-log').each(function () {
				$(this).unmask();
				total += Number($(this).attr('value'));
			});
			wrapper.find('.js-sum').html(total);

			this._formatNumber(wrapper.find('.js-sum'));
		},

		handleIncomeInputDelete: function (e) {

			e.preventDefault();
			var self = this;

			var $link = $(e.currentTarget);
			var $table = $(e.currentTarget).closest('table');

			$link.addClass('text-danger');
			$link.find('.fa')
				.removeClass('fa-trash')
				.addClass('fa-spinner')
				.addClass('fa-spin');

			var deleteUrl = $link.data('url');
			var $row = $link.closest('tr');
			// var self = this;
			$.ajax({
				url: deleteUrl,
				method: 'DELETE',
				success: function () {
					$row.fadeOut('normal', function () {
						$(this).remove();
						self._calculate($table);
					});
				}
			});
		},

		_formatNumber: function (number) {
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

})(window, jQuery);

$(document).ready(function() {
	var $wrapper = $('.js-table');
	var income = new IncomeApp($wrapper);
});