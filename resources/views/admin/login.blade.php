@extends('layouts.site')
@section('title', 'Admin-kirjautuminen')

@section('content')
<div class="container">
    <div class="login-box">
        <h1 style="font-size:1.9rem">Hallinta</h1>
        <p style="color:var(--muted)">Syötä admin-salasana, niin pääset muokkaamaan huoneita, galleriaa ja hinnastoa.</p>
        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="field">
                <label for="password">Salasana</label>
                <input id="password" type="password" name="password" required autofocus autocomplete="current-password">
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            <button class="btn" type="submit" style="width:100%">Kirjaudu</button>
        </form>
    </div>
</div>
@endsection
