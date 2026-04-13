@forelse($patients as $pat)
@php
    $gClass   = $pat->gender === 'M' ? 'male' : ($pat->gender === 'F' ? 'female' : 'unknown');
    $initials = strtoupper(substr($pat->first_name,0,1).substr($pat->last_name,0,1));
@endphp
<tr class="anim-row"
    data-id="{{ $pat->id }}"
    data-name="{{ strtolower($pat->first_name.' '.$pat->last_name) }}"
    data-cedula="{{ strtolower($pat->cedula ?? '') }}"
    data-gender="{{ $pat->gender }}"
    data-branch="{{ $pat->branch_id ?? '0' }}">
    <td>
        <div class="d-flex align-items-center gap-2">
            <div class="pat-avatar {{ $gClass }}">{{ $initials }}</div>
            <div>
                <div class="fw-semibold lh-sm" style="font-size:.9rem;">
                    {{ $pat->first_name }} {{ $pat->last_name }}
                </div>
                <div class="mt-1">
                    @if($pat->gender === 'M')
                        <span class="gender-m"><i class="ri-men-line me-1"></i>M</span>
                    @elseif($pat->gender === 'F')
                        <span class="gender-f"><i class="ri-women-line me-1"></i>F</span>
                    @endif
                </div>
            </div>
        </div>
    </td>
    <td class="col-hide-sm">
        @if($pat->cedula)
            <span class="cedula-chip">{{ $pat->cedula }}</span>
        @else
            <span class="text-muted" style="font-size:.82rem;">—</span>
        @endif
    </td>
    <td>
        @if($pat->phone)
            <span style="font-size:.85rem;"><i class="ri-phone-line me-1 text-muted"></i>{{ $pat->phone }}</span>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td class="col-hide-md">
        @if($pat->branch)
            <span class="branch-chip"><i class="ri-building-2-line"></i>{{ $pat->branch->name }}</span>
        @else
            <span class="text-muted" style="font-size:.82rem;">—</span>
        @endif
    </td>
    <td class="col-hide-md">
        @if($pat->insurance)
            <span class="ins-badge"><i class="ri-shield-check-line"></i>{{ $pat->insurance->name }}</span>
        @else
            <span class="ins-badge ins-private"><i class="ri-user-line"></i>Privado</span>
        @endif
    </td>
    <td class="col-hide-md">
        <span style="font-size:.83rem;" class="text-muted">{{ $pat->email ?: '—' }}</span>
    </td>
    <td class="text-center">
        <div class="d-flex gap-1 justify-content-center">
            <button type="button" class="btn btn-action bg-info-subtle text-info"
                    title="Ver detalle" onclick="openShowModal({{ $pat->id }})">
                <i class="ri-eye-fill fs-13"></i>
            </button>
            <button type="button" class="btn btn-action bg-warning-subtle text-warning"
                    title="Editar" onclick="openEditModal({{ $pat->id }})">
                <i class="ri-pencil-fill fs-13"></i>
            </button>
            <button type="button" class="btn btn-action bg-danger-subtle text-danger"
                    title="Eliminar"
                    onclick="openDeleteModal({{ $pat->id }}, '{{ addslashes($pat->first_name.' '.$pat->last_name) }}')">
                <i class="ri-delete-bin-fill fs-13"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr id="empty-row">
    <td colspan="7">
        <div class="text-center py-5">
            <i class="ri-search-line d-block text-muted mb-3" style="font-size:3.5rem;opacity:.3;"></i>
            <p class="text-muted mb-0">No se encontraron pacientes.</p>
        </div>
    </td>
</tr>
@endforelse