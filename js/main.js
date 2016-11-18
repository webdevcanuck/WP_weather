
(function ($, window, document) {

    $(document).on("click", "#my_addToSelectedCities", function () {
        var addList = [];
        $('select[name=my_cityOptions] :selected').each(function (i, selected) {
            $(this).attr('disabled','disabled');
            addList[i] = '<option>' + $(selected).text() + "</option>";
        });
        $("#selectedcities").append(addList);
    });

    $(document).on("click", "#my_removeSelectedCities", function () {
        var removeList = [];
        $('select[name=my_selectedCities] :selected').each(function (i, selected) {
            $(this).remove();
        });
    });

})(window.jQuery, window, document);
