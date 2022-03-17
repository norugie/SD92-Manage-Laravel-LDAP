@extends ( 'cms.layout.layout' )

@section( 'custom-js' )

    <script src="/cms/js/ra-dt-buttons.js"></script>
    <script src="/cms/js/emp-form.js"></script>

@endsection

@section ( 'content' )

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                    <div class="header">
                        <div class="row clearfix">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-xs-sm-center">
                                <h4>INACTIVE EMPLOYEE LIST</h4>      
                            </div>
                        </div>
                    </div>
                <div class="body">
                    <div class="table-responsive">
                        <table id="inactive_employee_table" class="table dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th class="all">Name</th>
                                    <th>Username</th>
                                    <th>Email Address</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email Address</th>
                                    <th>Options</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <center>
                                            <input type="checkbox" class="filled-in chk-col-blue-grey employee-checkbox" id="employee_checkbox_{{ $employee->getFirstAttribute('samaccountname') }}" name="employee_checkbox" value="{{ $employee->getFirstAttribute('samaccountname') }}" data-name="{{ $employee->getFirstAttribute('displayname') }}">
                                            <label for="employee_checkbox_{{ $employee->getFirstAttribute('samaccountname') }}"></label>
                                        </center>
                                    </td>
                                    <td>{{ $employee->getFirstAttribute('displayname') }}</td>
                                    <td>{{ $employee->getFirstAttribute('samaccountname') }}</td>
                                    <td>{{ $employee->getFirstAttribute('mail') }}</td>
                                    <td>
                                        <center>
                                            <a href="/cms/inactive/{{ $employee->getFirstAttribute('samaccountname') }}/enable" type="button" class="btn bg-green waves-effect" style="display: inline-block;"><i class="material-icons">check</i><span>ENABLE EMPLOYEE</span></a>
                                        </center>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal - Enable Accounts --}}
    @include( 'cms.layout.modals.enable' )

@endsection