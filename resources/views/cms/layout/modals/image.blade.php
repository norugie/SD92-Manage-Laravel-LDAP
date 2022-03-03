<div class="modal fade" id="imageCropper" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="imageCropperLabel">New ID Image</h4>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="img-container">
                        <div class="row">
                            <div class="col-md-8">
                                {{-- Image Crop Area --}}
                                <img src="" class="img-responsive" id="card_img_crop_area" alt="ID Card New Image Crop Area">
                            </div>
                            <div class="col-md-4">
                                {{-- Image Crop Preview --}}
                                <center><div class="preview"></div></center>
                            </div>
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