<div class="move-meter">
    @if(isset($parents))
    <ul class="breadcrumb">
        <li class="breadcrumb-home">
            <a href="{{route('meters.structure', ['id' => 0])}}">
                <i class="fa fa-home"></i>
            </a>
        </li>

        @foreach($parents as $parent)
            <li>
                <a href="{{route('meters.structure', ['id' => $parent->id])}}">
                    {{$parent->serial}}
                </a>
            </li>
        @endforeach
    </ul>
    @endif

    <input type="hidden" name="move" id="moveId" value="{{$move}}">

    @if(!empty($meters->total()))
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="meters-list row">
                    @foreach($meters as $meter)
                        <div class="col-lg-3 col-md-3 col-xs-3 meter-info" data-serial="{{$meter->serial}}"
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

                <div class="pull-right">
                    {{$meters->links()}}
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
        <a class="btn btn-default reset-parent" href="{{route('meter.resetParent', ['id' => $move])}}">
            Сбросить
        </a>

        <button class="btn btn-info move-meter-btn">
            Перенести в <span></span>
        </button>
    </div>
</div>