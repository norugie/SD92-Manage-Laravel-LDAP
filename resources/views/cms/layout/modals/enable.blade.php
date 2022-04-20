<div class="modal fade" id="enableAccounts" tabindex="-1" role="dialog" aria-labelledby="enableAccountsTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="enableAccountsLabel">Re-enable the following user account(s):</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="new_form_validate" action="/cms/inactive/enable" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="text" id="employee_enable" name="employee_enable" value="" hidden>
                    <div class="row">
                        <div class="col-lg-4 col-sm-12">
                            <ul id="employees-to-enable"></ul>
                        </div>
                        <div class="col-lg-8 col-sm-12">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="employee_department">Department/School *</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" name="employee_department" id="employee_department" title="Select employee department/school" required>
                                            {{-- Department Options --}}
                                            @foreach($config['locations'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="employee_locations">Locations</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" multiple name="employee_locations[]" id="employee_locations" title="Select employee locations">
                                            {{-- Location Options --}}
                                            @foreach($config['locations'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="employee_roles">Roles and Sub-Departments</label>
                                    <div class="form-group">
                                        <select class="form-control show-tick" multiple name="employee_roles[]" id="employee_roles" title="Select employee roles" data-live-search="true">
                                            {{-- Role Options --}}
                                            <optgroup label="General Roles">
                                                @foreach($config['global_roles'] as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                            </div>
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