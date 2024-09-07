@props(['width', 'delete', 'disabled'])
<!--begin::Menu wrapper-->
<div>
    <!--begin::Toggle-->
    <button class="btn btn-light btn-sm btn-icon"
        data-kt-menu-trigger="click"
        data-kt-menu-placement="bottom-end"
        type="button"
        {{ isset($disabled) && $disabled == 'disabled' ? 'disabled' : '' }}>
        <i class="bi bi-three-dots-vertical text-primary"
            style="font-weight: bold; font-size: 2rem;"></i>
    </button>
    <!--end::Toggle-->

    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-{{ $width }}px"
        data-kt-menu="true">
        <div class="menu-item px-3">
            {{ $slot }}

            @if (isset($delete) && $delete != false)
                <form action="{{ $delete }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to delete?')">
                    @method('delete')
                    @csrf
                    <button class="btn w-100 menu-link px-3 text-danger"
                        type="submit">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>
    <!--end::Menu-->
</div>
<!--end::Dropdown wrapper-->
