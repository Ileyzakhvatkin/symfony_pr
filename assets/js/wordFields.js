import $ from "jquery";

$(document).ready(function() {

    let $wrapper = $('.words-fields-list');

    $(document).on('click', '.add-another-word', function(e) {
        e.preventDefault();

        // Get the data-prototype explained earlier
        let prototype = $wrapper.data('prototype');
        // get the new index
        let index = $wrapper.data('index');
        // Replace 'name' in the prototype's HTML to
        // instead be a number based on how many items we have
        let newForm = prototype.replace(/key/g, index);
        // increase the index with one for the next item
        $wrapper.data('index', index + 1);
        // Display the form in the page before the "new" link
        $('.add-another-word').before(newForm);
    });
});