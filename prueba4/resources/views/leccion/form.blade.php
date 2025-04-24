
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('nombre_leccion') }}</label>
    <div>
        {{ Form::text('nombre_leccion', $leccion->nombre_leccion, ['class' => 'form-control' .
        ($errors->has('nombre_leccion') ? ' is-invalid' : ''), 'placeholder' => 'Nombre Leccion']) }}
        {!! $errors->first('nombre_leccion', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">leccion <b>nombre_leccion</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('descripcion') }}</label>
    <div>
        {{ Form::text('descripcion', $leccion->descripcion, ['class' => 'form-control' .
        ($errors->has('descripcion') ? ' is-invalid' : ''), 'placeholder' => 'Descripcion']) }}
        {!! $errors->first('descripcion', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">leccion <b>descripcion</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('nivel') }}</label>
    <div>
        {{ Form::text('nivel', $leccion->nivel, ['class' => 'form-control' .
        ($errors->has('nivel') ? ' is-invalid' : ''), 'placeholder' => 'Nivel']) }}
        {!! $errors->first('nivel', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">leccion <b>nivel</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('instrumento') }}</label>
    <div>
        {{ Form::text('instrumento', $leccion->instrumento, ['class' => 'form-control' .
        ($errors->has('instrumento') ? ' is-invalid' : ''), 'placeholder' => 'Instrumento']) }}
        {!! $errors->first('instrumento', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">leccion <b>instrumento</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('tablatura') }}</label>
    <div>
        {{ Form::text('tablatura', $leccion->tablatura, ['class' => 'form-control' .
        ($errors->has('tablatura') ? ' is-invalid' : ''), 'placeholder' => 'Tablatura']) }}
        {!! $errors->first('tablatura', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">leccion <b>tablatura</b> instruction.</small>
    </div>
</div>

    <div class="form-footer">
        <div class="text-end">
            <div class="d-flex">
                <a href="#" class="btn btn-danger">Cancelar</a>
                <button type="submit" class="btn btn-primary ms-auto ajax-submit">Agregar</button>
            </div>
        </div>
    </div>
