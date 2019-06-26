jQuery(document).ready(function() {

    var types = [];
    $.each( $(".js-machValue"), function() {
        types.push($(this).data('type'));
    });


    var machOutArray = [];
    $.each( $(".js-machValueOut"), function() {

        var machOut = $(this).find(".js-machOut").html();

        var machOutOld = $(this).find(".js-machOutOld").html();

        var machOutDiff = machOut - machOutOld;
        $(this).find(".js-machOutDiff").html(machOutDiff);
        machOutArray.push(machOutDiff);
    });

    var inSum = [];
    $.each(types, function( index, value ) {
        inSum[value] = []
    });
    var sumTotal = {};

    $.each( $(".js-machValue"), function(index) {

        var machIn = $(this).find(".js-machIn").html();
        var machInOld = $( this ).find(".js-machInOld").html();

        var machInDiff = machIn - machInOld;
        $(this).find(".js-machInDiff").html(machInDiff);

        var credits = machInDiff - machOutArray[index];
        $(this).find(".js-credits").html(credits);

        var ratio = $(this).data('ratio');
        var din = Math.round(credits / ratio);
        $(this).find(".js-din").html(din);

        var type = $(this).data('type');
        inSum[type].push(machInDiff);
        var total = 0;
        for (var i = 0; i < inSum[type].length; i++) {
            total += inSum[type][i]/$(this).data('ratio') << 0;
        }
        sumTotal[type] = total;
    });

    $.each( sumTotal, function(index, value) {
        $.each( $(".js-statistics"), function() {
            if($(this).data('type') == index) {
                $(this).find('.js-inValue').html(value);
            }
        });
    });

    var sum = 0;
    $.each($('.js-din'), function() {
        sum = sum + Number($(this).html());
    });

    $.each($(".js-jp-din"), function() {
            sum = sum + Number($(this).html());
    });

    $("#js-total").html(sum);

    var profit = [];
    $.each(types, function( index, value ) {
        profit[value] = []
    });
    var sumProfit = {};
    $.each($(".js-machValue"), function() {
        var type = $(this).data('type');
        var din = $(this).find(".js-din").html();
        profit[type].push(din);

        var total = 0;
        for (var i = 0; i < profit[type].length; i++) {
            total += profit[type][i] << 0;
        }
        sumProfit[type] = total;
    });

    $.each($(".js-jp-din"), function() {
        var type = $(this).closest('tr').data('type-name');
        sumProfit[type] += Number($(this).html());
    });

    $.each(sumProfit, function( index, value ) {
        $.each($('.js-statistics'), function() {
            if($(this).data('type') == index) {
                $(this).find('.js-profit').html(value);
            }
        });
    });

    $.each($(".js-statistics"), function() {
        var inValue = $(this).find('.js-inValue').html();
        var profit = $(this).find('.js-profit').html();
        var percentage = Math.round(profit/inValue * 100);

        $(this).find('.js-percentage').html(percentage);
    });

    if (sum < 200000) {
        var salary = 220*124;
    } else {
        salary = 220*124 + sum/100*2;
        salary = Math.round(salary);
    }

    $(".js-salary").html(salary);

    $('.js-format').priceFormat({
        allowNegative: true,
        clearPrefix: true,
        prefix: '',
        centsLimit: 0,
        thousandsSeparator: ','
    });
});