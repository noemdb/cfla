<div>
    @php
        $class['iteration'] = '';
        $class['debate_id'] = 'd-none d-lg-table-cell';
        $class['category'] = 'd-none d-sm-table-cell';
        $class['text'] = 'd-none d-lg-table-cell';
        $class['time'] = 'd-none d-lg-table-cell';
        $class['weighting'] = 'd-none d-lg-table-cell';
        $class['observation'] = 'd-none d-lg-table-cell';
        $class['pensum_id'] = 'd-none d-lg-table-cell';
        $class['status_active'] = 'd-none d-lg-table-cell';
        $class['attachment'] = 'd-none d-sm-table-cell';
        $class['action'] = '';
        $table_id = 'table-data-default-competition';
        // name, description, motive, date, status_active, attachment
    @endphp

    <div class="form-group">
        @php
            $name = 'category';
            $model = '' . $name;
        @endphp
        <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        {!! Form::select($model, $list_category, old($model), [
            'wire:model' => $model,
            'class' => 'form-control',
            'id' => $model,
            'placeholder' => 'Selecciones',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <table width="100%" class="table table-striped table-hover table-sm small p-1">
        <thead>
            <tr>
                <th class="{{ $class['iteration'] ?? '' }}">N</th>
                <th class="{{ $class['category'] ?? '' }}">Nivel/Categoría</th>
                <th class="{{ $class['text'] ?? '' }}">{{ $list_comment['text'] ?? '' }}</th>
                <th class="{{ $class['time'] ?? '' }}">
                    {{ $list_comment['time'] ?? '' }}<br>{{ $list_comment['weighting'] ?? '' }}</th>
                <th class="{{ $class['action'] ?? '' }}">Acciones</th>
            </tr>
        </thead>

        <tbody>

            @forelse($questions as $item)
                @php
                    $key = Str::random() . '-' . $item->id;
                    $debate = $item->debate;
                    $pensum = $item->pensum;
                    $user = $item->user;
                    $attachment_url = $item->attachment_url;
                @endphp

                <tr data-id="{{ $item->id }}"
                    class="{{ $item->id == $question_id ? 'bg-secondary font-weight-bold text-light' : null }}">
                    <td class="{{ $class['iteration'] ?? '' }}">{{ $loop->iteration }}</td>
                    <td class="{{ $class['category'] ?? '' }} font-weight-bold text-nowrap">
                        <div class="">{{ $pensum->fullname ?? '' }}</div>
                        <div class="text-success">{{ $item->category ?? '' }}</div>
                        <div class="text-dark font-weight-light pl-2">Últ. revisión: <strong
                                class="font-weight-bold text-uppercase small rounded border table-light text-black p-1 m-1">{{ $user->username ?? '' }}</strong>
                        </div>
                    </td>
                    <td class="{{ $class['text'] ?? '' }}">
                        {{ $item->text ?? '' }}
                        <div class="small text-muted">{{ $item->observation ?? '' }}</div>
                    </td>
                    <td class="{{ $class['time'] ?? '' }}"> {{ $item->time ?? '' }} / {{ $item->weighting ?? '' }}
                    </td>
                    <td class="{{ $class['action'] ?? '' }}">
                        <div class="btn-group btn-group-sm">
                            @php $disabled = null @endphp
                            <button class="btn btn-warning btn-sm"
                                wire:key="{{ $key }}-btn-edit-{{ $item->id }}"
                                wire:click="edit({{ $item->id }})" {{ $disabled ?? null }}
                                wire:loading.attr="disabled" title="Editar"><i
                                    class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i></button>
                            <button class="btn btn-info btn-sm"
                                wire:key="{{ $key }}-btn-option-{{ $item->id }}"
                                wire:click="setModeOptions({{ $item->id }})" {{ $disabled ?? null }}
                                wire:loading.attr="disabled" title="Opciones"><i
                                    class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i></button>
                            @php $disabled = ($item->user_id <> auth()->id()) ? 'disabled' : false; @endphp
                            <button wire:click="question_delete({{ $item->id }})" class="btn btn-danger btn-sm"
                                wire:key="{{ $key }}-btn-item-debate_delete-{{ $item->id }}"
                                {{ $disabled ?? null }} {{ $status ?? null }} {{ $status_date ?? null }}
                                title="Eliminar"><i class="{{ $icon_menus['eliminar'] ?? '' }} fa-1x"></i></button>
                        </div>
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="10" align="center">
                        <strong>No hay datos</strong>
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>



</div>
