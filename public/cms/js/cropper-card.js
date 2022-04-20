(function() {

    var $modal = $('#imageCropper');
    var image = document.getElementById('card_img_crop_area');
    var username = $('#upload_id_image').data('username');
    var userID = $('#upload_id_image').data('usernumber');
    var type = $('#upload_id_image').data('type');
    var cropper;

    $('#new_card_img').change(function(event) {
        var files = event.target.files;

        var done = function(url) {
            image.src = url;
            $modal.modal('show');
        };

        var reader;
        var file;

        if (files && files.length > 0) {
            file = files[0];

            if (URL) {
                done(URL.createObjectURL(file));
            } else if (FileReader) {
                reader = new FileReader();
                reader.onload = function(e) {
                    done(reader.result);
                };
                reader.readAsDataURL(file);
            }
        }
    });

    $modal.on('shown.bs.modal', function() {
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
        $modal.modal('hide');
        var canvas;
        if (cropper) {
            canvas = cropper.getCroppedCanvas({
                width: 160,
                height: 160
            });

            canvas.toBlob(function(blob) {
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function() {
                    var base64data = reader.result;
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/cms/' + type + '/' + username + '/update/image/' + userID,
                        method: 'POST',
                        data: { image: base64data },
                        success: function() {
                            location.reload();
                        }
                    });
                };
            });
        }
    });
})();