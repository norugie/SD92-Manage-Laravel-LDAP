<div class="modal fade" id="imageCropper" tabindex="-1" role="dialog" aria-labelledby="imageCropperTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="imageCropperLabel">New ID Image</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="img-container">
                                {{-- Image Crop Area --}}
                                <img src="" id="card_img_crop_area" alt="ID Card New Image Crop Area">
                            </div>
                        </div>
                        <div class="col-md-4 ml-auto">
                            {{-- Image Crop Preview --}}
                            <center><div class="preview"></div></center>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="save_crop" class="btn btn-link waves-effect">SAVE CHANGES</button>
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>