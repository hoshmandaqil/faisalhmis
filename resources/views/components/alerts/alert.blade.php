@props(['class', 'title'])
<!--begin::Alert-->
<div @class(['alert', $class])>
    <!--begin::Wrapper-->
    <div class="d-flex flex-column">
        <!--begin::Title-->
        <h5 class="mb-2">{{ $title }}</h5>
        <!--end::Title-->
        <!--begin::Content-->
        <span>
            {{ $slot }}
        </span>
        <!--end::Content-->
    </div>
    <!--end::Wrapper-->
</div>
<!--end::Alert-->
