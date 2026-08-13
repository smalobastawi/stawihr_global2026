@extends('admin.master')
@section('content')
@section('title')
    Set My Performance Goals
@endsection
<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ess.performance.myAppraisals') }}">My Performance</a></li>
                <li class="breadcrumb-item active">Set Goals</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-target fa-fw"></i>
                    Set Goals / Objectives / Focus Areas — {{ $appraisal->review_period }}
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('info'))
                            <div class="alert alert-info alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                                <strong>{{ session()->get('info') }}</strong>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            Define your focus areas and the metrics/criteria under each area.
                            Focus area weights must total <strong>100%</strong>.
                            After saving, you will rate yourself against these goals, then submit for supervisor review.
                            @if(!empty($setting->policy_notes))
                                <hr>
                                <strong>Policy notes:</strong> {{ $setting->policy_notes }}
                            @endif
                        </div>

                        <form action="{{ route('ess.performance.saveGoals', $appraisal->appraisal_id) }}" method="POST" id="staffGoalsForm">
                            @csrf
                            <div id="focusAreasContainer">
                                @php
                                    $seedAreas = old('focus_areas');
                                    if (!$seedAreas) {
                                        $seedAreas = $existingFocusAreas->map(function ($fa) {
                                            return [
                                                'focus_area_name' => $fa->focus_area_name,
                                                'weight' => $fa->weight,
                                                'description' => $fa->description,
                                                'goals' => $fa->goals->map(function ($g) {
                                                    return [
                                                        'strategic_objective' => $g->strategic_objective,
                                                        'performance_metric' => $g->performance_metric,
                                                        'performance_target' => $g->performance_target,
                                                        'key_initiatives' => $g->key_initiatives,
                                                        'itemized_weighting' => $g->itemized_weighting,
                                                    ];
                                                })->values()->all(),
                                            ];
                                        })->values()->all();
                                    }
                                    if (empty($seedAreas)) {
                                        $seedAreas = [[
                                            'focus_area_name' => '',
                                            'weight' => '',
                                            'description' => '',
                                            'goals' => [[
                                                'strategic_objective' => '',
                                                'performance_metric' => '',
                                                'performance_target' => '',
                                                'key_initiatives' => '',
                                                'itemized_weighting' => '',
                                            ]],
                                        ]];
                                    }
                                @endphp

                                @foreach($seedAreas as $faIndex => $focusArea)
                                    <div class="panel panel-default focus-area-block" data-fa-index="{{ $faIndex }}">
                                        <div class="panel-heading">
                                            <strong>Focus Area #<span class="fa-number">{{ $faIndex + 1 }}</span></strong>
                                            <button type="button" class="btn btn-danger btn-xs pull-right remove-focus-area" {{ count($seedAreas) <= 1 ? 'disabled' : '' }}>
                                                <i class="fa fa-trash"></i> Remove Area
                                            </button>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Focus Area / Objective Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control"
                                                               name="focus_areas[{{ $faIndex }}][focus_area_name]"
                                                               value="{{ $focusArea['focus_area_name'] ?? '' }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Weight (%) <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" min="0" max="100" class="form-control fa-weight"
                                                               name="focus_areas[{{ $faIndex }}][weight]"
                                                               value="{{ $focusArea['weight'] ?? '' }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <input type="text" class="form-control"
                                                               name="focus_areas[{{ $faIndex }}][description]"
                                                               value="{{ $focusArea['description'] ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <h5>Metrics / Criteria</h5>
                                            <div class="goals-container">
                                                @foreach(($focusArea['goals'] ?? []) as $gIndex => $goal)
                                                    <div class="well well-sm goal-block" data-g-index="{{ $gIndex }}">
                                                        <div class="row">
                                                            <div class="col-md-11">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Strategic Objective <span class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                   name="focus_areas[{{ $faIndex }}][goals][{{ $gIndex }}][strategic_objective]"
                                                                                   value="{{ $goal['strategic_objective'] ?? '' }}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Performance Metric / Criteria <span class="text-danger">*</span></label>
                                                                            <input type="text" class="form-control"
                                                                                   name="focus_areas[{{ $faIndex }}][goals][{{ $gIndex }}][performance_metric]"
                                                                                   value="{{ $goal['performance_metric'] ?? '' }}" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>Performance Target <span class="text-danger">*</span></label>
                                                                            <textarea class="form-control" rows="2"
                                                                                      name="focus_areas[{{ $faIndex }}][goals][{{ $gIndex }}][performance_target]" required>{{ $goal['performance_target'] ?? '' }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label>Key Initiatives</label>
                                                                            <textarea class="form-control" rows="2"
                                                                                      name="focus_areas[{{ $faIndex }}][goals][{{ $gIndex }}][key_initiatives]">{{ $goal['key_initiatives'] ?? '' }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="form-group">
                                                                            <label>Weighting <span class="text-danger">*</span></label>
                                                                            <input type="number" step="0.01" min="0" max="100" class="form-control"
                                                                                   name="focus_areas[{{ $faIndex }}][goals][{{ $gIndex }}][itemized_weighting]"
                                                                                   value="{{ $goal['itemized_weighting'] ?? '' }}" required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 text-right">
                                                                <button type="button" class="btn btn-danger btn-xs remove-goal" title="Remove metric" {{ count($focusArea['goals'] ?? []) <= 1 ? 'disabled' : '' }}>
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-default btn-sm add-goal">
                                                <i class="fa fa-plus"></i> Add Metric / Criteria
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="m-b-20">
                                <button type="button" class="btn btn-info" id="addFocusArea">
                                    <i class="fa fa-plus"></i> Add Focus Area
                                </button>
                                <span class="m-l-15">
                                    Total focus area weight: <strong id="weightTotal">0</strong>%
                                </span>
                            </div>

                            <div class="form-actions">
                                <a href="{{ route('ess.performance.myAppraisals') }}" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-check"></i> Save Goals & Continue to Self Rating
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
(function () {
    function reindex() {
        $('#focusAreasContainer .focus-area-block').each(function (faIndex) {
            var $fa = $(this);
            $fa.attr('data-fa-index', faIndex);
            $fa.find('.fa-number').text(faIndex + 1);
            $fa.find('[name^="focus_areas["]').each(function () {
                var name = $(this).attr('name');
                name = name.replace(/focus_areas\[\d+]/, 'focus_areas[' + faIndex + ']');
                $(this).attr('name', name);
            });
            $fa.find('.goal-block').each(function (gIndex) {
                $(this).attr('data-g-index', gIndex);
                $(this).find('[name*="[goals]["]').each(function () {
                    var name = $(this).attr('name');
                    name = name.replace(/\[goals\]\[\d+\]/, '[goals][' + gIndex + ']');
                    $(this).attr('name', name);
                });
            });
            $fa.find('.remove-focus-area').prop('disabled', $('#focusAreasContainer .focus-area-block').length <= 1);
            $fa.find('.remove-goal').prop('disabled', $fa.find('.goal-block').length <= 1);
        });
        updateWeightTotal();
    }

    function updateWeightTotal() {
        var total = 0;
        $('.fa-weight').each(function () {
            total += parseFloat($(this).val() || 0);
        });
        $('#weightTotal').text(total.toFixed(2));
        $('#weightTotal').css('color', Math.abs(total - 100) < 0.01 ? 'green' : 'red');
    }

    function goalTemplate(faIndex, gIndex) {
        return `
            <div class="well well-sm goal-block" data-g-index="${gIndex}">
                <div class="row">
                    <div class="col-md-11">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Strategic Objective <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="focus_areas[${faIndex}][goals][${gIndex}][strategic_objective]" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Performance Metric / Criteria <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="focus_areas[${faIndex}][goals][${gIndex}][performance_metric]" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Performance Target <span class="text-danger">*</span></label>
                                    <textarea class="form-control" rows="2" name="focus_areas[${faIndex}][goals][${gIndex}][performance_target]" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Key Initiatives</label>
                                    <textarea class="form-control" rows="2" name="focus_areas[${faIndex}][goals][${gIndex}][key_initiatives]"></textarea>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Weighting <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" name="focus_areas[${faIndex}][goals][${gIndex}][itemized_weighting]" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1 text-right">
                        <button type="button" class="btn btn-danger btn-xs remove-goal" title="Remove metric"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>`;
    }

    function focusAreaTemplate(faIndex) {
        return `
            <div class="panel panel-default focus-area-block" data-fa-index="${faIndex}">
                <div class="panel-heading">
                    <strong>Focus Area #<span class="fa-number">${faIndex + 1}</span></strong>
                    <button type="button" class="btn btn-danger btn-xs pull-right remove-focus-area">
                        <i class="fa fa-trash"></i> Remove Area
                    </button>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Focus Area / Objective Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="focus_areas[${faIndex}][focus_area_name]" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Weight (%) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control fa-weight" name="focus_areas[${faIndex}][weight]" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" class="form-control" name="focus_areas[${faIndex}][description]">
                            </div>
                        </div>
                    </div>
                    <h5>Metrics / Criteria</h5>
                    <div class="goals-container">
                        ${goalTemplate(faIndex, 0)}
                    </div>
                    <button type="button" class="btn btn-default btn-sm add-goal">
                        <i class="fa fa-plus"></i> Add Metric / Criteria
                    </button>
                </div>
            </div>`;
    }

    $(document).on('click', '#addFocusArea', function () {
        var index = $('#focusAreasContainer .focus-area-block').length;
        $('#focusAreasContainer').append(focusAreaTemplate(index));
        reindex();
    });

    $(document).on('click', '.remove-focus-area', function () {
        if ($('#focusAreasContainer .focus-area-block').length <= 1) return;
        $(this).closest('.focus-area-block').remove();
        reindex();
    });

    $(document).on('click', '.add-goal', function () {
        var $fa = $(this).closest('.focus-area-block');
        var faIndex = $fa.index();
        var gIndex = $fa.find('.goal-block').length;
        $fa.find('.goals-container').append(goalTemplate(faIndex, gIndex));
        reindex();
    });

    $(document).on('click', '.remove-goal', function () {
        var $fa = $(this).closest('.focus-area-block');
        if ($fa.find('.goal-block').length <= 1) return;
        $(this).closest('.goal-block').remove();
        reindex();
    });

    $(document).on('input', '.fa-weight', updateWeightTotal);
    reindex();
})();
</script>
@endsection
