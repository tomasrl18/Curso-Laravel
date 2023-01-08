@if($errors->any())
    <div class="alert alert-danger">
        <h5>Por favor, corrige los errores:</h5>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif