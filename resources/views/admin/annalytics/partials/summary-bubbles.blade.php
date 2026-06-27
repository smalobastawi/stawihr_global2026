<div class="row" style="margin-bottom: 20px;">
    @foreach($summary as $bubble)
        <div class="col-lg-3 col-md-6">
            <div class="white-box">
                <h3 class="box-title">{{ $bubble['label'] }}</h3>
                <ul class="list-inline two-part">
                    <li><i class="mdi {{ $bubble['icon'] }} text-{{ $bubble['color'] }}"></i></li>
                    <li class="text-right"><span class="counter">{{ $bubble['value'] }}</span></li>
                </ul>
            </div>
        </div>
    @endforeach
</div>
