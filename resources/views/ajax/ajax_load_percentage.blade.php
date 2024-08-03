<form action="{{ url('setPercentage') }}" method="POST">
    @csrf
    <input type="hidden" value="{{ $employee->id }}" class="form-control" name="id">
    <div class="row">
        <div class="col-md-6">
            OPD (%)
        </div>
        <div class="form-group col-md-6">
            <input type="number" step="0.01" class="form-control" value="{{ $employee->opd_percentage }}"
                name="opd" max="100">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            IPD (%)
        </div>
        <div class="form-group col-md-6">
            <input type="number" step="0.01" class="form-control" value="{{ $employee->ipd_percentage }}"
                name="ipd" max="100">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            OPD (Amount)
        </div>
        <div class="form-group col-md-6">
            <input type="number" step="0.01" class="form-control" value="{{ $employee->opd_amount }}"
                name="opd_amount">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            IPD (Amount)
        </div>
        <div class="form-group col-md-6">
            <input type="number" step="0.01" class="form-control" value="{{ $employee->ipd_amount }}"
                name="ipd_amount">
        </div>
    </div>
    @foreach ($mainLabs as $lab)
        <div class="row">
            <div class="col-md-6">
                {{ $lab->dep_name }} (%)
            </div>
            <div class="form-group col-md-6">
                <input type="hidden" value="{{ $lab->id }}" name="lab_id[]" />
                <input type="number" step="0.01" class="form-control"
                    value="{{ isset($labPercentage[$lab->id]) ? $labPercentage[$lab->id] : '' }}" name="percentage[]"
                    max="100">
            </div>
        </div>
    @endforeach
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary w-100">Save</button>
        </div>
    </div>
</form>
