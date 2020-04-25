@php
    $brands = array();
@endphp
<div class="sub-cat-main row no-gutters">
    <div class="col-9">
        <div class="sub-cat-content">
            <div class="sub-cat-list">
                <div class="card-columns">
                    @foreach ($category->subcategories as $subcategory)
                        <div class="card">
                            <ul class="sub-cat-items">
                                <li class="sub-cat-name"><a href="{{ route('products.subcategory', $subcategory->slug) }}">{{ __($subcategory->name) }}</a></li>
                                @foreach ($subcategory->subsubcategories as $subsubcategory)
                                    @php
                                        foreach (json_decode($subsubcategory->brands) as $brand) {
                                            if(!in_array($brand, $brands)){
                                                array_push($brands, $brand);
                                            }
                                        }
                                    @endphp
                                    <li><a href="{{ route('products.subsubcategory', $subsubcategory->slug) }}">{{ __($subsubcategory->name) }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-3">
        <div class="sub-cat-brand">
            <ul class="sub-brand-list">
                @foreach ($brands as $brand_id)
                    @if(\App\Brand::find($brand_id) != null)
                        <li class="sub-brand-item text-center">
                            <a href="{{ route('products.brand', \App\Brand::find($brand_id)->slug) }}" ><img src="{{ asset('frontend/images/placeholder.jpg') }}" data-src="{{ asset(\App\Brand::find($brand_id)->logo) }}" class="img-fluid lazyload" alt="{{ asset(\App\Brand::find($brand_id)->name) }}"></a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</div>
