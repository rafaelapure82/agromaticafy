@extends('layouts.admin')

@section('title', 'Actualizar Cliente')
@section('content-header', 'Actualizar Cliente')

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="first_name">Nombre</label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                           id="first_name"
                           placeholder="Nombre" value="{{ old('first_name', $customer->first_name) }}">
                    @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name">Apellido</label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                           id="last_name"
                           placeholder="Apellido" value="{{ old('last_name', $customer->last_name) }}">
                    @error('last_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="document_id">Cédula/RIF</label>
                    <input type="text" name="document_id" class="form-control @error('document_id') is-invalid @enderror"
                           id="document_id"
                           placeholder="Cédula/RIF" value="{{ old('document_id', $customer->document_id) }}">
                    @error('document_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="email"
                           placeholder="Correo Electrónico" value="{{ old('email', $customer->email) }}">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="birthday">Fecha de Nacimiento</label>
                    <input type="date" name="birthday" class="form-control @error('birthday') is-invalid @enderror" id="birthday" value="{{ old('birthday', $customer->birthday) }}">
                    @error('birthday')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tags">Etiquetas</label>
                    <input type="text" name="tags" class="form-control @error('tags') is-invalid @enderror" id="tags" placeholder="VIP, Mayorista" value="{{ old('tags', $customer->tags) }}">
                    @error('tags')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" id="phone"
                           placeholder="Teléfono" value="{{ old('phone', $customer->phone) }}">
                    @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address">Dirección</label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                           id="address"
                           placeholder="Dirección" value="{{ old('address', $customer->address) }}">
                    @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notas / Ficha Técnica</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" id="notes" placeholder="Notas">{{ old('notes', $customer->notes) }}</textarea>
                    @error('notes')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="credit_limit">Límite de Crédito</label>
                    <input type="number" name="credit_limit" class="form-control @error('credit_limit') is-invalid @enderror" id="credit_limit" placeholder="Límite de Crédito" value="{{ old('credit_limit', $customer->credit_limit) }}">
                    @error('credit_limit')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="avatar" id="avatar">
                        <label class="custom-file-label" for="avatar">Elegir archivo</label>
                    </div>
                    @error('avatar')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                    @enderror
                </div>


                <button class="btn btn-success btn-block btn-lg" type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection
