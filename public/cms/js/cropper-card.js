(function() {

    var modal = $('#imageCropper');
    var image = document.getElementById('employee_card_img_crop_area');
    var cropper;

    $('#new_employee_card_img').change(function(event) {
        var files = event.target.files;

        var done = function(url) {
            image.src = url;
            modal.modal('show');
        };

        if (files && files.length > 0) {
            reader = new FileReader();
            reader.onload = function(event) {
                done(reader.result);
            };
            reader.readAsDataURL(files[0]);
        }
    });

    modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 3,
            preview: '.preview'
        });
    }).on('hidden.bs.modal', function() {
        cropper.destroy();
        cropper = null;
    });
})();