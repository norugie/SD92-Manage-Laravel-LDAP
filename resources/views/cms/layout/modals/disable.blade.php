<div class="modal fade" id="disableAccounts" tabindex="-1" role="dialog" aria-labelledby="disableAccountsTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="disableAccountsLabel">Disable the following user account(s):</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="new_form_validate" action="/cms/employees/disable" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="text" id="employee_disable" name="employee_disable" value="" hidden>
                    <div class="row">
                        <div class="col-sm-12">
                            <ul id="employees-to-disable"></ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-link waves-effect">SAVE CHANGES</button>
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>