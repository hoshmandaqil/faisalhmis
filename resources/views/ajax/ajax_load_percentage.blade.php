<form action="{{ url('setPercentage') }}" method="POST">
    @csrf
    <input type="hidden" value="{{ $employee->id }}" class="form-control" name="id">
    <div class="row mb-4">
        <div class="col-md-4">
            <h6>Department Name</h6>
        </div>
        <div class="col-md-4">
            <h6>Percentage</h6>
        </div>
        <div class="col-md-4">
            <h6>Tax</h6>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            OPD
        </div>
        <div class="form-group col-md-4">
            <input type="number" step="0.01" class="form-control" value="{{ $employee->opd_percentage }}"
                name="opd" max="100" placeholder="Percentage">
        </div>
        <div class="form-group col-md-4">
            <input type="number" step="0.01" class="form-control" value="{{ $employee->opd_tax }}"
                name="opd_tax" max="100" placeholder="0.00">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            IPD
        </div>
        <div class="form-group col-md-4">
            <input type="number" step="0.01" class="form-control" value="{{ $employee->ipd_percentage }}"
                name="ipd" max="100" placeholder="Percentage">
        </div>
        <div class="form-group col-md-4">
            <input type="number" step="0.01" class="form-control" value="{{ $employee->ipd_tax }}"
                name="ipd_tax" max="100" placeholder="0.00">
        </div>
    </div>
    @foreach ($mainLabs as $lab)
        <div class="row">
            <div class="col-md-4">
                {{ $lab->dep_name }}
            </div>
            <div class="form-group col-md-4">
                <input type="hidden" value="{{ $lab->id }}" name="lab_id[]" />
                <input type="number" step="0.01" class="form-control"
                    value="{{ isset($labPercentage[$lab->id]) ? $labPercentage[$lab->id] : '' }}" name="percentage[]"
                    max="100" placeholder="0.00">
            </div>
            <div class="form-group col-md-4">
                <input type="number" step="0.01" class="form-control"
                    value="{{ isset($labTax[$lab->id]) ? $labTax[$lab->id] : '' }}" name="tax[]" max="100"
                    placeholder="0.00">
            </div>
        </div>
    @endforeach
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary w-100">Save</button>
        </div>
    </div>
</form>
