<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">{{ $attributes['title'] }}</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="javascript:;"></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $attributes['pagetitle'] }}</li>
            </ol>
        </nav>
    </div>
    @if($attributes['settings'] == 'new_store')
        <div class="ms-auto">
            <a href="{{route('store.create')}}" class="btn btn-primary">{{ __('page_title_new_store_button') }}</a>
        </div>
    @endif
    @if($attributes['settings'] == 'statistics')
        <div class="ms-auto">
            <select class="form-select" id="storeSelect" name="store_id">
                <option value="all" selected>{{ __('page_title_all_sources') }}</option>
                @foreach($stores as $store)
                    <option value="{{ $store['store_id'] }}">{{ $store['domain'] }}</option>
                @endforeach
            </select>
        </div>
        {{--        <div class="ms-2">--}}
        {{--            <select class="form-select" id="monthSelect" name="store_id">--}}
        {{--                <option value="all" selected>Összesített adatok</opt>--}}
        {{--                @foreach($months as $monthKey => $monthValue)--}}
        {{--                    <option value="{{ $monthKey }}">{{ $monthValue }}</option>--}}
        {{--                @endforeach--}}
        {{--            </select>--}}
        {{--        </div>--}}
    @endif
    @if($attributes['settings'] == 'store_helper')
        <div class="ms-auto">
            <div class="btn-group">
                <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle" data-bs-toggle="dropdown">
                    {{ __('page_title_store_helper_info') }}
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#apiKeysModal">{{ __('page_title_api_keys') }}</a>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#integrationStepModalLabel">{{ __('page_title_integration_steps') }}</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:;">{{ __('page_title_help') }}</a>
                </div>
            </div>
        </div>
    @endif
</div>
