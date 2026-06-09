@if(isset($companies) && $companies->isNotEmpty())
    <div class="col-md-3">
        <div class="form-group">
            <label for="company_id">Company</label>
            <select name="company_id" id="company_id" class="form-control select2">
                <option value="" {{ empty($selectedCompanyId) ? 'selected' : '' }}>All Companies</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ (int) ($selectedCompanyId ?? 0) === (int) $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
@endif
