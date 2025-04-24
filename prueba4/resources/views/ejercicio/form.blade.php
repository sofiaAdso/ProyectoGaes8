
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('titulo') }}</label>
    <div>
        {{ Form::text('titulo', $ejercicio->titulo, ['class' => 'form-control' .
        ($errors->has('titulo') ? ' is-invalid' : ''), 'placeholder' => 'Titulo']) }}
        {!! $errors->first('titulo', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">ejercicio <b>titulo</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('descripcion') }}</label>
    <div>
        {{ Form::text('descripcion', $ejercicio->descripcion, ['class' => 'form-control' .
        ($errors->has('descripcion') ? ' is-invalid' : ''), 'placeholder' => 'Descripcion']) }}
        {!! $errors->first('descripcion', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">ejercicio <b>descripcion</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('instrucciones') }}</label>
    <div>
        {{ Form::text('instrucciones', $ejercicio->instrucciones, ['class' => 'form-control' .
        ($errors->has('instrucciones') ? ' is-invalid' : ''), 'placeholder' => 'Instrucciones']) }}
        {!! $errors->first('instrucciones', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">ejercicio <b>instrucciones</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('nivel') }}</label>
    <div>
        {{ Form::text('nivel', $ejercicio->nivel, ['class' => 'form-control' .
        ($errors->has('nivel') ? ' is-invalid' : ''), 'placeholder' => 'Nivel']) }}
        {!! $errors->first('nivel', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">ejercicio <b>nivel</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('tipo_ejercicio') }}</label>
    <div>
        {{ Form::text('tipo_ejercicio', $ejercicio->tipo_ejercicio, ['class' => 'form-control' .
        ($errors->has('tipo_ejercicio') ? ' is-invalid' : ''), 'placeholder' => 'Tipo Ejercicio']) }}
        {!! $errors->first('tipo_ejercicio', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">ejercicio <b>tipo_ejercicio</b> instruction.</small>
    </div>
</div>

    <div class="form-footer">
        <div class="text-end">
            <div class="d-flex">
                <a href="#" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary ms-auto ajax-submit">Submit</button>
            </div>
        </div>
    </div>
