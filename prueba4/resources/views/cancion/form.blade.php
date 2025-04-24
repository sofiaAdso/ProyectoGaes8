
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('titulo') }}</label>
    <div>
        {{ Form::text('titulo', $cancion->titulo, ['class' => 'form-control' .
        ($errors->has('titulo') ? ' is-invalid' : ''), 'placeholder' => 'Titulo']) }}
        {!! $errors->first('titulo', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">cancion <b>titulo</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('artista') }}</label>
    <div>
        {{ Form::text('artista', $cancion->artista, ['class' => 'form-control' .
        ($errors->has('artista') ? ' is-invalid' : ''), 'placeholder' => 'Artista']) }}
        {!! $errors->first('artista', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">cancion <b>artista</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('descripcion') }}</label>
    <div>
        {{ Form::text('descripcion', $cancion->descripcion, ['class' => 'form-control' .
        ($errors->has('descripcion') ? ' is-invalid' : ''), 'placeholder' => 'Descripcion']) }}
        {!! $errors->first('descripcion', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">cancion <b>descripcion</b> instruction.</small>
    </div>
</div>
<div class="form-group mb-3">
    <label class="form-label">   {{ Form::label('nivel') }}</label>
    <div>
        {{ Form::text('nivel', $cancion->nivel, ['class' => 'form-control' .
        ($errors->has('nivel') ? ' is-invalid' : ''), 'placeholder' => 'Nivel']) }}
        {!! $errors->first('nivel', '<div class="invalid-feedback">:message</div>') !!}
        <small class="form-hint">cancion <b>nivel</b> instruction.</small>
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
