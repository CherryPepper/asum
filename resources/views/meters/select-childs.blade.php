<div class="select-meters">
    @if(!empty($meters))
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="meters-list row">
                        @foreach($meters as $meter)
                            <div class="col-lg-3 col-md-3 col-xs-3 meter-popup" data-serial="{{$meter->serial}}"
                                 data-address="{{\App\Helpers\Addresses::AdrString($meter->address)}}" data-type="{{$meter->type_id}}"
                                 data-id="{{$meter->id}}" data-status="{{$meter->status->name}}">

                                @if($meter->type_id == 1)
                                    <img src="/images/meter-ico.png">
                                @else
                                    <img src="/images/folder-ico.png">
                                @endif

                                <span class="serial">{{$meter->serial}}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="clear"></div>
        <p class="text-center mt-100 mb-100">По данным критериям не найдено ни одного счетчика</p>
    @endif

    <hr class="mt-50">
    <div class="col-lg-12">
        <button class="btn btn-info select-meters-btn f-r hidden">
            Выбрать
        </button>
    </div>
</div>