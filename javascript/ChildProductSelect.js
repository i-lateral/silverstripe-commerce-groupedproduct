/**
 * @package groupedproduct
 */
(function($) {
    $(".childproductselect input[type=radio]").change(function () {
        $(".price .value").text($(this).attr("data-price"));
    });
})(jQuery);


