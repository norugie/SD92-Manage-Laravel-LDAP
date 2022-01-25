(function() {
    $('#new_employee_card_img').change(function(event) {
        var files = event.target.files;

        var done = function(url) {
            image.src = url;
            $modal.modal('show');
        };

        if (files && files.length > 0) {
            reader = new FileReader();
            reader.onload = function(event) {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });
})();