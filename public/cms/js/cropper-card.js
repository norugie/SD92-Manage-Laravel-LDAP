(function() {

    var modal = $('#imageCropper');
    var image = document.getElementById('employee_card_img_crop_area');
    var username = $('#upload_id_image').data('username');
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

    $('#save_crop').click(function() {
        var canvas = cropper.getCropperCanvas({
            width: 250,
            height: 250
        });

        canvas.toBlob(function(blob) {
            var url = URL.createObjectURL(blob);
            var reader = new FileReader();
            reader.readAsDataURL(blob);
            reader.onloadend = function() {
                var base64data = reader.result;
                $.ajax({
                    url: '/cms/employees/' + username + '/upload',
                    method: 'POST',
                    data: { image: base64data },
                    success: function(data) {
                        modal.modal('hide');
                    }
                });
            };
        });
    });
})();