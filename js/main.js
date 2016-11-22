(function($, window, document) {

  // Assign handlers immediately after making the request,
// and remember the jqxhr object for this request
var ajCityList = $.getJSON( "../wp-content/plugins/wp-plugin-test/us.json", function() {
console.log( "success" );
})
.done(function(data) {
  $.each( data, function( i, item ) {
        $( "<option>"+item.name+"</option>" ).appendTo( "select[name=my_cityOptions]" );
        console.log(item.name);
      });
  console.log( "second success" );
})
.fail(function() {
  console.log( "error" );
})
.always(function() {

  console.log( "complete" );
});

// Perform other work here ...

// Set another completion function for the request above
ajCityList.complete(function() {
console.log( "second complete" );
});

    $(document).on("click", "#my_addToSelectedCities", function() {
        var addList = [];
        $('select[name=my_cityOptions] :selected').each(function(i, selected) {
            $(this).attr('disabled', 'disabled');
            addList[i] = '<option>' + $(selected).text() + "</option>";
        });
        $("#selectedcities").append(addList);
    });

    $(document).on("click", "#my_removeSelectedCities", function() {
        var removeList = [];
        $('select[name=my_selectedCities] :selected').each(function(i, selected) {
            $(this).remove();
        });
    });

})(window.jQuery, window, document);
