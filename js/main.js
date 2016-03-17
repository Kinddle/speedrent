
$(document).ready(function () {
	$('#modal-filter').modal('hide');
    /* search mobile */
    new UISearch( document.getElementById( 'sb-search' ) );

	$(".card").click(function(){
      window.location=$(this).find("a").attr("href"); return false;
	});
    /* equal-heights-responsive */
    $('.equal-height').equalHeights({
    });

    $(".fixed-form").stick_in_parent({
       parent: "#side-block",
       offset_top: 80,
       // bottoming: true
       inner_scrolling: false,
       spacer: true
    })
      .on("sticky_kit:stick", function(e) {
      console.log("has stuck!", e.target);
    })
      .on("sticky_kit:unstick", function(e) {
      console.log("has unstuck!", e.target);
    });

        /* Range of price */
    var bedroom = 1;
    var isFilter = $('#isFilter').val();

        var rangeValues = [600, 3500];
        if (isFilter == "1") {
           rangeValues = [parseInt($('#minPrice').val()), parseInt($('#maxPrice').val())];
        }
        // $('#landed').change(function() {
        //     $('#isFilter').val('1');
        //     $('#highrise').prop('checked', false); 
        //     $('#filters').submit();
        // });
       //    $('#highrise').change(function() {
       //        $('#isFilter').val('1');
      // //        $('#landed').prop('checked', false); 
      //        $('#filters').submit();
      //    });
    var slider = $( ".filter-price-range--ui" ).slider({
      range: true,
      min: 600,
      max: 3500,
      step: 100,
      values: rangeValues,
      slide: function( event, ui ) {
        $('.filter-price-range .min-price .amount').html(ui.values[0]);
        $('.filter-price-range .max-price .amount').html(ui.values[1]);
      },
      change: function( event, ui ) {
        $('.filter-price-range .min-price .amount').html(ui.values[0]);
        $('.filter-price-range .max-price .amount').html(ui.values[1]);
        $('#minPrice').val(ui.values[0]);
        $('#maxPrice').val(ui.values[1]);
        $('#isFilter').val('1');
        // $('#filters').submit();
      }
    });
    $('.filter-price-range .min-price .amount').html(rangeValues[0]);
    $('.filter-price-range .max-price .amount').html(rangeValues[1]);

    /* Custom selects */

    $('.select-item-text .option').html(function() {
      return $(this).siblings('.hidden').val();
    });

    $('.filter-selects-item-box .arr-left').click(function(e) {
      e.preventDefault();

      var select = $(this).siblings('.filter-select-item').find('select.hidden');
      if (select.find(':first').is(':not(:selected)')) {
        var optionIndex = select.find(':selected').index();
        select.find('option:selected').removeAttr("selected");
        select.find('option').eq(optionIndex - 1).attr('selected', 'selected');
        select.change();
        // $('#isFilter').val('1');
        // $('#filters').submit();
      }
    });

    $('.filter-selects-item-box .arr-right').click(function(e) {
      e.preventDefault();
      var select = $(this).siblings('.filter-select-item').find('select.hidden');
      if (select.find(':last').is(':not(:selected)')) {
        var optionIndex = select.find(':selected').index();
        select.find('option:selected').removeAttr("selected");
        select.find('option').eq(optionIndex + 1).attr('selected', 'selected');
        select.change();
        // $('#isFilter').val('1');
        // $('#filters').submit();
      }
    });

    $('.select-item-text select.hidden').change(function() {
      var value = $(this).val();
      $(this).siblings('.option').html(value);
    });

    /* Reset filters */

    // $('.left-side .reset-filters').click(function(e) {
    //   e.preventDefault();
    //   $('.left-side .select-item-text select.hidden').each(function() {
    //     $(this).find('option:first').attr('selected', 'selected');
    //     $(this).change();
    //   });

    //   console.dir(slider);
    //   slider.slider("values", rangeValues);
    //   $('#isFilter').val('0');
    //   $('#filters').submit();
    // });
});